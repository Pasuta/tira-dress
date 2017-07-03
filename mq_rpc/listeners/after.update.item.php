<?php

function after_update_item($rs){
    $old = $rs[1];
    $new = $rs[0];

    if(count($new->mainphotov)) return;

    $m = new Message();
    $m->action = 'load';
    $m->urn = $new->urn;
    $i = $m->deliver();

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-mainphotov';
    $m->item = $i->urn;
    $photo = $m->deliver();

    if(count($photo) != 1){

        if($i->mp){
            $img = BASE_DIR.$i->mp->image->uri;

            $m = new Message('{"action": "create", "urn": "urn-mainphotov"}');
            $m->file = $img;
            $m->uri = $img;
            $av = $m->deliver();

            $m = new Message();
            $m->action = 'update';
            $m->urn = $i->urn;
            $m->mainphotov = $av->urn;
            $m->deliver();
        }
    } else return;

}

$broker = Broker::instance();
$broker->queue_declare ("ENITYCONSUMER_RDS", DURABLE, NO_ACK);
$broker->bind("ENTITY", "ENITYCONSUMER_RDS", "after.update.item");
$broker->bind_rpc ("ENITYCONSUMER_RDS", "after_update_item");
?>