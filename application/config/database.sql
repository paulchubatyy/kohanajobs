CREATE TABLE jobs (
  id int(10) unsigned NOT NULL auto_increment,
  title varchar(100) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;