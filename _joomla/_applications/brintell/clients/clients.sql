--
-- Estrutura da tabela `cms_brintell_clients`
--

CREATE TABLE `cms_brintell_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `portfolio` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `due_date` tinyint(4) NOT NULL,
  `start_date` DATE NOT NULL,
  `website` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_brintell_clients_files`
--

CREATE TABLE `cms_brintell_clients_files` (
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

--
-- Estrutura da tabela `cms_brintell_rel_clients_banksAccounts`
--

CREATE TABLE `cms_brintell_rel_clients_banksAccounts` (
  `client_id` int(11) NOT NULL,
  `bankAccount_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `contact_id` (`client_id`,`bankAccount_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_brintell_rel_clients_callCenters`
--

CREATE TABLE `cms_brintell_rel_clients_callCenters` (
  `client_id` int(11) NOT NULL,
  `callCenter_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `contact_id` (`client_id`,`callCenter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_brintell_rel_clients_locations`
--

CREATE TABLE `cms_brintell_rel_clients_locations` (
  `client_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `contact_id` (`client_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relacionamento entre conveniados e endereços';
