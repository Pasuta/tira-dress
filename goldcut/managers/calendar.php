<?php

class Calendar extends EManager
{
	protected function config()
	{
		$this->behaviors[] = 'general_crud';
	}
}

?>