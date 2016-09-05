<?php
$MYSQL_host = 'localhost';
$MYSQL_port = 3306;

$MYSQL_user = 'tracking';
$MYSQL_pass = 'tracking';

$MYSQL_db = 'tracking';

$TOKEN = isset($argv[1]) ? $argv[1] : '';

if($TOKEN != '') {
    $db = mysqli_connect($MYSQL_host, $MYSQL_user, $MYSQL_pass, $MYSQL_db);
    if($db) {
        $db -> query('set character set utf8');
        $db -> query('set collation_connection = utf8_unicode_ci');

        
        // check table exists
        
        $is_table_device_exists = false;
        $q = $db -> query('select count((1)) as table_exists from information_schema.tables where table_schema = \'' . $MYSQL_db . '\' and table_name = \'devices\'');
        if($a = mysqli_fetch_assoc($q)) {
            if($a['table_exists'] > 0)
                $is_table_device_exists = true;
        }
        
        if(!$is_table_device_exists) {
            $db -> query(file_get_contents('devices.sql'));
        }
        
        $is_table_device_log_exists = false;
        $q = $db -> query('select count((1)) as table_exists from information_schema.tables where table_schema = \'' . $MYSQL_db . '\' and table_name = \'device_log\'');
        if($a = mysqli_fetch_assoc($q)) {
            if($a['table_exists'] > 0)
                $is_table_device_log_exists = true;
        }
        
        if(!$is_table_device_log_exists) {
            $db -> query(file_get_contents('device_log.sql'));
        }
        
        // check table exists : end
        
        
        // cache device
        $devices = [];
        $q = $db -> query('select device_id, device_key from devices');
        while($a = mysqli_fetch_assoc($q)) {
            $devices[$a['device_key']] = $a['device_id'] + 0;
        }
        
        
        
        // get data
        $ctx = stream_context_create(['http'=> ['timeout' => 10]]);

        $url_host = 'http://s2.gpsiam.net';
        $url_command = '/restful/' . $TOKEN . '/device/currents';
        
        $result_text = file_get_contents($url_host . $url_command, false, $ctx);
        
        if($result_text !== '') {
            $data = @json_decode($result_text, true);
            if($data['ok']) {
                if($data['devices']) {
                    foreach($data['devices'] as $device) {
                        $device_id = 0;
                        if(isset($devices[$device['key']])) {
                            $device_id = $devices[$device['key']];
                        }
                        
                        else {
                            $res = $db -> query('insert into devices (device_key, device_name) values (\'' . $db -> real_escape_string($device['key']) . '\', \'' . $db -> real_escape_string($device['name']) . '\')');
                            if($res) {
                                $device_id = $db -> insert_id;
                                $devices[$device['key']] = $device_id;
                            }
                            
                            else {
                                trigger_error('DB ERROR [' . $db -> error . ']');
                            }
                        }
                        
                        if($device_id > 0) {
                            $time = strtotime($device['datetime']);
                            if($time > 0) {
                                $res = $db -> query('update devices
                                    set 
                                        current_engine = ' . ($device['engine'] ? 1 : 0) . ', current_speed = ' . ($device['speed'] + 0) . ',
                                        current_gsm = ' . ($device['gsm'] + 0) . ', current_gps = ' . ($device['gps'] + 0) . ',
                                        location_lat = ' . ($device['location']['lat']) . ', location_lng = ' . ($device['location']['lng']) . ',
                                        update_time = \'' . $db -> real_escape_string(date('Y-m-d H:i:s', $time)) . '\'
                                    where device_id = ' . $device_id);
                                if(!$res) {
                                    trigger_error('DB ERROR [' . $db -> error . ']');
                                }
                                
                                $res = $db -> query('insert ignore into device_log (device_id, update_time, 
                                        log_engine, log_speed, log_gsm, log_gps,
                                        location_lat, location_lng) 
                                    values (' . $device_id . ', \'' . $db -> real_escape_string(date('Y-m-d H:i:s', $time)) . '\', 
                                        ' . ($device['engine'] ? 1 : 0) . ', ' . ($device['speed'] + 0) . ', ' . ($device['gsm'] + 0) . ', ' . ($device['gps'] + 0) . ',
                                        ' . ($device['location']['lat']) . ', ' . ($device['location']['lng']) . ')');
                                if(!$res) {
                                    trigger_error('DB ERROR [' . $db -> error . ']');
                                }
                            }
                        }
                    }
                }
            }
        
            else {
                trigger_error('ERROR [' . $data['error'] . ']');
            }
        }
        
        else {
            trigger_error('CANT_GET_DATA');
        }
        
        $db -> close();
    }

    else {
        trigger_error('DATABASE_CONNECT_FAILED');
    }
}

else {
    trigger_error('TOKEN_EMPTY');
}
