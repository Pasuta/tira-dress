<?php
/**
// TODO instead if null  Blank object (has onw @no image@ image, dataset->illustration->image - cant cact as single datarow, cos empty)
CACHE datarow = rowhash.
KEY = entity:id
*/
class DataRow implements Countable
{
	public $entitymeta;
	private $link_to_dataset;
	private $datarow = array();
	private $dblink;
	public $extended = false;

	function __construct(&$rowhash, $dataset)
	{
		$this->link_to_dataset = $dataset;
		$this->entitymeta = $this->link_to_dataset->entitymeta;
		$E = $this->entitymeta;
		
		$this->datarow = $rowhash;
		$this->datarow['id'] = (integer) $this->datarow['id'];
		
		if ($this->datarow['_properties']) 
		{
			$this->datarow['_properties'] = json_decode($this->datarow['_properties'], true);
			$this->datarow = array_merge($this->datarow, $this->datarow['_properties']);			
			unset($this->datarow['_properties']);
		}

		/**
		TODO preload all dataset at once
		*/
		if ($this->datarow['_variators']) 
		{
			$this->datarow['_variators'] = json_decode($this->datarow['_variators'], true);

			$m = new Message();
			$m->action = 'load';
			$m->urn = 'urn-variation';
			$na = array();
			foreach ($this->datarow['_variators'] as $k => $var)
			{
				if (is_array($var) && count($var)) 
					$na = array_merge($na, $var);
				else if (is_array($var) && !count($var)) 
					$xa = 'skip';
				else
					$na = array_merge($na, array($var));
			}
			$m->id = $na;
			
			if (count($na))
				$variations = $m->deliver();

			foreach ($variations as $variation)
			{
				$variator = $variation->variator;
				if ($variator->multiple)
					$this->datarow[$variator->uri][] = $variation;
				else
					$this->datarow[$variator->uri] = $variation;
			}

			$this->datarow['__variators'] = $this->datarow['_variators'];
			unset($this->datarow['_variators']);
			$this->extended = true;
		}
		
		/**
		ENTITY DECORATORS (TODO as plugins)
		*/
		if ($E->class == 'Photo')
		{
			Photo::extendFields($this->datarow, $this->entitymeta);
		}
		
		if ($E->class == 'Video')
		{
			Video::extendFields($this->datarow, $this->entitymeta);
		}
		
		if ($E->class == 'File')
		{
			File::extendFields($this->datarow, $this->entitymeta);
		}
	}
	
	public function count()
	{
		if ($this->datarow['id'])
			return 1;
		else
			return 0;
	}
	
	public function is($status)
	{
		if ($this->datarow[$status])
			return true;
		else
			return false;
	}

	public function is_translated($lang_code)
	{
		$fn = 'translated_'.$lang_code;
		if ($this->datarow[$fn] == 1)
			return true;
		else
			return false;
	}

	public function urn()
	{
		return URN::name_for($this);
	}

	public function uuid()
	{
		return new UUID($this->datarow['id']);
	}

	public function __call($name, $arguments)
	{
		$plugin = Plugin::manager()->get($this, $name);
		return call_user_func_array(array($plugin, $name), $arguments);
	}

