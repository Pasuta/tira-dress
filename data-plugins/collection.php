<?php

class CollectionPlugin extends RowPlugin {

	public function adminview() {
		return $this->ROW->title;
	}

}