<?php

class StatusMeta
{
	public $uid;
	public $name;
	public $title;
	public $default;

	function __construct($config)
	{
		foreach ($config as $option => $value)
			$this->$option = $value;
	}
}

?>