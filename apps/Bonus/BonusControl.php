<?php

class BonusControl extends WebApplication implements ApplicationFreeAccess {

    function handle(){

        $this->view = false;

        $p = $this->message;
        $m = new Message();
        $m->action = 'create';
        $m->urn = 'urn-bonus';
        $m->name = $p->name;
        $m->phone = $p->phone;
        $m->email = $p->email;
        $m->dresscolor = $p->dresscolor;
        $m->siluet = $p->siluet;
        $m->dekor= $p->dekor;
        $m->fata = $p->fata;
        $m->size = $p->size;
        $m->ready = $p->ready;
        $m->weddingdate = $p->weddingdate;
        $bonus = $m->deliver();

        //send($from = null, $namefrom = null, $to, $nameto, $subject, $body)
        $body = "
                  <p>Имя: {$p->name}</p>
                  <p>Телефон: {$p->phone}</p>
                  <p>Email: {$p->email}</p>
                  <p>Цвет платья: {$p->dresscolor}</p>
                  <p>Силует: {$p->siluet}</p>
                  <p>Декор: {$p->dekor}</p>
                  <p>Фата: {$p->fata}</p>
                  <p>Размер: {$p->size}</p>
                  <p>Пошив или готовое: {$p->ready}</p>
                  <p>Дата свадьбы: {$p->weddingdate}</p>

                  ";
        Mail::send(SITE_NAME, SITE_NAME, "info@whitebride.com.ua", SITE_NAME, 'Анкета', $body);

        $this->redirect('/');

    }

}
?>