<?php

class grid_crowd_cards_box extends  grid_list_box {
	public function __construct()
	{
		parent::__construct();
	}

	public function type()
	{
		return 'crowd_cards';
	}

	public function build($editmode) {
		if($editmode){
			return 'Crowd Cards';
		}
		
		return $this->content;
	}

	public function contentStructure() {
		$cs = parent::contentStructure();
		return $cs;
	}
}
