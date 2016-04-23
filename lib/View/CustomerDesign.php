<?php

namespace xepan\commerce;

class View_CustomerDesign extends \View {

	public $options=[];
	function init(){
		parent::init();

		//Check Customer is login or not
		$customer = $this->add('xepan/commerce/Model_Customer');
		if(!$customer->loadLoggedIn()){
			$this->add('View_Error')->set('Not Authorized');
			return;
		}

		if(!($designer_page = $this->options['designer-page'])){
			$this->add('View_Warning')->set('Specify the designer page');
			return ;
		}
		
		$crud = $this->add('xepan\base\CRUD',array('allow_add'=>false,'allow_edit'=>false),null,["view\\tool\\grid\\".$this->options['customer-design-grid-layout']]);

		$customer_designs_model = $this->add('xepan\commerce\Model_Item_Template_Design');
		$customer_designs_model->addCondition('contact_id',$customer->id);

		$customer_designs_model->setOrder('id','desc');
		$crud->setModel($customer_designs_model,array('name','sku','short_description','description','is_party_publish','duplicate_from_item_id'),array('name','sku','designs','is_ordered','is_party_publish'));
		
		if(!$crud->isEditing()){
			$g = $crud->grid;
			$g->addHook('formatRow',function($g)use($designer_page){		
					//designs
					$design_thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',['item_member_design_id'=>$g->model->id,'width'=>100]);
					$g->current_row['design_thumb_url'] = $design_thumb_url;
					
					$design_edit_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>'not-available','xsnb_design_template'=>'false','item_member_design'=>$g->model->id));
					$g->current_row['design_edit'] = $design_edit_url;
			});
			$g->removeColumn('sku');
			$g->removeColumn('is_ordered');
		}
		

	}
}