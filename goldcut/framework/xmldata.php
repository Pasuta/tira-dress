<?php 
class XMLData 
{

	public static function iterateXMLfolders($onlymanagers=null, $onlyentities=null, $imported_callback_each=null, $imported_callback_before=null, $imported_callback_after=null)
	{
        //println('XML');
		//Log::debug('start', 'xmlimport');
		foreach (Entity::each_managed_entity($onlymanagers, $onlyentities) as $m => $es)
		{
			//if ($m == 'User' || $m == 'Audio' || $m == 'Video' || $m == 'Attach') continue;
			foreach($es as $entity)
			{
                // SKIP entities
				if (in_array($entity->name, array('online','visits'))) continue;
                // ONLY
				//if (!in_array($entity->name, array('illustration'))) continue;
				$fullDataDir = realpath(FIXTURES_DIR .'/'. $entity->class .'/'. $entity->name);
				if (file_exists($fullDataDir))
				{
					if ($imported_callback_before) $imported_callback_before($entity);
					$i=0; // used in gc_collect every N
					if ($handle = opendir($fullDataDir)) 
					{
						while (false !== ($entry = readdir($handle))) 
						{
							if ($entry == '.' || $entry == '..') continue;
							$i++;
							//if ($i > 5) break;
							$xmlFile = $fullDataDir.'/'.$entry;
							XMLData::importXMLentity($xmlFile, $imported_callback_each);
							$i++;
							if (($i % 200) === 0) gc_collect_cycles();
						}
						closedir($handle);
						if ($imported_callback_after) $imported_callback_after($entity);
						gc_collect_cycles();
					}
				}
                else
                {
                    //println("Not found $fullDataDir");
                }
			}
		}
	}
	
	public static function importXMLentity($xmlFile, $imported_callback_each=null)
	{
		//Log::debug($xmlFile, 'xmlimport');
		$doc = new DOMDocument();
		$doc->load($xmlFile);
		if (!$doc->documentElement) throw new Exception("Error in config file {$entry}");
		$urn = $doc->documentElement->getAttribute('urn');
		$urn = new URN($urn);
		
		$m = new Message();
		$m->action = 'create';
		$m->urn = $urn->generalize();
		$m->id = $urn->uuid->toInt();
		
		$domx = new DOMXPath($doc);
		$entries = $domx->evaluate("//statuses/status");
		foreach ($entries as $n) 
		{
			$f = $n->getAttribute('name');
			$v = $n->nodeValue;
			$m->$f = ($v == 'yes') ? true : false;
		}
		$entries = $domx->evaluate("//data/belongsto");
		foreach ($entries as $n) 
		{
			//$f = $n->getAttribute('name');
			$v = $n->nodeValue;
			if (!$v) continue;
			$v = new URN($v);
			$f = $v->entity->name;
			$m->$f = $v;
		}
		$entries = $domx->evaluate("//data/hasone");
		foreach ($entries as $n) 
		{
			//$f = $n->getAttribute('name');
			$v = $n->nodeValue;
			if (!$v) continue;
			$v = new URN($v);
			$f = $v->entity->name;
			$m->$f = $v;
		}
        $entries = $domx->evaluate("//data/useone");
        foreach ($entries as $n)
        {
            //$f = $n->getAttribute('as');
            $v = $n->nodeValue;
            if (!$v) continue;
            $v = new URN($v);
            $f = $v->entity->name;
            $m->$f = $v;
        }
		$entries = $domx->evaluate("//data/field");
		foreach ($entries as $n) 
		{
			$f = $n->getAttribute('name');
			$v = $n->nodeValue;
			//if ($f == 'anons' || $f == 'text') continue;
			$f = str_replace('count_','count',$f);
			$m->$f = $v;
		}
		try
		{
			$created = $m->deliver();
			$Class = $urn->entity->getClass();
			if (method_exists($Class,'fromXMLextractor'))
			{
				$Class->fromXMLextractor($doc);
			}
			if ($imported_callback_each) $imported_callback_each($created);
		}
		catch (Exception $e) 
		{
			//println($m);
			println($e->getMessage(),1,TERM_RED);
			//continue;
			return;
		}
		
		
		$entries = $domx->evaluate("//data/lists/list");
		foreach ($entries as $n) 
		{
			$f = $n->getAttribute('name');
			$cu = $created->urn;
			$cu->set_list($f);
			foreach ($n->childNodes as $li)
			{
				if ($li->nodeType == XML_TEXT_NODE) continue; 
				$liurn = $li->nodeValue;
				$m = new Message();
				$m->action = 'add';
				$m->urn = $liurn;
				$m->to = (string) $cu;
				try
				{
					//println($m,1,TERM_GREEN);
					$m->deliver();
				}
				catch (Exception $e) 
				{
					println($e->getMessage(),1,TERM_RED);
				}
			}
		}
		
		$entries = $domx->evaluate("//data/related/rel");
		foreach ($entries as $n) 
		{
			$f = $n->getAttribute('name');
			$cu = $created->urn;
			$cu->set_list($f);
			foreach ($n->childNodes as $li)
			{
				if ($li->nodeType == XML_TEXT_NODE) continue; 
				$liurn = $li->nodeValue;
				$m = new Message();
				$m->action = 'add';
				$m->urn = $liurn;
				$m->to = (string) $cu;
				try
				{
					//println($m,1,TERM_VIOLET);
					$m->deliver();
				}
				catch (Exception $e) 
				{
					println($e->getMessage(),1,TERM_RED);
				}
			}
		}
	}
	
}
?>