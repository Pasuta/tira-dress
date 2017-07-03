<?php
require "boot.php";

try {
    $m = new Message($_POST);
    Log::info($m,"ABC");
    $r = $m->deliver();
    Log::info($r,"ABC");
    print $r;
}
catch (Exception $e)
{
    header("HTTP/1.0 500 Ajax soft error");
    $m = new Message();
    $m->status = 500;
    $m->text = json_decode($e->getMessage());
    print $m;
}

?>