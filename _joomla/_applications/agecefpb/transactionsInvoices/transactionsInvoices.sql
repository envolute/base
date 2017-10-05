--
-- Estrutura da tabela `cms_agecefpb_phones_invoices`
--

CREATE TABLE IF NOT EXISTS `cms_agecefpb_transactions_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- View com valor total da fatura por número
--

CREATE OR REPLACE VIEW `vw_agecefpb_transactions_invoice` AS
SELECT
	`T1`.`id`,
	`T1`.`due_date`,
	DAY(`T1`.`due_date`) due_day,
	MONTH(`T1`.`due_date`) due_month,
	YEAR(`T1`.`due_date`) due_year,
	`T2`.`id` transaction_id,
	`T2`.`provider_id`,
	`T3`.`name` provider_name,
	`T2`.`client_id`,
	`T4`.`name` client_name,
	`T2`.`dependent_id`,
	`T6`.`name` dependent_name,
	`T2`.`description`,
	`T2`.`fixed`,
	`T2`.`isCard`,
	`T2`.`date_installment`,
	`T2`.`price`,
	`T2`.`installment`,
	`T2`.`total` installments_total,
	`T2`.`doc_number`
FROM
	`cms_agecefpb_transactions_invoices` T1
	JOIN `cms_agecefpb_transactions` T2
	ON `T2`.`invoice_id` = `T1`.`id` AND `T2`.`state` = 1
	JOIN `cms_base_providers` T3
	ON `T3`.`id` = `T2`.`provider_id`
	JOIN `cms_agecefpb_clients` T4
	ON `T4`.`id` = `T2`.`client_id`
	JOIN `cms_users` T5
	ON `T5`.`id` = `T4`.`user_id`
	LEFT OUTER JOIN `cms_agecefpb_dependents` T6
	ON `T6`.`id` = `T2`.`dependent_id`
WHERE `T1`.`state` = 1
ORDER BY `T1`.`due_date` DESC;

-- --------------------------------------------------------

--
-- View com um sumário de valores por tipo de serviço 'seção'
--

CREATE OR REPLACE VIEW `vw_agecefpb_transactions_invoices_total` AS
SELECT
	`T1`.`id`,
	`T1`.`due_date`,
	DAY(`T1`.`due_date`) due_day,
	MONTH(`T1`.`due_date`) due_month,
	YEAR(`T1`.`due_date`) due_year,
	`T2`.`client_id`,
	`T3`.`user_id`,
	SUM(`T2`.`price`) total,
	IF(`T5`.`invoice_id` IS NULL, 0, 1) unpaid,
	`T5`.`reason`
FROM
	`cms_agecefpb_transactions_invoices` T1
	JOIN `cms_agecefpb_transactions` T2
	ON `T2`.`invoice_id` = `T1`.`id` AND `T2`.`state` = 1
	JOIN `cms_agecefpb_clients` T3
	ON `T3`.`id` = `T2`.`client_id`
	JOIN `cms_users` T4
	ON `T4`.`id` = `T3`.`user_id`
	LEFT OUTER JOIN `cms_agecefpb_transactions_invoices_unpaid` T5
	ON `T5`.`client_id` = `T2`.`client_id` AND `T5`.`invoice_id` = `T1`.`id`
WHERE `T1`.`state` = 1
GROUP BY `T1`.`id`, `T2`.`client_id`
ORDER BY `T1`.`due_date` DESC
