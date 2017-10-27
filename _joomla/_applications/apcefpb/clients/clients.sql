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
