<?php

class BlogApp extends WebApplication implements ApplicationFreeAccess {

    function init(){
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> array('Блог')));
    }

    function request(){
        $this->view = 'v2';
        $this->register_widget('header','header',array(true,'blog'));
        $this->register_widget('footer','footer',false);

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->order = array('created'=>'desc');
        $blog = $m->deliver();
        $this->context['blog'] = $blog;

        $this->register_widget('metadata','metadata', array('url'=>true));
    }


    function resource($uri){
        $this->view = 'v3';
        $this->register_widget('header','header',array(false,'blog'));
        $this->register_widget('footer','footer',false);

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->uri = $uri;
        $blog = $m->deliver();
        if(!count($blog)) $this->redirect('/page404');
        $this->context['blog'] = $blog;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->order = array('created'=>'desc');
        $allBlogs = $m->deliver()->toArray();

        $prev = false;
        $next = false;

        for($i=0; $i<count($allBlogs); $i++){
            if($allBlogs[$i]['urn'] == $blog->urn){
                if($i == count($allBlogs)-1){
                    $prev = false;
                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $allBlogs[$i-1]['urn'];
                    $next = $m->deliver();
                } else if($i == 0){
                    $next = false;
                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $allBlogs[$i+1]['urn'];
                    $prev = $m->deliver();
                } else {
                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $allBlogs[$i-1]['urn'];
                    $next = $m->deliver();

                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $allBlogs[$i+1]['urn'];
                    $prev = $m->deliver();
                }
            }
        }

        $this->context['prev'] = $prev;
        $this->context['next'] = $next;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->last = 2;
        $m->order = array('created'=>'desc');
        $m->admin = $blog->admin->urn;
        $blogAdmin = $m->deliver();
        $this->context['blogAdmin'] = $blogAdmin;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-comment';
        $m->blog = $blog->urn;
        $m->order = array('created'=>'desc');
        $comment = $m->deliver();
        $this->context['comment'] = $comment;

        $this->register_widget('metadata','metadata', array('ent'=>$blog));

    }

}
?>
