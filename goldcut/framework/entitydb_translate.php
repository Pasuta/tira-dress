<?php 
/**
EntityDB_translate
*/

class EntityDB_translate extends EntityDB
{
	/**
	TRANSLATE
	 */
	public function provide_translate($id, $lang, $data)
	{
		$q = "UPDATE ".$this->E->name." SET translated_{$lang} = 1, ";
		foreach ($this->E->lang_fields() as $F)
		{
			$fname = $F->name;
			if ( strlen($data->$fname) > 0 )
			{
				// PREPROCESS URI TRANSLIT
				if (is_array($this->E->translit))
				{
					foreach ($this->E->translit as $tf => $tt)
					{
						$data->$tt = translit($data->$tf, $data->lang);
					}
				}	

				$secured = EntityStore::fieldSecure($F, $data->$fname);
				$safe = Security::mysql_escape($secured);
				if (strlen($safe))
					$ft = $safe;
				else
					$ft = str_replace("'", "’", $data->$fname);
				if ($lang != DEFAULT_LANG)
					$qf[] = "`".$fname."_{$lang}` = '" . $ft . "'";
				else
					$qf[] = "`".$fname."` = '" . $ft . "'";
			}
		}
		$q .= join($qf, ", ");
		$q .= " WHERE id = $id";
		//print_r($q);
		$this->dblink->nquery($q);
	}

}

?>