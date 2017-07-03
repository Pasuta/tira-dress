<?php

class AdminPlugin extends RowPlugin
{
	public function adminview()
	{
		$uri = $this->ROW->name;
		return $uri;
	}
}