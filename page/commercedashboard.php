<?php

namespace xepan\commerce;

class page_commercedashboard extends \Page{
	public $title = "Dashboard";	
	function init(){
		parent::init();

		// $this->add('View_Info')->set('Graphical Information About Commerce');
	}

	function defaultTemplate()
	{
		return['page/dashboard/commerce'];
	}
}