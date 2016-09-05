CREATE TABLE `devices` (
  `device_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `device_key` varchar(200) NOT NULL,
  `device_name` varchar(200) NOT NULL,
  `current_engine` tinyint(3) unsigned NOT NULL,
  `current_speed` smallint(6) NOT NULL,
  `current_gsm` smallint(6) NOT NULL,
  `current_gps` smallint(6) NOT NULL,
  `location_lat` decimal(10,6) NOT NULL,
  `location_lng` decimal(10,6) NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `device_key` (`device_key`)
) ENGINE=MyISAM AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8;
