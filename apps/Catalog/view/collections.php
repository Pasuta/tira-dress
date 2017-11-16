<? extract($this->context);?>
<header />

<div class="section secondary-section " id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class=" title">
            <h1>Наши коллекции</h1>
            <p>Эксклюзивные свадебные коллекции нашего бренда TIRA BRIDAL</p>
        </div>
        <div id="single-project">

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
                               <div class='thumbnail' style='background: white;height: 605px'>
                                    <a href='/catalog/collection/{$o->uri}' rel='#slidingDiv{$slidingDiv}'>
                                        <img src='{$src}' alt='project {$i}' class='centerImg'>
                                    </a>
                                    <h3 style='color:black;text-transform: capitalize;font-weight: 100'>{$o->title}</h3>
                                </div>
                            </li>
                            ";
                }?>

                <div class=" title">
                    <h1>Наши Категории</h1>
                </div>

                <?
                $j = 0;
                $length = count($category);
                $span = 'span4';
                if ($length == 1) $span = 'span12';
                if ($length == 2) $span = 'span6';

                foreach($category as $c) {
                    $j++;
                    if ($j != 1) $slidingDiv = $j;
                    $m = new Message();
                    $m->action = 'load';
                    $m->urn = $c['urn'];
                    $o = $m->deliver();
                    $src = $o->mainphoto ? $o->mainphoto->image->uri : "/public/images/logo/Tira_logo03.jpg";
                    echo "
                            <li class='{$span} mix web'>
                               <div class='thumbnail' style='background: white;height: 605px'>
                                    <a href='/catalog/category/{$o->uri}' rel='#slidingDiv{$slidingDiv}'>
                                        <img src='{$src}' alt='project {$j}' class='centerImg'>
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