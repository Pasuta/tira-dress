<?php

class CategoryPlugin extends RowPlugin {

	public function adminview() {
		return $this->ROW->title;
	}

}