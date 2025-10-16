-- Arquivo: schema.sql
-- Descrição: Script para criação da estrutura do banco de dados do e-commerce de instrumentos musicais.

CREATE DATABASE  IF NOT EXISTS `ecommerce` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecommerce`;

--
-- Estrutura da tabela `usuario`
--
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `idUsuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `nomeUsuario` varchar(60) NOT NULL,
  `emailUsuario` varchar(60) NOT NULL,
  `senhaHash` varchar(100) NOT NULL,
  `telefoneUsuario` varchar(14) NOT NULL,
  `CPFUsuario` varchar(14) NOT NULL,
  `tipo` enum('user','admin') NOT NULL DEFAULT 'user',
  `dataCriacao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `emailUsuario` (`emailUsuario`),
  UNIQUE KEY `telefoneUsuario` (`telefoneUsuario`),
  UNIQUE KEY `CPFUsuario` (`CPFUsuario`),
  KEY `idx_usuario_nome` (`nomeUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `carrinho`
--
DROP TABLE IF EXISTS `carrinho`;
CREATE TABLE `carrinho` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint(20) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `categoriaproduto`
--
DROP TABLE IF EXISTS `categoriaproduto`;
CREATE TABLE `categoriaproduto` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`idCategoria`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `endereco`
--
DROP TABLE IF EXISTS `endereco`;
CREATE TABLE `endereco` (
  `idEndereco` bigint(20) NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint(20) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `cep` varchar(9) NOT NULL,
  PRIMARY KEY (`idEndereco`),
  UNIQUE KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `endereco_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `produto`
--
DROP TABLE IF EXISTS `produto`;
CREATE TABLE `produto` (
  `idProduto` bigint(20) NOT NULL AUTO_INCREMENT,
  `nomeProduto` varchar(60) NOT NULL,
  `descricaoProduto` text DEFAULT NULL,
  `precoProduto` decimal(10,2) NOT NULL,
  `estoqueProduto` int(11) NOT NULL DEFAULT 0,
  `idCategoria` int(11) NOT NULL,
  PRIMARY KEY (`idProduto`),
  KEY `fk_produto_categoriaproduto` (`idCategoria`),
  KEY `idx_produto_idCategoria` (`idCategoria`),
  KEY `idx_produto_nome` (`nomeProduto`),
  CONSTRAINT `fk_produto_categoriaproduto` FOREIGN KEY (`idCategoria`) REFERENCES `categoriaproduto` (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `itemcarrinho`
--
DROP TABLE IF EXISTS `itemcarrinho`;
CREATE TABLE `itemcarrinho` (
  `idCarrinho` bigint(20) NOT NULL,
  `idProduto` bigint(20) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idCarrinho`,`idProduto`),
  KEY `itemcarrinho_ibfk_1` (`idProduto`),
  CONSTRAINT `fk_carrinho` FOREIGN KEY (`idCarrinho`) REFERENCES `carrinho` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `itemcarrinho_ibfk_1` FOREIGN KEY (`idProduto`) REFERENCES `produto` (`idProduto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `statuspedido`
--
DROP TABLE IF EXISTS `statuspedido`;
CREATE TABLE `statuspedido` (
  `idStatusPedido` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`idStatusPedido`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `transportadora`
--
DROP TABLE IF EXISTS `transportadora`;
CREATE TABLE `transportadora` (
  `idTransportadora` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`idTransportadora`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `pedido`
--
DROP TABLE IF EXISTS `pedido`;
CREATE TABLE `pedido` (
  `idPedido` bigint(20) NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint(20) NOT NULL,
  `idEnderecoEntrega` bigint(20) DEFAULT NULL,
  `idStatusPedido` bigint(20) DEFAULT NULL,
  `idTransportadora` bigint(20) DEFAULT NULL,
  `dataPedido` datetime DEFAULT current_timestamp(),
  `valorFrete` decimal(10,2) DEFAULT 0.00,
  `valorTotalPedido` decimal(10,2) DEFAULT NULL,
  `codigoRastreio` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idPedido`),
  KEY `idx_pedido_idUsuario` (`idUsuario`),
  KEY `idx_pedido_idStatusPedido` (`idStatusPedido`),
  CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`idEnderecoEntrega`) REFERENCES `endereco` (`idEndereco`),
  CONSTRAINT `pedido_ibfk_3` FOREIGN KEY (`idStatusPedido`) REFERENCES `statuspedido` (`idStatusPedido`),
  CONSTRAINT `pedido_ibfk_4` FOREIGN KEY (`idTransportadora`) REFERENCES `transportadora` (`idTransportadora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `itempedido`
--
DROP TABLE IF EXISTS `itempedido`;
CREATE TABLE `itempedido` (
  `idItemPedido` bigint(20) NOT NULL AUTO_INCREMENT,
  `idPedido` bigint(20) NOT NULL,
  `idProduto` bigint(20) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `precoUnitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idItemPedido`),
  UNIQUE KEY `uc_pedido_produto` (`idPedido`,`idProduto`),
  KEY `idx_itempedido_idProduto` (`idProduto`),
  CONSTRAINT `itempedido_ibfk_1` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE,
  CONSTRAINT `itempedido_ibfk_2` FOREIGN KEY (`idProduto`) REFERENCES `produto` (`idProduto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `logestoque`
--
DROP TABLE IF EXISTS `logestoque`;
CREATE TABLE `logestoque` (
  `idLog` int(11) NOT NULL AUTO_INCREMENT,
  `idProduto` bigint(20) NOT NULL,
  `quantidade_antiga` int(11) NOT NULL,
  `quantidade_nova` int(11) NOT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_responsavel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idLog`),
  KEY `idx_logestoque_idProduto` (`idProduto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `statuspagamento`
--
DROP TABLE IF EXISTS `statuspagamento`;
CREATE TABLE `statuspagamento` (
  `idStatusPagamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`idStatusPagamento`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `tipopagamento`
--
DROP TABLE IF EXISTS `tipopagamento`;
CREATE TABLE `tipopagamento` (
  `idTipoPagamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`idTipoPagamento`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `pagamento`
--
DROP TABLE IF EXISTS `pagamento`;
CREATE TABLE `pagamento` (
  `idPagamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `idPedido` bigint(20) NOT NULL,
  `idTipoPagamento` bigint(20) NOT NULL,
  `idStatusPagamento` bigint(20) NOT NULL,
  `valorTotal` decimal(10,2) NOT NULL,
  `dataPagamento` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idPagamento`),
  UNIQUE KEY `idPedido` (`idPedido`),
  KEY `idx_pagamento_idTipoPagamento` (`idTipoPagamento`),
  KEY `idx_pagamento_idStatusPagamento` (`idStatusPagamento`),
  CONSTRAINT `fk_pagamento_pedido` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE,
  CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`idTipoPagamento`) REFERENCES `tipopagamento` (`idTipoPagamento`),
  CONSTRAINT `pagamento_ibfk_2` FOREIGN KEY (`idStatusPagamento`) REFERENCES `statuspagamento` (`idStatusPagamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Trigger para log de estoque
--
DELIMITER $$
CREATE TRIGGER `LOG_ESTOQUE_UPDATE`
AFTER UPDATE ON `produto`
FOR EACH ROW
BEGIN
    IF OLD.estoqueProduto <> NEW.estoqueProduto THEN
        INSERT INTO LogEstoque (idProduto, quantidade_antiga, quantidade_nova, usuario_responsavel)
        VALUES (OLD.idProduto, OLD.estoqueProduto, NEW.estoqueProduto, USER());
    END IF;
END$$
DELIMITER ;

--
-- Function para calcular desconto
--
DELIMITER $$
CREATE FUNCTION `CALCULAR_DESCONTO`(id_usuario BIGINT, valor_compra_atual DECIMAL(10,2)) 
RETURNS decimal(10,2)
DETERMINISTIC
BEGIN
    DECLARE total_gasto DECIMAL(10,2);
    DECLARE percentual_desconto DECIMAL(4,2) DEFAULT 0.00;
    
    SELECT IFNULL(SUM(valorTotalPedido), 0) INTO total_gasto
    FROM pedido
    WHERE idUsuario = id_usuario;
    
    IF total_gasto > 2000 THEN
        SET percentual_desconto = 0.15; 
    ELSEIF total_gasto > 1000 THEN
        SET percentual_desconto = 0.10; 
    END IF;

    RETURN valor_compra_atual - (valor_compra_atual * percentual_desconto);
END$$
DELIMITER ;

--
-- Procedure para registrar pedido
--
DELIMITER $$
CREATE PROCEDURE `REGISTRA_PEDIDO`(
    IN id_usuario BIGINT,
    IN id_produto BIGINT,
    IN quantidade INT
)
BEGIN
    DECLARE estoque_atual INT;
    DECLARE preco_unitario DECIMAL(10,2);
    DECLARE valor_total_sem_desconto DECIMAL(10,2);
    DECLARE valor_final_com_desconto DECIMAL(10,2);
    DECLARE id_endereco BIGINT;
    DECLARE novo_pedido_id BIGINT;

    SELECT estoqueProduto, precoProduto INTO estoque_atual, preco_unitario
    FROM produto
    WHERE idProduto = id_produto;

    IF estoque_atual >= quantidade THEN
        SELECT idEndereco INTO id_endereco
        FROM endereco
        WHERE idUsuario = id_usuario LIMIT 1;

        SET valor_total_sem_desconto = preco_unitario * quantidade;
        SET valor_final_com_desconto = calcular_desconto(id_usuario, valor_total_sem_desconto);

        START TRANSACTION;

        INSERT INTO pedido (idUsuario, idEnderecoEntrega, idStatusPedido, valorTotalPedido)
        VALUES (id_usuario, id_endereco, 1, valor_final_com_desconto); -- Status 1: Aguardando Pagamento

		SET novo_pedido_id = LAST_INSERT_ID();

        INSERT INTO itempedido (idPedido, idProduto, quantidade, precoUnitario)
        VALUES (novo_pedido_id, id_produto, quantidade, preco_unitario);
        
        UPDATE produto
        SET estoqueProduto = estoqueProduto - quantidade
        WHERE idProduto = id_produto;
		COMMIT;

        SELECT 'Pedido registrado com sucesso!' as `Status`;

    ELSE
        SELECT 'Erro: Estoque insuficiente.' as `Status`; 
    END IF;
END$$
DELIMITER ;

--
-- View para classificação de clientes
--
DROP VIEW IF EXISTS `classificacao_clientes`;
CREATE VIEW `classificacao_clientes` AS 
SELECT 
    `u`.`nomeUsuario` AS `nomeUsuario`,
    SUM(`p`.`valorTotalPedido`) AS `total_gasto`,
    CASE
        WHEN SUM(`p`.`valorTotalPedido`) <= 1000 THEN 'Bronze'
        WHEN SUM(`p`.`valorTotalPedido`) <= 3000 THEN 'Prata'
        ELSE 'Ouro'
    END AS `classificacao`
FROM
    (`pedido` `p`
    JOIN `usuario` `u` ON (`p`.`idUsuario` = `u`.`idUsuario`))
GROUP BY `u`.`nomeUsuario`;