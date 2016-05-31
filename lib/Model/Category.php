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

		$cat_j->hasOne('xepan\commerce\ParentCategory','parent_category_id')->defaultValue('Null')->sortable(true);

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

		$item_count = $this->addExpression('item_count')->set(function($m){
			return $m->refSQL('xepan\commerce\CategoryItemAssociation')
							->addCondition('is_template', false)->count();
		})->sortable(true);

		$template_count = $this->addExpression('template_count')->set(function($m){
			return $m->refSQL('xepan\commerce\CategoryItemAssociation')
						  ->addCondition('is_template', true)->count();	
		})->sortable(true);

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

	function quickSearch($app,$search_string,$view){
		$this->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$this->addCondition('Relevance','>',0);
 		$this->setOrder('Relevance','Desc');
 		if($this->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($this);
    		$cc->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_category',['status'=>$g->model['status']]);	
     		});	
 		}

 		$item = $this->add('xepan\commerce\Model_Item');
 		$item->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$item->addCondition('Relevance','>',0);
 		$item->setOrder('Relevance','Desc');
 		if($item->count()->getOne()){
 			$ic = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$ic->setModel($item);
    		$ic->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_itemdetail',['document_id'=>$g->model->id]);	
     		});	
 		}

 		$customer = $this->add('xepan\commerce\Model_Customer');
 		$customer->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$customer->addCondition('Relevance','>',0);
 		$customer->setOrder('Relevance','Desc');
 		if($customer->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($customer);
    		$cc->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_customerdetail',['contact_id'=>$g->model->id]);	
     		});	
 		}

 		$supplier = $this->add('xepan\commerce\Model_Supplier');
 		$supplier->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$supplier->addCondition('Relevance','>',0);
 		$supplier->setOrder('Relevance','Desc');
 		if($supplier->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($supplier);
    		$cc->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_supplierdetail',['contact_id'=>$g->model->id]);	
     		});	
 		}

 		$master = $this->add('xepan\commerce\Model_QSP_Master');
 		$master->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$master->addCondition('Relevance','>',0);
 		$master->setOrder('Relevance','Desc');
 		if($master->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($master);
    		$cc->addHook('formatRow',function($g){
    			if($g->model['type'] == 'Quotation')
    				$g->current_row_html['url'] = $this->app->url('xepan_commerce_quotationdetail',['action'=>'view','document_id'=>$g->model->id]);	
    			if($g->model['type'] == 'SalesOrder')
    				$g->current_row_html['url'] = $this->app->url('xepan_commerce_salesorderdetail',['action'=>'view','document_id'=>$g->model->id]);	
    			if($g->model['type'] == 'SalesInvoice')
    				$g->current_row_html['url'] = $this->app->url('xepan_commerce_salesinvoicedetail',['action'=>'view','document_id'=>$g->model->id]);	
    			if($g->model['type'] == 'PurchaseOrder')
    				$g->current_row_html['url'] = $this->app->url('xepan_commerce_purchaseorderdetail',['action'=>'view','document_id'=>$g->model->id]);	
    			if($g->model['type'] == 'PurchaseInvoice')
    				$g->current_row_html['url'] = $this->app->url('xepan_commerce_purchaseinvoicedetail',['action'=>'view','document_id'=>$g->model->id]);	
     		});	
 		}

 		$tax = $this->add('xepan\commerce\Model_Taxation');
 		$tax->addExpression('Relevance')->set('MATCH(name, type) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$tax->addCondition('Relevance','>',0);
 		$tax->setOrder('Relevance','Desc');
 		if($tax->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($tax);
    		$cc->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_tax');	
     		});	
 		}

 		$tnc = $this->add('xepan\commerce\Model_TNC');
 		$tnc->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$tnc->addCondition('Relevance','>',0);
 		$tnc->setOrder('Relevance','Desc');
 		if($tnc->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($tnc);
    		$cc->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_tnc');	
     		});	
 		}

 		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
 		$warehouse->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" IN NATURAL LANGUAGE MODE)');
		$warehouse->addCondition('Relevance','>',0);
 		$warehouse->setOrder('Relevance','Desc');
 		if($warehouse->count()->getOne()){
 			$cc = $view->add('Completelister',null,null,['view/quicksearch-commerce-grid']);
 			$cc->setModel($warehouse);
    		$cc->addHook('formatRow',function($g){
    			$g->current_row_html['url'] = $this->app->url('xepan_commerce_store_warehouse');	
     		});	
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