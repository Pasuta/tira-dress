<? extract($this->context);?>
<header />
<!-- Portfolio section start -->
<div class="section secondary-section " id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class=" title">
            <h1><?=$collection->title?></h1>
        </div>

        <div id="single-project">

            <ul id="portfolio-grid" class="thumbnails row">

                <?
                foreach($item as $i){
                    $src = $i->mainphotov->image->uri;
                    echo "
                        <li class='span4 mix web' style='height: 550px'>
                            <div class='thumbnail' style='background: white'>
                                <a href='/catalog/{$i->uri}'>
                                    <img src='{$src}' alt='project {$i}' style='height: 450px'>
                                </a>
                                <br>
                                <br>
                                <p style='color:black; text-transform: capitalize'>{$i->title}</p>
                                <p style='color:black'>{$i->priceShow}</p>
                            </div>
                        </li>";
                }
                ?>

            </ul>

            <hr>

            <br>
            <div><?=$collection->text?></div>
            <br>
        </div>
    </div>
</div>