	public function __get($field_name)
	{
		/**
		TODO if ->name not in [datarow] or [->relations] 
			if not in [plugins] (Managed or Entity) (CACHE LIST! or will slow next level fallback request) 
				then try local [->id, entity, lang, parent, url etc]
		*/ 
		$entitymeta = $this->link_to_dataset->entitymeta;
		if ($entitymeta)
		{
			$emtype = $entitymeta->ftype($field_name); // any variator field is general field too
			//println($field_name.' is '.$emtype,1,TERM_RED);
		}
		else
		{
			if (ENV != 'PRODUCTION')
			{
				var_dump($this->link_to_dataset);
				var_dump($this->datarow);
				die('ERROR. NO ->link_to_dataset->entitymeta');
			}
			else
				throw Exception('DataRow __get NO ->link_to_dataset->entitymeta');
		}
		
		$Ename = $this->link_to_dataset->entitymeta->name;

		if ($field_name == 'id')
			return $this->datarow['id'];
		
		if ($field_name == 'entity')
			return $this->entitymeta;
		
		if ($field_name == 'lang')
			return $this->link_to_dataset->query->lang;

		if ($field_name == 'parent')
		{
			if ($parentid = $this->datarow['_parent'])
			{
				if ($parentid == 0 or $parentid == '0') return null;
				$m = new Message();
				$m->action = 'load';
				$m->urn = "urn-{$Ename}-{$parentid}";
				$m->id = $parentid;
				$parent = $m->deliver();
				return $parent;
			}
			else
				return null;
		}
		
		/**
		TODO Children
		if ($field_name == 'children')
		*/
		
		if ($field_name == 'urn')
		{
			$uuid = new UUID($this->datarow['id']);
			return new URN("urn-".$Ename."-".$uuid);
		}

		if ($field_name == 'last')
		{
			return $this->datarow['last'];
		}

		if ($field_name == 'first')
		{
			return $this->datarow[0];
		}

		if ($emtype == 'status')
		{
			$st = (integer) $this->datarow[$field_name];
			if ($st == 1) return true;
			elseif ($st == 0 or $st == -1) return false;
			else throw new Exception("Status [$field_name] code is greater then 1 - [{$st}]");
		}
		
		// BT
		if ($emtype == 'belongs_to')
		{
			if ($this->datarow["{$field_name}_id"] == 0) return null;
			$curHoId = $this->datarow["{$field_name}_id"];
			
			//printH('HO');
			//printlnd($this->link_to_dataset->query->chain);			
			if (count($this->link_to_dataset) > 1)
			{
				// RETURN PRELOADED & CACHED
				if ($this->link_to_dataset->preloads[$field_name] == true && $curHoId instanceof DataRow)
				{
					return $curHoId;	
				}
				// !ПОВТОРНЫЙ ВЫЗОВ ТОГО ЖЕ DS->DR, DS->DR ДО EACH NEXT
				else if ($this->link_to_dataset->preloads[$field_name] == true && is_numeric($curHoId))					
				{
					// we have cur but it is stale
					return $this->link_to_dataset->byURN($this->urn)->$field_name;
				}
				
				/**
				PRELOAD ALL INCLUDED DATASET ON FIRST REQUEST
				*/
				$this->link_to_dataset->preloads[$field_name] = true;
				
				$hoids = $this->link_to_dataset->getColumn($field_name.'_id');
				$m = new Message();
				$m->action = 'load';
				$m->urn = "urn-{$field_name}";
				$m->id = array_unique(array_values($hoids));
				$m->subrequest_from = $this->datarow["id"];
				$m->chain = 1;				
				$hos = $m->deliver();
				//printlnd($m);				
				
				foreach ($hoids as $hurn => $hoid)
				{
					$ho = $hos->byId($hoid);
					if ($ho->id == $curHoId && !$found) 
					{
						$hocurrequest = $ho;
						$found = true;
					}
					$this->link_to_dataset->patch($hurn, $field_name.'_id', $ho);
				}
				return $hocurrequest;
			}
			else
			// single subrequest
			{
				$m = new Message();
				$m->action = 'load';
				$m->lang = $this->link_to_dataset->query->lang;
				$m->urn = "urn-".$field_name."-".$this->datarow["{$field_name}_id"];
				if (!$this->link_to_dataset->query->lang) 
					$m->lang = SystemLocale::$REQUEST_LANG;
				else
					$m->lang = $this->link_to_dataset->query->lang;
				return Entity::query($m);
			}
		}
		
		// HAS ONE
		if ($emtype == 'has_one' || $emtype == 'use_one')
		{
			$FN = $emtype;
			$realEntity = $this->link_to_dataset->entity->$FN($field_name);
            $realEntity = $realEntity[$field_name];
			//println("+1 $emtype $realEntity",1,TERM_RED);
			if (!$this->datarow["{$field_name}_id"]) return null; 
			$curHoId = $this->datarow["{$field_name}_id"];
			if (count($this->link_to_dataset) > 1 && !$this->link_to_dataset->query->chain) // 'PRELOAD & CACHED'
			{
				if ($this->link_to_dataset->preloads[$field_name] == true) return $curHoId; // FIRST PRELOAD	
				$this->link_to_dataset->preloads[$field_name] = true;
				$hoids = $this->link_to_dataset->getColumn($field_name.'_id');
				$m = new Message();
				$m->action = 'load';
				$m->urn = (string) $realEntity;
				$m->id = array_values($hoids);
				$m->subrequest_from = $this->datarow["id"];
				$m->chain = 1;
				$hos = $m->deliver();
				foreach ($hoids as $hurn => $hoid)
				{
					$ho = $hos->byId($hoid);
					if ($ho->id == $curHoId) $hocurrequest = $ho; 
					$this->link_to_dataset->patch($hurn, $field_name.'_id', $ho);
				}
				return $hocurrequest;
			}
			else // single subrequest
			{
				$m = new Message();
				$m->action = 'load';
				$m->urn = "urn-".$realEntity->name."-".$this->datarow["{$field_name}_id"];
				$m->subrequest_from = $this->datarow["id"];
				/*
				сквозные статусы - предача вниз только если родиттельские и дочерние имеют одинаковые статусы
				if ($this->link_to_dataset->query->exists('statuses')) $m->statuses = $this->link_to_dataset->query->statuses;
				*/
				if (!$this->link_to_dataset->query->lang) 
					$m->lang = SystemLocale::$REQUEST_LANG;
				else
					$m->lang = $this->link_to_dataset->query->lang;
				$r = $m->deliver();
				return $r;
			}
		}
		
		// HAS MANY
		if ($emtype == 'has_many')
		{
			$m = new Message();
			$m->urn = "urn-".$field_name;
			$mk = $this->link_to_dataset->entitymeta->name;
			$m->$mk = "urn-".$this->link_to_dataset->entitymeta->name."-".$this->datarow['id'];
			if (!$this->link_to_dataset->query->lang) 
			{
				$m->lang = SystemLocale::$REQUEST_LANG;
			}
			else
			{
				$m->lang = $this->link_to_dataset->query->lang;
			}
			return Entity::query($m);
		}
		
		// LIST
		if ($emtype == 'list')
		{
			$listmeta = $this->link_to_dataset->entitymeta->listbyname($field_name);
			$list_entity_name = $listmeta['entity']; 
			
			$urn = new URN('urn-'.$this->link_to_dataset->entitymeta->name.'-'.$this->datarow['id']);
			$urn->set_list($field_name);
			$m = new Message();
			$m->action = 'members';
			$m->urn = $urn;
			$listing = $m->deliver();
			
			if ($listing->count())
			{
				$m = new Message();
				$m->action = 'load';
				$m->urn = 'urn-'.$listmeta['entity'];
				$m->in = $listing;
				if ($this->link_to_dataset->entitymeta->defaultorder) $m->order = $this->link_to_dataset->entitymeta->defaultorder;
				//if ($listmeta['order']) $m->order = $listmeta['order'];
				$ds = $m->deliver();
				return $ds;
			}
			else return new DataSet(array(), $m->urn->entity, null, 0);
		}
		
		/**
		FUNCTION ENTITY PLUGIN
		*/
		$fieldCanonical = $field_name;
		$fx = explode('_',$field_name);
		if (count($fx) == 2) $fieldCanonical = $fx[1];
		/**
		EXTENDED FIELD NAME HAVE TO BE UNIQUE E AND NOT EXISTS IN BASE_FIELDS
		*/
		if (Field::exists($fieldCanonical) && isset($this->datarow[$field_name]))
		{
			$F = Field::id($fieldCanonical);
			if ($F->type == "integer")
			{
				return (integer) $this->datarow[$field_name];
			}
			elseif ($F->type == "float") // $wrapped = number_format(floatval($value), 2, '.', '');
			{
				$precision = $F->precision ? $F->precision : 2;
				$fv = (float) $this->datarow[$field_name];
				$wrapped = number_format($fv, $precision, '.', '');
				return $wrapped;
			}
			else
			{
				$ft = $F->type;
				if ($ft == 'string' || $ft == 'text' || $ft == 'richtext')
				{
					$fieldValue = html_entity_decode($this->datarow[$field_name]);
				}
				elseif ($ft == 'option')
				{
					if ($F->values)
					{
						$option1value = key($F->values[0]);
						$option2value = key($F->values[1]);
					}
					else
					{
						$option1value = 'Y';
						$option2value = 'N';
					}
					if ($this->datarow[$field_name] == '1') $fieldValue = $option1value;
					else if ($this->datarow[$field_name] == '0') $fieldValue = $option2value;
					else $fieldValue = null;
				}
				else
				{
					$fieldValue = $this->datarow[$field_name];
				}
				return $fieldValue;
			}
		}
		else
		{
			if (!$this->datarow[$field_name]) 
				return $this->$field_name(); // Call plugin
			else
				return $this->datarow[$field_name]; // return general field (including extended variator variations)
		}
		
	}

