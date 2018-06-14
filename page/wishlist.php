<?php

namespace xepan\commerce;

class page_wishlist extends \xepan\base\Page{
    public $title = "Wish List Page";

    function init(){
	parent::init();

		$crud = $this->add('xepan\base\CRUD');
		$model = $this->add('xepan\commerce\Model_Wishlist');
		$crud->setModel($model);
	}
}


