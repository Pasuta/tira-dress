<?php

// TODO xml logs
// TODO xmpp logs

define('LOGERROR', 1);
define('LOGINFO',  2);
define('LOGDEBUG', 3);

class Log
{

	public static $monitor_sql = true;
	private static $instance;
	private function __construct() {}
	private static $buffer = '';

	public static function instance()
	{
		if (!self::$instance) { self::$instance = new Log(); self::$instance->init(); } return self::$instance;
	}

	private function init()
	{
		// external logging connect
	}

	public static function buffer($message)
	{
		// sql из предыдущего в теста в теущем?
		//self::$buffer .= $message."\n";
		self::$buffer = $message."\n";
	}

	public static function buffer_clear()
	{
		$r = self::$buffer;
		self::$buffer = '';
		return $r;
	}


	/**
	reason - error
	*/
	private static function log($l, $NS, $level = LOGINFO)
	{
		if (ENV !== LOG_ENV) return false;
		if (!$NS)
		{
			$trace = debug_backtrace();
			$callerClass = $trace[2]['class'];
			$callerFunction = $trace[2]['function']; 
			//$trace[0]['file']
			//$trace[0]['line']
			$NS = 'trace';
			$l .= "\t({$callerClass}::{$callerFunction})";
		}
		
		if (ENV == 'DEVELOPMENT' && SCREENLOG === true && $NS != 'sql' && $NS != 'test' && $NS != 'mail'  && $NS != 'list' && $NS != 'listdb'  && $NS != 'mysql' && $NS != 'main' && !strstr($NS,'mail')) println($l,1,TERM_GRAY);
		
		$date = date("d/m H:i:s");
		
		if ($level == LOGERROR) $levelstr = '!';
		if ($level == LOGINFO) $levelstr = ' ';
		if ($level == LOGDEBUG) $levelstr = '.';
		$filename = BASE_DIR.'/log/'.HOST.'-'.$NS.'.log';
		$file = fopen($filename, 'a+');
		if (flock($file, LOCK_EX))
		{
			fwrite($file, "[{$levelstr}] $date\t".$l.PHP_EOL); // "\n"
			flock($file, LOCK_UN); // release the lock
		}
		else
		{
			println("Couldn't get the WRITE EX lock for $filename file!", 1, TERM_RED);
		}
		fclose($file);
	}
	
	public static function info($l, $NS)
	{
		self::log($l, $NS, LOGINFO);
	}

	public static function error($l, $NS)
	{
		self::log($l, $NS, LOGERROR);
	}

	public static function debug($l, $NS)
	{
		self::log($l, $NS, LOGDEBUG);
	}
	
}

?>