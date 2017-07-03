<?php require dirname(__FILE__).'/../goldcut/boot.php';

define('DEBUG_SQL',TRUE);
//define('PRODUCTION_DB_IN_TEST_ENV',TRUE);
define('NOCLEARDB',true);

class Media_Test implements TestCase
{
    public $con = 10;

	function media()
	{


	}
}
?>