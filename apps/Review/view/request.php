<?
extract($this->context);
?>

<div class="toast animated">
    <span style="color:black">
        Thank you<br> Manager will contact you as soon as possible.
    </span>
</div>

<header />

<div id="contact">
    <div class="section">
        <div class="triangle"></div>
        <div class="container">
            <div class="title">
                <h1 class="black">Отзывы</h1>
            </div>
            <div class="row">

                <?
                foreach($review as $c) {
                    $d = date('Y-m-d',$c->created);
                    echo "
                            <div class='span4'>
                                <div class='testimonial'>
                                    {$c->text}
                                    <div class='whopic'>
                                        <div class='arrow'></div>
                                        <img src='/img/girl-512.png' class='centered' alt='client 1'>
                                        <strong class='black'>{$c->name}
                                            <small class='b'>{$d}</small>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            ";
                }?>

            </div>

            <br>
            <br>
            <?include $_SERVER['DOCUMENT_ROOT'].'/apps/Index/view/addReview.php'?>
        </div>
    </div>
</div>
<script>
    leaveComment();
</script>
