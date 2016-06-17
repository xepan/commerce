<?php

 namespace xepan\commerce;

 class Model_Category extends \xepan\hr\Model_Document{
 	public $status = ['Active','InActive'];
 	public $actions = [
 						'Active'=>['view','edit','delete','deactivate'],
 						'InActive'=>['view','edit','delete','activate']
 					];
	var $table_alias = 'category';

	function init(){
		parent::init();

		$cat_j=$this->join('category.document_id');

		$cat_j->hasOne('xepan\commerce\ParentCategory','parent_category_id')->sortable(true);

		$cat_j->addField('name')->sortable(true);
		$cat_j->addField('display_sequence')->type('int')->hint('change the sequence of category, sort by decenting order')->defaultValue(0);
		$cat_j->addField('alt_text')->hint('set alt_text of image tag');
		$cat_j->addField('description')->display(['form'=>'xepan\base\RichText'])->type('text');

		$cat_j->addField('custom_link');
		$cat_j->addField('meta_title');
		$cat_j->addField('meta_description')->type('text');
		$cat_j->addField('meta_keywords');

		$this->add('filestore\Field_Image','cat_image_id')->display(['form'=>'xepan\base\Upload'])->from($cat_j);
		

		$cat_j->hasMany('xepan\commerce\Filter','category_id');
		$cat_j->hasMany('xepan\commerce\CategoryItemAssociation','category_id');
		$cat_j->hasMany('xepan\commerce/Category','parent_category_id',null,'SubCategories');

		$this->addCondition('type','Category');
		// $this->addCondition('epan_id',$this->app->epan->get('id'));
		$this->getElement('status')->defaultValue('Active');
			
		$this->addExpression('item_count')->set(function($m,$q){
				$cat_item_model = $m->add('xepan\commerce\Model_CategoryItemAssociation');
				$cat_item_j = $cat_item_model->leftJoin('item.document_id','item_id');
				$cat_item_j->addField('is_saleable');
				$cat_item_j->addField('website_display');

				$item_doc_j = $cat_item_j->join('document','document_id');
				$item_doc_j->addField('status');
				
				$cat_item_model->addCondition('status','Published');
				$cat_item_model->addCondition('is_saleable',1);
				$cat_item_model->addCondition('website_display',1);
				$cat_item_model->addCondition('is_template',0);
				$cat_item_model->addCondition('category_id',$m->getElement('id'));

				return $cat_item_model->count();
		})->sortable(true);

		$this->addExpression('min_price')->set(function($m,$q){
			return $q->expr("IFNULL([0],0)",[$m->refSQL('xepan\commerce\CategoryItemAssociation')->setOrder('sale_price','asc')->setLimit(1)->fieldQuery('sale_price')]);
		});

		$this->addExpression('max_price')->set(function($m,$q){
			return $q->expr("IFNULL([0],0)",[$m->refSQL('xepan\commerce\CategoryItemAssociation')->setOrder('sale_price','desc')->setLimit(1)->fieldQuery('sale_price')]);
		});


		$this->addHook('beforeDelete',$this);

		$this->is([
				'name|to_trim|required|unique_in_epan',
				'display_sequence|int'
			]);
		$this->addHook('beforeSave',[$this,'updateSearchString']);		
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['name'];
		$search_string .=" ". $this['type'];
		$search_string .=" ". $this['status'];
		$search_string .=" ". $this['alt_text'];
		$search_string .=" ". $this['meta_title'];
		$search_string .=" ". $this['meta_description'];
		$search_string .=" ". $this['meta_keywords'];

		$this['search_string'] = $search_string;
	}

	function quickSearch($app,$search_string,&$result_array){
		$this->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$this->addCondition('Relevance','>',0);
 		$this->setOrder('Relevance','Desc');
 		
 		if($this->count()->getOne()){
 			foreach ($this->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_category',['status'=>$data['status']])->getURL(),
 				];
 			}
		}

 		$item = $this->add('xepan\commerce\Model_Item');
 		$item->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$item->addCondition('Relevance','>',0);
 		$item->setOrder('Relevance','Desc');
 		
 		if($item->count()->getOne()){
 			foreach ($item->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_itemdetail',['status'=>$data['status'],'document_id'=>$data['id']])->getURL(),
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 					'quick_info'=>'Sale Price: '.$data['sale_price'].' Total Orders: '.$data['total_orders'],
 				];
 			}
		}

 		$customer = $this->add('xepan\commerce\Model_Customer');
 		$customer->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$customer->addCondition('Relevance','>',0);
 		$customer->setOrder('Relevance','Desc');
 		
 		if($customer->count()->getOne()){
 			foreach ($customer->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_customerdetail',['status'=>$data['status'],'contact_id'=>$data['id']])->getURL(),
 				];
 			}
		}

 		$supplier = $this->add('xepan\commerce\Model_Supplier');
 		$supplier->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$supplier->addCondition('Relevance','>',0);
 		$supplier->setOrder('Relevance','Desc');
 		
 		if($supplier->count()->getOne()){
 			foreach ($supplier->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_supplierdetail',['status'=>$data['status'],'contact_id'=>$data['id']])->getURL(),
 				];
 			}
		}

 		$master = $this->add('xepan\commerce\Model_QSP_Master');
 		$master->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN BOOLEAN MODE)');
		$master->addCondition('Relevance','>',0);
 		$master->setOrder('Relevance','Desc');

 		$details_j = $master->join('qsp_detail.qsp_master_id');
 		$details_j->hasOne('xepan\commerce\Item','item_id');
 		$details_j->addField('qsp_master_id');


 		$master->addExpression('item_list')->set(function($m,$q){
			return $q->expr('group_concat([0])',[$m->getElement('item')]);
		});

		$master->_dsql()->group('qsp_master_id');
 		
 		if($master->count()->getOne()){
 			foreach ($master->getRows() as $data) {	

 				if($data['type'] == 'Quotation')
    				$url = $this->app->url('xepan_commerce_quotationdetail',['action'=>'view','status'=>$data['status'],'document_id'=>$data['id']])->getURL();	
    			if($data['type'] == 'SalesOrder')
    				$url = $this->app->url('xepan_commerce_salesorderdetail',['action'=>'view','status'=>$data['status'],'document_id'=>$data['id']])->getURL();	
    			if($data['type'] == 'SalesInvoice')
    				$url = $this->app->url('xepan_commerce_salesinvoicedetail',['action'=>'view','status'=>$data['status'],'document_id'=>$data['id']])->getURL();	
    			if($data['type'] == 'PurchaseOrder')
    				$url = $this->app->url('xepan_commerce_purchaseorderdetail',['action'=>'view','status'=>$data['status'],'document_id'=>$data['id']])->getURL();	
    			if($data['type'] == 'PurchaseInvoice')
    				$url = $this->app->url('xepan_commerce_purchaseinvoicedetail',['action'=>'view','status'=>$data['status'],'document_id'=>$data['id']])->getURL();	 				 				

 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['document_no'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$url,
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 					'quick_info'=>'Total Amount: '.$data['total_amount'].' Item List: '.$data['item_list'],
 				];
 			}
		}


 		$tax = $this->add('xepan\commerce\Model_Taxation');
 		$tax->addExpression('Relevance')->set('MATCH(name, type) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$tax->addCondition('Relevance','>',0);
 		$tax->setOrder('Relevance','Desc');
 		
 		if($tax->count()->getOne()){
 			foreach ($tax->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_tax')->getURL(),
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 				];
 			}
		}

 		$tnc = $this->add('xepan\commerce\Model_TNC');
 		$tnc->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$tnc->addCondition('Relevance','>',0);
 		$tnc->setOrder('Relevance','Desc');
 		
 		if($tnc->count()->getOne()){
 			foreach ($tnc->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_tnc')->getURL(),
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 				];
 			}
		}

 		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
 		$warehouse->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$warehouse->addCondition('Relevance','>',0);
 		$warehouse->setOrder('Relevance','Desc');
 		
 		if($warehouse->count()->getOne()){
 			foreach ($warehouse->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_commerce_store_warehouse')->getURL(),
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 				];
 			}
		}
	}

	function activate(){
		$this['status'] = "Active";
		$this->app->employee
            ->addActivity("Item's '".$this['name']."' category now active", $this->id/* Related Document ID*/, null /*Related Contact ID*/)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	function deactivate(){
		$this['status'] = "InActive";
		$this->app->employee
            ->addActivity("Item's '". $this['name'] ."' category has been deactivated", $this->id /*Related Document ID*/, null /*Related Contact ID*/)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	function beforeDelete($m){
		$this->ref('xepan\commerce\CategoryItemAssociation')->deleteAll();
	}
}