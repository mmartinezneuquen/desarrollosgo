<?php

$globalPath = dirname(__FILE__)."/../global";
$xmlApplicationPath = dirname(__FILE__)."/protected";

// Archivo actual:
$dbFileContent = file_get_contents("$globalPath/db.php");
$hash = md5($dbFileContent);
$actualizarXml = ($hash != file_get_contents("$xmlApplicationPath/application.hash"));

if ($actualizarXml)
{
	$pregMatch = [];

	//$regEx = "/(['(\\\")])(DB_[^(\1)]+)\1\s*\,\s*(['(\\\")])([^(\3)]+)\3/";  // Por alguna razon no anda ak y en los testers si 
	$regEx = "/'(DB_[^']+)'\s*\,\s*'([^']+)'/";  // version simplificada, compensada con el posterior str_replace:
	preg_match_all($regEx, str_replace('"', "'", $dbFileContent), $pregMatch);
	$params = $pregMatch[1];
	$values = $pregMatch[2];

	$xml = str_replace($params, $values, file_get_contents("$xmlApplicationPath/application_template.xml"));
	file_put_contents("$xmlApplicationPath/application.xml", $xml);
	file_put_contents("$xmlApplicationPath/application.hash", $hash);
}


$frameworkPath='includes/prado-3.2.2.r3297/framework/prado.php';

// The following directory checks may be removed if performance is required
$basePath=dirname(__FILE__);
$assetsPath=$basePath.'/assets';
$runtimePath=$basePath.'/protected/runtime';

if(!is_file($frameworkPath))
	die("Unable to find prado framework path $frameworkPath.");
if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");
if(!is_writable($runtimePath))
	die("Please make sure that the directory $runtimePath is writable by Web server process.");

require_once($frameworkPath);


$application=new TApplication;
//$application=new TApplication('protected',false,TApplication::CONFIG_TYPE_PHP); //>> Para cuando pueda resolver el application.php
$application->run();

?>