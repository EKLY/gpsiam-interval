CREATE TABLE IF NOT EXISTS `device_state` (
  `device_id` smallint(5) unsigned NOT NULL,
  `state_port` tinyint(3) unsigned NOT NULL,
  `state_name` varchar(200) NOT NULL,
  `state_name_on` varchar(200) NOT NULL,
  `state_name_off` varchar(200) NOT NULL,
  PRIMARY KEY (`device_id`,`state_port`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
