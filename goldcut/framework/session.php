<?php
/**
bool setcookie ( string $name [, string $value [, int $expire = 0 [, string $path [, string $domain [, bool $secure = false [, bool $httponly = false ]]]]]] )
*/

define('COOKIEEXPIRE', 3600*24*7);

class Session
{

	private static $instance;
	private static $SID;
	public $vars = array();

	public static function manager()
	{
		if (!self::$instance) { self::$instance = new Session(); self::$instance->start(); } return self::$instance;
	}

	private function start()
	{
	}

	public static function ID()
	{
		return false;
	}

	public static function put($k,$v)
	{
		self::manager();
		$value = serialize($v);
		setcookie($k, $value, time() + COOKIEEXPIRE, '/');
		self::$instance->vars[$k] = $v;
	}

	public static function get($k)
	{
		self::manager();
		if ($v = self::$instance->vars[$k]) return $v;
		if ($_COOKIE[$k]) return unserialize($_COOKIE[$k]);
		return false;
	}

	public static function pop($k)
	{
		self::manager();
		$v = unserialize($_COOKIE[$k]);
		if ($v) setcookie($k, $value, time() - 1, '/' );
		return $v;
	}

	public static function destroy()
	{
		self::manager();
		foreach($_COOKIE as $k => $v)
		{
			setcookie($k, '', time() - 1, '/' );
		}
	}

	public static function debug()
	{
		print "<pre>";
		print_r($_COOKIE);
		print "</pre>";
	}

}

?>