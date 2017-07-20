<?php

class IndexApp extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->view = 'v2';
        $this->register_widget('header', 'header', array(true, false));
        $this->register_widget('footer', 'footer', false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> 'Главная'));

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-comment';
        $m->last = 3;
        $m->order = array('created'=>'desc');
        $comment = $m->deliver();
        $this->context['comment'] = $comment;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 1;
        $m->new = 1;
        $m->order = array('created'=>'desc');
        $this->context['new'] = $m->deliver();

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 1;
        $m->top = 1;
        $m->order = array('created'=>'desc');
        $this->context['top'] = $m->deliver();

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 1;
        $m->order = array('' => 'RAND()');
        $hot = $m->deliver();
        $this->context['hot'] = $hot;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 1;
        $m->new = 1;
        $m->order = array('created'=>'desc');
        $this->context['new'] = $m->deliver();

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 6;
        $m->order = array('created'=>'desc');
        $last6 = $m->deliver();
        $last6 = $last6->toArray();
        shuffle($last6);
        $this->context['last6'] = $last6;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 6;
        $m->accessory = false;
        $m->rank = 5;
        $m->order = array('created'=>'desc');
        $rank5 = $m->deliver();
        $rank5 = $rank5->toArray();
        shuffle($rank5);
        $this->context['rank5'] = $rank5;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-itemChildren';
        $m->last = 6;
        $m->order = array('created'=>'desc');
        $itemChildren = $m->deliver();
        $itemChildren = $itemChildren->toArray();
        shuffle($itemChildren);
        $this->context['itemChildren'] = $itemChildren;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->last = 3;
        $m->order = array('created'=>'desc');
        $blog = $m->deliver();
        $this->context['blog'] = $blog;

        $this->register_widget('metadata','metadata', array('url'=>true));
    }

    function requestOld(){
        $this->register_widget('header','header',array(true,false));
        $this->register_widget('footer','footer',false);
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> 'Главная'));

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $m->last = 5;
        $blog = $m->deliver();
        $this->context['blog'] = $blog;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-review';
        $m->order = array('created'=>'desc');
        $m->last = 10;
        $review = $m->deliver();
        $this->context['review'] = $review;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->order = array('created'=>'desc');
        $m->last = 3;
        $lastitem = $m->deliver();
        $this->context['lastitem'] = $lastitem;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->order = array('created'=>'desc');
        $m->last = 1;
        $lastitemone = $m->deliver();
        $this->context['lastitemone'] = $lastitemone;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->order = array('created'=>'desc');
        $m->last = 4;
        $m->top = true;
        $topitem = $m->deliver();
        $this->context['topitem'] = $topitem;

        $this->register_widget('metadata','metadata', array('url'=>true));

    }

    function s(){
        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $m->last = 6;
        $this->context['last6'] = $m->deliver();
    }

    function upd(){
        $this->view = false;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $items = $m->deliver();

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $blogs = $m->deliver();

        foreach ($items as $item) {
            $title = $item->title;

            $m = new Message();
            $m->action = 'update';
            $m->urn = $item->urn;
            $m->title = $title;
            $m->deliver();

        }

        foreach ($blogs as $blog) {
            $title = $blog->title;

            $m = new Message();
            $m->action = 'update';
            $m->urn = $blog->urn;
            $m->title = $title;
            $m->deliver();
        }


    }

    function writeXml(){
        $this->view = false;

        $file = BASE_DIR."/sitemap.xml";

        $xml = new DOMDocument();
        $xml->load($file);
        $nodes = $xml->getElementsByTagName('urlset');
        if ($nodes->length > 0) {
            $xml_url = $xml->createElement("url");

            $xml_loc = $xml->createElement("loc");
            $xml_loc->nodeValue = 1;
            $xml_url->appendChild($xml_loc);

            $xml_loc = $xml->createElement("lastmod");
            $xml_loc->nodeValue = 1;
            $xml_url->appendChild($xml_loc);

            $xml_loc = $xml->createElement("changefreq");
            $xml_loc->nodeValue = 1;
            $xml_url->appendChild($xml_loc);

            $xml_loc = $xml->createElement("priority");
            $xml_loc->nodeValue = 1;
            $xml_url->appendChild($xml_loc);

            $nodes->item(0)->appendChild($xml_url);
        }
        $xml->save($file);
    }

    function updSitemap(){
        $this->view = false;
        println(1);

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $item = $m->deliver();

        foreach ($item as $i) {
            println($i);
            writeXml('http://www.'.BASEURL.'/item/'.$i->uri);
        }

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-blog';
        $blog = $m->deliver();

        foreach ($blog as $i) {
            println($i);
            writeXml('http://www.'.BASEURL.'/blog/'.$i->uri);
        }

    }

    function updPhoto(){
        $this->view = false;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-item';
        $item = $m->deliver();

        foreach ($item as $i) {

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

        }

    }

}
?>
