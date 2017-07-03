<? extract($this->context); ?>

<header />

<div class="FW">
    <div class="CW TM">

        <div class="photoBlock">
            <div class="photoBlockL">
                <div class="photoBlockP"><img class="add" src="<?=$item->getphoto[2]?>"></div>
                <div class="photoBlockP"><img class="add"  src="<?=$item->getphoto[3]?>"></div>
            </div>
            <div class="photoBlockMP1"><img src="<?=$item->getphoto[1]?>" class="big"></div>
            <br class="clear">
        </div>

        <div class="photoBlockR">
            <div class="itemMainInfo">
                <h1><?=$item->title?></h1>
                <div class="itemMainInfoPrice">
                    <div class="price">
                        <?=$item->price?> уе
                    </div>

                    <div class="stars">
                        <?
                        for($i=1; $i<=5; $i++){
                            if($i <= $item->rank){
                                echo "<span style='background: url(/img/star.svg) 50% 50% no-repeat;background-size: cover'></span>";
                            } else {
                                echo "<span style='background: url(/img/star_cover.svg) 50% 50% no-repeat;background-size: cover'></span>";
                            }
                        }
                        ?>
                    </div>

                    <div class="howto">
                        <a href="/contact/howto">Как заказать?</a>
                    </div>

                    <br class="clear">
                </div>

                <div class="itemMainInfoPrice">
                    <div>Преобладающие материалы: <?=$material;?></div>

                    <br class="clear">
                </div>

                <div class="itemMainInfoPrice">
                    <div><?=$item->text;?></div>

                    <br class="clear">
                </div>
            </div>

            <div style="height: 270px;">
                <h3 style="margin-bottom: 20px">Поделиться:</h3>

                <div>
                    <div class="soc socfb ext_link" data-url="http://www.facebook.com/sharer/sharer.php?u=<?=$currentUrl?>"></div>

                    <!-- vk share btn -->
<!--                    <script type="text/javascript">-->
<!--                        document.write(VK.Share.button(-->
<!--                            {-->
<!--                                url: "--><?//=$currentUrl?><!--",-->
<!--                                title: '--><?//=$item->title?><!--',-->
<!--                                image: "http://whitebride.com.ua/--><?//=$item->mp->image->uri?><!--",-->
<!--                                noparse: true-->
<!--                            },-->
<!--                            {type: 'custom', text: '<div class="soc socvk"></div>'}-->
<!--                        ));-->
<!--//                    </script>-->


<!--                    <div class="soc socin ext_link"></div>-->
                    <div class="soc socvk ext_link" data-url="http://vk.com/share.php?url=http://<?=$currentUrl?>"></div>
                    <div class="soc socpin ext_link" data-url="http://pinterest.com/pin/create/button/?url=<?=$currentUrl?>&media=http://www.whitebride.com.ua<?=$item->mp->image->uri?>&description=<?=$item->metadesc;?>"></div>
                    <div class="soc soctw ext_link" data-url="https://twitter.com/intent/tweet?text=<?echo $item->metadesc;?>&url=https://<?=$currentUrl?>"></div>
                </div>

                <h3 style="margin-bottom: 20px;margin-top: 20px">К платью подойдет:</h3>
                <div>
                    <?
                    foreach ($acc as $a) {
                        $src = $a->mainphoto->first()->image->uri;
                        echo " <a href='/item/{$a->uri}' class='additional'><img src='{$src}'></a>";
                    }
                    ?>
                </div>

            </div>
        </div>

        <br class="clear">

    </div>

    <br>

    <div class="CW TM ">
        <h3>ТОП ПРОДАЖ</h3><br>
        <?
        $i = 0;
        foreach ($top as $t) {
            $i++;
            $src = $t->mainphoto->first()->image->uri;
            $m = count($top) == $i ? "style='margin-right:0'" : '';
            echo "<a href='/item/{$t->uri}'><div {$m} class='photoBlockP photoBlockP1top'><img src='{$src}'></div></a>";
        }
        ?>
    </div>


    <div class="CW TM ">
        <h3>ПОСЛЕДНЕЕ ПОСТУПЛЕНИЕ</h3><br>
        <?
        $i = 0;
        foreach ($last as $t) {
            $i++;
            $src = $t->mainphoto->first()->image->uri;
            $m = count($top) == $i ? "style='margin-right:0'" : '';
            echo "<a href='/item/{$t->uri}'><div {$m} class='photoBlockP photoBlockP1top'><img src='{$src}'></div></a>";
        }
        ?>
    </div>

</div>

<footer />

<script>
    (function(){ // смена фото
        var main = document.getElementsByClassName('big')[0];
        var additional = document.getElementsByClassName('add');

        for(var i=0; i<additional.length;i++){
            var photo = additional[i];
            Event.add(photo, 'click', function(){
                var bigsrc = main.src;
                main.src = this.src;
                this.src = bigsrc;
            });
        }
    })();

    (function(){
        var ext_link = document.getElementsByClassName('ext_link');
        for (var i = 0; i < ext_link.length; i++) {
            var obj = ext_link[i];
            Event.add(obj, 'click', function(){
                var link = this.getAttribute("data-url");
                var new_window = window.open(link, "_blank");
                new_window.focus();
            });
        }
    })(); // share links
</script>