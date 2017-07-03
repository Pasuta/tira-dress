<?
extract($this->context);
$item = $item;
print "<input type=hidden id='urn' value='{$item->urn}'>";
?>

<header />

<div class="FW">
    <div class="CW">
        <h3 class="itemHeader"><a href='/item'>Каталог</a>&rarr;ART<?=$item->id;?></h3>
        <div class="contentmain">

            <div class='productLeft'>
                <div class="sliderLeft">
                    <div class='sliderUp up'><img src='/img/arrowTop.png'></div>
                    <div class='sliderCont cont'>
                        <div class='sliderBody animatedFast bod'>
                            <?
                            foreach($item->mainphoto as $photo){
                                $ph = $photo->image->uri;
                                print "<div class='animated smallPhoto int'> <img src='{$ph}' alt='whitebride'> </div>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class='sliderDown down'><img src='/img/arrowBottom.png'></div>
                </div>

                <?
                $arr = array();
                foreach($item->mainphoto as $pp) {
                    $arr[]=$pp->image->uri;
                }
                $arr = array_reverse($arr);
                $count = count($arr);
                $i = 0;
                foreach($arr as $ph){
                    $i++;
                    if($i == $count) $z = 1;
                    else $z = 0;
                    print "<div data-n='{$i}' class='bigPhoto animatedFast' style='background: #ffffff;opacity: {$z}'><img src='{$ph}' alt='whitebride'></div>";
                }
                ?>
            </div>
            <div class="FR itemRight">
                <a href="/contact/howto" class="itemPrice"><?=$item->price;?> грн</a>
                <div class="itemRightDesc">
                    <p>Описание:</p>
                    <?=$item->text;?>
                </div>
                <div class="itemSizes">
                    <p>Размеры:</p>
                    <?=$item->sizes;?>
                </div>
                <div class="itemSoc">
                    <div class="cataloglistitemBlock itemSocIcons">
                        <div class="ctlglistIm ctlglistImY" style="margin-left: 40px"></div>
                        <div class="ctlglistText"><?=$item->countview;?></div>
<!--                        <div class="ctlglistIm ctlglistImL" style=""></div>-->
<!--                        <div class="ctlglistText">7</div>-->
                        <div class="ctlglistIm ctlglistImC" style=""></div>
                        <div class="ctlglistText" style="margin-right: 0"><?=$countcomment;?></div>
                        <br class="clear">
                    </div>
                </div>
            </div>

            <br class="clear">

            <div style="margin-top: 10px;">

                <!--                <div style="width: "></div>-->
                <div class="FL itemLeft itemLeftB">
                    <?
                    if(count($comment)){
                        echo "<h3>Комментарии:</h3>";
                        foreach($comment as $com){
                            echo "
                                    <div class='commentBlock'>
                                        <div style='margin-bottom: 10px'>{$com->text}</div>
                                        <div class='FL'>{$com->name}</div>
                                        <div class='FR'>{$com->date}</div>
                                        <br class='clear'>
                                    </div>
                                 ";
                        }
                    }
                    ?>


                    <h3>Оставить комментарий:</h3>
                    <form class="commentForm ajaxForm" data-which="item" data-urn="<?=$item->urn;?>" data-type="comment">
                        <textarea name="text" placeholder="Ваше сообщение"></textarea>
                        <input type="text" name="name" placeholder="Ваше имя">
                        <input type="submit" value="Отправить">
                        <br class="clear">
                    </form>
                </div>

                <div class="FR itemRight itemRightB">
                    <h3 style="margin-bottom: 10px">Смотрите также</h3>
                    <?
//                    foreach($also as $al){
//                        $mn = $al->mainphoto->first()->image->uri;
//                        print "
//                                <a class='cataloglistitem itemAlso' href='' style='background: url({$mn}) 50% 50% no-repeat;background-size: cover'>
//
//                                </a>
//                              ";
//                    }
                    ?>
                    <?
                    foreach($also as $i){
                        $src = $i->mainphoto->first()->image->uri;
                        $countComment = count($i->comment);
                        echo "
                        <a href='/item/{$i->id}' class='itemCatalog animatedFast itemItem'>
                            <div class='itemCatalogImg' style='background: url($src) 50% 0 no-repeat;background-size: cover'></div>
                            <div class='itemCatalogInfo'>
                                <div class='itemCatalogInfoArt'>
                                    <h3>ART{$i->id}</h3>
                                    <div><b>{$i->price} грн</b></div>
                                </div>
                                <br class='clear'>
                            </div>
                        </a>";
                    }
                    ?>
                    <br class="clear">
                </div>
                <br class="clear">
            </div>
        </div>
    </div>
</div>

<footer />

<script>
    ajaxForm();
    slider();
    infinitySliderWithImgs();
    countview();
</script>