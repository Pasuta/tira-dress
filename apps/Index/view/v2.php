<?
extract($this->context);
?>

<div class="toast animated">
    <span style="color:black">
        Thank you<br> Manager will contact you as soon as possible.
    </span>
</div>


<header />

<div id="home">
    <!-- Start cSlider -->
    <div id="da-slider" class="da-slider">
        <div class="triangle"></div>
        <!-- mask elemet use for masking background image -->
        <div class="mask"></div>
        <!-- All slides centred in container element -->
        <div class="container">
            <!-- Start first slide -->
            <div class="da-slide">
                <h2 class="fittext2">Добро пожаловать в мир Tira!</h2>
                <h4>TIRA BRIDAL!</h4>
                <p>Эксклюзивная свадебная коллекция лето-осень 2017 нашего бренда TIRA BRIDAL</p>
                <a href="#about" class="da-link button">Подробнее</a>
<!--                <div class="da-img">-->
<!--                    <img src="public/images/Slider01.png" alt="image01" width="320">-->
<!--                </div>-->
            </div>
            <!-- End first slide -->
            <!-- Start second slide -->
            <div class="da-slide">
                <h2>Уникальность!</h2>
                <h4>Модные свадебные платья<br> от украинских дизайнеров!</h4>
<!--                <p></p>-->
                <a href="#about" class="da-link button">Подробнее</a>
<!--                <div class="da-img">-->
<!--                    <img src="public/images/Slider02.png" width="320" alt="image02">-->
<!--                </div>-->
            </div>
            <!-- End second slide -->
            <!-- Start third slide -->
            <div class="da-slide">
                <h2>Индивидуальность!</h2>
                <h4>Индивидуальный пошив<br> свадебного платья</h4>
<!--                <p></p>-->
                <a href="#about" class="da-link button">Подробнее</a>
<!--                <div class="da-img">-->
<!--                    <img src="public/images/Slider03.png" width="320" alt="image03">-->
<!--                </div>-->
            </div>
            <!-- Start third slide -->
            <!-- Start cSlide navigation arrows -->
            <div class="da-arrows">
                <span class="da-arrows-prev"></span>
                <span class="da-arrows-next"></span>
            </div>
            <!-- End cSlide navigation arrows -->
        </div>
    </div>
</div>
<!-- End home section -->
<!-- Service section start -->
<div class="section primary-section" id="service">
    <div class="container">
        <!-- Start title section -->
        <div class="title">
            <h1>Какие услуги мы предоставляем?</h1>
        </div>
        <div class="row-fluid">
            <div class="span4">
                <div class="centered service">
                    <div class="circle-border zoom-in">
                        <img class="img-circle" src="/img/tira/showroom-512.png" alt="service 1">
                    </div>
                    <h3>Шоурум</h3>
                    <p class="w">У нас вы можете примерить любой свадебный наряд</p>
                </div>
            </div>
            <div class="span4">
                <div class="centered service">
                    <div class="circle-border zoom-in">
                        <img class="img-circle" src="/img/tira/needle.png" alt="service 2" />
                    </div>
                    <h3>Платья на пошив</h3>
                    <p class="w">индивидуальный пошив свадебного или вечернего платья по Вашему эскизу</p>
                </div>
            </div>
            <div class="span4">
                <div class="centered service">
                    <div class="circle-border zoom-in">
                        <img class="img-circle" src="/img/tira/necklace-512.png" alt="service 3">
                    </div>
                    <h3>Аксессуары</h3>
                    <p class="w">Мы создаём авторские украшения ручной работы под Ваш образ</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service section end -->
<!-- Portfolio section start -->
<div class="section secondary-section " id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class=" title">
            <h1>Наши колекции</h1>
