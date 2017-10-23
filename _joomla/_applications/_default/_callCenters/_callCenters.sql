--
-- Estrutura da tabela `cms_base_callCenters`
--

CREATE TABLE IF NOT EXISTS `cms_base_callCenters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isPublic` tinyint(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
