<?php

function greetEmailSystem($createduser)
{
    Log::info("REGISTERED [$createduser->urn] with email [$createduser->email]", 'audit');

    $m = new Message();
    $m->action = 'load';
    $m->urn = 'urn-mailtemplate';
    $m->uri = 'onregister';
    $hasmailt = $m->deliver();

    if (!count($hasmailt))
    {
        $to = $createduser->email;
        $nameto = $createduser->name ? $createduser->name : $createduser->email;
        $from = webmaster.'@'.DOMAIN;
        $namefrom = SITE_NAME;
        $subject = "Регистрация на сайте";
        $body = "<h2>{$createduser->email} зарегистрирован</h2>\nПароль в сервисе {$createduser->password}";
        if (!$createduser->active) $message .= "<h3>Дождитесь активации Вашего аккаунта</h3>";
        if ($to)
        {
            Mail::send($from, $namefrom, $to, $nameto, $subject, $body);
        }
        else
        {
            dprintln('No email in created user');
            dprintln($createduser);
        }
    }
    else
    {
        $context = array('user' => $createduser);
        Mail::sendUserTemplatesContext($createduser, 'mailview', 'onregister', $context);
    }

}

$broker = Broker::instance();

$broker->queue_declare ("USEREXTENDED", DURABLE, NO_ACK);
$broker->bind("MANAGERS", "USEREXTENDED", "user.onregister");
$broker->bind_rpc ("USEREXTENDED", "greetEmailSystem");

?>