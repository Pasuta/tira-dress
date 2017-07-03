<?
extract($this->context);
?>

<header />

<div class="FW">
    <div class="CW">
        <div class="contentmain blogitem">
            <?
            $avatar = $blog->admin->avatar->image->uri;
            $name = $blog->admin->name;
            $date = date('d.m.y',$blog->created);
            echo "<h1>{$blog->title}</h1>";
            echo "
                 <div>
                 {$blog->text}
                     <div class='FL bloglistitem'>
                        <a class='FL bloglistitemAv'  style='background: url($avatar) 50% 50% no-repeat;background-size: cover;'></a>
                        <div class='FL bloglistitemDiv'>
                            <a>$name</a>
                            <div>$date</div>
                        </div>
                        <br class='clear'>
                    </div>

                    <div class='FL'>
                        <div>Предыдущая статья автора:</div>";
                        foreach($blogAdmin as $ba){
                            if($ba->urn == $blog->urn) continue;
                            else echo "<div><a href=''>$ba->title</a></div>";
                        }
            echo "      </div>
                    <br class='clear'>
                </div>
                 ";
            ?>

            </div>
        </div>

    </div>

    <div class="itemLeft itemLeftB CW" style="margin-top: 10px">
<!--        <h3>Последние комментарии:</h3>-->
<!--        <div class="commentBlock">-->
<!--            <div style="margin-bottom: 10px">-->
<!--                Sed ut perspiciatis unde omnis iste natus error sit-->
<!--                voluptatem accusantium doloremque laudantium, totam rem aperiam,-->
<!--            </div>-->
<!--            <div class="FL">Кристина</div>-->
<!--            <div class="FR">20.11.2013</div>-->
<!--            <br class="clear">-->
<!--        </div>-->

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

        <div style="width: 650px">
            <h3>Оставить комментарий:</h3>
            <form class="commentForm ajaxForm" data-which="blog" data-urn="<?=$blog->urn;?>" data-type="comment">
                <textarea name="text" placeholder="Ваше сообщение"></textarea>
                <input type="text" name="name" placeholder="Ваше имя">
                <input type="submit" value="Отправить">
                <br class="clear">
            </form>
        </div>
    </div>
</div>

<footer />

<script>
    ajaxForm();
</script>
