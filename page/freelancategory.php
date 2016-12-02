<?php

namespace xepan\commerce;

/**
* 
*/
class page_freelancategory extends \xepan\commerce\page_configurationsidebar{
	public $title = "Freelancer Category";
	function init(){
		parent::init();

		$category = $this->add('xepan\commerce\Model_FreelancerCategory');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/freelancer/category-grid']);
		$crud->setModel($category);
	}
}