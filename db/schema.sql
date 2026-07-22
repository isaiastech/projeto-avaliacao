-- ============================================================
-- Banco de dados: sistema_avaliacao
-- Arquivo: schema.sql
-- Descrição: Estrutura do banco para versionamento no Git
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sistema_avaliacao`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE `sistema_avaliacao`;

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

SET FOREIGN_KEY_CHECKS = 0;


-- ============================================================
-- TABELA: usuarios
-- ============================================================

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('user','avaliador','gerente','admin') NOT NULL DEFAULT 'user',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: perguntas
-- ============================================================

CREATE TABLE IF NOT EXISTS `perguntas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pergunta` text NOT NULL,
  `tipo` enum('objetiva','aberta') DEFAULT 'objetiva',
  `bloco` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `polaridade` enum('positiva','negativa') DEFAULT 'positiva',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: questoes
-- ============================================================

CREATE TABLE IF NOT EXISTS `questoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `questoes` varchar(255)
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- ============================================================
-- TABELA: avaliacoes
-- ============================================================

CREATE TABLE IF NOT EXISTS `avaliacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `avaliador_id` int NOT NULL,
  `avaliado_id` int NOT NULL,
  `ano` int NOT NULL,
  `mes` int NOT NULL,
  `data_avaliacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  UNIQUE KEY `unica_avaliacao`
    (`avaliador_id`, `avaliado_id`, `ano`, `mes`),

  KEY `avaliado_id`
    (`avaliado_id`),

  CONSTRAINT `avaliacoes_ibfk_1`
    FOREIGN KEY (`avaliador_id`)
    REFERENCES `usuarios` (`id`),

  CONSTRAINT `avaliacoes_ibfk_2`
    FOREIGN KEY (`avaliado_id`)
    REFERENCES `usuarios` (`id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: avaliacao_notas
-- ============================================================

CREATE TABLE IF NOT EXISTS `avaliacao_notas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `avaliacao_id` int NOT NULL,
  `questao_id` int NOT NULL,
  `nota` int NOT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: avaliacao_respostas
-- ============================================================

CREATE TABLE IF NOT EXISTS `avaliacao_respostas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `avaliacao_id` int NOT NULL,
  `questao_id` int NOT NULL,
  `nota` tinyint NOT NULL,

  PRIMARY KEY (`id`),

  KEY `avaliacao_id`
    (`avaliacao_id`),

  KEY `questao_id`
    (`questao_id`),

  CONSTRAINT `avaliacao_respostas_ibfk_1`
    FOREIGN KEY (`avaliacao_id`)
    REFERENCES `avaliacoes` (`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: controle_pesquisa
-- ============================================================

CREATE TABLE IF NOT EXISTS `controle_pesquisa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `data_resposta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: respostas_clima
-- ============================================================

CREATE TABLE IF NOT EXISTS `respostas_clima` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `setor` varchar(100) NOT NULL,
  `pergunta_id` int NOT NULL,
  `resposta` text,
  `data_resposta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: recuperacao_senha
-- ============================================================

CREATE TABLE IF NOT EXISTS `recuperacao_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiracao` datetime NOT NULL,
  `utilizado` tinyint(1) DEFAULT '0',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  KEY `usuario_id`
    (`usuario_id`),

  CONSTRAINT `recuperacao_senha_ibfk_1`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- TABELA: avaliacoes_hospedes
-- ============================================================

CREATE TABLE IF NOT EXISTS `avaliacoes_hospedes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hospede` varchar(150)
    COLLATE utf8mb4_unicode_ci NOT NULL,
  `apto` varchar(20)
    COLLATE utf8mb4_unicode_ci NOT NULL,
  `fone` varchar(30)
    COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150)
    COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_avaliacao` date NOT NULL,

  `cafe_manha`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `colchao`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `travesseiro`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `limpeza`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `frigobar`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `chuveiro_aquecimento`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `chuveiro_ducha`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `ar_condicionado`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `roupa_cama`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `internet`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `bar`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `atendimento`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `reserva`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `recepcao`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `camareira`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `garcom`
    enum('otimo','bom','satisfatorio','ruim')
    COLLATE utf8mb4_unicode_ci NOT NULL,

  `sugestoes` text
    COLLATE utf8mb4_unicode_ci,

  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- FINALIZAÇÃO
-- ============================================================

SET FOREIGN_KEY_CHECKS = 1;