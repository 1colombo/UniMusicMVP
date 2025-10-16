
USE `ecommerce`;

INSERT INTO `categoriaproduto` (`idCategoria`, `nome`, `descricao`) VALUES
(1, 'Instrumentos de Corda', 'Violões, guitarras, baixos, etc.'),
(2, 'Instrumentos de Tecla', 'Pianos digitais, teclados arranjadores, sintetizadores.'),
(3, 'Instrumentos de Percussão', 'Baterias, cajons, pandeiros e acessórios.');

INSERT INTO `produto` (`nomeProduto`, `descricaoProduto`, `precoProduto`, `estoqueProduto`, `idCategoria`) VALUES
('Violão Clássico Acústico', 'Violão de nylon ideal para iniciantes e estudantes de música.', 499.90, 15, 1),
('Teclado Digital 61 Teclas', 'Teclado com sensibilidade nas teclas, 128 timbres e ritmos variados.', 899.00, 10, 2),
('Bateria Acústica Completa', 'Kit de bateria com 5 peças, pratos, banco e baquetas.', 2499.50, 5, 3),
('Cabo para Guitarra P10', 'Cabo de 3 metros para instrumentos, com pontas reforçadas.', 79.90, 50, 1);

INSERT INTO `usuario` (`idUsuario`, `nomeUsuario`, `emailUsuario`, `senhaHash`, `telefoneUsuario`, `CPFUsuario`, `tipo`, `dataCriacao`) VALUES
(1, 'Admin Teste', 'admin@unicode.com', 'hash_de_senha_muito_seguro', '99999999999', '111.111.111-11', 'admin', NOW());

INSERT INTO `statuspedido` (`idStatusPedido`, `nome`, `descricao`) VALUES
(1, 'Aguardando Pagamento', 'Pedido realizado, esperando confirmação de pagamento.'),
(2, 'Em Processamento', 'Pagamento confirmado, pedido sendo preparado.'),
(3, 'Enviado', 'Pedido saiu para entrega.'),
(4, 'Entregue', 'Pedido entregue ao cliente.');

INSERT INTO `tipopagamento` (`idTipoPagamento`, `nome`) VALUES
(1, 'Cartão de Crédito'),
(2, 'PIX'),
(3, 'Boleto Bancário');

INSERT INTO `statuspagamento` (`idStatusPagamento`, `nome`, `descricao`) VALUES
(1, 'Pendente', 'Pagamento aguardando processamento.'),
(2, 'Aprovado', 'Pagamento efetuado com sucesso.'),
(3, 'Recusado', 'Pagamento não foi aprovado.');

INSERT INTO `transportadora` (`idTransportadora`, `nome`) VALUES
(1, 'Correios'),
(2, 'Harmonia Express'),
(3, 'MeloLog');