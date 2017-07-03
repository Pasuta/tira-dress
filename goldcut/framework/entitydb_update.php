<?php 
/**
class
*/
class EntityDB_Update extends EntityDB
{
	/**
	UPDATE
	 */
	function update($lang, $id, $data_array)
	{
		//dprintln($data_array,1,TERM_GREEN);
		
		$q = "UPDATE `".$this->E->name."` SET ";
		
		foreach ($this->E->general_fields() as $v => $F)
		{
			if ($data_array[$v] !== null)
			{
				if (($F->type == 'integer' || $F->type == 'float') && $data_array[$v] instanceof Message)
				{
					$a = $data_array[$v]->toArray();
					$k = (key($a));
					if ($F->type == 'integer') $v = (integer) $a[$k];
					if ($F->type == 'float') $v = (float) $a[$k];
					//$v = $a[$k];
					if ($k == 'increment')
						$qf[] = '`'.$F->name.'` = `'.$F->name.'` + '.$v;
					else if ($k == 'decrement')
						$qf[] = '`'.$F->name.'` = `'.$F->name.'` - '.$v;
					else
						throw new Exception('Unknown action '.$k);
				}
				else
				{
					$safe = Security::mysql_escape((string)$data_array[$v]);
					$data_array[$v] = $safe;
					$value = Field::sqlwrap($F, $data_array[$v]);
					$qf[] = '`'.$v.'` = '.$value;
				}
			}
		}

		foreach ($this->E->lang_fields() as $F)
		{
			/**
			TODO sqlwrap as upper
			*/
			$safe = Security::mysql_escape((string)$data_array[$F->name]);
			$data_array[$F->name] = $safe;
			
			if (SystemLocale::default_lang() != $lang)
			{
				if ($data_array[$F->name])
					$qf[] = '`'.$F->name."_$lang` = '" . $data_array[$F->name] . "'";
			}
			else
			{
				if ($data_array[$F->name])
					$qf[] = '`'.$F->name."` = '" . $data_array[$F->name] . "'";
			}
		}

		foreach ($this->E->has_one() as $usedAs => $F)
		{
			if (isset($data_array[$usedAs]))
			{
				if ($data_array[$usedAs] === 0) $data_array[$usedAs] = 'NULL';
				$qf[] = '`'.$usedAs."_id` = " . $data_array[$usedAs];
			}
		}
		
		foreach ($this->E->use_one() as $usedAs => $F)
		{
			if (isset($data_array[$usedAs]))
			{
				if ($data_array[$usedAs] === 0) $data_array[$usedAs] = 'NULL';
				$qf[] = '`'.$usedAs."_id` = " . $data_array[$usedAs];
			}
		}

		foreach ($this->E->belongs_to() as $F)
		{
			if (isset($data_array[$F->name]))
			{
				if ($data_array[$F->name] === 0) $data_array[$F->name] = 'NULL';
				$qf[] = '`'.$F->name."_id` = " . $data_array[$F->name];
			}
		}

		foreach ($this->E->statuses as $statusid)
		{
			$status = Status::ref($statusid)->name;
			if ($data_array[$status] !== null) $qf[] = '`'.$status."` = '" . $data_array[$status] . "'";
		}
		
		// optimized		
		if (count($this->E->extendstructure)) 
		{
			$EO = $data_array;
			if (count($EO['_properties']))
			{
				$qf[] = '_properties = ' . "'".UnicodeOp::decodeUnicodeString(json_encode($EO['_properties']))."'";
			}
			if (count($EO['_variators']))
			{
				$qf[] = '_variators = ' . "'".json_encode($EO['_variators'])."'";
			}
		}

		// выполнять запрос только если есть мимнимум одно поле для обновления
		if (count($qf))
		{
			$q .= join($qf, ", ");
			if (is_numeric($id))
				$q .= " WHERE `id` = ".$id;
			$this->dblink->nquery($q);
		}
	}
	
}	
?>