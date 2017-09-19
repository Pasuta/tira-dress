<?php

class BonusApp extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->register_widget('header','header',array(true,'bonus'));
        $this->register_widget('footer','footer',false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> array('Бонусы')));
        $this->register_widget('metadata','metadata', array('url'=>true));
    }

}
?>