<!--            <p>Тут будет небольшой текст, но его можно и не выводить, тут уж как сами девули хотите</p>-->
            <a href="/catalog">Перейти ко всем коллекциям</a>
        </div>
        <div id="single-project">
            <ul id="portfolio-grid" class="thumbnails row">
                <?
                $i = 0;
                $slidingDiv = '';
                foreach($last6 as $o) {
                    $i++;
                    if ($i != 1) $slidingDiv = $i;
                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $o['urn'];
                    $o = $m->deliver();
                    if($o->mainphotov){
                        $src = $o->mainphotov->thumb->uri;
                        echo "
                                <li class='span4 mix web'>
                                    <div class='thumbnail' style='background: white'>
                                        <a href='/catalog/{$o->uri}' rel='#slidingDiv{$slidingDiv}'>
                                            <img src='{$src}' alt='project {$i}'>
                                        </a>
                                        <br>
                                        <p style='color:black;text-transform: capitalize'>{$o->title}</p>
                                        <p style='color:black'>Цену уточняйте по телефону</p>
                                    </div>
                                </li>
                                ";
                    }
                }?>
            </ul>
        </div>
    </div>
</div>
<!-- Portfolio section end -->
<!-- About us section start -->
<div class="section primary-section" id="about">
    <div class="triangle"></div>
    <div class="container">
        <div class="title">
            <h1>О нас</h1>
            <p>Что нас отличает от других салонов?</p>
        </div>
        <div class="row-fluid team">
            <div class="span6" id="first-person">
                <div class="thumbnail" style='background: #b90077'>
                    <img src="/public/images/g/t1.jpg" alt="team 1">
                    <h3>Татьяна Ковальчук</h3>
                    <ul class="social">
                        <li>
                            <a href="https://www.facebook.com/profile.php?id=100001799804600">
                                <span class="icon-facebook-circled"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="span6" id="second-person">
                <div class="thumbnail" style='background: #b90077'>
                    <img src="/public/images/g/i.jpg" alt="team 1">
                    <h3>Ирина Довгань</h3>
                    <ul class="social">
                        <li>
                            <a href="https://www.facebook.com/irisha.dovgan">
                                <span class="icon-facebook-circled"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="about-text centered">
            <p class="w">Мы сестры Татьяна и Ирина, решили отойти от классического представления невесты в пышном белом платье и создать
                свою эксклюзивную коллекцию свадебных платьев.<p>
            <p class="w">Мы - новый вектор в свадебной и вечерней моде, мы с удовольствием и комфортом подберем Ваш неповторимый свадебный образ.<p>
            <p class="w">У нас представлены свадебные платья украинских производителей, наша собственная линия свадебных платьев TIRA Bridal и платья известных брендов.<p>
            <p class="w">Мы любим творить красоту и дарить счастье молодоженам, поэтому 90% наших платьев - это эксклюзивные модели, которые Вы больше нигде не купите.<p>
            <p class="w">Также мы разработали линию будуарных платьев для съёмки "утро невесты", чтобы ваш САМЫЙ долгожданный день удался!<p>
            <p class="w">Мы ЗА индивидуальность - поэтому с радостью выслушаем все Ваши пожелания и подберем то, что нужно, или сошьем по вашему эскизу.<p>
            <p class="w">   Ваша красота и улыбка в день свадьбы - самая большая награда для нас и, конечно же, не волнуйтесь,
                у нас Вы сможете подобрать авторское украшение для волос и фату.</p>
        </div>
<!--        <h3>Если ты:</h3>-->
        <div class="row-fluid" style="display: none;">
            <div class="span6">
                <ul class="skills">
                    <li>
                        <span class="bar" data-width="80%"></span>
                        <h3>Трудолюбивый</h3>
                    </li>
                    <li>
                        <span class="bar" data-width="95%"></span>
                        <h3>Ответственные</h3>
                    </li>
                    <li>
                        <span class="bar" data-width="68%"></span>
                        <h3>Честный</h3>
                    </li>
                    <li>
                        <span class="bar" data-width="70%"></span>
                        <h3>Бицуха 50 см</h3>
                    </li>
                </ul>
            </div>
            <div class="span6">
                <div class="highlighted-box center">
                    <h1>Мы нанимаем!</h1>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, ullamcorper suscipit lobortis nisl ut aliquip consequat. I learned that we can do anything, but we can't do everything...</p>
                    <button class="button button-sp">Присоединись к нам!</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About us section end -->