	public function __toString()
	{
		return $this->debug();
	}

	public function debug()
	{
		$txt = "[" . $this->link_to_dataset->entitymeta->name . "] ";
		foreach ($this->datarow as $k=>$v)
		{
			if ($v)
			{
				if (is_string($v))
				{
					if (strlen($v)>20) $v = mb_substr($v,0,7).">";
					$v = str_replace("\n", ".", $v);
					$v = str_replace("\r", ".", $v);
					$fs[] = "$k: $v";
				}
				else
				{
					$fs[] = "$k: ".json_encode($v);
				}
			}
			else $emptys[] = "$k:-";
		}
		return $txt . join($fs, ", ") . "; " . join($emptys, ", ");
	}

	
	public function asMessage()
	{
		return new Message( $this->toArray() );
	}

	
	public function toArray($recursion=array()) // , $special_include=null
	{
		$E = $this->link_to_dataset->entitymeta;
		$json = array();
		
		// URN
		$this_uuid = new UUID((integer)$this->id);
		$json["urn"]	= "urn-".$E->name.'-'.$this_uuid;
		
		if ($E->is_multy_lang())
		{
			$json['lang'] = $this->link_to_dataset->query->lang;
		}
		
		foreach ($E->has_one() as $F)
		{
			if (!is_array($recursion)) continue;
			$keycanonical = $F->name;
			$dro = $this->$keycanonical;
			if (!$dro) continue;
			$json[$keycanonical] = $dro->toArray(null);
		}
		
		foreach ($E->belongs_to() as $F)
		{
			if (!is_array($recursion)) continue;
			$keycanonical = $F->name;
			$drbt = $this->$keycanonical;
			if (!$drbt) continue;
			$json[$keycanonical] = $drbt->toArray(null);
		}
		
		foreach ($E->has_many() as $F)
		{
			$key = $F->name;
			if (is_array($recursion))
			{
				$json[$key] = array();
				foreach($this->$key->toArray(null) as $hm)
				{
					$json[$key][] = $hm;
				}
			}
		}
		
		foreach ($E->statuses as $statusid)
		{
			$status = Status::ref($statusid)->name;
			$json[$status] = $this->$status;
		}

        /*
		foreach($E->general_fields() as $fname => $F)
		{
			$key = $F->name;
			if (!in_array($key, $o))
				$json[$key] = $this->$key;
		}
        */
		
		foreach($E->fields() as $fname => $F)
		{
			//$key = $F->name;
            $key = $fname;
			if (!in_array($key, $o))
				$json[$key] = $this->$key;
		}
		
		if ($this->datarow['_level']) $json['_level'] = $this->datarow['_level'];
		if ($this->datarow['relation']) $json['relation'] = $this->datarow['relation'];
		if ($this->datarow['ns']) $json['ns'] = $this->datarow['ns'];
		
		if ($this->url)
			$json['href'] = $this->url;
		
		// provide Title from plugin if not exists in data
		if (!$E->has_field('title')) 
		{
			if ($title = $this->title())
				$json['title'] = $title;
		}

		if ($presentation = $this->presentation()) 
		{
			$json['presentation'] = $presentation;
		}
		
		// TODO MOVE THIS TO DATAROW DECORATOR/FORMATTER
		/*
		if ($E->class == 'Photo')
		{
			$thumburi = "/preview/{$this->uuid()}.".$this->ext;
			$thumbsize = getimagesize(BASE_DIR.$thumburi);
			$json['thumbnail'] = array('uri' => $thumburi, 'width'=> $thumbsize[0], 'height' => $thumbsize[1]);
		}
		*/
		return $json;
	}

