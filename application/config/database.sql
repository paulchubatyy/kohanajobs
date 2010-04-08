CREATE TABLE `jobs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `apply` text NOT NULL,
  `created` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
  `session_id` varchar(24) NOT NULL,
  `last_active` int(10) unsigned NOT NULL,
  `contents` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `last_active` (`last_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;