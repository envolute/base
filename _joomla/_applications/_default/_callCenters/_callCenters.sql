--
-- Estrutura da tabela `cms_base_callCenters`
--

CREATE TABLE `cms_base_callCenters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isPublic` tinyint(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `showTitle` tinyint(4) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `phone_desc` text NOT NULL,
  `email` text NOT NULL,
  `chat_name` varchar(255) NOT NULL,
  `chat_user` varchar(255) NOT NULL,
  `weblink_text` varchar(255) NOT NULL,
  `weblink_url` text NOT NULL,
  `extra_info` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
