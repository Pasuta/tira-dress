<?php

function widget_metadata($params){

    if($params['ent']){

        $m = new Message();
        $m->action = 'load';
        $m->urn = $params['ent']->urn;
        $data = $m->deliver();

    }

    if($params['url']){
        $currentUrl = 'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-metadata';
        $m->url = $currentUrl;
        $data = $m->deliver();
    }


    if(count($data) == 0) return;
    if(strlen($data->metakey)>0){
        $txt = trim(strip_tags($data->metakey));
        echo "<meta name='Keywords' content='{$txt}'>\n";
    }
    if(strlen($data->metadesc)>0){
        $txt = trim(strip_tags($data->metadesc));
        echo "<meta name='Description' content='{$txt}'>\n";
    }
    if(strlen($data->metatitle)>0){
        $txt = trim(strip_tags($data->metatitle));
        echo "<meta name='Title' content='{$txt}'>\n";
    }

}

?>