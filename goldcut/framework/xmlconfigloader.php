<?php
function groupsfeed(&$n, &$groups, &$virtualgroups, &$groupnames)
{
	$pname = null;
	if (($pn = $n->parentNode) && $pn->tagName == 'group')
	{
		$fname = $n->getAttribute('name');
		if (!$fname) // not field, entity
		{
			$enamebase = $n->getAttribute('entity');
			$enameas = $n->getAttribute('as');
			if ($enameas) $fname = $enameas;
			else $fname = $enamebase;
		}
		$pname = $n->parentNode->getAttribute('name');
		$ptitle = $n->parentNode->getAttribute('title');
		if (!$fbasename) $fbasename = $fname;
		if ($pname) 
		{
			$fname = $pname.'_'.$fname;
			$groupnames[$pname] = $ptitle; // group name > group title (a[groupname])
			$groups[$fname] = $ptitle; // (a[field_name])
		}
		else // if ($fbasename && !$pname)
		{
			$virtualgroups[$fbasename] = $ptitle; // field name > is in virtual (no name_ prefix) group title
		}
	}
	return $pname;
}

class XMLConfigLoader
{
	private static $filepath;
	
	public static function load($filepath, $type)
	{
		self::$filepath = $filepath;
		$doc = new DOMDocument();
		$doc->load($filepath);
		if ($type == 'entity') self::loadEntity($doc);
	}
	
