<?php 
// datos para la coneccion a mysql 
//define('DB_SERVER','192.168.2.8'); 
//define('DB_NAME','ssp'); 
//define('DB_USER','root'); 
//define('DB_PASS','Raferuzzi123');

define('DB_SERVER','localhost'); 
define('DB_NAME','sspdesarrollo'); 
define('DB_USER','root'); 
define('DB_PASS','root'); 

//define('DB_SERVER','200.59.225.10'); 
//define('DB_NAME','ssp'); 
//define('DB_USER','sgo'); 
//define('DB_PASS','nevermind321'); 

$con = mysql_connect(DB_SERVER,DB_USER,DB_PASS); 
mysql_select_db(DB_NAME,$con); 
mysql_set_charset ('utf8');
?>