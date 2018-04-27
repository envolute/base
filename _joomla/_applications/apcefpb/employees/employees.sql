--
-- Estrutura da tabela `cms_apcefpb_students`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `card_name` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(10) NOT NULL,
  `rg_orgao` varchar(10) NOT NULL,
  `pis` varchar(20) NOT NULL,
  `ctps` varchar(20) NOT NULL,
  `blood_type` varchar(3) NOT NULL,
  `driver` char(1) NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `gender` tinyint(4) NOT NULL DEFAULT '1',
  `birthday` date NOT NULL,
  `place_birth` varchar(255) NOT NULL,
  `marital_status` tinyint(4) NOT NULL,
  `partner` varchar(100) NOT NULL,
  `partner_cpf` varchar(14) NOT NULL,
  `partner_birthday` date NOT NULL,
  `children` int(11) NOT NULL,
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
  `bank_id` int(11) NOT NULL,
  `agency` varchar(10) NOT NULL,
  `account` varchar(10) NOT NULL,
  `operation` varchar(10) NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_employees_files`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_employees_files` (
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


-- migração

INSERT INTO `cms_apcefpb_employees` (
	`id`,
	`name`,
	`card_name`,
	`email`,
	`birthday`,
	`gender`,
	`mother_name`,
	`father_name`,
	`has_disease`,
	`disease_desc`,
	`has_allergy`,
	`allergy_desc`,
	`blood_type`,
	`state`,
	`created_date`,
	`created_by`,
	`zip_code`,
	`address`,
	`address_number`,
	`address_info`,
	`address_district`,
	`address_city`,
	`address_state`
) SELECT
	`T1`.`id`,
	`T1`.`name`,
	'',
	`T1`.`email`,
	`T1`.`birthday`,
	`T1`.`gender`,
	`T1`.`mother_name`,
	`T1`.`father_name`,
	`T1`.`has_disease`,
	`T1`.`disease_desc`,
	`T1`.`has_allergy`,
	`T1`.`allergy_desc`,
	`T1`.`blood_type`,
	`T1`.`state`,
	`T1`.`created_date`,
	`T1`.`created_by`,
	`T3`.`zip_code`,
	`T3`.`address`,
	`T3`.`address_number`,
	`T3`.`address_info`,
	`T3`.`address_district`,
	`T3`.`address_city`,
	`T3`.`address_state`
FROM `migracao_employees` T1
	LEFT JOIN `migracao_rel_employees_addresses` T2
	ON `T2`.`student_id` = `T1`.`id`
	LEFT JOIN `migracao_addresses` T3
	ON `T3`.`id` = `T2`.`address_id`
ORDER BY `id`

INSERT INTO `cms_apcefpb_employees_files` (
	`id`,
    `id_parent`,
    `index`,
    `filename`,
    `originalName`,
    `filesize`,
    `mimetype`,
    `extension`,
    `created_by`,
    `date_created`
) SELECT
	`id`,
	`id_parent`,
	`index`,
	`filename`,
	`originalName`,
	`filesize`,
	`mimetype`,
	`extension`,
	`created_by`,
	`date_created`
FROM `migracao_employees_files`
ORDER BY `id`
