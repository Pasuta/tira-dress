<?php 
/*
Core
*/
if (NEWUSERMODEL !== true)
{
//echo "OLD";
$GLOBALS['CONFIG']['ENTITY'][20] = new EntityMeta(  array(
'system'=>true,														  
	"class"=>"Online", "title"=>array("ru"=>"Online", "en"=>"Online"),
	"uid" => "20", "name" => "online",
	"statuses" => array(), 
	"field_metas" => array('ip','created','hash'),
	"has_one" => array('user'),
	"has_many"=>array(),
	"related"=>array(),
	"adminadd" => false,
	"directmanage" => false,
	"checkunique" => array('hash'),
	"index" => array('hash','created'),
) );

}
?>