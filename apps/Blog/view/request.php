<?
extract($this->context);
?>

<header />

<!-- Portfolio section start -->
<div class="section secondary-section " id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class=" title">
            <h1>Блог TIRA Showroom</h1>
<!--            <p>Тут будет небольшой текст, но его можно и не выводить, тут уж как сами девули хотите</p>-->
        </div>
        <div id="single-project">
            <!-- End details for portfolio project 9 -->
            <ul id="portfolio-grid" class="thumbnails row">
                <?
                foreach($blog as $b) {
                    $src = $b->mainphoto->image->uri;
                    if ($src == NULL) $src = '/public/images/logo/Tira_logo03.jpg';
                    echo "
                            <li class='span4 mix web' style='height: 400px'>
                                <div class='thumbnail' style='background: white'>
                                    <a href='/blog/{$b->uri}'>
                                        <img src='{$src}' alt='project {$i}' style='max-height: 315px;'>
                                        <h3 style='color: black'>{$b->title}</h3>
                                    </a>
                                </div>
                            </li>
                            ";
                }?>
            </ul>

            <div class="title">
                <h3 class="black">
                    <?
                    if ($pages > 1) {
                        for($i = 0; $i < $pages; $i++) {
                            $n = $i + 1;
                            $c = $i == $page ? 'style="font-weight: bold"' : '';
                            echo "<a href='/blog?page=$i' $c>&nbsp; $n &nbsp;</a>";
                        }
                    }
                    ?>
                </h3>
            </div>
        </div>
    </div>
</div>
<!-- Portfolio section end -->