<?php
$MYSQL_host = 'locahost';
$MYSQL_port = 3306;

$MYSQL_user = 'tracking';
$MYSQL_pass = 'tracking';

$MYSQL_db = 'tracking';


$db = mysqli_connect($MYSQL_host, $MYSQL_user, $MYSQL_pass, $MYSQL_db);
if($db) {
    $db -> query('set character set utf8');
    $db -> query('set collation_connection = utf8_unicode_ci');

    
    
    
    
    
    
    
}

else {
    trigger_error('DATABASE_CONNECT_FAILED');
}
