<?php

class EntityDump
{
	static function report()
	{
		$usage = array();
		foreach (Entity::each_managed_entity(null, null) as $m => $es)
		{
			foreach($es as $e)
			{
				if ($e->is_system() && $e->name != 'user') continue;
				foreach ($e->belongs_to() as $er)
				{
					if (!is_array($usage[$er->name])) $usage[$er->name] = array();
					if (!in_array($e->name, $usage[$er->name])) array_push($usage[$er->name], $e->name.'(@)');
				}
				foreach ($e->has_many() as $er)
				{
					if (!is_array($usage[$er->name])) $usage[$er->name] = array();
					if (!in_array($e->name, $usage[$er->name])) array_push($usage[$er->name], $e->name.'(>>)');
				}
				foreach ($e->lists() as $list)
				{
					$rel = $list['entity'];
					$listname = $list['name'];
					$er = $rel;
					if (!is_array($usage[$er->name])) $usage[$er->name] = array();
					if (!in_array($e->name, $usage[$er->name])) array_push($usage[$er->name], $e->name."(L/{$listname})");
				}
				foreach ($e->has_one() as $usedas => $er)
				{
					if (!is_array($usage[$er->name])) $usage[$er->name] = array();
					$sufx = '(.1)';
					if ($usedas != $er->name) $sufx = "(.1/{$usedas})";
					if (!in_array($e->name, $usage[$er->name])) array_push($usage[$er->name], $e->name.$sufx);
				}
				foreach ($e->use_one() as $usedas => $er)
				{
					if (!is_array($usage[$er->name])) $usage[$er->name] = array();
					$sufx = '(~1)';
					if ($usedas != $er->name) $sufx = "(~1/{$usedas})"; 
					if (!in_array($e->name, $usage[$er->name])) array_push($usage[$er->name], $e->name.$sufx);
				}
			}
		}
		
		foreach (Entity::each_managed_entity(null, null) as $m => $es)
		{
			$ess = array();
			foreach($es as $e)
			{
				if ($e->is_system() && $e->name != 'user') continue;
				$ess[] = $e;
			}
			if (!count($ess)) continue;
			
			printH($m);
			foreach($ess as $e)
			{
				$status = '';
				$statuses = null;
				$cby = null;
				$fields = null;
				$bt = null;
				$bt_list = null;
				$ho = null;
				$ho_list = null;
				$hm = null;
				$hm_list = null;
				$re = null;
				$re_list = null;
	
				foreach ($e->statuses as $statusid)
					$statuses[] = Status::ref($statusid)->name . " - " . Status::ref($statusid)->title;
				if (count($statuses)>0)
					$status = "+-[" . join(', ', $statuses) . "]";
	
				foreach ($e->general_fields() as $F)
					$fields[] = $F->name . " - " . $F->type; // . '#'. $F->uid
				if (count($fields)>0)
					$fields_list = join(', ', $fields);
	
				foreach ($e->lang_fields() as $F)
					$fields[] = $F->name . " - " . $F->type; // . '#'. $F->uid
				if (count($fields)>0)
					$fields_list .= join(', ', $fields);
	
	
				foreach ($e->belongs_to() as $EBT)
					$bt[] = $EBT->name . " - " . $EBT->title['ru'];
				if (count($bt)>0)
					$bt_list = "@ " . join(', ', $bt);
	
				foreach ($e->has_many() as $EHM)
					$hm[] = $EHM->name . " - " . $EHM->title['ru'];
				if (count($hm)>0)
					$hm_list = ">> " . join(', ', $hm);
				/*
				foreach ($e->related() as $RE)
					$re[] = $RE->name . " - " . $RE->title['ru']; //  . ' #' . $RE->uid
				if (count($re)>0)
					$re_list = "~~ " . join(', ', $re);
				*/
				foreach ($e->lists() as $list)
				{
					$rel = $list['entity'];
					$listname = $list['name'];
					if ($listname != $rel->name)
						$re[] = $listname . '(' . $rel->name .')'; // . " - " . $rel->title['ru'];
					else
						$re[] = $rel->name;
					$re_list = "LIST " . join(', ', $re);
				}
				
				foreach ($e->has_one() as $usedas => $EHO)
				{
					if ($usedas != $EHO->name) $usedas = $EHO->name . ' as ' . $usedas;
					$ho[] = $usedas . " - " . $EHO->title['ru'];
				}
				
				foreach ($e->use_one() as $usedas => $EHO)
				{
					if ($usedas != $EHO->name) $usedas = $EHO->name . ' as ' . $usedas;
					$ho[] = $usedas . " - " . $EHO->title['ru'];
				}
				
				if (count($ho)>0)
					$ho_list = "1 " . join(', ', $ho);

                if ($e->imagesettings)
                {
                    $moa = "IMAGE: {$e->imagesettings['mainimage']['paradigm']} {$e->imagesettings['mainimage']['size']['dim']} {$e->imagesettings['mainimage']['size']['size']}. ";
                    if ($e->imagesettings['previews']) $moa .= "Previews: {$e->imagesettings['previews']['paradigm']}";
                    $psa = array();
                    foreach ($e->imagesettings['previews']['sizes'] as $psize)
                    {
                        $psp = $psize['dim'];
                        $pss = $psize['size'];
                        $psa[]= $pss;
                    }
                    $moa .= ' '. $psp . ' ' . join(', ',$psa);
                }
                else $moa = '';

                //var_dump($e->multy_lang);
                $langs = "";
                if ($e->is_multy_lang()) $langs = " [Переводы: ".join(', ', $e->lang_codes())."] ";

				println(strtoupper($e)." - {$e->title['ru']}{$langs} {$moa}", 1); //  - {$e->class} // #{$e->uid}
	
				if (count($usage[$e->name])) 
				{
					sort($usage[$e->name]);
					println("used in: ".join(', ',$usage[$e->name]),1,TERM_GRAY);
				}
				
				print "\t";
				$machine = array();
				//$i=0;
				foreach ($e->general_fields() as $F)
				{
					//$i++;
					$sfx = "";
					//if ($i == 1) $sfx = "\t";
					$machine []= "'".$F->name."'";
					printColor($sfx.$F->name,TERM_VIOLET);
					print ":";
					printColor(substr($F->type,0,1),TERM_BLUE);
					//print '#'. $F->uid;
					print ", ";
				}
				//print "[".join(',',$machine)."]";
	
				if ($e->is_multy_lang())
					print "MULTYLANG ";
	
				foreach ($e->lang_fields() as $F)
				{
					printColor($F->name,TERM_VIOLET);
					print ": ";
					printColor($F->type,TERM_BLUE);
					//print '#'. $F->uid;
					print ", ";
				}
				
				if ($cby)
					println($cby,2,TERM_GREEN);
				if (count($statuses)>0)
					println($status,2,TERM_BLUE);
				if (count($bt)>0)
					println("{$bt_list}",2,TERM_GREEN);
				if (count($ho)>0)
					println("{$ho_list}",2,TERM_GREEN);
				if (count($hm)>0)
					println("{$hm_list}",2,TERM_YELLOW);
				if (count($re)>0)
					println("{$re_list}",2,TERM_YELLOW);
				
	
				//printLine();
				print "\n";
	
			}
	}
	}

}
?>