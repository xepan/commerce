<?php

namespace xepan\commerce;

class page_wishlist extends \xepan\base\Page{
    public $title = "Wish List Page";

    function init(){
	parent::init();


		$crud = $this->add('xepan\hr\CRUD');
		$model = $this->add('xepan\commerce\Model_Wishlist');
		$model->add('xepan\base\Controller_TopBarStatusFilter');
		$crud->setModel($model);

		$crud->grid->removeAttachment();

		$crud->grid->addQuickSearch(['contact','item']);
		$crud->grid->addPaginator(25);
	}
}


