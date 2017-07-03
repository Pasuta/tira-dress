<?php 
/**
EntityDB_insert
*/

class EntityDB_insert extends EntityDB
{
	/**
	INSERT
	*/
	public function insert($lang, $EO, $EOi) // , $eoid can del eoid
	{
		
		//dprintln($EO,1,TERM_GRAY);
		
		$q = "INSERT INTO {$this->E->name} ( ";

		if (!$EO['id'])
		{
			$newuuid = new UUID();
			$EO['id'] = $newuuid->toInt();
		}

		if ($EO['id'] > 0)
		{
			$fs[] = 'id';
			$fd[] = $EO['id'];
		}

		foreach ($this->E->statuses as $statusid)
		{
			$status = Status::ref($statusid)->name;
			$fs[] = "`" . $status . "`";
			$fd[] = $EO[$status];
		}

		if ( $this->E->is_multy_lang() )
		{
			$fs[] = "`translated_{$lang}`";
			$fd[] = 1;
		}

		foreach ($this->E->general_fields() as $v => $F)
		{
			$fs[] = "`".$v."`";
			$EO[$v] = Security::mysql_escape((string)$EO[$v]);
			$fd[] = Field::sqlwrap($F, $EO[$v]);
		}

		foreach ($this->E->lang_fields() as $F)
		{
			$safe = Security::mysql_escape((string)$EOi[$F->name]);
			$EOi[$F->name] = $safe;

			if (SystemLocale::default_lang() != $lang)
				$fs[] = "`".$F->name."_{$lang}`";
			else
				$fs[] = "`".$F->name."`";
			$fd[] = "'".$EOi[$F->name]."'";
		}

		foreach ($this->E->belongs_to() as $E)
		{
			$fs[] = "`".$E->name."_id`";
			if ($EO[$E->name] == '')
				$fd[] = "NULL";
			else
				$fd[] = "'".$EO[$E->name]."'";
		}

		foreach ($this->E->has_one() as $usedAs => $E)
		{
			$fs[] = "`".$usedAs."_id`";
			if ($EO[$usedAs] == '')
				$fd[] = "NULL";
			else
				$fd[] = "'".$EO[$usedAs]."'";
		}
		
		foreach ($this->E->use_one() as $usedAs => $E)
		{
			$fs[] = "`".$usedAs."_id`";
			if ($EO[$usedAs] == '')
				$fd[] = "NULL";
			else
				$fd[] = "'".$EO[$usedAs]."'";
		}
		
		if (count($this->E->extendstructure)) 
		{
			if (count($EO['_properties']))
			{
				$fs[] = '_properties';
				$fd[] = "'".json_encode($EO['_properties'])."'";
			}
			if (count($EO['_variators']))
			{
				$fs[] = '_variators';
				$fd[] = "'".json_encode($EO['_variators'])."'";
			}
		}

		$q .= join($fs, ", ");
		$q .= " ) VALUES ( ";
		$q .= join($fd, ", ");
		$q .= " )";

		$this->dblink->nquery($q);
		return $EO['id'];
	}
}	
?>