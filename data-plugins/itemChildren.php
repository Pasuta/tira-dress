<?php

class ItemChildrenPlugin extends RowPlugin {

	public function adminview() {
		$uri = $this->ROW->name;
		return $uri;
	}

    public function onephoto() {
        $uri = $this->ROW->mainphoto->first()->image->uri;
        return "<div style='background: url({$uri}) 0 50% no-repeat;background-size: cover;width: 50px;height: 50px'></div>";
    }
    public function onephotov() {
        $uri = $this->ROW->mainphotov->image->uri;
        return "<div style='background: url({$uri}) 0 50% no-repeat;background-size: cover;width: 50px;height: 50px'></div>";
    }

    public function getphoto(){
        $ret = array();
        $i = 0;
        foreach ($this->ROW->mainphoto as $photo) {
            $ret[++$i] = $photo->image->uri;
        }

        return $ret;
    }

    public function mp(){
        $p = '';
        foreach ($this->ROW->mainphoto as $photo) {
            $p = $photo;
            break;
        }
        return $p;

    }
}