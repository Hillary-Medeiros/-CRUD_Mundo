CREATE DATABASE crud_mundo;
USE crud_mundo;

-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09-Nov-2025 às 23:48
-- Versão do servidor: 10.4.19-MariaDB
-- versão do PHP: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `crud_mundo`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cidade`
--

CREATE TABLE `cidade` (
  `id_Cidade` int(11) NOT NULL,
  `nome_Cidade` varchar(100) NOT NULL,
  `populacao_Cidade` bigint(20) NOT NULL,
  `id_Pais` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `cidade`
--

INSERT INTO `cidade` (`id_Cidade`, `nome_Cidade`, `populacao_Cidade`, `id_Pais`) VALUES
(1, 'Paris', 2050000, 2),
(2, 'São José dos Campos', 697050, 1),
(3, 'Washington, D.C.', 702250, 13);

-- --------------------------------------------------------

--
-- Estrutura da tabela `continente`
--

CREATE TABLE `continente` (
  `id_Continente` int(11) NOT NULL,
  `nome_Continente` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `continente`
--

INSERT INTO `continente` (`id_Continente`, `nome_Continente`) VALUES
(2, 'África'),
(1, 'América'),
(6, 'Antártida'),
(3, 'Ásia'),
(4, 'Europa'),
(5, 'Oceania');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pais`
--

CREATE TABLE `pais` (
  `id_Pais` int(11) NOT NULL,
  `nome_Pais` varchar(100) NOT NULL,
  `populacao_Pais` float NOT NULL,
  `idioma_Pais` varchar(100) NOT NULL,
  `id_Continente` int(11) NOT NULL,
  `capital_Pais` varchar(100) DEFAULT NULL,
  `moeda_Pais` varchar(100) DEFAULT NULL,
  `bandeira_Pais_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `pais`
--

INSERT INTO `pais` (`id_Pais`, `nome_Pais`, `populacao_Pais`, `idioma_Pais`, `id_Continente`, `capital_Pais`, `moeda_Pais`, `bandeira_Pais_url`) VALUES
(1, 'Brasil', 220050000, 'Português', 1, 'Brasília', 'Brazilian real (BRL)', 'https://flagcdn.com/br.svg'),
(2, 'França', 68370000, 'Francês', 4, 'Paris', 'Euro (EUR)', 'https://flagcdn.com/fr.svg'),
(8, 'Cuba', 10970000, 'Espanhol Cubano', 1, 'Havana', 'Cuban convertible peso (CUC)', 'https://flagcdn.com/cu.svg'),
(13, 'Estados Unidos', 341960000, 'Inglês', 1, '', '', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cidade`
--
ALTER TABLE `cidade`
  ADD PRIMARY KEY (`id_Cidade`),
  ADD KEY `id_Pais` (`id_Pais`);

--
-- Índices para tabela `continente`
--
ALTER TABLE `continente`
  ADD PRIMARY KEY (`id_Continente`),
  ADD UNIQUE KEY `nome_Continente` (`nome_Continente`);

--
-- Índices para tabela `pais`
--
ALTER TABLE `pais`
  ADD PRIMARY KEY (`id_Pais`),
  ADD UNIQUE KEY `nome_Pais` (`nome_Pais`),
  ADD KEY `id_Continente` (`id_Continente`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cidade`
--
ALTER TABLE `cidade`
  MODIFY `id_Cidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `continente`
--
ALTER TABLE `continente`
  MODIFY `id_Continente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pais`
--
ALTER TABLE `pais`
  MODIFY `id_Pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `cidade`
--
ALTER TABLE `cidade`
  ADD CONSTRAINT `cidade_ibfk_1` FOREIGN KEY (`id_Pais`) REFERENCES `pais` (`id_Pais`);

--
-- Limitadores para a tabela `pais`
--
ALTER TABLE `pais`
  ADD CONSTRAINT `pais_ibfk_1` FOREIGN KEY (`id_Continente`) REFERENCES `continente` (`id_Continente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
