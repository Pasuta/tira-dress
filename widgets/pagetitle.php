<?php

function widget_pagetitle($options)
{

    extract($options);

    if (!is_array($title) && $title) $title = array($title);
    else if (!is_array($title) && !$title) $title = array();
    if (!$sitetitle) $sitetitle = SITE_NAME;

    array_push($title, $sitetitle);

    echo join(' / ', $title);
}

?>