<?php
	
function widget_footer()
{
    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-item';
    $m->order = array('created'=>'desc');
    $m->last = 3;
    $item3 = $m->deliver();

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-item';
    $item = $m->deliver();
    $item = count($item);

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-blog';
    $m->last = 3;
    $blog3 = $m->deliver();

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-blog';
    $blog = $m->deliver();
    $blog = count($blog);

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-review';
    $m->last = 5;
    $review5 = $m->deliver();

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-review';
    $review = $m->deliver();
    $review = count($review);

    echo "
            <footer class='FW footercont'>
                <div class='CW' style='padding: 20px 0 20px 0'>
                    <div class='FL footerL'>
                        <div class='footerLD'>
                            <a href='/item' class='footerLB'>Каталог | {$item}</a>";
                        foreach($item3 as $i) echo "<a href='/item/{$i->uri}' class='footerS'>{$i->title}</a>";
                 echo " </div>
                        <div class='footerLD'>
                            <a href='/blog' class='footerLB'>Блог | {$blog}</a>";
                        foreach($blog3 as $i) echo "<a href='/blog/{$i->uri}' class='footerS'>{$i->title}</a>";
                 echo " </div>
                        <div class='footerLD'>
                            <a href='/review' class='footerLB'>Отзывы | {$review}</a>";
                        foreach($review5 as $i){
                            echo "<a href='/review' class='footerS footerSRev'>{$i->text}</a>";
                        }
                 echo " </div>
                        <div class='footerLD' style='margin-right: 0'>
                            <a href='/contact' class='footerLB'>Контакты</a>
                            <span>+38050 2000-103</span>
                            <span><a href='mailto:info@whitebride.com.ua'>info@tira.com</a></span>
                        </div>
                        <br class='clear'>
                    </div>
                    <div class='FL' style='margin-left: 10px'>
                        <div class='footerLD' style='width: 330px;margin-right:0'>
                            <a class='footerLB'>Подписаться</a>
                            <form method='POST' action='/subscribe' class='form_subscribe'>
                                <input type='text' name='email' placeholder='Введите ваш E-mail'>
                                <p class='ok formBtn subscribeBtn'>Подписаться</p>
                                <br class='clear'>
                                <div class='form_message'>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            </form>
                        </div>
                        <br class='clear'>
                    </div>
                    <br class='clear'>
                </div>
                <div class='FW wetasphalt'>
                    <div class='CW' style='text-align: right'>
                        ©Tira all rights reserved
                    </div>
                </div>
            </footer>
            <script>
                subscribe();
            </script>
         ";
}
?>