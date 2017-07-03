<?
extract($this->context);
?>

<link rel="stylesheet" href="/css/960_12_col.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" charset="utf-8">

<header />

<div class="FW">
    <div class="CW TM container_12">

        <main class="clouds mt">

            <?
            foreach ($blog as $b) {
                $src = $b->mainphoto->image->uri;
                if($src == NULL){
                    $src = '/img/logo.jpg';
                }
                echo "
                        <a href='/blog/{$b->uri}' class='grid_4 height_3 bm blogLink'>
                            <img src='{$src}' alt='{$b->title}'>
                            <div><i>{$b->title}</i></div>
                        </a>
                     ";
            }
            ?>

        </main>
    </div>
</div>

<footer />

