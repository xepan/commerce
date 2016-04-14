<?php

namespace xepan\commerce;

class Tool_Accountviews_Myaccount extends \View{
	function init(){
		parent::init();
	}

	function defaultTemplate(){
		return['view\tool\accountmain_info'];
	}
}