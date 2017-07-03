<?
extract($this->context);
?>

<header />

<div class="FW">
    <div class="CW TM container_12">

        <div class="height_8 mt animated imgToAllSizes" style="margin-bottom: 30px">
            <img src="/img/main/11.jpg" class="bigPhotoMain animated">
            <a href="/item?created=high" class="mainCatalogBtn grid_4 push_4">Каталог</a>
        </div>

        <main class="height_5" style="margin-top: 20px">
            <div class="grid_8 height_5">
                <div class="height_5">
                    <div class="mainPopularHead">
                        <h2>Популярное</h2>
                    </div>
                    <div class="sliderHorizontal mt">
                        <div class="sliderArr" data-move="left">&larr;</div>

                        <div class="sliderFrame">
                            <div class="sliderContent animated">
                                <?
                                foreach($rank5 as $o) {
                                    $m = new Message();
                                    $m->action = 'load';
                                    $m->urn = $o['urn'];
                                    $o = $m->deliver();
                                    if($o->mainphotov){
                                        $src = $o->mainphotov->thumb->uri;
                                        echo "<a href='/item/{$o->uri}'><img src='{$src}' alt='{$o->metadesc}'></a>";
                                    }
                                }?>
                                <br class="clear">
                            </div>
                        </div>

                        <div class="sliderArr" data-move="right">&rarr;</div>
                        <br class="clear">
                    </div>
                </div>
                <br class="clear">

            </div>

            <div class="grid_4 height_5">
                <div class="height_4">
                    <h2 style="text-align: right">Новинка</h2>
                    <div style="width: 100%;;height:310px;" class="imgToAllSizes mt">
                        <?
                        $src = $new->mainphotov->image->uri;
                        echo "<a href='/item/{$new->uri}'>
                                <img src='{$src}' alt='{$new->metadesc}' style='object-fit: cover;width: 270px;height: 310px'>
                                <div class='nephritis badge'> NEW</div>
                              </a>";
                        ?>
                    </div>
                </div>
            </div>

            <br class="clear">
        </main>

        <main class="height_5">
            <div class="grid_4 height_5">
                <div class="height_4 mt">
                    <h2>Топ продаж</h2>
                    <div style="width: 100%;height: 310px" class="imgToAllSizes mt">
                        <?
                        $src = $top->mainphotov->image->uri;
                        echo "<a href='/item/{$top->uri }'>
                                <img src='{$src}' alt='{$top->metadesc}' style='object-fit: cover;width: 270px;height: 310px'>
                                <div class='alizarin badge badgeBlue' style='right: 0'>TOP</div>
                              </a>";
                        ?>
                    </div>
                </div>
            </div>

            <div class="grid_8 height_5">
                <div class="height_4 mainPopular mt">
                    <div class="mainPopularHead">
                        <h2 style="text-align: right">Последние поступления</h2>
                    </div>
                    <div class="sliderHorizontal mt">
                        <div class="sliderArr" data-move="left">&larr;</div>

                        <div class="sliderFrame">
                            <div class="sliderContent animated">
                                <?
                                foreach($last6 as $o) {
                                    $m = new Message();
                                    $m->action = 'load';
                                    $m->urn = $o['urn'];
                                    $o = $m->deliver();
                                    if($o->mainphotov){
                                        $src = $o->mainphotov->thumb->uri;
                                        echo "<a href='/item/{$o->uri}'><img src='{$src}' alt='{$o->metadesc}'></a>";
                                    }
                                }?>
                                <br class="clear">
                            </div>
                        </div>

                        <div class="sliderArr" data-move="right">&rarr;</div>
                        <br class="clear">
                    </div>
                </div>
                <br class="clear">

            </div>

            <br class="clear">
        </main>

        <main class="height_5" style="margin-top: 20px">
            <div class="grid_8 height_5">
                <div class="height_5">
                    <div class="mainPopularHead">
                        <h2>Детская коллекция</h2>
                    </div>
                    <div class="sliderHorizontal mt">
                        <div class="sliderArr" data-move="left">&larr;</div>

                        <div class="sliderFrame">
                            <div class="sliderContent animated">
                                <?
                                foreach($itemChildren as $o) {
                                    $m = new Message();
                                    $m->action = 'load';
                                    $m->urn = $o['urn'];
                                    $o = $m->deliver();
                                    if($o->mainphotov){
                                        $src = $o->mainphotov->thumb->uri;
                                        echo "<a href='/item/{$o->uri}'><img src='{$src}' alt='{$o->metadesc}'></a>";
                                    }
                                }?>
                                <br class="clear">
                            </div>
                        </div>

                        <div class="sliderArr" data-move="right">&rarr;</div>
                        <br class="clear">
                    </div>
                </div>
                <br class="clear">

            </div>

            <div class="grid_4 height_5">
                <div class="height_4">
                    <h2 style="text-align: right">Горячее</h2>
                    <div style="width: 100%;;height:310px;" class="imgToAllSizes mt">
                        <?
                        $src = $hot->mainphotov->image->uri;
                        echo "<a href='/item/{$hot->uri}'>
                                <img src='{$src}' alt='{$hot->metadesc}' style='object-fit: cover;width: 270px;height: 310px'>
                                <div class='alizarin badge'>HOT</div>
                              </a>";
                        ?>
                    </div>
                </div>
            </div>

            <br class="clear">
        </main>


    </div>
</div>

<footer />

<script>
    mainslider();
    sliderHorizontal();
    changeBigPhoto();
</script>
