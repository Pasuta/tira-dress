<?
extract($this->context);
?>

<header />

<div class="FW">
    <div class="CW">
        <div class="mainimg">
            <div>
                <div><h1><b>WHITEBRIDE</b> - это место, где каждая невеста найдет себе наряд из сказки!</h1></div>
            </div>
        </div>

        <div class="contentmain">

            <div class="FL contentmainL">
                <?
                $mi = $lastitemone->mainphoto->first()->image->uri;
                ?>
                <a href="/item/<?=$lastitemone->id?>" onclick="return false" class="mainslider animated" style="background:url(<?=$mi;?>) 50% 0 no-repeat;background-size: cover">
                    <div class="mainsliderpoint">
                        <?
                        $q = 0;
                        foreach($lastitem as $li){
                            $mi = $li->mainphoto->first()->image->uri;
                            print "<div class='mainsliderdiv' data-uri='{$mi}' data-n='{$q}' data-href='{$li->id}'></div>";
                        }
                        ?>
                        <br class="clear">
                    </div>
                </a>

                <div class="mainblock">
                    <a href="/item" class="mainlink">Каталог</a>
                    <p style="line-height: 1.5">Будучи маленькими девочками, мы все мечтаем выйти замуж за принца и купить роскошное
                        свадебное платье, если с принцем везет далеко не всем, то разнообразие свадебных платьев
                        поражает воображение даже самых избалованных модниц. Мы зачастую думаем, что чем выше
                        цена за платье, тем красивее невеста, но это далеко не так. В моде стильная скромность,
                        скромные аксессуары и кружево, дань камням и многотысячным юбкам  сошла на нет, к моему
                        счастью</p>
                </div>

                <div class="mainblock">
                    <a href="/item" class="mainlink">Топ продаж</a>
                    <div class="maintoplist">

                        <?
                        foreach($topitem as $ti){
                            $src = $ti->mainphoto->first()->image->uri;
                            echo "
                                    <a href='/item/$ti->id' style='background: url($src) 50% 50% no-repeat;background-size: cover'>
                                        <div class='maintoplistshow animatedFast'>
                                            <div></div>
                                            <div>10</div>
                                            <br class='clear'>
                                        </div>
                                    </a>
                                 ";
                        }
                        ?>

                        <br class="clear">
                    </div>
                </div>

            </div>

            <div class="FR contentmainR">
                <div class="mainblock" style="border-bottom: none">
                    <a href="/blog" class="mainlink" style="margin-bottom: 10px">Блог</a>
                    <?
                    foreach($blog as $b){
                        $avatar = $b->admin->avatar->image->uri;
                        $name = $b->admin->name;
                        $date = date('d.m.y',$b->created);
                        echo "
                                <div class='mainbloglist'>
                                    <a href='/blog/$b->id' class='mainbloglistlink'>$b->title</a>
                                    <a class='mainbloglistav' style='background: url($name) 50% 50% no-repeat;background-size: cover'></a>
                                    <a class='FL mainbloglistname'>$name</a>
                                    <div class='FR'>$date</div>
                                    <br class='clear'>
                                </div>
                             ";
                    }
                    ?>
                </div>

                <div class="mainblock" style="margin-bottom: 0">
                    <a href="/review" class="mainlink">Отзывы</a>
                    <div class="mainthnxlist">
                        <?
                        $countReview = count($review);
                        foreach($review as $r){
                            echo "<a href='/review'>$r->text<div></div></a>";
                        }
                        ?>

                        <a href="/review">Всего отзывов: <?=$countReview;?></a>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
</div>

<footer />

<script>
    mainslider();
</script>
