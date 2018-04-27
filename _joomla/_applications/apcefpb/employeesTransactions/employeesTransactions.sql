--
-- Estrutura da tabela `cms_apcefpb_employees_transactions`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_employees_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `doc_number` varchar(50) NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- migração

INSERT INTO `cms_apcefpb_employees_transactions` (
	`id`,
	`registry_id`,
	`invoice_id`,
	`price`,
    `note`,
    `state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`registry_id`,
	`invoice_id`,
	`price`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_employees_payments`
ORDER BY `id`
