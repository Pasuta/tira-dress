<?php

class ItemPlugin extends RowPlugin {

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

    public function disc() {
        $d = $this->ROW->discount ? ($this->ROW->price / 100) * $this->ROW->discount : false;
        if ($d) {
          $d = round($d);
        }
        return $d;
    }

    public function priceShow() {
        if (!$this->ROW->price) return "Цену уточняйте по телефону";
        if ($this->ROW->price && $this->ROW->disc) {
            return "<span style='text-decoration:line-through '>{$this->ROW->price} Грн</span> <b>{$this->ROW->disc} Грн</b>";
        }
        return $this->ROW->price . " Грн";
    }
}