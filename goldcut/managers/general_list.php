<?php

class General_List
{
	
	public function addv($data)
	{
		ListDatabase::vectorAddLeft('abc',rand(1,100));
	}
	
	public function membersv($data)
	{
		$objectID = $data->urn->uuid->toInt();
		$objectEntity = $data->urn->entity;
		$objectEntityName = $objectEntity->name;
		$subjectList = $data->urn->listmeta();
		$type = $subjectList['ns'];
		$subjectEntity = Entity::ref($subjectList['entity']);
		if (ListDatabase::is_enabled()) 
		{
			$setmembers = ListDatabase::vectorRange($data->urn->keys(), 0, -1);
			$listing = new Listing($subjectEntity, $setmembers);
			return $listing;
		}
		else throw new Exception('Vector ops enabled only in LISTDB mode');
	}
	
	
	
	public function add($data)
	{
		$objectID = $data->urn->uuid->toInt();
		$objectEntity = $data->urn->entity;
		$objectEntityName = $objectEntity->name;
		if (!$data->to) throw new Exception('Provide `to` for add To list');
		$subject = $data->to;
		$subjectEntity = $subject->entity;
		$subjectID = $subject->uuid->toInt();
		$subjectList = $data->to->listmeta();
		$type = $subjectList['ns'];
		$reverse = $subjectList['reverse'];
		$graphcopy = $subjectList['graph'];
		$notify = $subjectList['notify'];
		if (ListDatabase::is_enabled()) 
		{
			ListDatabase::setAdd($data->to->keys(), $objectID);
			if ($reverse)
			{
				$revk = $data->urn->keys();
				array_push($revk, $reverse);
				ListDatabase::setAdd($revk, $subjectID);
			}
			/*
			if ($notify)
			{
				println($data->to);
				$ton = new URN((string)$data->to);
				$ton->set_list($notify);
				println($ton,1,TERM_VIOLET);
				$notifywho = $ton->resolve();
				printlnd($notifywho,1,TERM_VIOLET);
				foreach ($notifywho->ids as $who)
				{
					$whourn = URN::build($subjectEntity->name, $who, 'notifications');
					$whourn->set_list($notify);
					println($whourn,2,TERM_VIOLET);
				}
			}
			*/
		}
		// relation db
		if (DISABLE_SQL_AS_LISTDB !== true)
		{
			$rdb = DB::link();
			$r = $rdb->nquery("INSERT INTO mappings (entity2, entity1, id2, id1, ns) VALUES ( {$objectEntity->uid}, {$subjectEntity->uid}, {$objectID}, {$subjectID}, {$type} )");
			if ($reverse) 
			{
				//println($reverse);
				//println($subjectEntity);
				//$typerev = $subjectEntity->listbyname($reverse);
				$typerev = $objectEntity->listbyname($reverse);
				$r = $rdb->nquery("INSERT INTO mappings (entity1, entity2, id1, id2, ns) VALUES ( {$objectEntity->uid}, {$subjectEntity->uid}, {$objectID}, {$subjectID}, {$typerev['ns']} )");
			}
		}
		/**
		MIRROR LINK IN FULL GRAPH DATABASE (neo4j etc. Non relation db storage (will dup)!)
		*/
		if ($graphcopy && FULLGRAPHDBUSED === true)
		{
			$m = new Message();
			$m->action = 'newedge';
			$m->urn = $data->to;
			$m->with = $data->urn;
			$m->type = $type;
			$r = $m->deliver();
		}
		// TODO REVERSE
		//$data->to->keys(), $objectID
		$mq = "list.add.{$data->to->entity->name}.{$data->to->listname}";
		//println($mq);
		Broker::instance()->send(array($data->to, $objectID), "ENTITY", $mq);
		if ($reverse)
		{
			$revk = $data->urn->keys();
			array_push($revk, $reverse);
			$revurn = new URN(join('-',$revk));
			$mq = "list.add.{$revurn->entity->name}.{$revurn->listname}";
			//println($mq);
			Broker::instance()->send(array($revurn, $subjectID), "ENTITY", $mq);
		}
		return $data;
	}
	
	public function intersect($message)
	{
		println(__METHOD__,1,TERM_RED);
	}
	
	public function union($message)
	{
		println(__METHOD__,1,TERM_RED);
	}
	
