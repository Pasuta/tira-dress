<? extract($this->context);?>
<header />
<!-- Portfolio section start -->
<div class="section secondary-section " id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class=" title">
            <h1>Наши коллекции</h1>
            <p>Эксклюзивные свадебные коллекции нашего бренда TIRA BRIDAL</p>
        </div>

        <!-- Start details for portfolio project 1 -->
        <div id="single-project">

            <!-- End details for portfolio project 9 -->
            <ul id="portfolio-grid" class="thumbnails row">

                <?
                $length = count($collection);
                $span = 'span4';
                if ($length == 1) $span = 'span12';
                if ($length == 2) $span = 'span6';

                foreach($collection as $c) {
                    $i++;
                    if ($i != 1) $slidingDiv = $i;
                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $c['urn'];
                    $o = $m->deliver();
                    $src = $o->mainphoto ? $o->mainphoto->image->uri : "/public/images/logo/Tira_logo03.jpg";
                    echo "
                            <li class='{$span} mix web'>
                                <div class='thumbnail' style='background: white'>
                                    <a href='/catalog/collection/{$o->uri}' rel='#slidingDiv{$slidingDiv}'>
                                        <img src='{$src}' alt='project {$i}'>
                                    </a>
                                    <h3 style='color:black;text-transform: capitalize;font-weight: 100'>{$o->title}</h3>
                                </div>
                            </li>
                            ";
                }?>

            </ul>
        </div>
    </div>
</div>