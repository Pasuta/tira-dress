<? extract($this->context); ?>

<div class="banner-top">
    <div class="container">
        <h1>Tira-showroom</h1>
        <em></em>
        <h2>
            <a style="color:white;" href="/">Главная</a>
            <label>/</label>
            <a style="color:white" href="/catalog">Каталог</a></h2>
    </div>
</div>

<br>
<div class="swiper-container gallery-top">
    <div class="swiper-wrapper">
        <?
        foreach($item->mainphoto as $photo){
            $ph1 = $photo->image->uri;
            echo "<div class='swiper-slide' style='background:url({$ph1}) 50% 50% no-repeat;background-size: contain'></div>";
        }
        ?>
    </div>
    <!-- Add Arrows -->
    <div class="swiper-button-next swiper-button-black"></div>
    <div class="swiper-button-prev swiper-button-black"></div>
</div>
<div class="swiper-container gallery-thumbs">
    <div class="swiper-wrapper">
        <?
        foreach($item->mainphoto as $photo){
            $ph2 = $photo->thumb->uri;
            echo "<div class='swiper-slide' style='background:url({$ph2}) 50% 50% no-repeat;background-size: contain'></div>";
        }
        ?>
    </div>
</div>

<div class="single">

    <div class="container">
        <div class="col-md-9">
            <div class="col-md-12 single-top-in">
                <div class="span_2_of_a1 simpleCart_shelfItem">
                    <h3><?=$item->title?></h3>
                    <?if($item->material){ ?> <p class="in-para"> <?=$item->material?></p><?}?>
                    <div class="price_single">
                        <span class="reducedfrom item_price"><?=$item->priceShow?></span>
<!--                        <span class="reducedfrom item_price">Цену уточняйте по телефону</span>-->
                        <div class="clearfix"></div>
                    </div>
                    <?if ($item->category){?><h4 class="quick">Категория: <b><?=$item->category->title?></b></h4><?}?>
                    <?if ($item->collection){?><h4 class="quick">Коллекция: <b><?=$item->collection->title?></b></h4><?}?>

                    <h4 class="quick">Краткое описание:</h4>
                    <p class="quick_desc"> <?=$item->text?></p>
                    <br>
                    <h4 class="quick">Размеры:</h4>
                    <div class="quick_desc"> <?=$item->sizes?></div>
                    <br>
                    <div class="wish-list">
                        <div class="stars">
                            <?
                            $rank = $item->rank ? $item->rank : 5;
                            for($i=1; $i<=5; $i++){
                                if($i <= $rank){
                                    echo "<span style='background: url(/img/star.svg) 50% 50% no-repeat;background-size: cover'></span>";
                                } else {
                                    echo "<span style='background: url(/img/star_cover.svg) 50% 50% no-repeat;background-size: cover'></span>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="quantity" style="display: none">
                        <div class="quantity-select">
                            <div class="entry value-minus">&nbsp;</div>
                            <div class="entry value"><span>1</span></div>
                            <div class="entry value-plus active">&nbsp;</div>
                        </div>
                    </div>

<!--                    <a href="/" class="add-to item_add hvr-skew-backward">Записаться на примерку</a>-->
                    <div class="clearfix"> </div>
                </div>

            </div>
            <div class="clearfix"> </div>
            <div style="display: none" class="tab-head">
                <nav class="nav-sidebar">
                    <ul class="nav tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">Product Description</a></li>
                        <li class=""><a href="#tab2" data-toggle="tab">Additional Information</a></li>
                        <li class=""><a href="#tab3" data-toggle="tab">Reviews</a></li>
                    </ul>
                </nav>
                <div class="tab-content one">
                    <div class="tab-pane active text-style" id="tab1">
                        <div class="facts">
                            <p > There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined </p>
                            <ul>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Research</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Design and Development</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Porting and Optimization</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>System integration</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Verification, Validation and Testing</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Maintenance and Support</li>
                            </ul>
                        </div>

                    </div>
                    <div class="tab-pane text-style" id="tab2">

                        <div class="facts">
                            <p > Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections </p>
                            <ul >
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Multimedia Systems</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Digital media adapters</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Set top boxes for HDTV and IPTV Player  </li>
                            </ul>
                        </div>

                    </div>
                    <div class="tab-pane text-style" id="tab3">

                        <div class="facts">
                            <p > There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined </p>
                            <ul>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Research</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Design and Development</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Porting and Optimization</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>System integration</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Verification, Validation and Testing</li>
                                <li><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Maintenance and Support</li>
                            </ul>
                        </div>

                    </div>

                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="col-md-3 product-bottom product-at">

            <section  class="sky-form">
                <h4 class="cate">Наши контакти</h4>
                <div class="row row1 scroll-pane">
                    <div class="col col-4">
                        <label class="checkbox">050-2000-103</label>
                        <label class="checkbox">050-2000-103</label>
                        <label class="checkbox">050-2000-103</label>
                        <label class="checkbox">050-2000-103</label>
                    </div>
                </div>
            </section>

            <section  class="sky-form">
                <h4 class="cate">Читайте наш блог</h4>
                <div class="row row1 scroll-pane">
                    <div class="col col-4">
                        <?
                        foreach ($blog as $b) {
                            echo "<a href='/blog/{$b->uri}' class='checkbox'>{$b->title}</a>";
                        }
                        ?>
                    </div>
                </div>
            </section>


        </div>
        <div class="clearfix"> </div>
    </div>

    <div class="container">
        <div class="brand">
            <hr>
            <h3 style="text-align: center">Топ продаж</h3>
            <br>
            <?
            $i = 0;
            foreach ($top as $t) {
                $i++;
                $src = $t->mainphoto->first()->image->uri;
                echo "<a href='/catalog/{$t->uri}' style='display: block' class='col-md-3 brand-grid'>
                            <img src='{$src}' class='img-responsive' alt=''>
                            <p style='text-align: center'>{$t->title}</p>
                        </a>";
            }
            ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        var menu_ul = $('.menu-drop > li > ul'),
            menu_a  = $('.menu-drop > li > a');
        menu_ul.hide();
        menu_a.click(function(e) {
            e.preventDefault();
            if(!$(this).hasClass('active')) {
                menu_a.removeClass('active');
                menu_ul.filter(':visible').slideUp('normal');
                $(this).addClass('active').next().stop(true,true).slideDown('normal');
            } else {
                $(this).removeClass('active');
                $(this).next().stop(true,true).slideUp('normal');
            }
        });

    });
</script>

<!-- Swiper JS -->
<script src="/public/swiper/dist/js/swiper.min.js"></script>

<!-- Initialize Swiper -->
<script>

    var galleryTop = new Swiper('.gallery-top', {
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        spaceBetween: 10
    });
    var galleryThumbs = new Swiper('.gallery-thumbs', {
        spaceBetween: 10,
        centeredSlides: true,
        slidesPerView: 'auto',
        touchRatio: 0.2,
        slideToClickedSlide: true
    });
    galleryTop.params.control = galleryThumbs;
    galleryThumbs.params.control = galleryTop;

</script>
