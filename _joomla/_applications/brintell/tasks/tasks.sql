--
-- Estrutura da tabela `cms_brintell_tasks`
--

CREATE TABLE `cms_brintell_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `issues` varchar(255) NOT NULL,
  `assign_to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` TEXT NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `deadline` DATETIME NOT NULL,
  `timePeriod` char(2) NOT NULL,
  `estimate` tinyint(4) NOT NULL,
  `executed` tinyint(4) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `visibility` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `closing_date` DATETIME NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `type` (`type`),
  KEY `assign_to` (`assign_to`),
  KEY `issues` (`issues`)
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

-- --------------------------------------------------------

--
-- View Issues
--

CREATE OR REPLACE VIEW `vw_brintell_tasks` AS
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
	IF(`T4`.`role_id` = '', `T4`.`occupation`, `T5`.`name`) author_role,
	`T4`.`usergroup` author_group,
	`T4`.`email` author_email,
	`T4`.`gender` author_gender,
	`T4`.`access` author_access,
	`T4`.`state` author_state,
	`T6`.`session_id` author_online,
	GROUP_CONCAT(`T7`.`user_id`) working
FROM
	`cms_brintell_tasks` T1
	JOIN `cms_brintell_projects` T2 ON `T2`.`id` = `T1`.`project_id`
	JOIN `cms_brintell_clients` T3 ON `T3`.`id` = `T2`.`client_id`
	JOIN `cms_brintell_staff` T4 ON `T4`.`user_id` = `T1`.`created_by`
	LEFT OUTER JOIN `cms_brintell_staff_roles` T5 ON `T5`.`id` = `T4`.`role_id`
	LEFT OUTER JOIN `cms_session` T6 ON `T6`.`userid` = `T1`.`created_by` AND `T6`.`client_id` = 0
	LEFT OUTER JOIN `cms_brintell_tasks_timer` T7 ON `T7`.`task_id` = `T1`.`id` AND `T7`.`start_hour` != "00:00:00" AND `T7`.`end_hour` = "00:00:00" AND `T7`.`time` = "00:00:00"
GROUP BY `T1`.`id`
