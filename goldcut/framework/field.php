<?php

class Field
{
	private $list;
	private $listt;
	private static $instance;

	private function __construct() {}

	private function init()
	{
		$this->list = $GLOBALS['CONFIG']['FIELD'];
		foreach ($this->list as $uid => $f)
			$this->listt[$f->name] = $f;
	}

	public static function manager()
	{
		if (!self::$instance) { self::$instance = new Field(); self::$instance->init(); } return self::$instance;
	}

	public static function exists($uid) 
	{
		$f = Field::ref($uid, true);
		if ($f) return true;
		else return false;
	}
	
	public static function ref($uid, $nothrow = false)
	{
		if (is_int($uid))
		{
			$f = Field::manager()->list[$uid];
		}
		else if (is_array($uid))
		{
			$usedAs = key($uid);
			$baseName = $uid[$usedAs];
			$f = Field::manager()->listt[$baseName];
		}
		else
		{
			$f = Field::manager()->listt[$uid];
		}
		if ($f)
		{
			return $f;
		}
		else 
		{
			if ($nothrow)
				return false;
			else
				throw new Exception("Field $uid not exists");
		}
	}

	public static function id($uid, $nothrow = false)
	{
		return Field::manager()->ref($uid, $nothrow);
	}

	static function each()
	{
		return Field::manager()->list;
	}
	
	public static function sqlwrap($F, $value)
	{
		//println("{$F->name} {$F->type} ");
		if ($value == '' or $value == 'NULL' or is_null($value))
		{
			$wrapped = "NULL";
		}
		else
		{
			if ($F->type == 'integer' or $F->type == 'timestamp')
			{
				$wrapped = (integer) $value;
			}
			elseif ($F->type == 'float')
			{
				$value = str_replace(',', '.', $value);
				$precision = $F->precision ? $F->precision : 2;
				$wrapped = number_format(floatval($value), $precision, '.', '');
			}
			else
			{
				//$wrapped = "'".Security::mysql_escape((string)$value)."'";
				$safe = $value;				
				$wrapped = "'".$safe."'";
			}
		}
		return $wrapped;
	}
	
	public static function formatValue($F, $value)
	{
		if ($F->type == 'integer')
			return (integer) $value;
		if ($F->type == 'float')
		{
			$value = str_replace(',', '.', $value);
			$precision = $F->precision ? $F->precision : 2;
			$wrapped = number_format(floatval($value), $precision, '.', '');
			return (float) $wrapped;
		}
		if ($F->type == 'string')
			return (string) $value;			
		if ($F->type == 'text')
			return (string) $value;			
		if ($F->type == 'set')
			return (string) $value;			
		if ($F->type == 'richtext')
			return (string) $value;			
		if ($F->type == 'option')
		{
			if ($value === 1 or $value === "1" or $value === true or $value === 'Y')
				return 'Да';
			else
				return 'Нет';
		}
	}

}

?>