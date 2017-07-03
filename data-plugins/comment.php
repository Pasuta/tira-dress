<?php

class CommentPlugin extends RowPlugin
{
	public function adminview()
	{
		$uri = $this->ROW->name;
		return $uri;
	}

    public function date()
    {
        return date('d.m.y H:i',$this->ROW->created);
    }
}