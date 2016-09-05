CREATE TABLE `device_ad` (
  `device_id` smallint(5) unsigned NOT NULL,
  `ad_port` tinyint(3) unsigned NOT NULL,
  `ad_name` varchar(200) NOT NULL,
  `ad_unit` varchar(10) NOT NULL,
  PRIMARY KEY (`device_id`,`ad_port`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
