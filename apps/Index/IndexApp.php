<?php

class IndexApp extends WebApplication implements ApplicationFreeAccess {

    function request(){
        $this->register_widget('header', 'header', array(true, false));
        $this->register_widget('pagetitle', 'pagetitle', array("title"=> 'Главная'));

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-review';
        $m->last = 3;
        $m->active = true;
        $m->order = array('created'=>'desc');
        $review = $m->deliver();
        $this->context['review'] = $review;

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

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-collection';
        $m->last = 6;
        $m->order = array('created'=>'desc');
        $collection = $m->deliver();
        $collection = $collection->toArray();
        $this->context['collection'] = $collection;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-text';
        $m->uri = "O-nas";
        $textAboutUs = $m->deliver();
        $this->context['textAboutUs'] = $textAboutUs;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-text';
        $m->uri = "Dobro-pojalovat-v-Tira-Bridal";
        $welcome = $m->deliver();
        $this->context['welcome'] = $welcome;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-text';
        $m->uri = "Unikalnost";
        $uniq = $m->deliver();
        $this->context['uniq'] = $uniq;

        $m = new Message();
        $m->action = 'load';
        $m->urn = 'urn-text';
        $m->uri = "Individualnost";
        $ind = $m->deliver();
        $this->context['ind'] = $ind;

        $this->register_widget('metadata','metadata', array('url'=>true));
    }

}
?>
