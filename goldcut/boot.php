<?php

if (version_compare(PHP_VERSION, '5.3.0') >= 0)
{
	set_include_path(get_include_path().PATH_SEPARATOR.__DIR__.'/framework');
	require "goldcut.php";
	$framework = new Goldcut(__DIR__);
}
else
{
	print "PHP 5.3 OR HIGHER REQUIRED";
}

?>