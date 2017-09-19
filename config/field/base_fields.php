<?php

$GLOBALS['CONFIG']['STATUS'][20] = new StatusMeta(  array( "uid" => "20", "name" => "article", "title"=>"Статья", "default" => 0 )  );
$GLOBALS['CONFIG']['STATUS'][211] = new StatusMeta(  array( "uid" => "21", "name" => "instaff", "title"=>"В наличии", "default" => 0 )  );
$GLOBALS['CONFIG']['STATUS'][221] = new StatusMeta(  array( "uid" => "22", "name" => "toorder", "title"=>"Под заказ", "default" => 0 )  );
$GLOBALS['CONFIG']['STATUS'][222] = new StatusMeta(  array( "uid" => "222", "name" => "accessory", "title"=>"Аксессуар", "default" => 0 )  );
$GLOBALS['CONFIG']['STATUS'][223] = new StatusMeta(  array( "uid" => "223", "name" => "new", "title"=>"Новинка", "default" => 0 )  );



$GLOBALS['CONFIG']['FIELD'][1000] = new FieldMeta(  array( "uid" => "1000", "name" => "sizes", "title" => "Размеры", "type" => "richtext", "illustrated" => true, "illustrations" => "urn-img",
    "htmlallowed" => "h2,h3,h4,h5,h6,p,span,br,a[href],cite,b,strong,i,em,img[alt|src],table[summary],tbody,th[abbr],tr,td[abbr],ul,ol,li,dl, dt, dd, blockquote", "autoparagraph" => true, "nofollow" => false )  );

$GLOBALS['CONFIG']['FIELD'][1001] = new FieldMeta(  array( "uid" => "1001", "name" => "price", "title" => "Цена", "type" => "integer" )  );


$GLOBALS['CONFIG']['FIELD'][1031] = new FieldMeta(  array( "uid" => "1031", "name" => "metadesc", "title" => "Meta-tags Описание", "type" => "text")  );
$GLOBALS['CONFIG']['FIELD'][1032] = new FieldMeta(  array( "uid" => "1032", "name" => "metakey", "title" => "Meta-tags Ключевые слова", "type" => "text")  );
$GLOBALS['CONFIG']['FIELD'][1033] = new FieldMeta(  array( "uid" => "1033", "name" => "metatitle", "title" => "Meta-tags Заголовок", "type" => "text")  );
$GLOBALS['CONFIG']['FIELD'][1034] = new FieldMeta(  array( "uid" => "1034", "name" => "material", "title" => "Материал", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1035] = new FieldMeta(  array( "uid" => "1035", "name" => "rank", "title" => "Рейтинг", "type" => "integer")  );

$GLOBALS['CONFIG']['FIELD'][1036] = new FieldMeta(  array( "uid" => "1036", "name" => "weddingdate", "title" => "Дата свадьбы", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1037] = new FieldMeta(  array( "uid" => "1037", "name" => "siluet", "title" => "Силует", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1038] = new FieldMeta(  array( "uid" => "1038", "name" => "dekor", "title" => "Декор", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1039] = new FieldMeta(  array( "uid" => "1038", "name" => "size", "title" => "Размер", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1040] = new FieldMeta(  array( "uid" => "1040", "name" => "fata", "title" => "Фата", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1041] = new FieldMeta(  array( "uid" => "1041", "name" => "ready", "title" => "Пошив или готовое", "type" => "string")  );
$GLOBALS['CONFIG']['FIELD'][1042] = new FieldMeta(  array( "uid" => "1042", "name" => "dresscolor", "title" => "Цвет платья", "type" => "string")  );

$GLOBALS['CONFIG']['FIELD'][1043] = new FieldMeta(  array( "uid" => "1043", "name" => "subtitle", "title" => "Подзаголовок", "type" => "string")  );
?>