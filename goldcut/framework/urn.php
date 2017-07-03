<?php

class URN
{
	private $urn;
	public $entitymeta;
	public $entity;
	public $uuid = null;
	public $listname = null;

	public function __construct($urn_string, $entity=null)
	{
		if (is_object($urn_string))
			$this->urn = (string) $urn_string;
		else
			$this->urn = $urn_string;
		if (!$urn_string) 
			throw new Exception("NULL URN [$urn_string]. INVALID");
		$this->urn = str_replace(':', '-', $this->urn);
		$urna = explode('-', $this->urn);
		if (count($urna) < 2 || count($urna) > 4 || $urna[0] != 'urn') 
			throw new Exception("INVALID URN [{$urn_string}]");
		if (count($urna)>=3 and !is_numeric($urna[2])) 
			throw new Exception("INVALID UUID [{$urn_string}]");
		if ($entity)
		{
			if ($urna[1] != $entity) throw new SecurityException("UNALLOWED ENTITY [{$urn_string}]"); // TODO clean invalid user input for write to log 
		}
		$urna[2] = (int) $urna[2];
		$this->entitymeta = Entity::ref($this->entitymeta());
		$this->entity = $this->E(); 
		$this->uuid = $this->uuid();
		if ($urna[3]) $this->listname = $urna[3];
	}

	function __toString()
	{
		$str = 'urn-'.$this->entity->name;
		if ($this->uuid) $str .= '-'.$this->uuid;
		if ($this->listname) $str .= '-'.$this->listname;
		return $str;
	}
	
	public static function build($entityname, $uuid=null, $list=null)
	{
		$urnstr = "urn-{$entityname}";
		if ($uuid) $urnstr .= "-{$uuid}";
		if ($list) $urnstr .= "-{$list}";
		return new URN($urnstr);
	}

	public static function buildConcrete($entityname, $uuid)
	{
		if (!$uuid) return null;
		return new URN("urn-{$entityname}-{$uuid}");
	}
	
	public function set_list($listname)
	{
		$this->listname = $listname;
	}
	
	public function listmeta()
	{
		return $this->entity->listbyname($this->listname);
	}
	
	public function entitymeta()
	{
		return $this->part(1);
	}

	public function E()
	{
		return Entity::ref($this->part(1));
	}

	/*
	DEPRECATED
	*/
	public function htmlid()
	{
		return str_replace(':', "-", $this->__toString());
	}

	public function uuid()
	{
		if ($uuid = $this->part(2))
			return new UUID($uuid);
		else
			return false;
	}

	public function is_general()
	{
		if ($uuid = $this->part(2))
		{
			if ($uuid)
				return false;
		}
		else
			return true;
	}

	public function is_concrete()
	{
		if ($uuid = $this->part(2))
		{
			if ($uuid)
				return true;
		}
		else
			return false;
	}
	
	public function is_list()
	{
		if ($list = $this->part(3))
		{
			if ($list)
				return true;
		}
		else
			return false;
	}

	public function generalize()
	{
		return new URN($this->entitymeta->urn);
	}

	public function hasUUID()
	{
		if ($this->part(2))
			return true;
		else
			return false;
	}

	// в отличе от uuid возвращает "как есть", не кастя в UUID
	public function resource()
	{
		return $this->part(2);
	}

	private function part($index=1)
	{
		$urna = explode('-', $this->urn);
		return $urna[$index];
	}

	public static function class_of($urn)
	{
		$urna = explode('-', $urn);
		if (count($urna) < 2 || $urna[0] != 'urn') throw new Exception("INVALID URN [{$urn}]");
		$class = ucfirst ( $urna[1] );
		return $class;
	}

	public static function uuid_of($urn)
	{
		$urna = explode('-', $urn);
		if (count($urna) < 2 || $urna[0] != 'urn') throw new Exception("INVALID URN [{$urn}]");
		$indexlast = count($urna)-1;
		println($urna[$indexlast]);
		$uuid = new UUID($urna[$indexlast]);
		if (!($uuid->toInt() > 0)) throw new Exception("URN NOT CONTAIN UUID FOR CLASS [{$class}]. full urn: [{$urn}]");
		return $uuid;
	}

	public function keys()
	{
		return explode('-', $this->urn);
	}
	
	public function resolve($lang=null, $nocache=false)
	{
		if (!$lang) $lang = DEFAULT_LANG;
		if (!$this->is_list())
		{
			return URN::object_by($this->urn, $lang, $nocache);
		}
		else
		{
			$m = new Message();
			$m->action = 'members';
			$m->urn = (string) $this;
			return $m->deliver();
		}
	}

	public static function object_by($urn, $lang=null, $nocache=false)
	{
		if (!$lang) $lang = DEFAULT_LANG;
		$m = new Message();
		$m->action = 'load';
		$m->urn = $urn;
		$m->lang = $lang;
		$m->limit = 1;
		$m->nocache = $nocache;
		return Entity::query($m);
	}

	public static function name_for($object)
	{
		$urna = array();
		$urna[] = 'urn';
		if (get_class($object) == 'DataRow')
			$class = strtolower($object->entitymeta->name);
		else
			$class = strtolower(get_class($object));
		$urna[] = $class;
		$urna[] = $object->uuid();
		return join('-',$urna);
	}
}

?>