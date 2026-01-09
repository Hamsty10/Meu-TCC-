-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/11/2025 às 22:50
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `servigera`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `IDavaliacao` int(11) NOT NULL,
  `IDsolicitacao` int(11) NOT NULL,
  `nota` tinyint(1) NOT NULL CHECK (`nota` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `IDcliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `data_nascimento` date NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `foto` longblob DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `especialidades`
--

CREATE TABLE `especialidades` (
  `IDespecialidade` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `especialidades`
--

INSERT INTO `especialidades` (`IDespecialidade`, `nome`) VALUES
(1, 'Pintura'),
(2, 'Elétrica'),
(3, 'Reformas e Reparo'),
(4, 'Pedreiro'),
(5, 'Encanador'),
(6, 'Assistência Técnica'),
(7, 'Eventos'),
(8, 'Design e Tecnologia'),
(9, 'Consultoria'),
(10, 'Aulas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `propostas`
--

CREATE TABLE `propostas` (
  `IDproposta` int(11) NOT NULL,
  `IDsolicitacao` int(11) NOT NULL,
  `IDtecnico` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `status` enum('pendente','aceita','rejeitada') DEFAULT 'pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes`
--

CREATE TABLE `solicitacoes` (
  `IDsolicitacao` int(11) NOT NULL,
  `IDcliente` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('Aberto','Em Negociação','Concluído') DEFAULT 'Aberto',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `cliente_finalizado` tinyint(1) DEFAULT 0,
  `tecnico_finalizado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tecnicos`
--

CREATE TABLE `tecnicos` (
  `IDtecnico` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `data_nascimento` date NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `foto` longblob DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tecnico_especialidade`
--

CREATE TABLE `tecnico_especialidade` (
  `IDtec_esp` int(11) NOT NULL,
  `IDtecnico` int(11) NOT NULL,
  `IDespecialidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `IDusuario` int(11) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `login` varchar(200) NOT NULL,
  `tipo` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`IDusuario`, `senha`, `login`, `tipo`) VALUES
(37, '$2y$10$Lm.FFJkJbieMkmhkdgH99.m.VGy/cgYWKKLaATFkhYJc8ebdg2AXS', 'admin', 'A');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`IDavaliacao`),
  ADD KEY `IDsolicitacao` (`IDsolicitacao`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`IDcliente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`IDespecialidade`);

--
-- Índices de tabela `propostas`
--
ALTER TABLE `propostas`
  ADD PRIMARY KEY (`IDproposta`),
  ADD KEY `IDsolicitacao` (`IDsolicitacao`),
  ADD KEY `IDtecnico` (`IDtecnico`);

--
-- Índices de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD PRIMARY KEY (`IDsolicitacao`),
  ADD KEY `IDcliente` (`IDcliente`);

--
-- Índices de tabela `tecnicos`
--
ALTER TABLE `tecnicos`
  ADD PRIMARY KEY (`IDtecnico`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `tecnico_especialidade`
--
ALTER TABLE `tecnico_especialidade`
  ADD PRIMARY KEY (`IDtecnico`,`IDespecialidade`),
  ADD UNIQUE KEY `IDtec_esp` (`IDtec_esp`),
  ADD KEY `IDespecialidade` (`IDespecialidade`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IDusuario`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `IDavaliacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `IDcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de tabela `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `IDespecialidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `propostas`
--
ALTER TABLE `propostas`
  MODIFY `IDproposta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  MODIFY `IDsolicitacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de tabela `tecnicos`
--
ALTER TABLE `tecnicos`
  MODIFY `IDtecnico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de tabela `tecnico_especialidade`
--
ALTER TABLE `tecnico_especialidade`
  MODIFY `IDtec_esp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IDusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`IDsolicitacao`) REFERENCES `solicitacoes` (`IDsolicitacao`) ON DELETE CASCADE;

--
-- Restrições para tabelas `propostas`
--
ALTER TABLE `propostas`
  ADD CONSTRAINT `propostas_ibfk_1` FOREIGN KEY (`IDsolicitacao`) REFERENCES `solicitacoes` (`IDsolicitacao`) ON DELETE CASCADE,
  ADD CONSTRAINT `propostas_ibfk_2` FOREIGN KEY (`IDtecnico`) REFERENCES `tecnicos` (`IDtecnico`) ON DELETE CASCADE;

--
-- Restrições para tabelas `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD CONSTRAINT `solicitacoes_ibfk_1` FOREIGN KEY (`IDcliente`) REFERENCES `cliente` (`IDcliente`);

--
-- Restrições para tabelas `tecnico_especialidade`
--
ALTER TABLE `tecnico_especialidade`
  ADD CONSTRAINT `tecnico_especialidade_ibfk_1` FOREIGN KEY (`IDtecnico`) REFERENCES `tecnicos` (`IDtecnico`) ON DELETE CASCADE,
  ADD CONSTRAINT `tecnico_especialidade_ibfk_2` FOREIGN KEY (`IDespecialidade`) REFERENCES `especialidades` (`IDespecialidade`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
