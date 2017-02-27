<?php

namespace xepan\commerce;

class page_pos extends \Page{
	public $title = "xEpan POS";
	function init(){
		parent::init();

	}

	function defaultTemplate(){
		return ['page\pos'];
	}

}