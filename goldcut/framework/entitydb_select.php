<?php 
/**
EntityDB_select
TODO SQL_NO_CACHE, SQL_CACHE (cache or not this query result) only works with my.cnf query-cache-type = 2
*/
class EntityDB_select extends EntityDB
{
	/**
	SELECT
	*/
	function select($langA=false, $parent_field=null, $limit=false, $conditions=null, $ids=null, $joins=null, $searchOR=null, $order = null, $offset = null, $select_fields, $group_fields, $nofuture, $not, $options)
	{
        $lang = $langA[0];
        $langFilter = $langA[1];
		//$imamanager = $category; $category=null,
		
		// ORDER DEFAULT
		/*
		if ($datefield = $this->E->has_date())
		{
			if (!$order)	$order = "`{$datefield->name}` DESC";
		}
		*/
		/**
		TODO move it to entityquery - this mod has to only do sql select not extend logic
		*/
		if ($this->E->has_field('ordered') && !$order && !$ids)
		{
			$order = "`ordered` ASC";
		}
		
		/**
		TODO 
		load $categorydataset
		load news in cats > m->category = $categorydataset >> load news by cat ids array 
		*/
		if ($ids)
		{
			//dprintlnd($ids,1,TERM_RED);
			if ( is_int($ids) )
				$sqla[] = "`{$this->E->name}`.id = {$ids}";
			elseif (is_string($ids))
			{
				$ids = Security::mysql_escape($ids);
				$sqla[] = "`{$this->E->name}`.uri = '{$ids}' "; // TODO URI OR UUID!!!
			}
			elseif (is_array($ids))
			{
				$ids = join(',',$ids);
				$sqla[] = "`{$this->E->name}`.id IN ({$ids})";	
			}
			elseif ($ids instanceof Message)
			{
				$ids = (array) $ids->get(); // $query->id is Message
				$ids = join(',',$ids);
				$sqla[] = "`{$this->E->name}`.id IN ({$ids})";	
			}
			else
				throw new Exception('EntityDBSelect ids provided but type is unsupported. May you use urn as uuid ids:'.$ids);
			//$order = null;
		}

		
		// LANG
		if ($lang !== false)
        {
            foreach ($langFilter as $lang_code => $filter_lang)
            {
                //println("$lang_code => $filter_lang");
                $filter_lang = (int) $filter_lang;
                if ($filter_lang === 1) $langIncludes[]= "translated_{$lang_code} = $filter_lang";
                else $langIncludes[]= "translated_{$lang_code} IS NULL OR translated_{$lang_code} = 0"; // = 0 OR != 1
                //println("translated_{$lang_code} = $filter_lang",2,TERM_GREEN);
            }
            if ($langFilter !== false) $sqla[] = join(", ", $langIncludes);
            //println($sqla);
        }

		/**
		BY PARENT BELONGS TO
		 */
		if ($parent_field)
		{
			foreach($parent_field as $bt)
			{
				$field = key($bt);
				if (is_array($bt[$field]))
				{
					$values = join(',', $bt[$field]);
					$sqla[] =  "`{$field}_id` IN ({$values})";		
				}
				else
				{
					$value = $bt[$field];
					if ($value === false or $value === 'NULL') // DOCUMENT IT
						$sqla[] =  "`{$field}_id` IS NULL";
					else
					{
						$value = Security::mysql_escape($value);
						$sqla[] =  "`{$field}_id` = {$value}";
					}
				}
			}
		}

		/**
		JOINS CUTTED
		*/

		/**
		CONDITIONS
		TODO if field->type == float
		 */
		if ($conditions !== null)
		{
			if (is_array($conditions))
			{
				foreach ($conditions as $condition)
				{
					$op = '=';
					if (is_array($condition))
					{
						$field = $condition['field'];
						$field = Security::mysql_escape($field);
						if ($condition['table'])
							$table = $condition['table'];
						else
							$table = $this->E->name;
						
						$ce = explode('_',$field); // field name widthout _lang suffix
						if (count($ce)>1) $field_g = $ce[0];
						else $field_g = $field;
						
						if (Field::exists($field_g))
						{
							$f = Field::ref($field_g);
							if ($f->type == 'integer' && !(is_array($condition['value']) or $condition['value'] instanceof Message))
							{
								$forceInteger = true;
								$opValue = $this->operatorAndValueFromCondition($condition, $forceInteger);
								$op = $opValue['op'];
								$value = $opValue['value'];
							}
							elseif ($f->type == 'timestamp' or $f->type == 'integer' or $f->type == 'float')
							{
								if (is_array($condition['value']) or $condition['value'] instanceof Message)
								{
									$op = 'BETWEEN';
									$value = join(' AND ', $condition['value']->toArray());
								}
								else
									$value = (int) $condition['value'];
							}
							elseif ($f->type == 'string')
							{
								$value = "'".Security::mysql_escape($condition['value'])."'";
							}
							elseif ($f->type == 'date')
							{
								
								if (is_array($condition['value']) or $condition['value'] instanceof Message)
								{
									$op = 'BETWEEN';
									$value = join(' AND ', $condition['value']->toArray());
								}
								else
								{								
									if ($condition['value'] == "today")
									{
										$value = "'".date("Y-m-d")."'";
									}
									else if ($condition['value'] == "yesterday")
									{
										$value = "'".date("Y-m-d")."' - INTERVAL 1 DAY";
									}
									else if ($condition['value'] == "tomorrow")
									{
										$value = "'".date("Y-m-d")."' + INTERVAL 1 DAY";
									}
									else if ($condition['value'] == "anyyeartoday")
									{
										$field = 'DATE_FORMAT(' . $condition['field'] . ", '%m-%d')";
										$value = "'".date("m-d")."'";
									}
									else if ($condition['value'] == "anyyearyesterday")
									{
										$field = 'DATE_FORMAT(' . $condition['field'] . ", '%m-%d')";
										$value = "'".date("m-d", strtotime('-1 day', time()))."'";
									}
									else if ($condition['value'] == "anyyeartomorrow")
									{
										$field = 'DATE_FORMAT(' . $condition['field'] . ", '%m-%d')";
										$value = "'".date("m-d", strtotime('+1 day', time()))."'";
									}
									else if ($condition['value'] == "anyyearnextweek")
									{
										$field = 'DATE_FORMAT(' . $condition['field'] . ", '%m-%d')";
										$value = "'".date("m-d", strtotime('+1 day', time()))."'";
										$searchOR[] = "$field = $value";
										$value = "'".date("m-d", strtotime('+2 day', time()))."'";
										$searchOR[] = "$field = $value";
										$value = "'".date("m-d", strtotime('+3 day', time()))."'";
										$searchOR[] = "$field = $value";
										$value = "'".date("m-d", strtotime('+4 day', time()))."'";
										$searchOR[] = "$field = $value";
										$value = "'".date("m-d", strtotime('+5 day', time()))."'";
										$searchOR[] = "$field = $value";
										$value = "'".date("m-d", strtotime('+6 day', time()))."'";
										$searchOR[] = "$field = $value";
										$value = "'".date("m-d", strtotime('+7 day', time()))."'";
										$searchOR[] = "$field = $value";
										$skip = true;
										/**
										// usage
										$m->field = "*, DATE_FORMAT(datebirdth, '%j') as bday";
										$m->order = 'bday ASC';
										*/
									}
									else
									{
										if ($condition['value'] < 0)
											$value = "'".date("Y-m-d")."' - INTERVAL ".abs($condition['value'])." DAY";
										else
											$value = "'".$condition['value']."'";
									}
								}
								//println($field);
							}
							else // field of type not in cases
							{
								$value = "'".Security::mysql_escape($condition['value'])."'";
							}
						}
						else // not a field (Statuses, Relations HasOne, BelongsTo etc)
						{
							$forceInteger = false;
							$opValue = $this->operatorAndValueFromCondition($condition, $forceInteger);
							$op = $opValue['op'];
							$value = $opValue['value'];
							//$value = "'".Security::mysql_escape($value)."'";
						}
						
						$table = Security::mysql_escape($table);
						if (!$skip) $sqla[] = "{$field} {$op} {$value}"; // prefix with {$table}.
					}
					else
						throw new Exception("DEPRECATED NON ARRAY CONDITION");
				}
			}
			else
			{
				throw new Exception("DEPRECATED PLAIN CONDITIONS");
			}
		}
		
		/**
		NOFUTURE
		*/
		if ($nofuture === true && $this->E->has_field('created'))
		{
			$sqla[] = "created < ".time();
		}

		/**
		CONDITIONS searchOR
		 */
		if ($searchOR !== null)
		{
			if (is_array($searchOR))
			{
				foreach ($searchOR as $condition)
				{
					if (is_array($condition))
					{
						$field = key($condition);
						$value = $condition[$field];
						$field = Security::mysql_escape($field);
						$value = Security::mysql_escape($value);
						$sqlor[] = "{$this->E->name}.{$field} = '{$value}'";
					}
					else
					{
						//$condition = Security::mysql_escape($condition); // SECURITY CHECK THIS
						$sqlor[] = $condition;
					}
				}

			}
			else // plain
			{
				//$searchOR = Security::mysql_escape($searchOR);
				$sqlor[] = $searchOR;
			}
		}

		// combine conditions
		if (count($sqla) > 1) $sqlwhere = join(' AND ', $sqla);
		if (count($sqla) == 1) $sqlwhere = $sqla[0];
		if (count($sqlor) == 1) $sqlwhereor = $sqlor[0];
		if (count($sqlor) > 1) $sqlwhereor = join(' OR ', $sqlor);

		if (count($sqla) == 0 && count($sqlor) > 0)
			$where = "WHERE {$sqlwhereor}";
		if (count($sqla) > 0 && count($sqlor) > 0)
			$where = "WHERE {$sqlwhere} AND ( {$sqlwhereor} )";
		if (count($sqla) > 0 && count($sqlor) == 0)
			$where = "WHERE {$sqlwhere}";

		/**
		NOT
		TODO combine to where subsets - from main query and from not "subquery"
		$where .= " AND ( id NOT IN (1,2,3) )";
		*/
		
		if ($not) 
		{
			if ($not instanceof Message)
			{
				$nota = $not->toArray();
			}
			elseif ($not instanceof DataSet)
			{
				$nota = $not->getColumn('id');
			}
			$notids = array();
			foreach($nota as $notf => $notv)
			{
				array_push($notids, (int)$notv);
				/**
				if (is_array($notv))
				{
					$notq = join(',', $notv);
				}
				else
					throw new Exception('QUERY NOT FOR NON ARRAY NOT IMPLEMENTED YET');
				*/
			}
			if (count($notids))
			{
				$notq = join(',', $notids);
				if (strlen($where))
					$where .= " AND `{$this->E->name}`.id NOT IN ({$notq})";
				else 
					$where .= "WHERE `{$this->E->name}`.id NOT IN ({$notq})";
			}
		}
		
		// offset
		if ($offset)
			$offsetsfx = $offset.", ";

		// limit NEED OPTIONS PAGE ($m->page = 2;)
		if ($limit!==false && is_integer($limit))
		{
			$limitsql = "LIMIT {$offsetsfx}{$limit}";
			if ($limit > 1 && is_int($options['page']))
			{
				$q = "SELECT count(id) FROM {$this->E->name} {$joinsql} {$where}";
				$resset = $this->dblink->count_query($q);
				$count_total = $resset[0];
			}
		}
		
		// order
		if ($order) $ordersql = "ORDER BY ".$order;

		// SELECT * FIELDS[]
		if ($lang or $joins)
			$qp = $this->select_fields_sql_helper($this->E, $lang);
		else
			$qp = '*';

		// select only fields 
		if (isset($select_fields))
		{
			if (is_string($select_fields)) $fields = $select_fields;
			else 
			{
				$sf = $select_fields->toArray();
				$sfv = array();
				foreach ($sf as $k=>$v)
				{
					if (is_int($k)) $sfv[] = '`'.$v.'`';
					else $sfv[] = "$k as `{$v}`";
				}
				$fields = join(',', $sfv);
			}
		}
		else
			$fields = "{$qp}{$addfields}";
		
		if ($group_fields instanceof Message && count($group_fields->toArray())>=1)	$groupby = 'GROUP BY ' .join(', ', $group_fields->toArray());

		// query
		$q = "SELECT {$fields} FROM {$this->E->name} {$joinsql} {$where} {$groupby} {$ordersql} {$limitsql}";
		$resset = $this->dblink->query($q, $this->E->name);
		$predataset = array('set'=>$resset,'total'=>$count_total);
		return $predataset;
	}	
	
	
	
