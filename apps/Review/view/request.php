<?
extract($this->context);
?>

<header />

<div class="toast animated">
    <span>
        Thank you<br> Manager will contact you as soon as possible.
    </span>
</div>

<div class='FW'>
    <div class='CW'>
        <?
        foreach($review as $r){
            $date = date('H:i d.m.y',$r->created);
            echo "
                    <div class='contentmain bloglist review'>
                        <div class='FL bloglistitem reviewL'>
                            <div class='FL bloglistitemDiv reviewInfo'>
                                <span>$r->name</span>
                                <div>$date</div>
                            </div>
                            <br class='clear'>
                        </div>
                        <div class='FL bloglistitemL reviewTxt'>
                            <div> $r->text </div>
                        </div>
                        <br class='clear'>
                    </div>
                 ";
        }
        ?>

        <div class='CW' style='margin-top: 10px'>
            <div class='FL itemLeft itemLeftB'>
                <h3>Оставить отзыв:</h3>
                <form class='commentForm ajaxForm' data-type="review">
                    <textarea name='text' placeholder='Ваше сообщение'></textarea>
                    <input type='text' name='name' placeholder='Ваше имя'>
                    <input type='submit' value='Отправить'>
                    <br class='clear'>
                </form>
            </div>

            <div class='FR itemRight itemRightB'>
                <div class='contactText'>
                    Мечтать иметь «правильное» платье, как у принцесс или голливудских звезд, —
                    хорошо, но надо отдавать себе отчет, что это абстракция, прекрасная только на картинке.
                    Несовершенство — вот главный элемент идеального свадебного образа. Потому, что это спутник
                    индивидуальности.
                </div>
                <div style='text-align: right'>Vera Wang</div>
            </div>

            <br class='clear'>
        </div>

    </div>
</div>

<footer />

<script>
    ajaxForm();
</script>

