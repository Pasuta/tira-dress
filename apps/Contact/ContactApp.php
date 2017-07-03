<?php

class ContactApp extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->register_widget('header','header',array(true,'contact'));
        $this->register_widget('footer','footer',false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> array('Контакты')));

        $this->register_widget('metadata','metadata', array('url'=>true));
    }

    function howto(){
        $this->register_widget('header','header',array(true,'contact/howto'));
        $this->register_widget('footer','footer',false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> array('Как заказать')));

        $this->register_widget('metadata','metadata', array('url'=>true));
    }

}
?>