<?php

class FieldMeta
{
	public $base;
	public $uid;
	public $name;
	public $title;
	public $type;
	public $units;
	public $default;
	public $virtual;
	public $validator;
	public $options;
	public $noneditable;
	public $disabled;
	public $illustrated;
	public $createDefault;
	public $updateDefault;
	
	public $autoparagraph;
	public $htmlallowed;
	public $nofollow;
	
	public $raw; // non filtered html etc
	
	public $values;

	function __construct($config)
	{
		foreach ($config as $option => $value)
			$this->$option = $value;
	}
	
	public function __toString()
	{
		return 'FIELD '.$this->name." ({$this->type})";
	}
}
?>