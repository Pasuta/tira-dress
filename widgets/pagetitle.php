<?php

function widget_pagetitle($options)
{

    extract($options);

    if (!is_array($title) && $title) $title = array($title);
    else if (!is_array($title) && !$title) $title = array();
    if (!$sitetitle) $sitetitle = SITE_NAME;
    $ceo = "Свадебные платья в Киеве пошив и продажа. Приятные цены. каталог с фото. Салон \"ТИРА Шоурум\" в центре.";

    array_push($title, $ceo);

    echo join(' / ', $title);
}

?>