<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require dirname(__FILE__).'/../../goldcut/boot.php';

$root_login = 'root';
$root_password = ROOT_PASS;
if ($_COOKIE['login']) 
{
	if (md5($root_login.$root_password) == $_COOKIE['login']) 
		$username = 'root';
	else
		die("You have sent a bad cookie.");
}
else
{
	header('Location: /goldcut/admin/aauth.php');
	exit(0);
}

Migrate::full();
print "Done";
?>