<? extract($this->context);?>
<header />
<!-- Portfolio section start -->
<div class="section secondary-section " id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class=" title">
            <h1>Наши колекции</h1>
            <p>Эксклюзивная свадебная коллекция лето-осень 2017 нашего бренда TIRA BRIDAL</p>
        </div>

        <!-- Start details for portfolio project 1 -->
        <div id="single-project">

            <!-- End details for portfolio project 9 -->
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
                                <p style='color:black; text-transform: capitalize'>{$i->title}</p>
                                <p style='color:black'>Цену уточняйте по телефону</p>
                            </div>
                        </li>";
                }
                ?>

            </ul>
        </div>
    </div>
</div>