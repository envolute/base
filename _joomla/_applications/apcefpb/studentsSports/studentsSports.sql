--
-- Estrutura da tabela `cms_apcefpb_sports`
--

CREATE TABLE `cms_apcefpb_students_sports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `sport_id` int(11) NOT NULL,
  `registry_date` date NOT NULL,
  `coupon_free` tinyint(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration` (`student_id`, `sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- migração

INSERT INTO `cms_apcefpb_students_sports` (
	`id`,
	`student_id`,
	`sport_id`,
	`registry_date`,
	`coupon_free`,
	`price`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`student_id`,
	`sport_id`,
	`registry_date`,
	`coupon_free`,
	`price`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_students_sports`
ORDER BY `id`