	private static function loadEntity($doc)
	{
		if (!$doc->documentElement) throw new Exception("Error in config file ".self::$filepath);
		$entityname = $doc->documentElement->getAttribute('name');
		$euid = (int) $doc->documentElement->getAttribute('uid');
		if (ENV == 'DEVELOPMENT' && $GLOBALS['CONFIG']['ENTITY'][$euid] && !$GLOBALS['CONFIG']['ENTITY'][$euid]->is_system()) throw new Exception("Duplicate Entity UID ".self::$filepath." already used by ".$GLOBALS['CONFIG']['ENTITY'][$euid]->name);
		$manager = $doc->documentElement->getAttribute('manager');
		$passportTitle = $doc->getElementsByTagName('title');
		$domx = new DOMXPath($doc);
		$entries = $domx->evaluate("//passport/title");
		foreach ($entries as $entry) {
			$entityTitle = $entry->nodeValue;
		}
		
		$field_metas = array();
		$hasone_entities = array();
		$useone_entities = array();
		$belongsto_entities = array();
		$hasmany_entities = array();
		$usemany_entities = array();
		$lists = array();
		$statuses = array();
		$usereditfields = array();
		$astitles = array();
		$usereditfieldsOrdered = array();
		$allOrdered = array();
		$required = array();
		
		// ORDERED ALL
		$domx = new DOMXPath($doc);
		$entries = $domx->evaluate("//states/status");
		foreach ($entries as $n) 
		{
			$fname = $n->getAttribute('name');
			array_push($allOrdered, $fname);
			$userEditable = $n->getAttribute('usereditable'); 
			if ($userEditable == 'yes') array_push($usereditfieldsOrdered, $fname);
		}

        $multy_lang = array();
        $entries = $domx->evaluate("//international//language");
        foreach ($entries as $n)
        {
            $lang = $n->getAttribute('code');
            array_push($multy_lang, $lang);
        }

		$entries = $domx->evaluate("//structure//*");
		foreach ($entries as $n) 
		{
			$fname = $n->getAttribute('name');
			if ($fname) // field
			{
				if (($pn = $n->parentNode) && $pn->tagName == 'group')
				{
					$pname = $n->parentNode->getAttribute('name');
					if ($pname) $fname = $pname.'_'.$fname;
				}
				array_push($allOrdered, $fname);
				$userEditable = $n->getAttribute('usereditable'); 
				if ($userEditable == 'yes') array_push($usereditfieldsOrdered, $fname); 
				if ($n->getAttribute('required') == 'yes') array_push($required, $fname);
			}
			else // entity
			{
				$enamebase = $n->getAttribute('entity');
				$enameas = $n->getAttribute('as');
				if ($enameas) $ename = array($enameas => $enamebase);
				elseif ($enamebase) $ename = $enamebase;
				else continue;
				/*
				if (($pn = $n->parentNode) && $pn->tagName == 'group')
				{
					$pname = $n->parentNode->getAttribute('name');
					if ($pname) $ename = $pname.'_'.$ename;
				}
				*/
				array_push($allOrdered, $ename);
				$userEditable = $n->getAttribute('usereditable'); 
				if ($userEditable == 'yes') array_push($usereditfieldsOrdered, $ename);
				if ($n->getAttribute('required') == 'yes') array_push($required, $ename);
			}
		}
		$entries = $domx->evaluate("//lists/list");
		foreach ($entries as $n) 
		{
			$fname = $n->getAttribute('name');
			array_push($allOrdered, $fname);
			$userEditable = $n->getAttribute('usereditable'); 
			if ($userEditable == 'yes') array_push($usereditfieldsOrdered, $fname);
		}
		//if ($entityname == 'user') println($allOrdered);
		//if ($entityname == 'user') println($usereditfieldsOrdered,1,TERM_GREEN);
		
		// ORDERED USER EDITABLES
		/*
		$domx = new DOMXPath($doc);
		$entries = $domx->evaluate("//*[@usereditable]"); // //structure/*[@usereditable]
		foreach ($entries as $n) {
			$userEditable = $n->getAttribute('usereditable'); 
			if ($userEditable != 'yes') continue; 
			//$nodename = $n->nodeName;
			$fname = $n->getAttribute('name');
			if ($fname) // field
			{
				array_push($usereditfieldsOrdered, $fname);
			}
			else // entity
			{
				$enamebase = $n->getAttribute('entity');
				$enameas = $n->getAttribute('as');
				if ($enameas) $ename = array($enameas => $enamebase);
				else $ename = $enamebase;
				array_push($usereditfieldsOrdered, $ename);
			}
		}
		*/
		
		// GET FIELDS
        $lang_field_metas = array();
		$nds = $doc->getElementsByTagName('field');
		foreach ($nds as $n) 
		{
			$fname = $n->getAttribute('name');
			//$fbasename = $n->getAttribute('base');
			$userEditable = $n->getAttribute('usereditable');
            $international = ($n->getAttribute('role') == 'international') ? true : false;
            if ($international) $lang_field_metas[] = $fname;
			$pname = groupsfeed($n, $groups, $virtualgroups, $groupnames);
			if ($pname) 
				$fsname = array($pname.'_'.$fname => $fname);
			else 
				$fsname = $fname;
			array_push($field_metas, $fsname);
			//if ($userEditable == 'yes') array_push($usereditfields, $pname.'_'.$fname);
		}
		foreach ($doc->getElementsByTagName('status') as $n) {
			$enamebase = $n->getAttribute('name');
			array_push($statuses, $enamebase);
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $enamebase);
		}
		foreach ($doc->getElementsByTagName('hasmany') as $n) {
			$enamebase = $n->getAttribute('entity');
			array_push($hasmany_entities, $enamebase);	
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $ename);
		}
		foreach ($doc->getElementsByTagName('usemany') as $n) {
			$enamebase = $n->getAttribute('entity');
			if (!$enamebase) throw new Exception('No entity attribute in usemany relation');
			array_push($usemany_entities, $enamebase);	
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $ename);
		}
		foreach ($doc->getElementsByTagName('belongsto') as $n) {
			$enamebase = $n->getAttribute('entity');
			if (!$enamebase) throw new Exception('No entity attribute in belongsto relation');
			array_push($belongsto_entities, $enamebase);
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $ename);
		}
		$nds = $doc->getElementsByTagName('hasone');
		foreach ($nds as $n) {
			$enamebase = $n->getAttribute('entity');
			if (!$enamebase) throw new Exception('No entity attribute in hasone relation');
			$enameas = $n->getAttribute('as');
			$astitle = $n->getAttribute('title');
			groupsfeed($n, $groups, $virtualgroups, $groupnames);
			if ($astitle) $astitles[$enameas] = $astitle;
			if ($enameas) $ename = array($enameas => $enamebase);
			else $ename = $enamebase;
			array_push($hasone_entities, $ename);	
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $ename);
		}
		$nds = $doc->getElementsByTagName('useone');
		foreach ($nds as $n) {
			$enamebase = $n->getAttribute('entity');
			if (!$enamebase) throw new Exception('No entity attribute in useone relation');
			$enameas = $n->getAttribute('as');
			if ($enameas) throw new Exception("Dont use AS in useone. Hasone only");
			$astitle = $n->getAttribute('title');
			groupsfeed($n, $groups, $virtualgroups, $groupnames);
			if ($astitle) $astitles[$enameas] = $astitle;
			if ($enameas) $ename = array($enameas => $enamebase);
			else $ename = $enamebase;
			array_push($useone_entities, $ename);
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $ename);
		}
		$nds = $doc->getElementsByTagName('list');
		foreach ($nds as $n) {
			$enamebase = $n->getAttribute('entity');
			$enameas = $n->getAttribute('name');
			$ns = (int) $n->getAttribute('ns');
			$listtitle = $n->getAttribute('title');
			$membership = $n->getAttribute('membership'); // shared or exclusive
			$graph = ($n->getAttribute('graph') == 'true') ? true : false;
			$reverse = $n->getAttribute('reverse');
			$notify = $n->getAttribute('notify');
			array_push($lists, array('ns'=>$ns, 'entity'=>$enamebase, 'name'=>$enameas, 'title'=>$listtitle, 'notify'=>$notify, 'graph'=>$graph, 'reverse'=>$reverse, 'membership'=>$membership, 'order' => array('id'=>'desc') ));
			//$userEditable = $n->getAttribute('usereditable'); 
			//if ($userEditable == 'yes') array_push($usereditfields, $enameas);
		}
		$reverserelated = array();
		foreach ($doc->getElementsByTagName('reverserelated') as $n) {
			$enameas = $n->getAttribute('name');
			array_push($reverserelated, $enameas);
		}
		$adminfields = array();
		foreach ($doc->getElementsByTagName('column') as $n) {
			$col = $n->getAttribute('selector');
			array_push($adminfields, $col);
		}

        /*
         * LEGACY
		$mediaoptions = array();
		foreach ($doc->getElementsByTagName('image') as $n) {
			$o = array();
			$name = $n->getAttribute('name');
			$o[] = $n->getAttribute('size');
			($n->getAttribute('fx')) ? ($o[] = 'fx_'.$n->getAttribute('fx')) : null;
			($n->getAttribute('crop') == 'yes') ? ($o[] = 'crop') : null;
			($n->getAttribute('trim') == 'yes') ? ($o[] = 'trim') : null;
			($n->getAttribute('watermark') == 'yes') ? ($o[] = 'watermark') : null;
			$complexsize = join(':',$o);
			$mediaoptions[$name] = $complexsize;
		}
        */
		
		$imagesettings = array();
		$imagesettingsImage = $domx->evaluate("//imagesettings/mainimage");
		foreach ($imagesettingsImage as $mi) 
		{
			$paradigm = $mi->getAttribute('paradigm');
			$hd = $mi->getAttribute('hd');
			$watermark = $mi->getAttribute('watermark');
			$imagesettings['mainimage'] = array();
			$imagesettings['mainimage']['paradigm'] = $paradigm;
			$imagesettings['mainimage']['hd'] = $hd;
			foreach ($mi->getElementsByTagName('size') as $sx) {
				$size = domGetImageDimSize($sx);
				//println($size);
				$imagesettings['mainimage']['size'] = $size;
				$verticalfixed = $sx->getAttribute('verticalfixed');
				$horizontalfixed = $sx->getAttribute('horizontalfixed');
				$verticalmin = $sx->getAttribute('verticalmin');
				$verticalmax = $sx->getAttribute('verticalmax');
				$horizontalmin = $sx->getAttribute('horizontalmin');
				$horizontalmax = $sx->getAttribute('horizontalmax');
				if ($verticalfixed) $imagesettings['mainimage']['size']['verticalfixed'] = $verticalfixed;
				if ($horizontalfixed) $imagesettings['mainimage']['size']['horizontalfixed'] = $horizontalfixed;
				if ($verticalmin) $imagesettings['mainimage']['size']['verticalmin'] = $verticalmin;
				if ($verticalmax) $imagesettings['mainimage']['size']['verticalmax'] = $verticalmax;
				if ($horizontalmin) $imagesettings['mainimage']['size']['horizontalmin'] = $horizontalmin;
				if ($horizontalmax) $imagesettings['mainimage']['size']['horizontalmax'] = $horizontalmax;
			}
		}
		$imagesettingsPreviews = $domx->evaluate("//imagesettings/previews");
		foreach ($imagesettingsPreviews as $mi) 
		{
			$paradigm = $mi->getAttribute('paradigm');
			$hd = $mi->getAttribute('hd');
			$watermark = $mi->getAttribute('watermark');
			$reframe = $mi->getAttribute('reframe');
			$verticalfixed = $mi->getAttribute('verticalfixed');
			$horizontalfixed = $mi->getAttribute('horizontalfixed');
			$verticalmin = $mi->getAttribute('verticalmin');
			$verticalmax = $mi->getAttribute('verticalmax');
			$horizontalmin = $mi->getAttribute('horizontalmin');
			$horizontalmax = $mi->getAttribute('horizontalmax');
			$imagesettings['previews'] = array();
			/*
			if ($verticalfixed) $imagesettings['previews']['verticalfixed'] = $verticalfixed;
			if ($horizontalfixed) $imagesettings['previews']['horizontalfixed'] = $horizontalfixed;
			if ($verticalmin) $imagesettings['previews']['verticalmin'] = $verticalmin;
			if ($verticalmax) $imagesettings['previews']['verticalmax'] = $verticalmax;
			if ($horizontalmin) $imagesettings['previews']['horizontalmin'] = $horizontalmin;
			if ($horizontalmax) $imagesettings['previews']['horizontalmax'] = $horizontalmax;
			*/
			$imagesettings['previews']['paradigm'] = $paradigm;
			$imagesettings['previews']['hd'] = $hd;
			$imagesettings['previews']['sizes'] = array();
			foreach ($mi->getElementsByTagName('size') as $sx) {
				$sizename = $sx->getAttribute('name');
				if ($sizename == 'thumbnail') throw new Exception('Dont use `thumbnail` as preview image name');
				$base64store = $sx->getAttribute('base64');
				$base64store = txt2boolean($base64store);
				$size = domGetImageDimSize($sx);
				$imagesettings['previews']['sizes'][$sizename] = $size;
				$imagesettings['previews']['sizes'][$sizename]['base64'] = $base64store;
			}
		}
		if ($manager == 'Photo' and !$imagesettings) throw new Exception("$entityname managed by Photo by have no xml imagesettings section");
		
		$directmanage = true;
		$translit = null;
		$options = array();
		foreach ($doc->getElementsByTagName('aparam') as $n) {
			$oname = $n->getAttribute('name');
			$oval = $n->getAttribute('value');
			if ($n->getAttribute('type')=="boolean") $oval = txt2boolean($oval);
			$options[$oname] = $oval;
			if ($oname == 'directmanage') $directmanage = $oval;
			if ($oname == 'clonable') $clonable = $oval;
			if ($oname == 'translit' && $oval == 'legacytitle2uri') $translit = array('title'=>'uri');
			if ($oname == 'extendstructure') 
			{
				$extendstructure = array($oval); // extended structure
				array_unshift($allOrdered, 'extended');
			}
			else $extendstructure = array();
		}
		foreach ($doc->getElementsByTagName('param') as $n) {
			$oname = $n->getAttribute('name');
			$oval = $n->getAttribute('value');
			if ($n->getAttribute('type')=="boolean") $oval = txt2boolean($oval);
			if ($oname == 'treeview') $treeview = $oval;
		}
		$indexes = array();
		foreach ($doc->getElementsByTagName('index') as $n) {
			array_push($indexes, $n->getAttribute('column'));
		}
		$uniqs = array();
		foreach ($doc->getElementsByTagName('unique') as $n) {
			array_push($uniqs, $n->getAttribute('column'));
		}
		$defaultorder = array();
		foreach ($doc->getElementsByTagName('by') as $n) { // defaultorder
			$field = (string) $n->getAttribute('field');
			$order = (string) $n->getAttribute('order');
			$defaultorder[$field] = $order;
		}
		$searchtextin = array();
		foreach ($doc->getElementsByTagName('searchin') as $n) {
			array_push($searchtextin, $n->getAttribute('column'));
		}
		$adminsearchtextin = array();
		foreach ($doc->getElementsByTagName('adminsearchin') as $n) {
			array_push($adminsearchtextin, $n->getAttribute('column'));
		}
		// REPLACE general $usereditfields with right ORDERED
		$usereditfields = $usereditfieldsOrdered;
		$GLOBALS['CONFIG']['ENTITY'][$euid] = new EntityMeta(array('uid'=>$euid,'class'=>$manager, 'name'=> $entityname, 'title'=>array('ru'=>$entityTitle, 'en'=>$entityTitle),
            "multy_lang"=> $multy_lang, "lang_field_metas" => $lang_field_metas, 'statuses'=>$statuses,'field_metas'=>$field_metas,
            "has_one" => $hasone_entities, "use_one" => $useone_entities, "has_many"=>$hasmany_entities, 'usemany_entities'=>$usemany_entities, "belongs_to" => $belongsto_entities,
            'lists' => $lists, 'reverserelated' => $reverserelated, 'adminfields' => $adminfields,
            'imagesettings' => $imagesettings, 'options' => $options, 'directmanage' => $directmanage, 'translit' => $translit,
            'index' => $indexes, 'checkunique' => $uniqs, 'usereditfields' => $usereditfields, 'defaultorder'=>$defaultorder, 'clonable'=>$clonable,
            'searchtextin'=>$searchtextin, 'adminsearchtextin'=>$adminsearchtextin,
            'groupnames'=> $groupnames, 'groups'=>$groups, 'virtualgroups'=>$virtualgroups, 'astitles'=>$astitles, 'allOrdered'=>$allOrdered,
            'required'=>$required, 'treeview'=>$treeview,
			'extendstructure'=>$extendstructure));
            //'mediaoptions' => $mediaoptions,
	}

    public static function loadoverlay($filepath, $type) // $type == 'entity'
    {
        $field_metas = array();
        $hasone_entities = array();
        $useone_entities = array();
        $belongsto_entities = array();
        $hasmany_entities = array();
        $usemany_entities = array();
        $lists = array();
        $statuses = array();

        $doc = new DOMDocument();
        $doc->load($filepath);
        if (!$doc->documentElement) throw new Exception("Error in config file ".$filepath);
        $entityname = $doc->documentElement->getAttribute('name');

        $E = Entity::ref($entityname);

        $nds = $doc->getElementsByTagName('field');
        foreach ($nds as $n)
        {
            $fname = $n->getAttribute('name');
            array_push($field_metas, $fname);
        }
        $E->extend('field_metas', $field_metas);
        foreach ($doc->getElementsByTagName('status') as $n) {
            $enamebase = $n->getAttribute('name');
            array_push($statuses, $enamebase);
        }
        $E->extend('statuses', $enamebase);
        foreach ($doc->getElementsByTagName('hasmany') as $n) {
            $enamebase = $n->getAttribute('entity');
            array_push($hasmany_entities, $enamebase);
        }
        $E->extend('has_many', $enamebase);
        foreach ($doc->getElementsByTagName('belongsto') as $n) {
            $enamebase = $n->getAttribute('entity');
            array_push($belongsto_entities, $enamebase);
        }
        $E->extend('belongs_to', $enamebase);
        $nds = $doc->getElementsByTagName('hasone');
        foreach ($nds as $n) {
            $ename = $n->getAttribute('entity');
            array_push($hasone_entities, $ename);
        }
        $E->extend('has_one', $hasone_entities);
        // lists overlay
        $nds = $doc->getElementsByTagName('list');
        foreach ($nds as $n) {
            $enamebase = $n->getAttribute('entity');
            $enameas = $n->getAttribute('name');
            $ns = (int) $n->getAttribute('ns');
            $listtitle = $n->getAttribute('title');
            $membership = $n->getAttribute('membership'); // shared or exclusive
            $graph = ($n->getAttribute('graph') == 'true') ? true : false;
            $reverse = $n->getAttribute('reverse');
            $notify = $n->getAttribute('notify');
            array_push($lists, array('ns'=>$ns, 'entity'=>$enamebase, 'name'=>$enameas, 'title'=>$listtitle, 'notify'=>$notify, 'graph'=>$graph, 'reverse'=>$reverse, 'membership'=>$membership, 'order' => array('id'=>'desc') ));
        }
        $E->extend('lists', $lists);

    }
}
/**
function processStructure($x)
{
	foreach($x->childNodes as $node)
	{
		if ($node->nodeType == 1) 
		{
			$name = $node->getAttribute('name');
			println("$node->tagName $name",2,TERM_VIOLET);
			if ($node->tagName == 'group')
			{
				foreach($node->childNodes as $nodeinner)
				{
					if ($nodeinner->nodeType == 1) 
					{
						$name = $nodeinner->getAttribute('name');
						println("$nodeinner->tagName $name",3,TERM_VIOLET);
					}
				}
			}
		}
	}
}
foreach($doc->documentElement->childNodes as $node)
{
	if ($node->nodeType == 1) 
	{
		println("$node->tagName",1,TERM_GREEN);
		if ($node->tagName == 'structure') processStructure($node);
	}
}
		*/
?>