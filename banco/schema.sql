-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Set-2022 às 03:35
-- Versão do servidor: 10.4.21-MariaDB
-- versão do PHP: 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `importnfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_itens_nota`
--

CREATE TABLE `tbl_itens_nota` (
  `id` int(11) NOT NULL,
  `data_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'DATA CADASTRO',
  `id_nota` int(11) NOT NULL COMMENT 'ID DA NOTA REFERENET AO PRODUTO',
  `codigo` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8_bin NOT NULL,
  `codigo_barra` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `ncm` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `cfop` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `un_medida` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `qtd` decimal(10,2) NOT NULL,
  `vl_unidade` decimal(14,2) DEFAULT NULL,
  `vl_total` decimal(14,2) DEFAULT NULL,
  `data_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `tbl_itens_nota`
--

INSERT INTO `tbl_itens_nota` (`id`, `data_created`, `id_nota`, `codigo`, `nome`, `codigo_barra`, `ncm`, `cfop`, `un_medida`, `qtd`, `vl_unidade`, `vl_total`, `data_update`) VALUES
(30, '2022-09-30 01:18:10', 22, '0035487', 'CALCA CONT. SKINNY COLOR', 'SEM GTIN', '62034200', '6108', 'UN', '1.00', '300.16', '300.16', NULL),
(31, '2022-09-30 01:18:10', 22, '0062680', 'POLO FRISO DUPLO', 'SEM GTIN', '61051000', '6108', 'UN', '1.00', '154.94', '154.94', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_notas`
--

CREATE TABLE `tbl_notas` (
  `id` int(11) NOT NULL,
  `data_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'DATA CADASTRO',
  `numero` int(11) NOT NULL,
  `chave` varchar(50) CHARACTER SET utf8 NOT NULL,
  `protocolo` varchar(50) CHARACTER SET utf8 NOT NULL,
  `natop` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Resumo da Natureza de operação venda / saida',
  `emissao` date NOT NULL COMMENT 'Data de emissão da Nota Fiscal',
  `serie` varchar(25) CHARACTER SET utf8 NOT NULL,
  `tpnf` int(11) NOT NULL COMMENT '0-entrada / 1-saída',
  `emit_nome` varchar(255) CHARACTER SET utf8 NOT NULL,
  `emit_cnpj_cpf` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `emit_ie` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `emit_uf` varchar(2) CHARACTER SET utf8 DEFAULT NULL,
  `emit_municipio` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `emit_bairro` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `emit_endereco` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `emit_numero` varchar(50) CHARACTER SET utf16 DEFAULT NULL,
  `emit_comp` varchar(50) CHARACTER SET utf16 DEFAULT NULL,
  `emit_cep` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `emit_fone` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `dest_nome` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'NOME DESTINATARIO',
  `dest_cnpj_cpf` varchar(25) CHARACTER SET utf8 DEFAULT NULL COMMENT 'CPF/ CNPJ DESTINATARIO',
  `vl_produto` decimal(14,2) DEFAULT NULL COMMENT 'VALOR DOS PRODUTOS',
  `vl_frete` decimal(14,2) DEFAULT NULL COMMENT 'VALOR FRETE',
  `vl_total` decimal(14,2) DEFAULT NULL COMMENT 'VALOR TOTAL NFE',
  `data_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `tbl_notas`
--

INSERT INTO `tbl_notas` (`id`, `data_created`, `numero`, `chave`, `protocolo`, `natop`, `emissao`, `serie`, `tpnf`, `emit_nome`, `emit_cnpj_cpf`, `emit_ie`, `emit_uf`, `emit_municipio`, `emit_bairro`, `emit_endereco`, `emit_numero`, `emit_comp`, `emit_cep`, `emit_fone`, `dest_nome`, `dest_cnpj_cpf`, `vl_produto`, `vl_frete`, `vl_total`, `data_update`) VALUES
(22, '2022-09-30 01:18:10', 49253681, '33220716590234006450550050004408981492536815', '333220137451183', 'VENDAS DE ECOMMERCE', '0000-00-00', '5', 1, 'AREZZO INDUSTRIA E COMERCIO S.A', '16590234006450', NULL, 'RJ', 'SAO JOAO DE MERITI', 'PARQUE JURITI', 'AV ARTHUR ANTONIO SENDAS', '999', 'GALPAO A B', '25585085', '', 'RHYANN CARVALHAIS', '09255238604', '455.10', '0.00', '455.10', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tbl_itens_nota`
--
ALTER TABLE `tbl_itens_nota`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tbl_notas`
--
ALTER TABLE `tbl_notas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tbl_itens_nota`
--
ALTER TABLE `tbl_itens_nota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `tbl_notas`
--
ALTER TABLE `tbl_notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
