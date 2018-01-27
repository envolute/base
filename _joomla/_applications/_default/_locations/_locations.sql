--
-- Estrutura da tabela `cms_base_locations`
--

CREATE TABLE `cms_base_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isPublic` tinyint(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `address_number` varchar(10) NOT NULL,
  `address_info` varchar(100) NOT NULL,
  `address_district` varchar(50) NOT NULL,
  `address_city` varchar(50) NOT NULL,
  `address_state` varchar(50) NOT NULL,
  `address_country` varchar(50) NOT NULL DEFAULT 'BRASIL',
  `onlyBR` tinyint(4) NOT NULL,
  `latitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `map_info` varchar(255) NOT NULL,
  `extra_info` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
