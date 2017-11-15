<div class="row">
    <div class="span6 contact-form centered">
        <h3>Оставьте ваш отзыв!</h3>
        <div id="successSend" class="alert alert-success invisible">
            <strong>Well done!</strong>Your message has been sent.</div>
        <div id="errorSend" class="alert alert-error invisible">There was an error.</div>
        <form id="contact-form" class="commentForm ajaxForm" data-type="question">
            <div class="control-group">
                <div class="controls">
                    <input class="span6 commentName" type="text" id="name" name="name" placeholder="* Ваше имя..." />
                    <div class="error left-align" id="err-name">Please enter name.</div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="span6 commentEmail" type="email" name="email" id="email" placeholder="* Ваш email..." />
                    <div class="error left-align" id="err-email">Please enter valid email adress.</div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <textarea class="span6 commentComment" name="text" id="comment" placeholder="* Отзыв..."></textarea>
                    <div class="error left-align" id="err-comment">Please enter your comment.</div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button id="send-mail" class="message-btn commentButton">Отправить ваше сообщение</button>
                </div>
            </div>
            <div id="questionDiv" class="errorMsg" style="color:black"></div>
        </form>
    </div>

</div>