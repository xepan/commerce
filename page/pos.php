<?php

namespace xepan\commerce;

class page_pos extends \Page{
	public $title = "xEpan POS";
	function init(){
		parent::init();

		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('pos')->xepan_pos(['show_custom_fields'=>true]);

	}

	function defaultTemplate(){
		return ['page\pos'];
	}

}