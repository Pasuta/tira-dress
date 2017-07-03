<?php
//$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['HOST'] = '127.0.0.1';
$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['HOST'] = 'localhost';
$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['USER'] = 'whitebride';
$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['PASSWORD'] = 'wpGeiYZWt';
$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['DBNAME'] = 'whitebride';
$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['CHARSET'] = 'utf8';
$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['COLLATE'] = 'utf8_general_ci';

$GLOBALS['CONFIG']['DEVELOPMENT']['DB']['REDISDBID'] = 14;



$repo3 = array('name'=>"site", 
			   'host'=>BASEURL, 
			   'policy'=>array('clientcan'=>array("push","pull"), 'compress'=>'gzip', 'encrypt'=>false), 'key'=>"SECRETKEY", 
			   'folders'=>array('apps', 'css', 'img', 'data-plugins', 'formwidgets', 'helpers', 'js', 'managers', 'mq_rpc', 'test', 'widgets', 'views'));
$repo1 = array('name'=>"goldcut", 
			   'host'=>BASEURL, 
			   'policy'=>array('clientcan'=>array("push","pull"), 'compress'=>'gzip', 'encrypt'=>false), 'key'=>"SECRETKEY", 
			   'folders'=>array('goldcut')); 
// config app sync repo
// img, watermark
// lib, goldcut

// preview, media, video, audio, original pull sync 
$GLOBALS['CONFIG']['DEVELOPMENT']['REPOS'] = array("site" => $repo3); // , , "goldcut" => $repo1 

?>
