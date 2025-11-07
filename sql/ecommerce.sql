CREATE DATABASE  IF NOT EXISTS `ecommerce` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `ecommerce`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: ecommerce
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carrinho`
--

DROP TABLE IF EXISTS `carrinho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrinho` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint(20) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrinho`
--

LOCK TABLES `carrinho` WRITE;
/*!40000 ALTER TABLE `carrinho` DISABLE KEYS */;
INSERT INTO `carrinho` VALUES (1,1,'2025-08-28 20:03:06'),(2,2,'2025-08-28 20:03:06'),(3,3,'2025-08-28 20:03:06');
/*!40000 ALTER TABLE `carrinho` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoriaproduto`
--

DROP TABLE IF EXISTS `categoriaproduto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoriaproduto` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`idCategoria`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoriaproduto`
--

LOCK TABLES `categoriaproduto` WRITE;
/*!40000 ALTER TABLE `categoriaproduto` DISABLE KEYS */;
INSERT INTO `categoriaproduto` VALUES (1,'Instrumentos de Corda','Violões, guitarras, baixos, etc.'),(2,'Instrumentos de Tecla','Pianos digitais, teclados arranjadores, sintetizadores.'),(3,'Instrumentos de Percussão','Baterias, cajons, pandeiros e acessórios.');
/*!40000 ALTER TABLE `categoriaproduto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `classificacao_clientes`
--

DROP TABLE IF EXISTS `classificacao_clientes`;
/*!50001 DROP VIEW IF EXISTS `classificacao_clientes`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `classificacao_clientes` AS SELECT 
 1 AS `nomeUsuario`,
 1 AS `total_gasto`,
 1 AS `classificacao`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `endereco`
--

DROP TABLE IF EXISTS `endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `ibge` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`idEndereco`),
  UNIQUE KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `endereco_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` VALUES (1,1,'Rua das Laranjeiras','123','Apto 10','Centro','São Paulo','SP','01001-000',NULL),(2,2,'Avenida Copacabana','456','Bloco B, Apto 5','Copacabana','Rio de Janeiro','RJ','22020-001',NULL),(3,3,'Praça da Liberdade','789',NULL,'Savassi','Belo Horizonte','MG','30140-010',NULL),(8,9,'Rua da Abolição','1000','','Ponte Preta','Campinas','SP','13041445','3509502'),(9,10,'Rua da Abolição','1000','','Ponte Preta','Campinas','SP','13041445','3509502');
/*!40000 ALTER TABLE `endereco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemcarrinho`
--

DROP TABLE IF EXISTS `itemcarrinho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemcarrinho`
--

LOCK TABLES `itemcarrinho` WRITE;
/*!40000 ALTER TABLE `itemcarrinho` DISABLE KEYS */;
INSERT INTO `itemcarrinho` VALUES (1,1,1,499.90),(2,2,1,899.00),(3,3,1,2499.50);
/*!40000 ALTER TABLE `itemcarrinho` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itempedido`
--

DROP TABLE IF EXISTS `itempedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itempedido` (
  `idItemPedido` bigint(20) NOT NULL AUTO_INCREMENT,
  `idPedido` bigint(20) NOT NULL,
  `idProduto` bigint(20) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `precoUnitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idItemPedido`),
  UNIQUE KEY `uc_pedido_produto` (`idPedido`,`idProduto`),
  KEY `idProduto` (`idProduto`),
  KEY `idx_itempedido_idProduto` (`idProduto`),
  CONSTRAINT `itempedido_ibfk_1` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE,
  CONSTRAINT `itempedido_ibfk_2` FOREIGN KEY (`idProduto`) REFERENCES `produto` (`idProduto`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itempedido`
--

LOCK TABLES `itempedido` WRITE;
/*!40000 ALTER TABLE `itempedido` DISABLE KEYS */;
INSERT INTO `itempedido` VALUES (1,1,1,1,499.90),(2,2,2,1,899.00),(3,3,3,1,2499.50),(4,4,1,3,499.90),(6,7,4,1,100.00);
/*!40000 ALTER TABLE `itempedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logestoque`
--

DROP TABLE IF EXISTS `logestoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logestoque` (
  `idLog` int(11) NOT NULL AUTO_INCREMENT,
  `idProduto` bigint(20) NOT NULL,
  `quantidade_antiga` int(11) NOT NULL,
  `quantidade_nova` int(11) NOT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_responsavel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idLog`),
  KEY `idx_logestoque_idProduto` (`idProduto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logestoque`
--

LOCK TABLES `logestoque` WRITE;
/*!40000 ALTER TABLE `logestoque` DISABLE KEYS */;
INSERT INTO `logestoque` VALUES (2,4,49,48,'2025-09-16 14:48:27','root@localhost'),(3,8,10,25,'2025-10-16 15:04:37','root@localhost');
/*!40000 ALTER TABLE `logestoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamento`
--

DROP TABLE IF EXISTS `pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamento` (
  `idPagamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `idPedido` bigint(20) NOT NULL,
  `idTipoPagamento` bigint(20) NOT NULL,
  `idStatusPagamento` bigint(20) NOT NULL,
  `valorTotal` decimal(10,2) NOT NULL,
  `dataPagamento` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idPagamento`),
  UNIQUE KEY `idPedido` (`idPedido`),
  KEY `idTipoPagamento` (`idTipoPagamento`),
  KEY `idStatusPagamento` (`idStatusPagamento`),
  KEY `idx_pagamento_idTipoPagamento` (`idTipoPagamento`),
  KEY `idx_pagamento_idStatusPagamento` (`idStatusPagamento`),
  CONSTRAINT `fk_pagamento_pedido` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE,
  CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`idTipoPagamento`) REFERENCES `tipopagamento` (`idTipoPagamento`),
  CONSTRAINT `pagamento_ibfk_2` FOREIGN KEY (`idStatusPagamento`) REFERENCES `statuspagamento` (`idStatusPagamento`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamento`
--

LOCK TABLES `pagamento` WRITE;
/*!40000 ALTER TABLE `pagamento` DISABLE KEYS */;
INSERT INTO `pagamento` VALUES (1,1,3,1,525.40,'2025-08-27 10:30:05'),(2,2,2,2,934.00,'2025-08-28 14:00:10'),(3,3,1,2,2584.50,'2025-08-20 09:15:05');
/*!40000 ALTER TABLE `pagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  KEY `idUsuario` (`idUsuario`),
  KEY `idEnderecoEntrega` (`idEnderecoEntrega`),
  KEY `idStatusPedido` (`idStatusPedido`),
  KEY `idTransportadora` (`idTransportadora`),
  KEY `idx_pedido_idUsuario` (`idUsuario`),
  KEY `idx_pedido_idStatusPedido` (`idStatusPedido`),
  KEY `idx_pedido_dataPedido` (`dataPedido`),
  KEY `idx_pedido_codigoRastreio` (`codigoRastreio`),
  CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`),
  CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`idEnderecoEntrega`) REFERENCES `endereco` (`idEndereco`),
  CONSTRAINT `pedido_ibfk_3` FOREIGN KEY (`idStatusPedido`) REFERENCES `statuspedido` (`idStatusPedido`),
  CONSTRAINT `pedido_ibfk_4` FOREIGN KEY (`idTransportadora`) REFERENCES `transportadora` (`idTransportadora`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,1,1,1,1,'2025-08-27 10:30:00',25.50,525.40,NULL),(2,2,2,3,2,'2025-08-28 14:00:00',35.00,934.00,'HA123456789BR'),(3,3,3,4,3,'2025-08-20 09:15:00',85.00,2584.50,'RR987654321BR'),(4,1,1,1,NULL,'2025-09-16 08:44:29',0.00,1499.70,NULL),(5,4,2,1,NULL,'2025-09-16 11:31:46',0.00,950.00,NULL),(7,4,NULL,1,NULL,'2025-09-16 11:48:27',0.00,100.00,NULL);
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto` (
  `idProduto` bigint(20) NOT NULL AUTO_INCREMENT,
  `nomeProduto` varchar(60) NOT NULL,
  `descricaoProduto` text DEFAULT NULL,
  `precoProduto` decimal(10,2) NOT NULL,
  `estoqueProduto` int(11) NOT NULL DEFAULT 0,
  `idCategoria` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  KEY `fk_produto_categoriaproduto` (`idCategoria`),
  KEY `idx_produto_idCategoria` (`idCategoria`),
  KEY `idx_produto_nome` (`nomeProduto`),
  CONSTRAINT `fk_produto_categoriaproduto` FOREIGN KEY (`idCategoria`) REFERENCES `categoriaproduto` (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'Violão Clássico Acústico','Violão de nylon ideal para iniciantes e estudantes de música.',1000.00,12,1,'68f15d3d4068f.jpg'),(2,'Teclado Digital 61 Teclas','Teclado com sensibilidade nas teclas, 128 timbres e ritmos variados.',899.00,10,2,'68f15d134c937.webp'),(3,'Bateria Acústica Completa','Kit de bateria com 5 peças, pratos, banco e baquetas.',2499.50,5,3,'68f15a0d9f686.webp'),(4,'Cabo para Guitarra P10','Cabo de 3 metros para instrumentos.',27.00,48,1,'68f15cd7cdacb.webp'),(8,'Piano Casio','Piano muito bom',2700.00,25,2,'68f10a249dc1c.webp');
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `LOG_ESTOQUE_UPDATE`
AFTER UPDATE ON `produto`
FOR EACH ROW
BEGIN
    IF OLD.estoqueProduto <> NEW.estoqueProduto THEN
        INSERT INTO LogEstoque (idProduto, quantidade_antiga, quantidade_nova, usuario_responsavel)
        VALUES (OLD.idProduto, OLD.estoqueProduto, NEW.estoqueProduto, USER());
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `statuspagamento`
--

DROP TABLE IF EXISTS `statuspagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuspagamento` (
  `idStatusPagamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`idStatusPagamento`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuspagamento`
--

LOCK TABLES `statuspagamento` WRITE;
/*!40000 ALTER TABLE `statuspagamento` DISABLE KEYS */;
INSERT INTO `statuspagamento` VALUES (1,'Pendente','Aguardando a confirmação da instituição financeira.'),(2,'Aprovado','O pagamento foi confirmado com sucesso.'),(3,'Recusado','O pagamento foi negado pela instituição financeira.');
/*!40000 ALTER TABLE `statuspagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuspedido`
--

DROP TABLE IF EXISTS `statuspedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuspedido` (
  `idStatusPedido` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`idStatusPedido`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuspedido`
--

LOCK TABLES `statuspedido` WRITE;
/*!40000 ALTER TABLE `statuspedido` DISABLE KEYS */;
INSERT INTO `statuspedido` VALUES (1,'Aguardando Pagamento','O pedido foi recebido e aguarda a confirmação do pagamento.'),(2,'Em Processamento','Pagamento aprovado, o pedido está sendo preparado para envio.'),(3,'Enviado','O pedido foi coletado pela transportadora e está a caminho.'),(4,'Entregue','O pedido foi entregue ao destinatário.');
/*!40000 ALTER TABLE `statuspedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipopagamento`
--

DROP TABLE IF EXISTS `tipopagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipopagamento` (
  `idTipoPagamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`idTipoPagamento`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipopagamento`
--

LOCK TABLES `tipopagamento` WRITE;
/*!40000 ALTER TABLE `tipopagamento` DISABLE KEYS */;
INSERT INTO `tipopagamento` VALUES (3,'Boleto Bancário'),(1,'Cartão de Crédito'),(2,'PIX');
/*!40000 ALTER TABLE `tipopagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportadora`
--

DROP TABLE IF EXISTS `transportadora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportadora` (
  `idTransportadora` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`idTransportadora`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportadora`
--

LOCK TABLES `transportadora` WRITE;
/*!40000 ALTER TABLE `transportadora` DISABLE KEYS */;
INSERT INTO `transportadora` VALUES (2,'Harmonia Express'),(1,'MeloLog'),(3,'Ritmo Rápido');
/*!40000 ALTER TABLE `transportadora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'admin','admin@admin.com','102030','','','admin','2025-08-28 23:03:06'),(2,'Maria Oliveira','maria.o@email.com','$2a$12$R9h/cIPz0e.pT.4s1T3I5uJc4qg8k9i5E3.K8zG3.B1z2.QzH4a','(21) 91234-567','222.333.444-55','user','2025-08-28 23:03:06'),(3,'Carlos Pereira','carlos.p@email.com','$2a$12$R9h/cIPz0e.pT.4s1T3I5uJc4qg8k9i5E3.K8zG3.B1z2.QzH4a','(31) 95678-123','333.444.555-66','admin','2025-08-28 23:03:06'),(4,'Miguel Silva','Miguel@gmail.com','','19995684253','26512032655','user','2025-09-16 14:27:26'),(9,'adm','adm@gmail.com','$2y$10$eL/7/0unMKSNjnbXs0ftKus3Fe.5N3wO25KDMcv7qp7hWFsrM2QIG','19556452653','45215623599','admin','2025-11-04 12:04:03'),(10,'user','user@gmail.com','$2y$10$rOSzZItq3DePZte1bf1dcOesfkmtMlgF6iwwIrRQgYnFMzyChDYDW','125435682','12536521588','user','2025-11-04 15:00:42');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'ecommerce'
--

--
-- Dumping routines for database 'ecommerce'
--
/*!50003 DROP FUNCTION IF EXISTS `CALCULAR_DESCONTO` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `CALCULAR_DESCONTO`(id_usuario BIGINT, valor_compra_atual DECIMAL(10,2)
) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    DECLARE total_gasto DECIMAL(10,2);
    DECLARE percentual_desconto DECIMAL(4,2) DEFAULT 0.00;
    DECLARE valor_final DECIMAL(10,2);
    
    SELECT IFNULL(SUM(valorTotalPedido), 0) INTO total_gasto
    FROM pedido
    WHERE idUsuario = id_usuario;
    
    IF total_gasto > 2000 THEN
        SET percentual_desconto = 0.15; 
    ELSEIF total_gasto > 1000 THEN
        SET percentual_desconto = 0.10; 
    END IF;

    SET valor_final = valor_compra_atual - (valor_compra_atual * percentual_desconto);
    RETURN valor_final;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `REGISTRA_PEDIDO` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `REGISTRA_PEDIDO`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `classificacao_clientes`
--

/*!50001 DROP VIEW IF EXISTS `classificacao_clientes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `classificacao_clientes` AS select `usuario`.`nomeUsuario` AS `nomeUsuario`,sum(`pedido`.`valorTotalPedido`) AS `total_gasto`,case when sum(`pedido`.`valorTotalPedido`) <= 1000 then 'Bronze' when sum(`pedido`.`valorTotalPedido`) <= 3000 then 'Prata' else 'Ouro' end AS `classificacao` from (`pedido` join `usuario`) where `pedido`.`idUsuario` = `usuario`.`idUsuario` group by `usuario`.`nomeUsuario` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-04 18:18:47
