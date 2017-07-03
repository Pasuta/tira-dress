<?php


function after_create_item($rs){
    require_once BASE_DIR.'/helpers/item_renderers.php';
    writeXml('http://www.'.BASEURL.'/item/'.$rs->uri);
}

$broker = Broker::instance();
$broker->queue_declare ("ENITYCONSUMER_RDS", DURABLE, NO_ACK);
$broker->bind("ENTITY", "ENITYCONSUMER_RDS", "after.create.item");
$broker->bind_rpc ("ENITYCONSUMER_RDS", "after_create_item");
?>