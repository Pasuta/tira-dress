<?php 
/**
Invoice for Order
Order holds info for what
Discounts - pre order stage. Year vs month. X with Y, X in mount > N

ORDER for named mq message call - accent ad, move up ad etc (Param? - MQ/PARAM)
for servicelevel record
for service custom (design etc)
for entity limits - add N photos
for extend packages - more space in Silver - as sub packages
for shop item (or combined shop order - cart (xml object with links to active or history deleted items))
	buy tshirt - pay for universal anon cart with 1 item (can customize color on order stage)
	
list of avaible options to Order
accent ad
	mq payed.accentad(urn-ad-1)
move up ad
	mq payed.moveup(urn-ad-1)
shop1, shop2, shop3 - open/continue shop (1,2,3 levels)
	mq payed.serviceshop(urn-servicelevel-2, URN-USER-7)

? buy vip for - payer and target user not equal	
? 1 order - N partly invoices? need stages. any stage is 1 order

INVOICE

payment way - internal wallet / external bank payment, cash
who closed payment (system(wallet), accountant(bank/cash), )
sms - ext with autoclose invoice

bt user(registered)/client(crm)

srv
1 Month FREE
1(2,3) month money back

uri - redirect to after pay

*/	
// INVOICE
	$GLOBALS['CONFIG']['ENTITY'][70] = new EntityMeta(  array(
'system'=>true,															  
	"class"=>"Payments", "title"=>array("ru"=>"Invoice", "en"=>"Invoice"),
	"uid" => "70", "name" => "invoice",
	"statuses" => array('payed'), 
	"field_metas" => array('amount', 'total', 'created', 'payedat',   'mqname', 'urnlink', 'targetuser', 'units', 'subject', 'uri'),
	"belongs_to" => array('user'),
	"has_one" => array(), // 'entity', servicelevel
	"has_many"=>array(),
	"related"=>array(),
	"adminadd" => false,
	"directmanage" => false
	) );

?>