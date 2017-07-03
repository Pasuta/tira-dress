<?php

class ReviewApp extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->register_widget('header','header',array(true,'review'));
        $this->register_widget('footer','footer',false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> 'Отзывы'));

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-review';
        $m->order = array('created'=>'desc');
        $review = $m->deliver();
        $this->context['review'] = $review;


        $this->register_widget('metadata','metadata', array('url'=>true));
    }

}
?>