--
-- Estrutura da tabela `cms_apcefpb_transactions_invoices`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_transactions_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `due_date` date NOT NULL,
  `description` varchar(30) NOT NULL,
  `custom_desc` varchar(30) NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_transactions_invoices_debits`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_transactions_invoices_debits` (
  `invoice_id` int(11) NOT NULL,
  `sequencial` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alter_date` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cms_apcefpb_transactions_invoices_unpaid`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_transactions_invoices_unpaid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- View com valor total da fatura por número
--

CREATE OR REPLACE VIEW `vw_apcefpb_transactions_invoice` AS
SELECT
	`T1`.`id` AS `invoice_id`,
	`T1`.`due_date` AS `due_date`,
	DAY(`T1`.`due_date`) AS `due_day`,
	MONTH(`T1`.`due_date`) AS `due_month`,
	YEAR(`T1`.`due_date`) AS `due_year`,
	IF((`T1`.`custom_desc` <> ''),
	`T1`.`custom_desc`,`T1`.`description`) AS `invoice_desc`,
	`T2`.`id` AS `transaction_id`,
	`T2`.`provider_id` AS `provider_id`,
	`T3`.`name` AS `provider_name`,
	`T2`.`client_id` AS `client_id`,
	`T4`.`user_id` AS `user_id`,
	`T4`.`name` AS `client_name`,
	`T2`.`dependent_id` AS `dependent_id`,
	`T5`.`name` AS `dependent_name`,
	`T2`.`phoneInvoice_id` AS `phoneInvoice_id`,
	`T2`.`phone_id` AS `phone_id`,
	`T2`.`description` AS `description`,
	`T2`.`doc_number` AS `doc_number`,
	`T2`.`fixed` AS `fixed`,
	`T2`.`isCard` AS `isCard`,
	`T2`.`date_installment` AS `date_installment`,
	`T2`.`price` AS `price`,
	`T2`.`installment` AS `installment`,
	`T2`.`total` AS `installments_total`
FROM
(
	(
		(
			(`cms_apcefpb_transactions_invoices` `T1`
				join `cms_apcefpb_transactions` `T2`
				on(((`T2`.`invoice_id` = `T1`.`id`) and (`T2`.`state` = 1)))
			)
			join `cms_base_providers` `T3`
			on((`T3`.`id` = `T2`.`provider_id`))
		)
		join `cms_apcefpb_clients` `T4`
		on((`T4`.`id` = `T2`.`client_id`))
	)
	left join `cms_apcefpb_dependents` `T5`
	on((`T5`.`id` = `T2`.`dependent_id`))
)
WHERE `T1`.`state` = 1
ORDER BY `T1`.`due_date` DESC;

-- --------------------------------------------------------

--
-- View com um sumário de valores por tipo de serviço 'seção'
--

CREATE OR REPLACE VIEW `vw_apcefpb_transactions_invoices_total` AS
SELECT
	`T1`.`id` AS `invoice_id`,
	`T1`.`due_date` AS `due_date`,
	DAY(`T1`.`due_date`) AS `due_day`,
	MONTH(`T1`.`due_date`) AS `due_month`,
	YEAR(`T1`.`due_date`) AS `due_year`,
	IF((`T1`.`custom_desc` <> ''),`T1`.`custom_desc`,`T1`.`description`) AS `invoice_desc`,
	`T2`.`client_id` AS `client_id`,
	`T3`.`user_id` AS `user_id`,
	`T3`.`name` AS `client_name`,
	`T3`.`cpf` AS `client_number`,
	SUM(`T2`.`price`) AS `total`,
	IF(ISNULL(`T5`.`invoice_id`),0,1) AS `unpaid`,
	`T5`.`reason` AS `reason`
FROM
(
	(
		(
			(`cms_apcefpb_transactions_invoices` `T1`
				join `cms_apcefpb_transactions` `T2`
				on(((`T2`.`invoice_id` = `T1`.`id`) and (`T2`.`state` = 1)))
			)
			join `cms_apcefpb_clients` `T3`
			on((`T3`.`id` = `T2`.`client_id`))
		)
		join `cms_users` `T4`
		on((`T4`.`id` = `T3`.`user_id`))
	)
	left join `cms_apcefpb_transactions_invoices_unpaid` `T5`
	on(((`T5`.`client_id` = `T2`.`client_id`) and (`T5`.`invoice_id` = `T1`.`id`)))
)
WHERE `T1`.`state` = 1
GROUP BY `T1`.`id`, `T2`.`client_id`
ORDER BY `T1`.`due_date` DESC




-- -------------------------------------------------------------------------


MIGRAÇÃO


1 - Copiar a tabela "cms_apcefpb_transactions_invoices" da base antiga e copiar para a tabela "migracao_invoices" na base nova

2 - Rodar o comando abaixo (migração dos dados da tabela "transactions_invoices" antiga para a nova)

INSERT INTO `cms_apcefpb_transactions_invoices` (
	`id`,
	`due_date`,
	`description`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`due_date`,
	'Fatura Mensal',
	`note`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_invoices`
ORDER BY `id`
