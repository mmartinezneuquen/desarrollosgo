<?php 
// datos para la coneccion a mysql 
define('DB_SERVER','localhost'); 
define('DB_NAME','sspdesarrollo'); 
define('DB_USER','root'); 
define('DB_PASS','root'); 
//define('DB_NAME','mospneuquen_db'); 
//define('DB_USER','mospneuquen_usr'); 
//define('DB_PASS','0wHBzSyyT7'); 

$con = mysql_connect(DB_SERVER,DB_USER,DB_PASS); 
mysql_select_db(DB_NAME,$con); 
mysql_set_charset ('utf8');
?>
