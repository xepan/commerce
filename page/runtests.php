<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\commerce;


class page_runtests extends \xepan\base\Page_TestRunner {
	
	public $title='xEpan Commerce Tests';
	public $dir='tests';
	public $namespace = __NAMESPACE__;

	function init(){
		if(!set_time_limit(0)) throw new \Exception("Could not limit time", 1);
		
		parent::init();
	}

}
