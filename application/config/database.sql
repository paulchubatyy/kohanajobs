CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `apply` text NOT NULL,
  `created` int(11) unsigned NOT NULL,
  `company` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=4;


INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Unconfirmed user, granted immediately when signin up.'),
(2, 'user', 'Confirmed user, granted after account confirmation via e-mail.'),
(3, 'admin', 'Administrative user, has access to everything.');



CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(24) NOT NULL,
  `last_active` int(10) unsigned NOT NULL,
  `contents` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `last_active` (`last_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `confirmed` tinyint(1) unsigned NOT NULL,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL default '',
  `password` char(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
