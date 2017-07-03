<?
extract($this->context);
$item = $item;
?>

<header />

<div class="FW">
    <div class="CW">
        <h3 class="itemHeader"><a href='/item'>Каталог</a>&rarr;ART<?=$item->id;?></h3>
        <div class="contentmain">
            <?
            $main = $item->mainphoto->first()->image->uri;
            echo "
                    <div class='FL itemLeft'>
                        <div class='slider'>
                            <div class='sliderArr sliderArrU animated'></div>";
                     foreach($item->mainphoto as $mp){
                        $src = $mp->image->uri;
                        echo "<div class='sliderSmall' style='background: url({$src}) 50% 50% no-repeat;background-size: cover'></div>";
                     }
            echo "          <div class='sliderArr sliderArrD animated'></div>
                        </div>
                        <div class='sliderBig' style='background: url({$main}) 50% 50% no-repeat;background-size: cover'></div>
                        <br class='clear'>
                    </div>";
            ?>
            <div class="FR itemRight">
                <div class="itemPrice"><?=$item->price;?> грн</div>
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
                        <div class="ctlglistIm ctlglistImY" style=""></div>
                        <div class="ctlglistText">12</div>
                        <div class="ctlglistIm ctlglistImL" style=""></div>
                        <div class="ctlglistText">7</div>
                        <div class="ctlglistIm ctlglistImC" style=""></div>
                        <div class="ctlglistText" style="margin-right: 0">3</div>
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
                    foreach($also as $al){
                        $mn = $al->mainphoto->first()->image->uri;
                        print "
                                <a class='cataloglistitem itemAlso' href='' style='background: url({$mn}) 50% 50% no-repeat;background-size: cover'>

                                </a>
                              ";
                    }
                    ?>
                </div>
                <br class="clear">
            </div>
        </div>
    </div>
</div>

<footer />

<script>
    ajaxForm();
</script>