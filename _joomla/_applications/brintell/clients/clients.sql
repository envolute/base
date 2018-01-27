--
-- Estrutura da tabela `cms_brintell_clients`
--

CREATE TABLE `cms_brintell_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
