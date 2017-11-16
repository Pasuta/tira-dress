<?php

function widget_header($array) {

    $sortBlock = 'display:none';
    if ($array[2] == 'catalog' && $array[3] != 'hide') {
        $sortBlock = '';
        $price = $_GET['price'] == 'low' ? "high" : "low";
        $priceI = $_GET['price'] == 'low' ? "&uparrow;" : "&darr;";
        $created = $_GET['created'] == 'low' ? "high" : "low";
        $createdI = $_GET['created'] == 'low' ? "&uparrow;" : "&darr;";
        $rank = $_GET['rank'] == 'low' ? "high" : "low";
        $rankI = $_GET['rank'] == 'low' ? "&uparrow;" : "&darr;";
    }

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-collection';
    $collection = $m->deliver();

    $collections = '';
    foreach ($collection as $item) {
        $collections .= "<li><a href='/catalog/collection/$item->uri'>&nbsp;&nbsp; $item->title</a></li>";
    }

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-category';
    $category = $m->deliver();

    $categories = '';
    foreach ($category as $item) {
        $categories .= "<li><a href='/catalog/category/$item->uri'>&nbsp;&nbsp; $item->title</a></li>";
    }

    echo "<div class='navbar'>
            <div class='navbar-inner'>
                <div class='container'>
                    <a href='/' class='brand'>
                        <img src='/public/images/logo/TIRA_Bridal_logo.svg' alt='Logo' />
                        <!-- This is website logo -->
                    </a>
                    <!-- Navigation button, visible on small resolution -->
                    <button type='button' class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
                        <i class='icon-menu'></i>
                    </button>
                    <!-- Main navigation -->
                    <div class='nav-collapse collapse pull-right' id='nav'>
                        <ul class='nav' id='top-navigation'>
                            <li class='active'><a href='/'>Главная</a></li>
                            <li><a href='/#service'>Услуги</a></li>
                            <li>
                                <a href='/catalog' aria-haspopup='true'>Каталог</a>
                                <ul class='uli'>
                                    <hr>
                                    <li>Категории</a></li>
                                    $categories
                                    <hr>
                                    <li>Колекции</a></li>
                                    $collections
                                </ul>
                            </li>
                            
                            <li><a href='/#about'>О нас</a></li>
                            <li><a href='/review'>Отзывы</a></li>
                            <li><a href='/#contact'>Контакты</a></li>
                            <li><a href='/blog'>Блог</a></li>
                            <li><a href='/bonus'>Бонус</a></li>
                        </ul>
                    </div>
                    <div class='nav-collapse collapse pull-right' style='{$sortBlock}'>
                        <ul class='nav' id='top-navigation'>
                            <li><a href='/catalog'>Сортировать:</a></li>
                            <li><a href='/catalog?price={$price}'>Цена {$priceI}</a></li>
                            <li><a href='/catalog?created={$created}'>Дата добавления {$createdI}</a></li>
                            <li><a href='/catalog?rank={$rank}'>Популярность {$rankI}</a></li>
                        </ul>
                    </div>
                    <!-- End main navigation -->
                </div>
            </div>
        </div>";
}

?>
