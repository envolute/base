--
-- Estrutura da tabela `cms_apcefpb_employees_invoices`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_employees_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `due_date` date NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- migração

INSERT INTO `cms_apcefpb_employees_invoices` (
	`id`,
	`due_date`,
    `note`,
    `state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`due_date`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_employees_invoices`
ORDER BY `id`
