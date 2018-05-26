<?php

namespace xepan\commerce;

class View_Wishlist extends \View{
	public $customer_id = null;
	public $show_status = 'Due,Complete';
	public $paginator = 10;
	public $detail_page = null;
	

	function init(){
	parent::init();

			$status_array = explode(",", $this->show_status);
			$this->status_count = count($status_array);

			if($this->status_count == 1)
				$tab = $view = $this->add('View');
			else
				$view = $this->add('Tabs');

			foreach ($status_array as $status) {
			if($this->status_count > 1)
					$tab = $view->addTab($status);

					$model = $tab->add('xepan\commerce\Model_Wishlist');
					$model->addCondition('contact_id',$this->customer_id);
					$model->addCondition('status',$status);	

					$model->setOrder('id','desc');
					$crud = $tab->add('xepan\base\CRUD',['allow_add'=>false,'allow_edit'=>false]);
					$crud->setModel($model);

					$crud->grid->addQuickSearch(['item']);
					$crud->grid->fixed_header = false;
					$crud->grid->addPaginator($this->paginator);

				if($status == "Due"){
						$crud->grid->addColumn('Button','from_wish_to_detail','Purchase Now');
					}
				if($id = $_GET['from_wish_to_detail']){
						$wish = $this->add('xepan\commerce\Model_Wishlist')->load($id);
						$this->app->redirect($this->app->url($this->detail_page,['commerce_item_id'=>$wish['item_id']]));
					}
				}
			}
		}