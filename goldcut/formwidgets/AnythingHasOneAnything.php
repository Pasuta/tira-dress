<?php 

class AnythingHasOneAnything extends FormWidget
{
	protected function build()
	{
		$ff = $this->subject->name;
		
		$ENTITY = Entity::ref($ff);
		$m = new Message();
		$m->urn = 'urn-'.$ENTITY->name;
		$m->action = 'load';
		
		if ($ENTITY->defaultorder)
		{
			$m->order = $ENTITY->defaultorder;
		}
		$data = $m->deliver();
		
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
		}
		
		// usedas
		if ($this->usedas)
			$MO = array($this->subject, $this->usedas, $usedTitle);
		else
			$MO = $this->subject;
		
		$this->html .= Form::category_selectbox($MO, $this->data, $selected_urns, true, false);
	}	
}	

?>