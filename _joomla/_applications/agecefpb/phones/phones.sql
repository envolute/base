--
-- Estrutura da tabela `cms_agecefpb_phones`
--

CREATE TABLE IF NOT EXISTS `cms_agecefpb_phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `phone_number` varchar(100) NOT NULL,
  `note` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone_number` (`phone_number`),
  KEY `client_id` (`client_id`,`plan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
