<?php

class CatalogApp extends WebApplication implements ApplicationFreeAccess {

    function request(){

        $this->view = 'request';
        $this->register_widget('header', 'header', array(true, 'item', 'catalog'));
        $this->register_widget('footer', 'footer','item');
        $this->register_widget('pagetitle', 'pagetitle', array("title" => array('Каталог')));

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        if ($_GET['price'] == 'high') $m->order = array('price'=>'desc');
        elseif ($_GET['price'] == 'low') $m->order = array('price'=>'asc');
        if ($_GET['created'] == 'high') $m->order = array('created'=>'desc');
        elseif ($_GET['created'] == 'low') $m->order = array('created'=>'asc');
        if ($_GET['countview'] == 'high') $m->order = array('countview'=>'desc');
        elseif ($_GET['countview'] == 'low') $m->order = array('countview'=>'asc');
        if ($_GET['rank'] == 'high') $m->order = array('rank'=>'desc');
        elseif ($_GET['rank'] == 'low') $m->order = array('rank'=>'asc');
        if ($_GET['instaff'] == '1') $m->instaff = 1;
        if ($_GET['toorder'] == '1') $m->toorder = 1;
        $item = $m->deliver();
        $this->context['item'] = $item;

        $this->register_widget('metadata','metadata', array('url'=>true));
    }

    function resource($uri){
        $this->layout = 'product';
        $this->register_widget('header','header',array(false,'item'));
        $this->register_widget('footer','footer','item');

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->uri = $uri;
        $item = $m->deliver();
        if(!count($item)) $this->redirect('/page404');
        $this->context['item'] = $item;
        $this->context['countcomment'] = count($item->comment);

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->top = true;
        $m->last = 4;
        $m->order = array('created'=>'desc');
        $this->context['top'] = $m->deliver();

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 8;
        $m->accessory = false;
        $m->order = array('created'=>'desc');
        $this->context['last'] = $m->deliver();

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 3;
        $m->accessory = true;
        $m->order = array('created'=>'desc');
        $this->context['acc'] = $m->deliver();

        $material = explode(',', $item->material);
        $m = '';
        foreach ($material as $mt) {
            $m .= "<b>{$mt}</b>, ";
        }
        $this->context['material'] =  substr($m, 0, strlen($m)-2);

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 2;
        $m->order = array('created'=>'desc');
        $also = $m->deliver();
        $this->context['also'] = $also;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-comment';
        $m->item = $item->urn;
        $m->order = array('created'=>'desc');
        $comment = $m->deliver();
        $this->context['comment'] = $comment;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->order = array('created'=>'desc');
        $m->last = 3;
        $blog = $m->deliver();
        $this->context['blog'] = $blog;

        $this->context['currentUrl'] = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> array($item->title)));
        $this->register_widget('metadata','metadata', array('ent'=>$item));

    }

}
?>