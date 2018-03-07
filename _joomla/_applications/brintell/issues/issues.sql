--
-- Estrutura da tabela `cms_brintell_issues`
--

CREATE TABLE `cms_brintell_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` TEXT NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `deadline` DATETIME NOT NULL,
  `timePeriod` char(2) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_brintell_issues_files`
--

CREATE TABLE `cms_brintell_issues_files` (
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
-- View Issues
--

CREATE OR REPLACE VIEW `vw_brintell_issues` AS
SELECT
	`T1`.*,
	`T2`.`name` project_name,
	`T2`.`state` project_state,
	`T2`.`client_id`,
	`T3`.`name` client_name,
	`T4`.`id` author_id,
	`T4`.`name` author_name,
	`T4`.`nickname` author_nickname,
	`T4`.`type` author_type,
	`T4`.`app` author_app,
	`T4`.`app_table` author_table,
	`T4`.`role` author_role,
	`T4`.`usergroup` author_group,
	`T4`.`email` author_email,
	`T4`.`gender` author_gender,
	`T4`.`access` author_access,
	`T4`.`state` author_state,
	`T5`.`session_id` author_online
FROM
	`cms_brintell_issues` T1
	JOIN `cms_brintell_projects` T2 ON `T2`.`id` = `T1`.`project_id`
	JOIN `cms_brintell_clients` T3 ON `T3`.`id` = `T2`.`client_id`
	JOIN `vw_brintell_teams` T4 ON `T4`.`user_id` = `T1`.`created_by`
	LEFT OUTER JOIN `cms_session` T5 ON `T5`.`userid` = `T1`.`created_by` AND `T5`.`client_id` = 0
