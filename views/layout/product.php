<!DOCTYPE html>
<html>
<head lang="ru">
    <meta charset=utf-8>
    <title><pagetitle /></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <metadata />

    <link rel="stylesheet" href="/public/css/footer.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="/public/swiper/dist/css/swiper.min.css">

    <link href="/public/product/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />

    <link href="/public/product/css/style.css" rel="stylesheet" type="text/css" media="all" />

    <link href="/public/product/css/style4.css" rel="stylesheet" type="text/css" media="all" />

    <script src="/public/product/js/jquery.min.js"></script>

    <script src="/public/product/js/jstarbox.js"></script>
    <link rel="stylesheet" href="/public/product/css/jstarbox.css" type="text/css" media="screen" />
    <script>
        jQuery(function() {
            jQuery('.starbox').each(function() {
                var starbox = jQuery(this);
                starbox.starbox({
                    average: starbox.attr('data-start-value'),
                    changeable: starbox.hasClass('unchangeable') ? false : starbox.hasClass('clickonce') ? 'once' : true,
                    ghosting: starbox.hasClass('ghosting'),
                    autoUpdateAverage: starbox.hasClass('autoupdate'),
                    buttons: starbox.hasClass('smooth') ? false : starbox.attr('data-button-count') || 5,
                    stars: starbox.attr('data-star-count') || 5
                }).bind('starbox-value-changed', function(event, value) {
                    if(starbox.hasClass('random')) {
                        var val = Math.random();
                        starbox.next().text(' '+val);
                        return val;
                    }
                })
            });
        });
    </script>
    <link href="/public/product/css/form.css" rel="stylesheet" type="text/css" media="all" />
</head>
    <body>
        <?echo $HTML;?>

        <?include 'footer.php'?>


        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

        <script src="/public/product/js/imagezoom.js"></script>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script defer src="/public/product/js/jquery.flexslider.js"></script>
        <link rel="stylesheet" href="/public/product/css/flexslider.css" type="text/css" media="screen" />

        <script>
            // Can also be used with $(document).ready()
            $(window).load(function() {
                $('.flexslider').flexslider({
                    animation: "slide",
                    controlNav: "thumbnails"
                });
            });
        </script>

        <script src="/public/product/js/simpleCart.min.js"> </script>
        <!-- slide -->
        <script src="/public/product/js/bootstrap.min.js"></script>
    </body>
</html>