<div class="section secondary-section">
    <div class="triangle"></div>
    <div class="container centered">
        <p class="large-text">У нас действует система бонусов и скидок!</p>
        <a href="/bonus" class="button">Получить скидку</a>
    </div>
</div>
<!-- Client section start -->
<div id="clients">
    <div class="section primary-section">
        <div class="triangle"></div>
        <div class="container">
            <div class="title">
                <h1>Отзывы</h1>
            </div>
            <div class="row">

                <?
                foreach($comment as $c) {
                    $d = date('Y-m-d',$c->created);
                    echo "
                            <div class='span4'>
                                <div class='testimonial'>
                                    {$c->text}
                                    <div class='whopic'>
                                        <div class='arrow'></div>
                                        <img src='/img/girl-512.png' class='centered' alt='client 1'>
                                        <strong>{$c->name}
                                            <small>{$d}</small>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            ";
                }?>

            </div>
        </div>
    </div>
</div>
<!-- Newsletter section start -->
<div class="section third-section">
    <div class="container newsletter">
        <div class="sub-section">
            <div class="title clearfix">
                <div class="pull-left">
                    <h3>Подписаться</h3>
                </div>
            </div>
        </div>
        <div id="success-subscribe" class="alert alert-success invisible">
            <strong>Well done!</strong>You successfully subscribet to our newsletter.</div>
        <div class="row-fluid">
            <div class="span5">
                <p class="w">Подпишись на нашу рассылку и получай новости о новых коллекциях и скидках!</p>
            </div>
            <div class="span7">
                <form class="inline-form form_subscribe" method='POST' action='/subscribe' >
                    <input type='text' name='email' class="span8" placeholder="Ваш емейл" required />
                    <button id="subscribe" class="button button-sp ok formBtn subscribeBtn">Подписаться</button>
                </form>
                <div id="err-subscribe" class="error centered form_message"></div>
            </div>
        </div>
    </div>
</div>
<!-- Newsletter section end -->
<!-- Contact section start -->
<div id="contact" class="contact">
    <div class="section secondary-section">
        <div class="container">
            <div class="title">
                <h1>Наши контакты</h1>
            </div>
        </div>
        <div class="map-wrapper">
            <div class="map-canvas" id="map-canvas">Загружаю карту...</div>

        </div>
        <div class="container">
            <div class="row-fluid">
                <div class="span6 contact-form centered">
                    <h3>Оставьте ваш вопросик!</h3>
                    <div id="successSend" class="alert alert-success invisible">
                        <strong>Well done!</strong>Your message has been sent.</div>
                    <div id="errorSend" class="alert alert-error invisible">There was an error.</div>
                    <form id="contact-form" class="commentForm ajaxForm" data-type="question">
                        <div class="control-group">
                            <div class="controls">
                                <input class="span12" type="text" id="name" name="name" placeholder="* Ваше имя..." />
                                <div class="error left-align" id="err-name">Please enter name.</div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input class="span12" type="email" name="email" id="email" placeholder="* Ваш email..." />
                                <div class="error left-align" id="err-email">Please enter valid email adress.</div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <textarea class="span12" name="text" id="comment" placeholder="* Комментарий..."></textarea>
                                <div class="error left-align" id="err-comment">Please enter your comment.</div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <button id="send-mail" class="message-btn">Отправить ваше сообщение</button>
                            </div>
                        </div>
                        <div id="questionDiv"></div>
                    </form>
                </div>
                <div class="span6 contact-form centered">
                    <p>ул. Антоновича (Горького), 48Б, офис 1</p>
                    <p>050-2000-103</p>
                    <div class="title">
                        <h3>Мы в социальных сетях</h3>
                    </div>
                    <div class="row-fluid centered">
                        <ul class="social">
                            <li>
                                <a class="mIc vk-icon" href="https://vk.com/tiradress"></a>
                            </li>
                            <li>
                                <a class="mIc in-icon" href="https://instagram.com/tirashowroom/"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script async="" defer="" type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&callback=initializeMap&key=AIzaSyBq1-XZVkXqK-pwgiIZGMThae5er4SgBYo"></script>
<script>
    ajaxForm();
    subscribe();
</script>