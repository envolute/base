--
-- Estrutura da tabela `cms_brintell_tasks`
--

CREATE TABLE `cms_brintell_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `requests` varchar(255) NOT NULL,
  `assign_to` varchar(255) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `description` TEXT NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `deadline` DATE NOT NULL,
  `estimate` tinyint(4) NOT NULL,
  `executed` tinyint(4) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `visibility` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `status_desc` varchar(255) NOT NULL,
  `orderer` tinyint(4) NOT NULL,
  `closing_date` DATETIME NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `type` (`type`),
  KEY `team` (`team`),
  KEY `requests` (`requests`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_brintell_tasks_files`
--

CREATE TABLE `cms_brintell_tasks_files` (
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
