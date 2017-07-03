<?
extract($this->context);
?>

<div class="FW sorting animatedFast">
    <div class="CW sortingI">
        <div class="sortingB">
            <div>
                <a href="#top" class="sortLogo"><img src="/img/logo.jpg"></a>
            </div>

            <div>
                <span>По цене:</span>
                <span>
                    <a href="/itemChildren?price=low" <?if($_GET['price']=='low') echo "style='color: firebrick'";?>>От меньшей к большей &uparrow;</a><br>
                    <a href="/itemChildren?price=high" <?if($_GET['price']=='high') echo "style='color: firebrick'";?>>От большей к меньшей &darr;</a>
                </span>
                <br class="clear">
            </div>

            <div>
                <span>По дате добавления:</span>
                <span>
                    <a href="/itemChildren?created=low" <?if($_GET['created']=='low') echo "style='color: firebrick'";?>>От старых к новым &uparrow;</a><br>
                    <a href="/itemChildren?created=high" <?if($_GET['created']=='high') echo "style='color: firebrick'";?>>От новых к старым &darr;</a>
                </span>
                <br class="clear">
            </div>

            <div>
                <span>По популярности:</span>
                <span>
                    <a href="/itemChildren?rank=low" <?if($_GET['rank']=='low') echo "style='color: firebrick'";?>>От менее популярных к более популярным &uparrow;</a><br>
                    <a href="/itemChildren?rank=high" <?if($_GET['rank']=='high') echo "style='color: firebrick'";?>>От более популярных к менее популярным &darr;</a>
                </span>
                <br class="clear">
            </div>


            <br class="clear">
        </div>
    </div>
</div>

<header />

<div class="FW">
    <div class="CW">

        <div class="contentmain contentcatalog" style="border: none">
            <?
            foreach($item as $i){
                $src = $i->mainphotov->image->uri;
                echo "
                    <a class='cataloglistitem' href='/itemChildren/{$i->uri}' style='position: relative'>
                        <img src='{$src}' alt='{$i->metadesc}' style='position: absolute'>
                        <div class='animatedFast cataloglistitemAn'>
                            <div class='cataloglistitemBlock'>
                                <div class='ctlglistIm ctlglistImY hide' style=''></div>
                                <div class='ctlglistText hide'>400 уе</div>
                                <div class='ctlglistIm hide ctlglistImL' style=''></div>
                                <div class='ctlglistText hide'>7</div>
                                <div class='ctlglistIm hide ctlglistImC' style=''></div>
                                <div class='ctlglistText hide' style='margin-right: 0'>3</div>
                                <br class='clear'>
                                <div class='iteminfo'>
                                    <p>{$i->title}</p>
                                    <p>{$i->price} у.е.</p>
                                </div>
                            </div>
                        </div>
                    </a> ";
            }
            ?>

            <br class="clear">
        </div>
    </div>
</div>

<footer />

<script>

    window.onscroll = function() {
        document.getElementsByClassName('sorting')[0].style.top = window.pageYOffset >= 50 ? 0 : '-100px'; // выпадающее меню
    };

</script>
