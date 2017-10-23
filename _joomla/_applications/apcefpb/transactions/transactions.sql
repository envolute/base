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
