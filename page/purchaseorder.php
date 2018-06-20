<?php 
 namespace xepan\commerce;
 class page_purchaseorder extends \xepan\base\Page{

	public $title='Purchase Orders';

	function init(){
		parent::init();

		$supplier_id = $this->app->stickyGET('supplier_id');

		$purchaseorder = $this->add('xepan\commerce\Model_PurchaseOrder');
		$purchaseorder->add('xepan\base\Controller_TopBarStatusFilter');

		if($supplier_id)
			$purchaseorder->addCondition('contact_id',$supplier_id);

		$purchaseorder->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});

		$purchaseorder->addExpression('contact_type',$purchaseorder->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_quickqsp&document_type=PurchaseOrder']
						,null,
						['view/order/purchase/grid']);

		$crud->grid->addColumn('other_info');
		$crud->grid->addHook('formatRow',function($g){
			$other_data = array_intersect_key($g->model->data,$g->model->otherInfoFields);
			if(count($other_data))
				$g->current_row_html['other_info'] = trim(trim(str_replace(",", "<br/>",json_encode($other_data)),'{'),'}');
			else
				$g->current_row_html['other_info'] = "-";
			
		});

		$crud->setModel($purchaseorder)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$frm=$crud->grid->addQuickSearch(array_merge(['contact','document_no'],$purchaseorder->otherInfoFields));
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
		$crud->add('xepan\base\Controller_MultiDelete');
		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Purchase Order Details',[$this->api->url('xepan_commerce_quickqsp'),'document_id'=>$this->js()->_selectorThis()->closest('[data-purchaseinvoice-id]')->data('id'),'readmode'=>1]);
			$crud->grid->js('click')->_selector('.do-view-supplier-frame')->univ()->frameURL('Supplier Details',[$this->api->url('xepan_commerce_supplierdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
		}	
	}
} 