CREATE TABLE IF NOT EXISTS `#__simplechatsupport_message` (
  `message_id` int(11) NOT NULL auto_increment,
  `chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `user_name` varchar(64) default NULL,
  `message` text,
  `post_time` datetime default NULL,
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__simplechatsupport_chat` (
  `chat_id` int(11) NOT NULL auto_increment,
  `chat_name` varchar(64) default NULL,
  `start_time` datetime default NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY  (`chat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__simplechatsupport_template` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `created_on` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__simplechatsupport_status` (
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__simplechatsupport_status` (`status`) VALUES (0);

