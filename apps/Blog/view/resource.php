<?
extract($this->context);
?>

<header />

<!-- Portfolio section start -->
<div class="section secondary-section" id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class="title">
            <h1><?=$blog->title?></h1>
            <?=$blog->text?>
        </div>
    </div>
</div>

<div id="clients" style="display: none">
    <div class="section primary-section">
        <div class="triangle"></div>
        <div class="container">
            <div class="title">
                <h1>Комментарии</h1>
            </div>
            <div class="row">

                <?
                foreach($comment as $c) {
                    $d = date('Y-m-d',$c->created);
                    echo "
                            <div class='span4'>
                                <div class='testimonial'>
                                    {$c->text}
                                    <div class='whopic'>
                                        <div class='arrow'></div>
                                        <img src='/img/girl-512.png' class='centered' alt='client 1'>
                                        <strong>{$c->name}
                                            <small>{$d}</small>
                                        </strong>
                                    </div>
                                </div>
                            ";
                }?>

            </div>
        </div>
    </div>
</div>
<!-- Portfolio section end -->