<?
extract($this->context);
?>

<header />

<div class="toast animated">
    <span>
        Thank you<br> Manager will contact you as soon as possible.
    </span>
</div>

<div class="FW">
    <div class='CW' style='margin-top: 10px'>
        <div class='FL itemLeft itemLeftB'>
            <br>
            <h3>Заполните эту анкету и получите скидку в размере 5% !</h3><br>
            <form class='commentForm bonus ajaxForm' method="POST" action="/bonus/handle">
                <label>Имя</label>
                <input type="text" name="name" placeholder="Ваше имя">
                <br class="clear">
                <br class="clear">

                <label>Дата свадьбы</label>
                <input type="text" name="weddingdate" placeholder="Дата свадьбы (25.12.2015)">
                <br class="clear">
                <br class="clear">

                <label>Цвет платья</label>
                <input type="text" name="dresscolor" placeholder="Цвет (белое, айвори, пудровое)">
                <br class="clear">
                <br class="clear">

                <label>Силует платья</label>
                <input type="text" name="siluet" placeholder="Силует (узкое, средней пышности, очень пышное, прямое, другое)">
                <br class="clear">
                <br class="clear">

                <label>Декор платья</label>
                <input type="text" name="dekor" placeholder="Декор (камни, бисер, кружево и т.д.)">
                <br class="clear">
                <br class="clear">

                <label>Размер</label>
                <input type="text" name="size" placeholder="Размер (от 38 до 58)">
                <br class="clear">
                <br class="clear">

                <label>Фата</label>
                <input type="text" name="fata" placeholder="Фата (длинная, короткая, средняя, не будет)">
                <br class="clear">
                <br class="clear">

                <label>Готовое платье или пошив</label>
                <input type="text" name="ready" placeholder="Пошив, готовая модель">
                <br class="clear">
                <br class="clear">

                <label>Ваш телефон</label>
                <input type="text" name="phone" placeholder="+380 50 55 444 33">
                <br class="clear">
                <br class="clear">

                <label>Ваш email</label>
                <input type="text" name="email" placeholder="john@gmail.com">
                <br class="clear">
                <br class="clear">

                <input type='submit' value='Отправить'>
                <br class='clear'>
                <br class='clear'>
            </form>
        </div>

        <div class='FR itemRight itemRightB'>
            <div class='contactText'>
               &nbsp;
            </div>
            <img src="/img/skidka5.png">
        </div>

        <br class='clear'>
    </div>



</div>

<footer />