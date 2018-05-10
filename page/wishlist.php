<?php

namespace xepan\commerce;

class page_Wishlist extends \xepan\base\Page{
    public $title = "Test Page";

    function init(){
		parent::init();
			/*$grid->addQuicksearch(['id']);*/
			$crud = $this->add('xepan\base\CRUD');
			/*$crud = $this->add('CRUD');*/
			$model = $this->add('xepan\commerce\Model_wishlist');
			$crud->setModel($model);


			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->makePanelCollepsible(true)
				->layout([
						'item_id'=>'Add New Item~c1~6',
						'create_at'=>'c2~6',
						'status'=>'c3~12',
						
					]);

		}

		}

