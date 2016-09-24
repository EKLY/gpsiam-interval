<?php
$MYSQL_host = 'localhost';
$MYSQL_port = 3306;

$MYSQL_user = 'tracking';
$MYSQL_pass = 'tracking';

$MYSQL_db = 'tracking';

$TOKEN = isset($argv[1]) ? $argv[1] : '';

$IS_UPDATE_CORE = date('G') + 0 == 0 && date('i') + 0 == 0;


if($TOKEN != '') {
    $db = mysqli_connect($MYSQL_host, $MYSQL_user, $MYSQL_pass, $MYSQL_db);
    if($db) {
        $db -> query('set character set utf8');
        $db -> query('set collation_connection = utf8_unicode_ci');

        
        // check table exists
        
        $table_found = 0;
        $is_table_device_exists = false;
        $q = $db -> query('select count((1)) as table_exists from information_schema.tables where table_schema = \'' . $MYSQL_db . '\' and table_name like \'device%\'');
        if($a = mysqli_fetch_assoc($q)) {
            $table_found = $a['table_exists'] + 0;
        }
        
        if($table_found < 4) {
            $db -> query(file_get_contents(__DIR__  . '/devices.sql'));
            $db -> query(file_get_contents(__DIR__  . '/device_ad.sql'));
            $db -> query(file_get_contents(__DIR__  . '/device_state.sql'));
            $db -> query(file_get_contents(__DIR__  . '/device_log.sql'));
        }
        
        
        
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
                                
                                $IS_UPDATE_CORE = true;
                            }
                            
                            else {
                                trigger_error('DB ERROR [' . $db -> error . ']');
                            }
                        }
                        
                        if($device_id > 0) {
                            $time = strtotime($device['datetime']);
                            if($time > 0) {
                                $_state = array_fill(0, 16, 0);
                                $_ad = array_fill(1, 5, 0);
                                
                                if($IS_UPDATE_CORE) {
                                    $db -> query('delete from device_ad where device_id = ' . $device_id);
                                    $db -> query('delete from device_state where device_id = ' . $device_id);
                                }
                                
                                if(!empty($device['state'])) {
                                    foreach($device['state'] as $state) {
                                        if(isset($_state[$state['port'] + 0])) {
                                            $_state[$state['port'] + 0] = $state['on'] ? 1 : 0;
                                            if($IS_UPDATE_CORE) {
                                                $db -> query('insert into device_state (device_id, state_port, state_name, state_name_on, state_name_off) 
                                                    values (' . $device_id . ', ' . ($state['port'] + 0) . ', 
                                                        \'' . $db -> real_escape_string($state['name']) . '\', \'' . $db -> real_escape_string($state['name_on']) . '\', \'' . $db -> real_escape_string($state['name_off']) . '\')');
                                            }
                                        }
                                    }
                                }
                                
                                if(!empty($device['ads'])) {
                                    foreach($device['ads'] as $ad) {
                                        if(isset($_ad[$ad['port'] + 0])) {
                                            $_ad[$ad['port'] + 0] = $ad['value'];
                                            if($IS_UPDATE_CORE) {
                                                $db -> query('insert into device_ad (device_id, ad_port, ad_name, ad_unit) 
                                                    values (' . $device_id . ', ' . ($ad['port'] + 0) . ', 
                                                        \'' . $db -> real_escape_string($ad['name']) . '\', \'' . $db -> real_escape_string($ad['unit']) . '\')');
                                            }
                                        }
                                    }
                                }
                                
                                
                                $res = $db -> query('update devices
                                    set device_name = \'' . $db -> real_escape_string($device['name']) . '\',
                                        current_engine = ' . ($device['engine'] ? 1 : 0) . ', current_speed = ' . ($device['speed'] + 0) . ',
                                        current_gsm = ' . ($device['gsm'] + 0) . ', current_gps = ' . ($device['gps'] + 0) . ',
                                        location_lat = ' . ($device['location']['lat']) . ', location_lng = ' . ($device['location']['lng']) . ',
                                        state_bit_0 = ' . ($_state[0] + 0) . ', state_bit_1 = ' . ($_state[1] + 0) . ', state_bit_2 = ' . ($_state[2] + 0) . ', state_bit_3 = ' . ($_state[3] + 0) . ', 
                                            state_bit_4 = ' . ($_state[4] + 0) . ', state_bit_5 = ' . ($_state[5] + 0) . ', state_bit_6 = ' . ($_state[6] + 0) . ', state_bit_7 = ' . ($_state[7] + 0) . ', 
                                            state_bit_8 = ' . ($_state[8] + 0) . ', state_bit_9 = ' . ($_state[9] + 0) . ', state_bit_10 = ' . ($_state[10] + 0) . ', state_bit_11 = ' . ($_state[11] + 0) . ', 
                                            state_bit_12 = ' . ($_state[12] + 0) . ', state_bit_13 = ' . ($_state[13] + 0) . ', state_bit_14 = ' . ($_state[14] + 0) . ', state_bit_15 = ' . ($_state[15] + 0) . ', 
                                        input_ad_1 = ' . ($_ad[1] + 0) . ', input_ad_2 = ' . ($_ad[2] + 0) . ', input_ad_3 = ' . ($_ad[3] + 0) . ', input_ad_4 = ' . ($_ad[4] + 0) . ', input_ad_5 = ' . ($_ad[5] + 0) . ',
                                        update_time = \'' . $db -> real_escape_string(date('Y-m-d H:i:s', $time)) . '\'
                                    where device_id = ' . $device_id);
                                
                                if(!$res) {
                                    trigger_error('DB ERROR [' . $db -> error . ']');
                                }
                                
                                $res = $db -> query('insert ignore into device_log (device_id, update_time, 
                                        log_engine, log_speed, log_gsm, log_gps,
                                        location_lat, location_lng,
                                        state_bit_0, state_bit_1, state_bit_2, state_bit_3, state_bit_4, state_bit_5, state_bit_6, state_bit_7, 
                                            state_bit_8, state_bit_9, state_bit_10, state_bit_11, state_bit_12, state_bit_13, state_bit_14, state_bit_15, 
                                        input_ad_1, input_ad_2, input_ad_3, input_ad_4, input_ad_5) 
                                    values (' . $device_id . ', \'' . $db -> real_escape_string(date('Y-m-d H:i:s', $time)) . '\', 
                                        ' . ($device['engine'] ? 1 : 0) . ', ' . ($device['speed'] + 0) . ', ' . ($device['gsm'] + 0) . ', ' . ($device['gps'] + 0) . ',
                                        ' . ($device['location']['lat']) . ', ' . ($device['location']['lng']) . ',
                                        ' . ($_state[0] + 0) . ', ' . ($_state[1] + 0) . ', ' . ($_state[2] + 0) . ', ' . ($_state[3] + 0) . ', 
                                            ' . ($_state[4] + 0) . ', ' . ($_state[5] + 0) . ', ' . ($_state[6] + 0) . ', ' . ($_state[7] + 0) . ', 
                                            ' . ($_state[8] + 0) . ', ' . ($_state[9] + 0) . ', ' . ($_state[10] + 0) . ', ' . ($_state[11] + 0) . ', 
                                            ' . ($_state[12] + 0) . ', ' . ($_state[13] + 0) . ', ' . ($_state[14] + 0) . ', ' . ($_state[15] + 0) . ', 
                                        ' . ($_ad[1] + 0) . ', ' . ($_ad[2] + 0) . ', ' . ($_ad[3] + 0) . ', ' . ($_ad[4] + 0) . ', ' . ($_ad[5] + 0) . ')');
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
