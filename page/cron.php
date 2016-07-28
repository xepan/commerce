<?php

namespace xepan\commerce;

class page_cron extends \Page{
	
	function init(){
		parent::init();

		$item = $this->add('xepan\commerce\Model_QSP_Detail');
		$item->renewableService();
	}
}