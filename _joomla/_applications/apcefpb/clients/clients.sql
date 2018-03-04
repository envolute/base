--
-- Estrutura da tabela `cms_apcefpb_clients`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `usergroup` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(10) NOT NULL,
  `rg_orgao` varchar(10) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `birthday` date NOT NULL,
  `marital_status` tinyint(4) NOT NULL,
  `partner` varchar(100) NOT NULL,
  `children` int(11) NOT NULL,
  `cx_email` varchar(255) NOT NULL,
  `cx_code` varchar(15) NOT NULL,
  `cx_role` varchar(100) NOT NULL,
  `cx_situated` varchar(100) NOT NULL,
  `cx_date` date NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `address_number` varchar(10) NOT NULL,
  `address_info` varchar(255) NOT NULL,
  `address_district` varchar(100) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state` varchar(100) NOT NULL DEFAULT 'PB',
  `address_country` varchar(100) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `phone_desc` text NOT NULL,
  `enable_debit` tinyint(4) NOT NULL,
  `agency` varchar(10) NOT NULL,
  `account` varchar(10) NOT NULL,
  `operation` varchar(10) NOT NULL,
  `card_name` varchar(30) NOT NULL,
  `card_limit` decimal(10,2) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `reasonStatus` varchar(100) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_clients_birthday_message`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_clients_birthday_message` (
  `client_id` int(11) NOT NULL,
  `viewed_date` date NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `client_id` (`client_id`,`viewed_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_clients_birthday_sendmail`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_clients_birthday_sendmail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `year` year(4) NOT NULL,
  `sending_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Registra os envios de emails para os aniversariantes';

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_clients_code`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_clients_code` (
  `code` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `cms_apcefpb_clients_code`
--

INSERT INTO `cms_apcefpb_clients_code` (`code`, `date`) VALUES
(100, '2017-10-10 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_clients_files`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_clients_files` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_dependents`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_dependents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `client_code` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '1',
  `gender` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `end_date` date NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `note` varchar(255) NOT NULL,
  `docs` int(11) NOT NULL,
  `name_card` varchar(50) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




-- -------------------------------------------------------------------------


MIGRAÇÃO


1 - Copiar a tabela "cms_apcefpb_clients" da base antiga e copiar para a tabela "migracao_clients" na base nova

2 - Limpar/esvaziar as tabelas "cms_users" e "cms_usermap_user_groups" na base nova

3 - Copiar os dados das tabelas "cms_users" e "cms_usermap_user_groups" da base antiga para a nova

4 - Rodar o comando abaixo (migração dos dados da tabela "clients" antiga para a nova)

INSERT INTO `cms_apcefpb_clients` (
	`id`,
	`user_id`,
	`usergroup`,
	`name`,
	`email`,
	`cpf`,
	`rg`,
	`rg_orgao`,
	`gender`,
	`birthday`,
	`place_birth`,
	`marital_status`,
	`partner`,
	`children`,
	`mother_name`,
	`father_name`,
	`cx_email`,
	`cx_code`,
	`cx_role`,
	`cx_situated`,
	`cx_date`,
	`enable_debit`,
	`card_name`,
	`card_limit`,
	`access`,
	`state`,
	`created_date`,
	`created_by`,
	`agency`,
	`account`,
	`operation`,
	`zip_code`,
	`address`,
	`address_number`,
	`address_info`,
	`address_district`,
	`address_city`,
	`address_state`
) SELECT
	`T1`.`id`,
	`T1`.`user_id`,
	`T1`.`usergroup`,
	`T1`.`name`,
	`T1`.`email`,
	`T1`.`cpf`,
	`T1`.`rg`,
	`T1`.`rg_orgao`,
	`T1`.`gender`,
	`T1`.`birthday`,
	`T1`.`place_birth`,
	IF(`T1`.`marital_status` = 'SOLTEIRO', 1, IF(`T1`.`marital_status` = 'CASADO', 2, IF(`T1`.`marital_status` = 'UNIÃO ESTÁVEL', 3, IF(`T1`.`marital_status` = 'DIVORCIADO', 4, IF(`T1`.`marital_status` = 'VIÚVO', 5, 0))))),
	`T1`.`partner`,
	`T1`.`children`,
	`T1`.`mother_name`,
	`T1`.`father_name`,
	`T1`.`cx_email`,
	`T1`.`cx_matricula`,
	`T1`.`cx_cargo`,
	`T1`.`cx_lotacao`,
	`T1`.`cx_admissao`,
	1,
	`T1`.`name_card`,
	`T1`.`card_limit`,
	`T1`.`state`,
	`T1`.`state`,
	`T1`.`created_date`,
	`T1`.`created_by`,
	`T3`.`agency`,
	`T3`.`account`,
	`T3`.`operation`,
	`T5`.`zip_code`,
	`T5`.`address`,
	`T5`.`address_number`,
	`T5`.`address_info`,
	`T5`.`address_district`,
	`T5`.`address_city`,
	`T5`.`address_state`
FROM `migracao_clients` T1
	LEFT JOIN `migracao_rel_clients_banksAccounts` T2
	ON `T2`.`client_id` = `T1`.`id`
	LEFT JOIN `migracao_banks_accounts` T3
	ON `T3`.`id` = `T2`.`bankAccount_id`
	LEFT JOIN `migracao_rel_clients_addresses` T4
	ON `T4`.`client_id` = `T1`.`id`
	LEFT JOIN `migracao_addresses` T5
	ON `T5`.`id` = `T4`.`address_id`
ORDER BY `id`

-- Obs 1:
-- Corrige o "Marital Status"
-- "SOLTEIRO"		=> 1 (231)
-- "CASADO"		=> 2 (900)
-- "UNIÃO ESTÁVEL"	=> 3 (30)
-- "DIVORCIADO"	=> 4 (55)
-- "VIÚVO"			=> 5 (53)
-- Obs 2:
-- Migra os endereços e as contas bancárias
-- Obs 3:
-- Tinha vários endereços duplicados, foram excluidos os mais antigos

5 - Migrar telefones
	5.1 - Criar view dos dados
	CREATE OR REPLACE VIEW `vw_migracao_clients_phones` AS
	SELECT
		T2.id,
	    T2.name,
	    GROUP_CONCAT(T3.phone_number) phone_number,
	    GROUP_CONCAT(T3.description) whatsapp,
	    GROUP_CONCAT(T3.description) description
	FROM migracao_rel_clients_phones T1
		JOIN cms_apcefpb_clients T2
		ON T1.client_id = T2.id
		JOIN migracao_phones T3
		ON T1.phone_id = T3.id
	GROUP BY T2.id

	5.2 - Atualizar a tabela de clients com base na view
	UPDATE cms_apcefpb_clients AS T1, vw_migracao_clients_phones AS T2
	SET T1.phone = REPLACE(T2.phone_number, ',', ';'), T1.whatsapp = REPLACE(T2.whatsapp, ',', ';'), T1.phone_desc = REPLACE(T2.description, ',', ';')
	WHERE T2.id = T1.id

6 - Atualizar a tabela "cms_apcefpb_clients_code" com o valor da tabela antiga

7 - Copiar dados da tabela "migracao_clients_files" para "cms_apcefpb_clients_files"

8 - Copiar os arquivos do antigo "images/uploads/clients" para o novo "images/apps/clients"
