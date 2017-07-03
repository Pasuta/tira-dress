<?
extract($this->context);
?>

<link rel="stylesheet" href="/css/960_12_col.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" charset="utf-8">

<header />

<div class="FW">
    <div class="CW TM container_12">

        <div class="height_8 mt animated imgToAllSizes" style="margin-bottom: 30px">
            <img src="/img/main/11.jpg" class="bigPhotoMain animated">
            <a href="/item?created=high" class="mainCatalogBtn grid_4 push_4">Каталог</a>
        </div>

        <main class="height_10" style="margin-top: 20px">
            <div class="grid_8 height_10">
                <div class="height_4">
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

                <div class="height_4 mainPopular mt">
                    <div class="mainPopularHead">
                        <h2>Последние поступления</h2>
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

                <div class="height_1 mainPopular mt" style="margin-top: 30px">

                    <div class="grid_3 alpha">
                        <div class="mainPopularHead">
                            <h2>Следите за нами:</h2>
                        </div>
                        <div class="mt">
                            <a href="https://vk.com/whitebride_dresses" target="_blank" class="soc socvk"></a>
                            <a href="https://instagram.com/whitebridekiev/" target="_blank" class="soc socin"></a>
                        </div>
                    </div>

                    <div class="grid_3">
                        <div class="mainPopularHead">
                            <h2>Скоро:</h2>
                        </div>
                        <div class="mt">
                            <a href="" class="soc socfb"></a>
                            <a href="" class="soc soctw"></a>
                            <a href="" class="soc socpin"></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid_4 height_10">
                <div class="height_5">
                    <h2>Новинка</h2>
                    <div style="width: 100%;height: 91.5%" class="imgToAllSizes mt">
                        <?
                        $src = $new->mainphotov->image->uri;
                        echo "<a href='/item/{$new->uri}'>
                                <img src='{$src}' alt='{$new->metadesc}'>
                                <div class='alizarin badge'>NEW</div>
                              </a>";
                        ?>
                    </div>
                </div>
                <div class="height_5 mt">
                    <h2>Топ продаж</h2>
                    <div style="width: 100%;height: 91.5%" class="imgToAllSizes mt">
                        <?
                        $src = $top->mainphotov->image->uri;
                        echo "<a href='/item/{$top->uri}'>
                                <img src='{$src}' alt='{$top->metadesc}'>
                                <div class='alizarin badge badgeBlue'>TOP</div>
                              </a>";
                        ?>
                    </div>
                </div>
            </div>

            <br class="clear">
        </main>

        <div class="mt">
            <h2>Блог</h2>
            <?
            foreach ($blog as $b) {
                $src = $b->mainphoto->image->uri;
                if($src == NULL){
                    $src = '/img/logo.jpg';
                }
                echo "
                        <a href='/blog/{$b->uri}' class='grid_4 height_3 tm blogLink'>
                            <img src='{$src}' alt='{$b->title}'>
                            <div><i>{$b->title}</i></div>
                        </a>
                     ";
            }
            ?>
            <br class="clear">
        </div>

    </div>
</div>

<footer />

<script>
    mainslider();
    sliderHorizontal();
    changeBigPhoto();
</script>
