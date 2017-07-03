<?php

class Message
{

	private $message;

	function keys()
	{
		return array_keys($this->message);
	}
	
	function keysNonSystem()
	{
		return array_diff(array_keys($this->message),array('id','urn','action'));
	}
	
	function values()
	{
		return array_values($this->message);
	}

	/**
	required params check (as ? in values or split from message)
	*/
	static function named($queryname)
	{
		$m = $GLOBALS['CONFIG']['QUERIES'][$queryname];
		return $m;
	}
	
	public function deliver() // $tx
	{
		//if (DEBUG_MESSAGE === TRUE && OPTIONS::get('pause_DEBUG_SQL') !== true) println($this->message, 1, TERM_VIOLET);
		try
		{
			// GET EMANAGER CLASS
			if ($this->urn->entitymeta)
			{
				$Class = $this->urn->entitymeta->getClass();
			}
			else
			{
				throw new Exception("Cant deliver message " . json_encode($this->message));
			}
			foreach ($this->message as $k => $v)
			{
				if ($v === '?') throw new Exception("Key `$k` ($v) is required in {$this}");
			}
			$response = $Class->recieve($this);
			
			// TODO if responce error, Transaction ROLLBACK
			// TODO use warnings for errors with possibility to continue flow 
			return $response;
		} 
		catch (Exception $e) 
		{
			// if (ENV === 'DEVELOPMENT') println($e->getMessage(), 1, TERM_RED);
			throw $e;
		}
	}

	public function __get($field)
	{
		if (!$field) return null;

		if (isset($this->message[$field]))
		{
			// sub message
			if (is_array($this->message[$field]))
				return new Message($this->message[$field]);
			else
			{
				// STRING
				if (is_string($this->message[$field]))
				{
					// URN
					if (substr($this->message[$field],0,3) == 'urn')
					{
						$urn = new URN($this->message[$field]);
						return $urn;
						/*
						if ($field == "urn")
							return $urn;
						else
							return $urn->resolve();
						 */
					}
					// TODO Delegate transform to Field
					else
						return $this->message[$field];
				}
				// OBJECT
				else
					return $this->message[$field];
			}
		}
		else
			return null;
	}

	public function __set($field, $value)
	{
		if ($value instanceof URN)
			$value = (string) $value;
		$this->message[$field] = $value;
	}

	function merge($m, $key=null)
	{
		if ($key)
			$m = array($key => $m);
		if (is_array($m))
			$this->message = array_merge($this->message, $m); // REPLACING BEHAVIOR
		else
			throw new Exception("Message merge with non array");
	}

	static function check($m)
	{
		if (is_string($m)) throw new Exception("expexted message is string");
		if (is_array($m)) throw new Exception("expexted message is array");
		if (get_class($m) != 'Message') throw new Exception("expexted message is object and not of Message class");
		if (!$m->urn) throw new Exception("msaage without URN".$m);
	}

	function clear($field)
	{
		unset($this->message[$field]);
	}

	function __construct($m=null)
	{
		if (!$m)
			$this->message = array();
		else
			$this->message = $m;
		if ( is_array($this->message) )
			$this->checkArray();
		elseif ( is_json($this->message) )
			$this->parseJson();
		elseif ( is_xml($this->message) )
			$this->parseXML();
		else
			throw new Exception("Message::construct() Unknown message format -> {$m}");
		$this->checkArray();
	}

	function checkArray()
	{
		foreach ($this->message as $k=>$v)
		{
			if ($v === null) unset($this->message[$k]);
		}
	}

	private function parseJson()
	{
		$tm = $this->message;
		$this->message = json_decode($this->message, true);
		if (!$this->message) throw new Exception("MESSAGE IS JSON WITH ERRORS [{$tm}]");
	}

	function get()
	{
		return $this->message;
	}

	function __toString()
	{
		$message_json = json_encode($this->message);
		return $message_json;
	}

	function toArray()
	{
		return $this->message;
	}

	public function __isset($name)
	{
		return isset($this->message[$name]);
	}

	public function exists($field)
	{
		if (isset($this->message[$field]))
			return true;
		else
			return false;
	}

	public function __unset($name)
	{
		unset($this->message[$name]);
	}
}

?>