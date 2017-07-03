<?
extract($this->context);
$src = $blog->mainphoto->image->uri;
$date = date('d.m.y',$blog->created);

if($src == NULL){
    $src = '/img/logo.jpg';
}
?>

<header />


<link rel="stylesheet" href="/css/960_12_col.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" charset="utf-8">

<div class="FW">
    <div class="CW TM container_12" >

        <main class="mt">
            <div class="height_6 blogBigPhoto">
                <img src="<?=$src?>" alt="<?=$blog->title?>">
            </div>
            <div class="grid_12 mt">
                <h1><?=$blog->title?></h1>
                <div class="blogText pb mt">
                    <?=$blog->text;?>
                </div>

                <div style="text-align: right" class="pb">
                    <?=$date;?>
                </div>

                <div class="mb">
                    <div class="grid_4 alpha">
                        <?if($prev){
                            echo "<a href='/blog/{$prev->uri}'>&larr; {$prev->title}</a>";
                        }else{
                            echo '&nbsp;';
                        }?>
                    </div>
                    <div class="grid_4">
                        <a href="/" class="logoBlog">
                            <img src="/img/logo.jpg" alt="whitebride.com.ua">
                        </a>
                    </div>
                    <div class="grid_4 omega" style="text-align: right">
                        <?if($next){
                            echo "<a href='/blog/{$next->uri}'>{$next->title} &rarr;</a>";
                        }else{
                            echo '&nbsp;';
                        }?>
                    </div>
                    <div class="clear"></div>
                </div>

            </div>
            <br class="clear">
        </main>

    </div>

</div>

<footer />
