--
-- Estrutura da tabela `cms_base_contacts`
--

CREATE TABLE `cms_base_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(10) NOT NULL,
  `rg_orgao` varchar(10) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `birthday` date NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `marital_status` varchar(15) NOT NULL,
  `partner` varchar(100) NOT NULL,
  `children` int(11) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `address_number` varchar(10) NOT NULL,
  `address_info` varchar(255) NOT NULL,
  `address_district` varchar(100) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state` varchar(100) NOT NULL,
  `address_country` varchar(100) NOT NULL,
  `onlyBR` tinyint(4) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `phone_desc` text NOT NULL,
  `chat_name` varchar(255) NOT NULL,
  `chat_user` varchar(255) NOT NULL,
  `weblink_text` varchar(255) NOT NULL,
  `weblink_url` text NOT NULL,
  `extra_info` text NOT NULL,
  `note` varchar(255) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `reasonStatus` varchar(100) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_base_contacts_files`
--

CREATE TABLE `cms_base_contacts_files` (
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
-- Estrutura da tabela `cms_base_rel_contacts_banksAccounts`
--

CREATE TABLE `cms_base_rel_contacts_banksAccounts` (
  `contact_id` int(11) NOT NULL,
  `bankAccount_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `contact_id` (`contact_id`,`bankAccount_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
