--
-- Estrutura da tabela `cms_base_app_sample`
--

CREATE TABLE IF NOT EXISTS `cms_base_app_sample` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(10) NOT NULL,
  `rg_orgao` varchar(10) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `birthday` date NOT NULL,
  `place_birth` varchar(100) NOT NULL,
  `marital_status` varchar(15) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `status_desc` varchar(50) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `address_number` varchar(10) NOT NULL,
  `address_info` varchar(255) NOT NULL,
  `address_district` varchar(100) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state` varchar(2) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_base_app_sample_files`
--

CREATE TABLE IF NOT EXISTS `cms_base_app_sample_files` (
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
