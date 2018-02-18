--
-- Estrutura da tabela `cms_brintell_rel_clients_staff`
--

CREATE TABLE `cms_brintell_rel_clients_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `main` tinyint(4) NOT NULL,
  `department` varchar(50) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`,`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relação entre conveniados e contatos';
