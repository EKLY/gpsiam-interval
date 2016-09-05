
CREATE TABLE `device_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` smallint(6) NOT NULL,
  `log_engine` tinyint(3) unsigned NOT NULL,
  `log_speed` smallint(6) NOT NULL,
  `log_gsm` smallint(6) NOT NULL,
  `log_gps` smallint(6) NOT NULL,
  `location_lat` decimal(10,6) NOT NULL,
  `location_lng` decimal(10,6) NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `device_id_update_time` (`device_id`,`update_time`)
) ENGINE=MyISAM AUTO_INCREMENT=10000001 DEFAULT CHARSET=utf8;
