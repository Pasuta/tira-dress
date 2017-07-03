<?
extract($this->context);
?>

<header />

<div class="FW">
    <div class="CW">
        <div class="contentmain contentcatalog">
            <div class="FL sort">
                <h3>Сортировать</h3>

                <div class="sortBlock">
                    <h4>По цене</h4>
                    <a href="/item?price=high" <?if($_GET['price']=='high') echo "style='color: #2C3E50'";?>>По возростанию &uarr;</a>
                    <a href="/item?price=low" <?if($_GET['price']=='low') echo "style='color: #2C3E50'";?>>По ниспадению &darr;</a>
                    <br class="clear">
                </div>

                <div class="sortBlock">
                    <h4>По дате добавления</h4>
                    <a href="/item?created=high" <?if($_GET['created']=='high') echo "style='color: #2C3E50'";?>>По возростанию &uarr;</a>
                    <a href="/item?created=low" <?if($_GET['created']=='low') echo "style='color: #2C3E50'";?>>По ниспадению &darr;</a>
                    <br class="clear">
                </div>

                <div class="sortBlock">
                    <h4>По просмотрам</h4>
                    <a href="/item?countview=high" <?if($_GET['countview']=='high') echo "style='color: #2C3E50'";?>>По возростанию &uarr;</a>
                    <a href="/item?countview=low" <?if($_GET['countview']=='low') echo "style='color: #2C3E50'";?>>По ниспадению &darr;</a>
                    <br class="clear">
                </div>

                <div class="sortBlock">
                    <a href="/item?instaff=1" <?if($_GET['instaff']=='1') echo "style='color: #2C3E50'";?>>Только в наличии</a>
                    <a href="/item?toorder=1" <?if($_GET['toorder']=='1') echo "style='color: #2C3E50'";?>>Только под заказ</a>
                    <br class="clear">
                </div>

                <div class="sortBlock">
                    <a href="/item">Сбросить фильтры</a>
                    <br class="clear">
                </div>
<!--                <div class="sortBlock">-->
<!--                    <a href="/item?top=first">Топ продаж</a>-->
<!--                    <a href="/item?sell=first">Скидки</a>-->
<!--                    <br class="clear">-->
<!--                </div>-->
            </div>

            <div class="FL" style="width: 750px;margin-left: 50px;">
                <?
                foreach($item as $i){
                $src = $i->mainphoto->first()->image->uri;
                $countComment = count($i->comment);
                echo "
                        <a href='/item/{$i->uri}' class='itemCatalog animatedFast'>
                            <div class='itemCatalogImg' style='background: url($src) 50% 0 no-repeat;background-size: cover'></div>
                            <div class='itemCatalogInfo'>
                                <div class='itemCatalogInfoArt'>
                                    <h3>ART{$i->id}</h3>
                                    <div><b>{$i->price} грн</b></div>
                                </div>
                                <div class='itemCatalogInfoIcon'>
                                    <div class='itemCatalogInfoIconDiv'>
                                        <div>{$countComment}</div>
                                        <div class='itemCatalogInfoIconDiv1'></div>
                                        <br class='clear'>
                                    </div>
                                    <div class='itemCatalogInfoIconDiv'>
                                        <div>{$i->countview}</div>
                                        <div class='itemCatalogInfoIconDiv2'></div>
                                        <br class='clear'>
                                    </div>
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

<footer />
