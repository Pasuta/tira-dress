<?
if($_GET['v'] != 1) {?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset=utf-8>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><pagetitle /></title>
        <metadata />
        <!-- Load Roboto font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        <!-- Load css styles -->
        <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="/public/css/bootstrap-responsive.css" />
        <link rel="stylesheet" type="text/css" href="/public/css/style.css" />
        <link rel="stylesheet" type="text/css" href="/public/css/pluton.css" />
        <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" href="/public/css/tira-ie7.css"/>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="/public/css/jquery.cslider.css" />
        <link rel="stylesheet" type="text/css" href="public/css/jquery.bxslider.css" />
        <link rel="stylesheet" type="text/css" href="public/css/animate.css" />
        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/public/images/ico/apple-touch-icon-144.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/public/images/ico/apple-touch-icon-114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/public/images/apple-touch-icon-72.png">
        <link rel="apple-touch-icon-precomposed" href="/public/images/ico/apple-touch-icon-57.png">
        <link rel="shortcut icon" href="/public/images/ico/favicon.ico">
        <script type="text/javascript" src="/goldcut/js/gcdom.js?3"></script>
        <script type="text/javascript" src="/js/site.js?11"></script>
        <script type="text/javascript" src="/lib/js/when/when.js?12"></script>
    </head>

    <body>
        <?echo $HTML;?>
        <!-- Footer section start -->
        <div class="footer">
            <p>&copy; 2017 Tira-dress!</p>
        </div>
        <!-- Footer section end -->
        <!-- ScrollUp button start -->
        <div class="scrollup">
            <a href="#">
                <i class="icon-up-open"></i>
            </a>
        </div>
        <!-- ScrollUp button end -->
        <!-- Include javascript -->
        <script src="/public/js/jquery.js"></script>
        <script type="text/javascript" src="/public/js/jquery.mixitup.js"></script>
        <script type="text/javascript" src="/public/js/bootstrap.js"></script>
        <script type="text/javascript" src="/public/js/modernizr.custom.js"></script>
        <script type="text/javascript" src="/public/js/jquery.bxslider.js"></script>
        <script type="text/javascript" src="/public/js/jquery.cslider.js"></script>
        <script type="text/javascript" src="/public/js/jquery.placeholder.js"></script>
        <script type="text/javascript" src="/public/js/jquery.inview.js"></script>
        <!-- Load google maps api and call initializeMap function defined in app.js -->
        <!-- css3-mediaqueries.js for IE8 or older -->
        <!--[if lt IE 9]>
        <script src="/public/js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript" src="/public/js/app.js"></script>

    </body>
</html>
<?
} else {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=1024"/>
    <meta name='wmail-verification' content='1ace10fd3407bd492337d68ea0f2d29f' />
    <title><pagetitle /></title>

    <metadata />

    <link rel="stylesheet" href="/css/site.css?11" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="/css/basic.css?11" type="text/css" media="screen" charset="utf-8">

    <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Serif' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="/css/960_12_col.css?11" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="/css/style.css?11" type="text/css" media="screen" charset="utf-8">


    <script type="text/javascript" src="/lib/js/mootools-compat/mootools-core.js"></script>
    <script type="text/javascript" src="/js/settings.js?11"></script>
    <script type="text/javascript" src="/goldcut/js/gcdom.js?3"></script>
    <script type="text/javascript" src="/goldcut/js/stacktrace.js?15"></script>
<!--    <script type="text/javascript" src="/goldcut/js/track.js?15"></script>-->
    <script type="text/javascript" src="/js/site.js?11"></script>
    <script type="text/javascript" src="/js/jquery/dist/jquery.min.js?11"></script>
    <script type="text/javascript" src="/js/youtube/tubular.js?11"></script>
    <script type="text/javascript" src="/lib/js/when/when.js?12"></script>

    <script type="text/javascript" src="http://vk.com/js/api/share.js?11" charset="windows-1251"></script>
</head>

<body>

    <div id="wrapper"></div>

    <div id="body">
        <?php echo $HTML;?>

        <div class="leaveMessage">Оставить нам сообщение</div>
        <div class="leaveMessageForm hide">
            <h3>Оставить нам сообщение</h3>
            <form id="questionForm">
                <input name="name" placeholder="Имя" required>
                <input name="phone" placeholder="Телефон" required>
                <input name="email" placeholder="Email" required type="email">
                <textarea name="text" placeholder="Ваше сообщение" rows="10" cols="45" ></textarea>
                <button>Отправить</button>
                <button style="float: right" id="closeForm">Закрыть</button>
            </form>
        </div>
    </div>

</body>

<script>

    (function() {

        var leaveMessage = document.getElementsByClassName('leaveMessage')[0];
        var leaveMessageForm = document.getElementsByClassName('leaveMessageForm')[0];
        var closeFormBtn = document.getElementById('closeForm');

        Event.add(leaveMessage, 'click', openForm);
        Event.add(closeFormBtn, 'click', closeForm);

        Event.add(document.getElementById('questionForm'), 'submit', function(e) {
            e.preventDefault();
            closeForm();
            ajax('/ajax/question', function(){}, {}, 'POST', collectFormData(this));
        });

        function openForm () {
            leaveMessageForm.classList.remove('hide');
            leaveMessage.classList.add('hide');
        }

        function closeForm () {
            leaveMessageForm.classList.add('hide');
            leaveMessage.classList.remove('hide');
        }

    })(); // form from left

    function collectFormData(form){
        var m = {};

        var input = form.querySelectorAll('input');
        for (var i = 0; i < input.length; i++) {
            var obj = input[i];
            var relation = obj.getAttribute('data-relation');
            if(obj.getAttribute('type') == 'radio'){
                if(obj.checked) m[obj.name] = obj.value;
                continue;
            }
            if(!obj.value) continue;
            relation ? m[relation] += '.' + obj.value : m[obj.name] = obj.value;
        }

        var textarea = form.querySelectorAll('textarea');
        for (var j = 0; j < textarea.length; j++) {
            var obj2 = textarea[j];
            if(!obj2.value) continue;
            m[obj2.name] = obj2.value;
        }

        return m;
    }

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-49948145-2', 'auto');
    ga('send', 'pageview');

    var ids = ['rQbdA8IhEpo', '7kjjPdUAYLo'];

    $().ready(function() {
        $('#wrapper').tubular({videoId: ids[randomInteger(0, ids.length - 1)] } );
    });

    function randomInteger(min, max) {
        var rand = min + Math.random() * (max - min)
        rand = Math.round(rand);
        return rand;
    }

</script>
<?
}
?>