	public function remove($data)
	{
		$objectID = $data->urn->uuid->toInt();
		$objectEntity = $data->urn->entity;
		$objectEntityName = $objectEntity->name;
		if (!$data->from) throw new Exception('Provide `from` for remove From list');
		$subject = $data->from;
		$subjectEntity = $subject->entity;
		$subjectID = $subject->uuid->toInt();
		$subjectList = $data->from->listmeta();
		$type = $subjectList['ns'];
		$reverse = $subjectList['reverse'];
		$graphcopy = $subjectList['graph'];
		if (ListDatabase::is_enabled()) 
		{
			ListDatabase::setRemove($data->from->keys(), $objectID);
			if ($reverse) 
			{
				$revk = $data->urn->keys();
				array_push($revk, $reverse);
				ListDatabase::setRemove($revk, $subjectID);
			}
		}
		// relation db
		if (DISABLE_SQL_AS_LISTDB !== true)
		{
			$rdb = DB::link();
			$r = $rdb->nquery("DELETE FROM mappings WHERE entity2 = {$objectEntity->uid} AND entity1 = {$subjectEntity->uid} AND id2 = {$objectID} AND id1 = {$subjectID} AND ns = {$type}");
			if ($reverse) 
			{
				//$typerev = $subjectEntity->listbyname($reverse);
				$typerev = $objectEntity->listbyname($reverse);
				//println($typerev);
				$r = $rdb->nquery("DELETE FROM mappings WHERE entity1 = {$objectEntity->uid} AND entity2 = {$subjectEntity->uid} AND id1 = {$objectID} AND id2 = {$subjectID} AND ns = {$typerev['ns']}");
			}
		}
		if ($graphcopy && FULLGRAPHDBUSED === true)
		{
			$m = new Message();
			$m->action = 'unedge';
			$m->urn = $data->from;
			$m->with = $data->urn;
			$m->type = $type;
			$r = $m->deliver();
		}
	}
	
	public function exists($data)
	{
		$objectID = $data->urn->uuid->toInt();
		$objectEntity = $data->urn->entity;
		$objectEntityName = $objectEntity->name;
		if (!$data->in) throw new Exception('Provide `in` for edge exists');
		$subject = $data->in;
		$subjectEntity = $subject->entity;
		$subjectID = $subject->uuid->toInt();
		$subjectList = $data->in->listmeta();
		$listname = $subjectList['name'];
		$type = $subjectList['ns'];
		if (ListDatabase::is_enabled())
		{
			$existsInList = ListDatabase::setExists(array('urn', $objectEntityName, $subjectID, $listname), $objectID);
		}
		else
		{
			if (DISABLE_SQL_AS_LISTDB === true) throw new Exception('LISTDB disabled, sqlaslistdb disabled too');
			$rdb = DB::link();
			$q = "SELECT count(entity1) AS `edgecount` FROM mappings WHERE entity2 = {$objectEntity->uid} AND entity1 = {$subjectEntity->uid} AND id2 = {$objectID} AND id1 = $subjectID AND ns = {$type}";
			$r = $rdb->count_query($q);
			$existsInList = $r['edgecount'];
		}
		$m = new Message();
		$m->exists = (int) $existsInList;
		return $m;
	}
	
	public function members($data)
	{
		$objectID = $data->urn->uuid->toInt();
		$objectEntity = $data->urn->entity;
		$objectEntityName = $objectEntity->name;
		$subjectList = $data->urn->listmeta();
		$type = $subjectList['ns'];
		$subjectEntity = Entity::ref($subjectList['entity']);
		if (ListDatabase::is_enabled()) 
		{
			$setmembers = ListDatabase::setAll($data->urn->keys());
			$listing = new Listing($subjectEntity, $setmembers);
			return $listing;
		}
		// relation db
		{
			$rdb = DB::link();
			$q = "SELECT id2 FROM mappings WHERE entity1 = {$objectEntity->uid} AND entity2 = {$subjectEntity->uid} AND id1 = {$objectID} AND ns = {$type}";
			$r = $rdb->tohashquery($q);
			// TODO sql to Listins (select id only)
			$listing = new Listing($subjectEntity); 
			foreach ($r as $edgeraw)
			{
				$listing->add($edgeraw['id2']);
			}
			return $listing;
		}
	}
}

?>