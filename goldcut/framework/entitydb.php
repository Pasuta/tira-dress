<?php
/**
TODO if INDEX exists but not UNIQ if we add same UNIQ we will hav 2 dup indexes with uniq and non uniq
*/
class EntityDB
{

	protected $dblink;
	protected $E;

	public function __construct($E)
	{
		$this->E = $E;
		$this->dblink = DB::link();
	}

	public function exists_table()
	{
		$dbname = $this->dblink->dbname();
		$q = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$this->E->name}' AND table_schema = '$dbname'";
		$r = $this->dblink->tohashquery($q);
		return $r;
	}
	
	public function indexes_in_table()
	{
		$dbname = $this->dblink->dbname();
		// Table 	Non_unique 	Key_name 	Seq_in_index 	Column_name 	Collation 	Cardinality 	Sub_part 	Packed 	Null 	Index_type 	Comment
		$q = "SHOW INDEX FROM `{$this->E->name}`";
		$r = $this->dblink->tohashquery($q);
		return $r;
	}
	
	public function sync_indexes_in_table($indexes, $uniqs)
	{
		$entity = $this->E;
		$exstidx = $this->indexes_in_table();
		
		foreach ($entity->checkunique as $uniqfs)
		{
			//println($uniqfs,2,TERM_GREEN);
			//"checkunique" => array( array('uri','_parent') )
			//"checkunique" => array( array('uri','_parent'), 'uniqf2' ) // uniq pair, uni qsingle 
		}

		foreach ($exstidx as $idxe )
		{
			//if ($idxe['Table']=='category') print_r($idxe);
			//Table 	Non_unique 	Key_name 	Seq_in_index 	Column_name 	Collation 	Cardinality 	Sub_part 	Packed 	Null 	Index_type 	Comment
			$has_indexes[$entity->name][] = $idxe['Column_name'];
			//if ($idxe['Column_name'] == 'uri')
			if ($idxe['Non_unique'] == '0') $has_uniq[$entity->name][] = $idxe['Column_name']; // pri id,
			//elseif ($idxe['Non_unique'] == '1') // parent_id 
		}
		// SYNC INDEXES
		foreach ($indexes[$entity->name] as $hi => $ct) // indexes in entity
		{
			//println($indexes[$entity->name]);
			if (!in_array($hi, $has_indexes[$entity->name])) // indexes in db
			{
				//println("INDEX NOT exists in db {$entity->name}.{$hi}");			
				//printH('INDX '.$entity->name);
				//printlnd($idx);
				$q = "ALTER TABLE `{$entity->name}` ADD INDEX (`{$hi}`)";
				$dblink = DB::link();
				try 	{ $dblink->nquery($q); }
				catch (Exception $e)	{ println("FAIL ADD INDEX {$entity->name}.{$hi}", 2, TERM_RED);	}
			}
			else
			{
				//println("EXISTS INDEX in db {$entity->name}.{$hi}");
			}
		}
		// SYNC UNIQS
		//printH('UNX '.$entity->name);
		/**
		uniq in e config. dont forget to specify f + f_lang!
		*/
		foreach ($uniqs as $ct => $hu)
		//foreach ($uniqs[$entity->name] as $hu)
		{
			//println("$hu => $ct",3);
			//println($uniqs[$entity->name]);
			if (!in_array($hu, $has_uniq[$entity->name])) // indexes in db
			{
				//println("- UNIQ NOT exists in db {$entity->name}.{$hu}");			
				$q = "ALTER TABLE `{$entity->name}` ADD UNIQUE (`{$hu}`)";
				$dblink = DB::link();
				try 	{ $dblink->nquery($q); }
				catch (Exception $e)	{ println("FAIL ADD UNIQUE {$entity->name}.{$hu}", 1, TERM_RED); $dblink->perror();	}
			}
			else
			{
				//println("+ UNIQ INDEX in db {$entity->name}.{$hu}");
			}
		}
	}
	

	function clean_table()
	{
		$this->dblink->raw_query("DELETE FROM `". $this->E->name."`");
	}

	function create_table($database_charset, $database_collate) // $lang=false
	{
		if (!$database_charset) 
			throw new Exception('NO DB CHARSET DEFINED!');
		
		/**
		TODO add per field charset creation option
		*/
		//$database_charset = 'cp1251';
		//$database_collate = 'cp1251_general_ci';
		$to_sqltype = array("set"=>"VARCHAR(32)", "string"=>"VARCHAR(255)","image"=>"TEXT","text"=>"TEXT","richtext"=>"MEDIUMTEXT", "integer"=>"INT", "float"=>"FLOAT","date"=>"DATE","timestamp"=>"INT", "option"=>"TINYINT");
		$this->dblink->nquery("DROP TABLE IF EXISTS `". $this->E->name."`");
		
		if (PHP_INT_MAX > 2147483647) $platform64 = true;
		$type = ($platform64) ? 'BIGINT' : 'INT';
		if (FORCE32BIT) $type = 'INT';
		
		$q = "CREATE TABLE `". $this->E->name ."` ( \n`id` {$type} unsigned NOT NULL,\n"; // auto_increment
		foreach ($this->E->general_fields() as $fname => $F)
		{
			$def = 'NULL';
			if ($F->default !== null) $def = $F->default;
			if (!$fname) throw new Exception("Unexistent field in entity `{$this->E}`");
			if (!$to_sqltype[$F->type]) throw new Exception("Unknown sql field type. name `$fname` of type `{$F->type}`");
			$q .= "`$fname` " . $to_sqltype[$F->type] . " DEFAULT $def, \n";
		}
		foreach ($this->E->belongs_to() as $e) {
			$q .= "`".$e->name."_id` INT,\n";
		}
		foreach ($this->E->has_one() as $usedAs => $e) {
			$q .= "`".$usedAs."_id` INT,\n";
		}
		foreach ($this->E->use_one() as $usedAs => $e) {
			$q .= "`".$usedAs."_id` INT,\n";
		}
		foreach ($this->E->has_statuses() as $e) {
			$q .= "`".$e->name."` TINYINT,\n";
		}
		/**
		if ($this->E->is_multy_lang())  // TODO check if type is string/text and not INT def
		{
			foreach ($this->E->lang_codes() as $lang)
			{
				$q .= "`translated_{$lang}` TINYINT, \n";
				foreach ($this->E->lang_fields() as $F)
					if ( SystemLocale::default_lang() != $lang)
						$q .= "`{$F->name}_{$lang}` " . $to_sqltype[$F->type] . ", \n";
					else
						$q .= "`{$F->name}` " . $to_sqltype[$F->type] . ", \n";
			}
		}
		*/
		$q .= "PRIMARY KEY (`id`) ) {$db_engine} DEFAULT CHARACTER SET {$database_charset} COLLATE {$database_collate};";
		// ENGINE=MyISAM
		// TODO $db_engine provide
		
		try 
		{
			$this->dblink->nquery($q);
		}
		catch (Exception $e)
		{
			println('Create table error - '.$e,1,TERM_RED);
			print '<pre>'.$q.'</pre>';
		}
	}

	public function add_column($eParent, $eChild)
	{
		$q = "ALTER TABLE `{$eParent}` ADD `{$eChild}` INT DEFAULT NULL";
		$this->dblink->nquery($q);
	}

}
?>