	protected function select_fields_sql_helper($E, $lang) // , $prefix=false
	{

		if ($E->is_community())
			$fns[] = "{$E->name}.`user_id`";

		if ( $E->is_multy_lang() )
		{
			foreach ($E->lang_codes() as $lang_code)
				$fns[] = "{$E->name}.`translated_{$lang_code}`";
		}

		foreach ( $E->belongs_to() as $F )
			$fns[] = "{$E->name}.`" . $F->name . "_id`";

		foreach ( $E->has_one() as $usedas => $F )
			$fns[] = "{$E->name}.`" . $usedas . "_id`";

        foreach ( $E->use_one() as $usedas => $F )
            $fns[] = "{$E->name}.`" . $usedas . "_id`";

		/*
		foreach ( $E->related() as $F )
			$fns[] = "mappings`." . $F->name . "_meta`";
		 */
		foreach ( $E->general_fields() as $F )
			$fns[] = "{$E->name}.`" . $F->name . "`";

		if ( $E->statuses )
		{
			foreach ($E->statuses as $status)
			{
				$fns[] = "{$E->name}.`" . Status::ref($status)->name . "`";
			}
		}

		foreach ( $E->lang_fields() as $F)
			if ( SystemLocale::default_lang() != $lang)
				$fns[] = "{$E->name}.`" . $F->name . "_" . $lang . "` AS `" . $F->name . "`";
			else
				$fns[] = "{$E->name}.`" . $F->name . "`";
		$qp = join( ", ", $fns );

		return "{$E->name}.`id`, " . $qp;
	}
	
	protected function operatorAndValueFromCondition($condition, $forceInteger=false)
	{
		$op = '=';
		if ($condition['value'] === 0)
		{
			$op = '=';
			$value = '0';
		}
		else if ($condition['value'] == 'NULL')
		{
			$op = 'IS';
			$value = 'NULL';
		}
		else if ($condition['value'] == 'NOT NULL')
		{
			$op = 'IS NOT';
			$value = 'NULL';
		}
		else if ($condition['value'] === 'positive')
		{
			$op = '>';
			$value = '0';
		}
		else if ($condition['value'] === 'present')
		{
			$op = 'IS';
			$value = 'NOT NULL';
		}
		else
		{
			if ($forceInteger === true)
				$value = (int) $condition['value'];
			else
				$value = $condition['value'];
		}
		return array('op' => $op, 'value' => $value);	
	}
}
?>