	public function toJSON($o=null,$include=null)
	{
		return json_encode($this->toArray($o,$include));
	}
	
	
	public function toGeneralData()
	{
		$E = $this->link_to_dataset->entitymeta;
		$json = array();
		
		$this_uuid = new UUID((integer)$this->id);
		$json["urn"] = "urn-".$E->name.'-'.$this_uuid;
		
		foreach ($E->has_one() as $F)
		{
			$json[$F->name] = (string) URN::buildConcrete($F->name, $this->datarow[$F->name.'_id']);
		}

		foreach ($E->belongs_to() as $F)
		{
			$json[$F->name] = (string) URN::buildConcrete($F->name, $this->datarow[$F->name.'_id']);
		}
		
		foreach ($E->statuses as $statusid)
		{
			$status = Status::ref($statusid)->name;
			$json[$status] = $this->$status;
		}
		
		foreach($E->general_fields() as $fname => $F)
		{
			$key = $F->name;
			if (!in_array($key, $o))
				$json[$key] = $this->$key;
		}

		foreach($E->fields() as $fname => $F)
		{
			$key = $F->name;
			if (!in_array($key, $o))
				$json[$key] = $this->$key;
		}

		if ($this->datarow['relation']) $json['relation'] = $this->datarow['relation'];
		if ($this->datarow['ns']) $json['ns'] = $this->datarow['ns'];
		
		return $json;
	}

