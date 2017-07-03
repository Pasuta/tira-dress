<?php

class ReviewPlugin extends RowPlugin
{
	public function adminview()
	{
		$uri = $this->ROW->name;
		return $uri;
	}

    public function date()
    {
        $date = date('H:i d.m.y',$this->ROW->created);
        return $date;
    }
}