<?php

interface TestCase { }

define('TEST_ENV', true);

//require BASE_DIR.'/lib/json/JSON.php';

function pendingTest()
{
	throw new PendingException();
}

function test_all_at_exit()
{
	TestRunner::run();
}

register_shutdown_function('test_all_at_exit');
//register_shutdown_function(array($this,"__destruct"));

?>