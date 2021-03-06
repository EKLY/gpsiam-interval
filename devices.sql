CREATE TABLE IF NOT EXISTS `devices` (
  `device_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `device_key` varchar(200) NOT NULL,
  `device_name` varchar(200) NOT NULL,
  `current_engine` tinyint(3) unsigned NOT NULL,
  `current_speed` smallint(6) NOT NULL,
  `current_gsm` smallint(6) NOT NULL,
  `current_gps` smallint(6) NOT NULL,
  `location_lat` decimal(10,6) NOT NULL,
  `location_lng` decimal(10,6) NOT NULL,
  `state_bit_0` tinyint(3) unsigned NOT NULL,
  `state_bit_1` tinyint(3) unsigned NOT NULL,
  `state_bit_2` tinyint(3) unsigned NOT NULL,
  `state_bit_3` tinyint(3) unsigned NOT NULL,
  `state_bit_4` tinyint(3) unsigned NOT NULL,
  `state_bit_5` tinyint(3) unsigned NOT NULL,
  `state_bit_6` tinyint(3) unsigned NOT NULL,
  `state_bit_7` tinyint(3) unsigned NOT NULL,
  `state_bit_8` tinyint(3) unsigned NOT NULL,
  `state_bit_9` tinyint(3) unsigned NOT NULL,
  `state_bit_10` tinyint(3) unsigned NOT NULL,
  `state_bit_11` tinyint(3) unsigned NOT NULL,
  `state_bit_12` tinyint(3) unsigned NOT NULL,
  `state_bit_13` tinyint(3) unsigned NOT NULL,
  `state_bit_14` tinyint(3) unsigned NOT NULL,
  `state_bit_15` tinyint(3) unsigned NOT NULL,
  `input_ad_1` decimal(8,2) NOT NULL,
  `input_ad_2` decimal(8,2) NOT NULL,
  `input_ad_3` decimal(8,2) NOT NULL,
  `input_ad_4` decimal(8,2) NOT NULL,
  `input_ad_5` decimal(8,2) NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `device_key` (`device_key`)
) ENGINE=MyISAM AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8;
