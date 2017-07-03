<?php

function after_delete_blog($rs){

}

$broker = Broker::instance();
$broker->queue_declare ("ENITYCONSUMER_RDS", DURABLE, NO_ACK);
$broker->bind("ENTITY", "ENITYCONSUMER_RDS", "after.delete.blog");
$broker->bind_rpc ("ENITYCONSUMER_RDS", "after_delete_blog");
?>