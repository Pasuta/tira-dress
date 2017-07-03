<?
extract($this->context);
?>

<header />

<div class="toast animated">
    <span style="color:black">
        Thank you<br> Manager will contact you as soon as possible.
    </span>
</div>

<!-- Portfolio section start -->
<div class="section secondary-section" id="portfolio">
    <div class="triangle"></div>
    <div class="container">
        <div class="title">
            <h1>Заполни анкету и получи скидку 5%!</h1>
            <form style="margin-top: 10px" class='commentForm bonus contact-form' method="POST" action="/bonus/handle">

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12" name="name" placeholder="Ваше имя">
                        <div class="error left-align" id="err-email">Имя</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12" name="weddingdate" placeholder="Дата свадьбы (25.12.2017)">
                        <div class="error left-align" id="err-email">Дата свадьбы</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="dresscolor" placeholder="Цвет (белое, айвори, пудровое)">
                        <div class="error left-align" id="err-email">Цвет платья</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="siluet" placeholder="Силует (узкое, средней пышности, очень пышное, прямое, другое)">
                        <div class="error left-align" id="err-email">Силует платья</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="dekor" placeholder="Декор (камни, бисер, кружево и т.д.)">
                        <div class="error left-align" id="err-email">Декор платья</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="size" placeholder="Размер (от 38 до 58)">
                        <div class="error left-align" id="err-email">Размер</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="fata" placeholder="Фата (длинная, короткая, средняя, не будет)">
                        <div class="error left-align" id="err-email">Фата</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="ready" placeholder="Пошив, готовая модель">
                        <div class="error left-align" id="err-email">Готовое платье или пошив</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="text" class="span12"  name="phone" placeholder="+380 50 55 444 33">
                        <div class="error left-align" id="err-email">Ваш телефон</div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="email" class="span12"  name="email" placeholder="john@gmail.com">
                        <div class="error left-align" id="err-email">Ваш email</div>
                    </div>
                </div>

                <input type='submit' value='Отправить' style="padding: 5px 20px;background: #b90077;color: white; border:none;">
            </form>
        </div>
    </div>
</div>

