<?php
 
namespace xepan\commerce;

class page_customer extends \xepan\base\Page {
	public $title='Customers';

	function init(){
		parent::init();

		$customer_model = $this->add('xepan\commerce\Model_Customer');
		$customer_model->add('xepan\commerce\Controller_SideBarStatusFilter');
		

		//Total Orders
		$customer_model->addExpression('orders')->set(" 'Todo 10' ");

		$crud = $this->add('xepan\hr\CRUD',
							[
							'action_page'=>'xepan_commerce_newcustomer',
							'edit_page'=>'xepan_commerce_customerdetail'
							],
							null,
							['view/customer/grid']
						);


		$crud->setModel($customer_model)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);

		$frm=$crud->grid->addQuickSearch(['name','organization_name']);
		
		$crud->add('xepan\base\Controller_Avatar');
		$crud->add('xepan\base\Controller_MultiDelete');
		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-customer-detail')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-customer-id]')->data('id')]);
			
			// $newcustomer_btn=$c->grid->addButton('new')->addClass('btn btn-primary');

			// $p=$this->add('VirtualPage');
			// $p->set(function($p){
			// 	$f=$p->add('Form');
			// 	$f->addField('text','json');
			// 	$f->addSubmit('Go');
				
			// 	if($f->isSubmitted()){
			// 		$import_m=$this->add('xepan\base\Model_GraphicalReport');

			// 		$import_m->importJson($f['json']);	
					
			// 		$f->js()->reload()->univ()->successMessage('Done')->execute();
			// 	}
			// });
			// if($import_btn->isClicked()){
			// 	$this->js()->univ()->frameURL('Import',$p->getUrl())->execute();
			// }
		}
	}
}



























// <?php
//  namespace xepan\commerce;
//  class page_customerprofile extends \Page{
//  	public $title='Customer';

// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/customerprofile'];
// 	}
// }