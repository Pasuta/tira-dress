<?php

class Page404App extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->register_widget('header','header', array(true));
        $this->register_widget('footer','footer','item');
    }

}
?>