    public function toCSV($fileid)
    {
        return fputcsv($fileid, $this->datarow);
    }

	public function toXML()
	{
		$dom = new DOMDocument("1.0", "UTF-8");
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		
		$E = $this->link_to_dataset->entitymeta;
		$this_uuid = new UUID((integer)$this->id);
		
		$xentity = $dom->createElement("entity");
		$xentity->setAttribute('urn', "urn-".$E->name.'-'.$this_uuid);
		$dom->appendChild($xentity);
		
		$xstructure = $dom->createElement("data");
		$xentity->appendChild($xstructure);
		
		$xstatuses = $dom->createElement("statuses");
		$xstructure->appendChild($xstatuses);
		foreach ($E->statuses as $statusid)
		{
			$status = Status::ref($statusid)->name;
			$statusv = $this->$status ? 'yes' : 'no'; 
			$text = $dom->createTextNode($statusv);
			$xfield = $dom->createElement("status");
			$xfield->setAttribute('name', $status);
			$xfield->appendChild($text);
			$xstatuses->appendChild($xfield);
		}
		
		/*
		foreach($E->general_fields() as $fname => $F)
		{
			$key = $F->name;
			if (!$this->$key) continue;
			if (!in_array($key, $o)) 
			{
				if ($F->type != 'richtext')
					$text = $dom->createTextNode($this->$key);
				else
					$text = $dom->createCDATASection($this->$key);
			}
			$xfield = $dom->createElement("field");
			$xfield->setAttribute('name', $fname);
			$xfield->appendChild($text);
			$xstructure->appendChild($xfield);
		}
		*/

		foreach($E->fields() as $fname => $F)
		{
			$key = $F->name;
			if (!$this->$key) continue;
			if (!in_array($key, $o)) 
			{
				if ($F->type != 'richtext')
					$text = $dom->createTextNode($this->$key);
				else
					$text = $dom->createCDATASection($this->$key);
			}
			$xfield = $dom->createElement("field");
			$xfield->setAttribute('name', $fname);
			if ($text)
            {
                $xfield->appendChild($text);
                $xstructure->appendChild($xfield);
            }
		}
		
		/**
		File, Photo, Attach will append its files as base64 field values
		*/
		$Class = $this->entitymeta->getClass();
		if (method_exists($Class,'toXMLdecorator'))
		{
			$Class->toXMLdecorator($dom, $this);
		}
		
		foreach ($E->belongs_to() as $F)
		{
			if (!$this->datarow[$F->name.'_id']) continue; 
			$bt = (string) URN::buildConcrete($F->name, $this->datarow[$F->name.'_id']);
			$text = $dom->createTextNode($bt);
			$xfield = $dom->createElement("belongsto");
			$xfield->appendChild($text);
			$xstructure->appendChild($xfield);
			
		}

        foreach ($E->use_one() as $as => $F)
        {
            if (!$this->datarow[$F->name.'_id']) continue;
            $ho = (string) URN::buildConcrete($F->name, $this->datarow[$F->name.'_id']);
            $text = $dom->createTextNode($ho);
            $xfield = $dom->createElement("useone");
            $xfield->appendChild($text);
            $xstructure->appendChild($xfield);
        }

        foreach ($E->has_one() as $as => $F)
        {
            if (!$this->datarow[$F->name.'_id']) continue;
            $ho = (string) URN::buildConcrete($F->name, $this->datarow[$F->name.'_id']);
            $text = $dom->createTextNode($ho);
            $xfield = $dom->createElement("hasone");
            $xfield->appendChild($text);
            $xstructure->appendChild($xfield);
        }

		$xrel = $dom->createElement("lists");
		$xstructure->appendChild($xrel);
		foreach ($E->lists() as $list)
		{
			$rel = $list['entity'];
			$listns = $list['ns'];
			$listname = $list['name'];
			
			$v = $rel->name;
			$relname = $v;
			$relmetaname = $v."_meta";
			$rels = array();
			$relname = $rel->name;			
			$rdb = DB::link();
			try
			{
				if ($E->uid < $rel->uid)
				{
					$EID1 = $E->uid;
					$EID2 = $rel->uid;
					$WID = 'id1';
				}
				else 
				{
					$EID2 = $E->uid;
					$EID1 = $rel->uid;
					$WID = 'id2';					
				}
				$WIDVAL = $this->id;
				$q = "SELECT * FROM mappings WHERE entity1 = $EID1 AND entity2 = $EID2 AND {$WID} = {$WIDVAL} AND ns = {$listns}";
				$links = $rdb->tohashquery($q);
				
				if (!count($links)) continue;
				$xrelin = $dom->createElement("list");
				$xrelin->setAttribute('name', $listname);
				$xrel->appendChild($xrelin);
				
				foreach ($links as $link)
				{
					if ($E->uid < $rel->uid)
						$urn = "urn-{$relname}-{$link['id2']}";
					else
						$urn = "urn-{$relname}-{$link['id1']}";
					$text = $dom->createTextNode($urn);
					$xfield = $dom->createElement("urn");
					$xfield->appendChild($text);
					$xrelin->appendChild($xfield);	
				}
			}
			catch (Exception $e)
			{
				return false;
			}
			
		}
		
		$xrel = $dom->createElement("related");
		$xstructure->appendChild($xrel);
		foreach ($E->related() as $rel)
		{
			$v = $rel->name;
			$relname = $v;
			$relmetaname = $v."_meta";
			$rels = array();
			$relname = $rel->name;			
			$rdb = DB::link();
			try
			{
				if ($E->uid < $rel->uid)
				{
					$EID1 = $E->uid;
					$EID2 = $rel->uid;
					$WID = 'id1';
				}
				else 
				{
					$EID2 = $E->uid;
					$EID1 = $rel->uid;
					$WID = 'id2';					
				}
				$WIDVAL = $this->id;
				$q = "SELECT * FROM mappings WHERE entity1 = $EID1 AND entity2 = $EID2 AND {$WID} = {$WIDVAL}";
				$links = $rdb->tohashquery($q);
				
				if (!count($links)) continue;
				$xrelin = $dom->createElement("rel");
				$xrelin->setAttribute('name', $v);
				$xrel->appendChild($xrelin);
				
				foreach ($links as $link)
				{
					if ($E->uid < $rel->uid)
						$urn = "urn-{$relname}-{$link['id2']}";
					else
						$urn = "urn-{$relname}-{$link['id1']}";
					$text = $dom->createTextNode($urn);
					$xfield = $dom->createElement("urn");
					$xfield->appendChild($text);
					$xrelin->appendChild($xfield);	
				}
			}
			catch (Exception $e)
			{
				return false;
			}

		}

		//if ($this->datarow['relation']) $json['relation'] = $this->datarow['relation'];
		//if ($this->datarow['ns']) $json['ns'] = $this->datarow['ns'];
		
		//return $json;
		return $dom->saveXML();
	}

}

?>