--
-- Estrutura da tabela `cms_brintell_clients_staff`
--

CREATE TABLE `cms_brintell_clients_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usergroup` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `role` varchar(50) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `reasonStatus` varchar(100) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_brintell_clients_staff_files`
--

CREATE TABLE `cms_brintell_clients_staff_files` (
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
-- View com todos os usuários do sistema 'staff' & 'clientsStaff'
--

CREATE OR REPLACE VIEW `vw_brintell_teams` AS
(SELECT `id` staff_id, '0' clientsStaff_id, `type`, 'staff' app, 'staff' app_table, `role_id`, '1' client_id, `user_id`, `usergroup`, `name`, `nickname`, `email`, `gender`, `occupation`, `access`, `state` FROM `cms_brintell_staff` WHERE `state` = 1)
UNION
(SELECT '0', `id`, '2', 'clientsStaff' app, 'clients_staff' app_table, '0', `client_id`, `user_id`, `usergroup`, `name`, '', `email`, `gender`, `role`, `access`, `state` FROM `cms_brintell_clients_staff` WHERE `state` = 1)
