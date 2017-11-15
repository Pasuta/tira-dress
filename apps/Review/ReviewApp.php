<?php

class ReviewApp extends WebApplication implements ApplicationFreeAccess {

    function init(){
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> array('Отзывы')));
    }

    function request(){
        $this->register_widget('header','header',array(true,'review'));
        $this->register_widget('footer','footer',false);

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-review';
        $m->active = true;
        $m->order = array('created'=>'desc');
        $all = $m->deliver();

        $perPage = 3;
        $page = $_GET['page'] ? $_GET['page'] : 0;
        $pages = ceil(count($all) / $perPage);
        if ($page <= 0 || $page >= $pages) $page = 0;

        $this->context['page'] = $page;

        $page = $perPage * $page;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-review';
        $m->active = true;
        $m->offset = $page;
        $m->last = 3;
        $m->order = array('created'=>'desc');
        $review = $m->deliver();
        $this->context['review'] = $review;
        $this->context['pages'] = $pages;

        $this->register_widget('metadata','metadata', array('url'=>true));
    }
}
?>
