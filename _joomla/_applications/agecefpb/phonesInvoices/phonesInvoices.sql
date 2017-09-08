--
-- Estrutura da tabela `cms_agecefpb_phones_invoices`
--

CREATE TABLE IF NOT EXISTS `cms_agecefpb_phones_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `note` varchar(255) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `reasonStatus` varchar(100) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_agecefpb_phones_invoices_details`
--

CREATE TABLE IF NOT EXISTS `cms_agecefpb_phones_invoices_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `tel` varchar(100) NOT NULL,
  `secao` varchar(100) NOT NULL,
  `data` varchar(100) NOT NULL,
  `hora` varchar(100) NOT NULL,
  `origem_destino` varchar(100) NOT NULL,
  `numero` varchar(100) NOT NULL,
  `duracao` varchar(100) NOT NULL,
  `tarifa` float NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_cobrado` decimal(10,2) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cc` varchar(100) NOT NULL,
  `matricula` varchar(100) NOT NULL,
  `sub_secao` varchar(100) NOT NULL,
  `tipo_imposto` varchar(100) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Detalhamento da Fatura Telefônica';

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_agecefpb_phones_invoices_files`
--

CREATE TABLE IF NOT EXISTS `cms_agecefpb_phones_invoices_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `index` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `originalName` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `mimetype` varchar(50) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `group` varchar(50) NOT NULL,
  `groupType` varchar(5) NOT NULL,
  `class` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`),
  KEY `id_parent` (`id_parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- View com valor total da fatura por número
--

CREATE OR REPLACE VIEW `vw_agecefpb_phones_invoices_phone_total` AS
SELECT
	`T1`.`invoice_id`,
	`T2`.`due_date`,
	`T3`.`client_id`,
	`T6`.`state` client_state,
	`T6`.`user_id`,
	`T6`.`name`,
	`T6`.`cpf`,
	`T6`.`cx_code`,
	`T3`.`id` phone_id,
	`T3`.`state` phone_state,
	`T1`.`tel`,
	`T4`.`id` plan_id,
	`T4`.`state` plan_state,
	`T4`.`name` plan,
	`T5`.`id` provider_id,
	`T5`.`state` provider_state,
	`T5`.`name` provider,
	SUM(`T1`.`valor_cobrado`) AS `valor_cobrado`,
	`T4`.`price` valor_plano,
	`T2`.`tax` taxa_servico,
	(SUM(`T1`.`valor_cobrado`) + `T4`.`price` + `T2`.`tax`) AS `total`,
	`T1`.`created_by`
FROM `cms_agecefpb_phones_invoices_details` T1
	LEFT OUTER JOIN `cms_agecefpb_phones_invoices` T2
	ON `T2`.`id` = `T1`.`invoice_id`
	LEFT OUTER JOIN `cms_agecefpb_phones` T3
	ON SUBSTRING_INDEX(`T3`.`phone_number`,' ',-1) LIKE SUBSTRING_INDEX(`T1`.`tel`,' ',-1)
	LEFT OUTER JOIN `cms_agecefpb_phones_plans` T4
	ON `T4`.`id` = `T3`.`plan_id`
	LEFT OUTER JOIN `cms_base_providers` T5
	ON `T5`.`id` = `T4`.`provider_id`
	LEFT OUTER JOIN `cms_agecefpb_clients` T6
	ON `T6`.`id` = `T3`.`client_id`
WHERE `T1`.`tel` <> ""
GROUP BY `T1`.`tel`
ORDER BY `T1`.`invoice_id`, `T6`.`name`;

-- --------------------------------------------------------

--
-- View com um sumário de valores por tipo de serviço 'seção'
--

CREATE OR REPLACE VIEW `vw_agecefpb_phones_invoices_summary` AS
SELECT
	`T1`.`invoice_id`,
	`T3`.`id` phone_id,
	`T1`.`tel`,
	`T1`.`sub_secao`,
	`T1`.`secao`,
	SUM(`T1`.`valor_cobrado`) valor_cobrado,
	`T1`.`created_by`
FROM `cms_agecefpb_phones_invoices_details` T1
	LEFT OUTER JOIN `cms_agecefpb_phones_invoices` T2
	ON `T2`.`id` = `T1`.`invoice_id`
	LEFT OUTER JOIN `cms_agecefpb_phones` T3
	ON SUBSTRING_INDEX(`T3`.`phone_number`,' ',-1) LIKE SUBSTRING_INDEX(`T1`.`tel`,' ',-1)
WHERE `tel` <> ""
GROUP BY CONCAT(`T1`.`tel`, " - ", `T1`.`secao`)
ORDER BY `T1`.`tel`, `T1`.`sub_secao`;
