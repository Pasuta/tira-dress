<?php
/**

gateway order_id <=> transaction_id

paymentgateway_id

user_id

maxage

openedamount
openedcurr
closedamount
closedcurr

remoteid - gateway transaction id

created
closed

ip
phoneverified
phoneprovided

invoice_id
+autocloseinvoice

*/

$GLOBALS['CONFIG']['ENTITY'][71] = new EntityMeta(  array(
'system'=>true,														  
	"class"=>"Payments", "title"=>array("ru"=>"Long Transaction"),
	"uid" => "71", "name" => "longtransaction",
	"statuses" => array('autocloseinvoice','payed'), 
	"field_metas" => array('paymentgateway', 'maxage', 'created', 'payedat', 'openedamount', 'openedcurr', 'closedamount', 'closedcurr', 'remoteid', 'ip', 'phoneverified', 'phoneprovided'),
	"belongs_to" => array('user'),
	"has_one" => array('invoice'),
	"has_many"=>array(),
	"related"=>array(),
	"adminfields" => array('user','paymentgateway', 'openedamount', 'phoneverified','date','time','id'), // 'openedcurr'
	"adminadd" => false,
	"directmanage" => false,
	"defaultorder" => array("created" => "DESC"),
	"gropuby" => array('date')
	) );

?>