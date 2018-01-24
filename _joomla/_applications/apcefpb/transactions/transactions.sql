--
-- Estrutura da tabela `cms_apcefpb_transactions`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `dependent_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `phoneInvoice_id` int(11) NOT NULL,
  `phone_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `fixed` tinyint(4) NOT NULL,
  `isCard` tinyint(4) NOT NULL,
  `date` date NOT NULL,
  `date_installment` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_total` decimal(10,2) NOT NULL,
  `installment` tinyint(4) NOT NULL,
  `total` tinyint(4) NOT NULL,
  `doc_number` varchar(50) NOT NULL,
  `note` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alter_date` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id` (`transaction_id`,`invoice_id`,`fixed`,`installment`,`price`,`price_total`,`description`,`doc_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;




-- -------------------------------------------------------------------------


MIGRAÇÃO


1 - Copiar a tabela "cms_apcefpb_transactions" da base antiga e copiar para a tabela "migracao_transactions" na base nova

2 - Rodar o comando abaixo (migração dos dados da tabela "transactions" antiga para a nova)

INSERT INTO `cms_apcefpb_transactions` (
	`id`,
	`transaction_id`,
	`parent_id`,
	`provider_id`,
	`client_id`,
	`dependent_id`,
	`invoice_id`,
	`description`,
	`fixed`,
	`isCard`,
	`date`,
	`date_installment`,
	`price`,
	`price_total`,
	`installment`,
	`charged`,
	`total`,
	`doc_number`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`transaction_id`,
	`parent_id`,
	`provider_id`,
	`client_id`,
	`dependent_id`,
	`invoice_id`,
	`description`,
	`fixed`,
	`isCard`,
	`date`,
	`date_installment`,
	`price`,
	`price_total`,
	`installment`,
	1,
	`total`,
	`doc_number`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_transactions`
ORDER BY `id`


-- Transações após a migração
INSERT INTO `cms_apcefpb_transactions` (`id`, `transaction_id`, `parent_id`, `provider_id`, `client_id`, `dependent_id`, `invoice_id`, `phoneInvoice_id`, `phone_id`, `description`, `fixed`, `isCard`, `date`, `date_installment`, `price`, `price_total`, `installment`, `charged`, `total`, `doc_number`, `note`, `state`, `created_by`, `created_date`, `alter_date`, `alter_by`) VALUES
(14621, 14621, 0, 12, 474, 0, 0, 0, 0, 'Aluguel do Salão de Festas para o dia 23/06/2018', 0, 0, '2018-01-19', '2018-01-19', '500.00', '500.00', 1, 0, 2, '', '', 1, 1511, '2018-01-19 12:25:17', '2018-01-19 12:27:19', 1511),
(14622, 14621, 14621, 12, 474, 0, 0, 0, 0, 'Aluguel do Salão de Festas para o dia 23/06/2018', 0, 0, '2018-02-19', '2018-02-19', '500.00', '500.00', 2, 0, 2, '', '', 1, 1511, '2018-01-19 12:25:17', '2018-01-19 12:27:44', 1511),
(14623, 14623, 0, 1, 59, 0, 0, 0, 0, 'Taxa de Churrasqueira Master', 0, 0, '2018-01-20', '2018-01-20', '100.00', '100.00', 1, 1, 1, '10504', '', 1, 1526, '2018-01-20 09:48:18', '0000-00-00 00:00:00', 0),
(14903, 14903, 0, 1, 1091, 0, 0, 0, 0, 'Diaria Apartamento', 0, 0, '2018-01-22', '2018-01-22', '160.00', '160.00', 1, 1, 1, '10505', '', 1, 1526, '2018-01-22 10:36:24', '2018-01-22 11:13:25', 1526);
COMMIT;


MIGRAÇÃO PROVIDERS


1 - Copiar a tabela "cms_apcefpb_providers" da base antiga e copiar para a tabela "migracao_providers" na base nova

2 - Rodar o comando abaixo (migração dos dados da tabela "providers" antiga para a nova)

INSERT INTO `cms_base_providers` (
	`id`,
	`group_id`,
	`name`,
	`email`,
	`state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`group_id`,
	`name`,
	`email`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_providers`
ORDER BY `id`
