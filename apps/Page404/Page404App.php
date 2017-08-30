<?php

class Page404App extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->register_widget('header', 'header', array(true, false));
        $this->register_widget('footer', 'footer', false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> '404'));
    }

}
?>