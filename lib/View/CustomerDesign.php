<?php

namespace xepan\commerce;

class View_CustomerDesign extends \View {

	public $options=[];
	function init(){
		parent::init();

		$this->js(true)
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/webfont.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fabric.min.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/designer.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jquery.colorpicker.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/cropper.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/pace.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/addtocart.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/slick.js')
				;
		// // RE DEFINED ALSO AT Tool_Item_Designer
		$this->js(true)
				->_library('WebFont')->load(['google'=>['families'=>[ 'Abel:bold,bolditalic,italic,regular', 'Abril Fatface:bold,bolditalic,italic,regular', 'Aclonica:bold,bolditalic,italic,regular', 'Acme:bold,bolditalic,italic,regular', 'Actor:bold,bolditalic,italic,regular', 'Cabin:bold,bolditalic,italic,regular','Cambay:bold,bolditalic,italic,regular','Cambo:bold,bolditalic,italic,regular','Candal:bold,bolditalic,italic,regular','Petit Formal Script:bold,bolditalic,italic,regular', 'Petrona:bold,bolditalic,italic,regular', 'Philosopher:bold,bolditalic,italic,regular','Piedra:bold,bolditalic,italic,regular', 'Ubuntu:bold,bolditalic,italic,regular']]]);

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
						
		
		$crud = $this->add('xepan\base\CRUD',array('allow_add'=>false,'allow_edit'=>false,'grid_options'=>['paginator_class'=>'Paginator']),null,["view\\tool\\grid\\".$this->options['customer-design-grid-layout']]);
		$paginator = $crud->grid->addPaginator(12);
		$crud->grid->addQuickSearch(['designs']);
		$customer_designs_model = $this->add('xepan\commerce\Model_Item_Template_Design');
		$customer_designs_model->addCondition('contact_id',$customer->id);

		$customer_designs_model->setOrder('id','desc');
		$crud->setModel($customer_designs_model,array('item_name','sku','short_description','description','is_party_publish','duplicate_from_item_id'),array('sku','designs','is_ordered','is_party_publish','item_name'));
		
		if(!$crud->isEditing()){
			$g = $crud->grid;
			$this->count = 1;
			$g->addHook('formatRow',function($g)use($designer_page,$paginator){
					//designs
					// $design_thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',['item_member_design_id'=>$g->model->id,'width'=>300]);
					
					$design_edit_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['item_id'],'xsnb_design_template'=>'false','item_member_design'=>$g->model->id));
					$g->current_row['design_edit'] = $design_edit_url;
					$design=json_decode($g->model['designs'],true);
					// echo("<pre>");
					// print_r($design);
					// exit;
					
					$item=$this->add('xepan\commerce\Model_Item')->load($g->model['item_id']);
					if(!$design['design']) return;
					$this->js(true)->_selector('#canvas-workspace-'.$g->model->id)->xepan_xshopdesigner(
												array(
														'width'=>$item->specification('width'),
														'height'=>$item->specification('height'),
														'trim'=>$item->specification('trim'),
														'unit'=> $item->specification('unit')?:'mm',
														'designer_mode'=> false,
														'design'=>json_encode($design['design']),
														'show_cart'=>'0',
														'selected_layouts_for_print'=>$design['selected_layouts_for_print'],
														'item_id'=>$g->model['item_id'],
														'item_member_design_id' => $g->model['id'],
														'item_name' => "asd",
														'base_url'=> $this->api->url()->absolute()->getBaseURL(),
														'calendar_starting_month'=> $design['calendar_starting_month'],
														'calendar_starting_year'=> $design['calendar_starting_year'],
														'calendar_event'=> $design['calendar_event'],
														'printing_mode'=>false,
														'show_canvas'=>true,
														'is_start_call'=>1,
														'show_tool_bar'=>0,
														'show_pagelayout_bar'=>0
												));


			});

			$g->removeColumn('sku');
			$g->removeColumn('is_ordered');
		}


		
		// $js = [
		// 		$this->js()->_selector('.customer-designer-image')->closest('.image-block')->attr('data-width')
		// 	];
		// $this
		// 	->js(true,$js)
		// 	->_selector('.customer-designer-image')
		// 	->closest('.image-block')
		// 	->css('border','5px solid red')
		// 	;

	}
}