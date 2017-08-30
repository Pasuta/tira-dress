<?php

$GLOBALS['CONFIG']['SITEMAP'][] = '{"uri": "index", "app":"Index" }';
$GLOBALS['CONFIG']['SITEMAP'][] = '{"uri": "ajax", "app":"Ajax" }';
$GLOBALS['CONFIG']['SITEMAP'][] = '{"uri": "blog", "app":"Blog" }';
$GLOBALS['CONFIG']['SITEMAP'][] = '{"uri": "bonus", "app":"Bonus" }';
$GLOBALS['CONFIG']['SITEMAP'][] = '{"uri": "page404", "app":"Page404" }';
$GLOBALS['CONFIG']['SITEMAP'][] = '{"uri": "catalog", "app":"Catalog" }';


foreach ($GLOBALS['CONFIG']['SITEMAP'] as $k=>$n)
{
	$GLOBALS['CONFIG']['SITEMAP'][$k] = json_decode($n, true);
}

?>