<?php

//namespace formwidgets;

class AnythingUseOneAnything extends FormWidget
{
	protected function build()
	{
		$ff = $this->subject->name;
		$ObjEntityMeta = $this->object;

		$ENTITY = Entity::ref($ff);
					
		$m = new Message();
		$m->urn = 'urn-'.$ENTITY->name;
		$m->action = 'load';
		if ($ENTITY->defaultorder)
			$m->order = $ENTITY->defaultorder;
		// if ($ENTITY->has_field('_parent')) $m->_parent = 'NULL';
		$data = $m->deliver();
		// $data->treesort();
		
		//$this->setData($this->eo->$ff);
		$this->setData($data);
		
		// usedas
		if ($this->usedas != $ff) 
		{
			$ff = $this->usedas;
			$usedTitle = $this->object->astitles[$ff];
		}
		
		$sel = $this->eo->$ff;
		if (count($sel))
		{
			$selected_urns = $sel->asURNs();
			if (count($selected_urns) == 1) $selTitle = $sel->title;
		}
	
		if (in_array($ff, $ObjEntityMeta->extendstructure) && count($selected_urns)) {
			$this->html = "<br><p>Смена рубрики <b>{$selTitle}</b> не допускается для расширенной модели данных</p>";
			return;
		}
	
		//$this->html .= Form::category_selectbox($this->subject, $this->data, $selected_urns, true, false);
		// usedas
		if ($this->usedas)
			$MO = array($this->subject, $this->usedas, $usedTitle);
		else
			$MO = $this->subject;
		
		$this->html .= Form::category_selectbox($MO, $this->data, $selected_urns, true, false);
		
	}
	
}

?>