<?php

namespace xepan\commerce;

class View_CustomerTemplate extends \View {

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
						
		$col = $this->add('Columns')->addClass('atk-box row');
		$left = $col->addColumn(6)->addClass('col-md-6');
		$right = $col->addColumn(6)->addClass('col-md-6');
		$crud = $this->add('xepan\base\CRUD',array('allow_add'=>false,'allow_edit'=>false,'grid_options'=>['paginator_class'=>'Paginator']),null,["view\\tool\\grid\\".$this->options['customer-template-grid-layout']]);
		$paginator = $crud->grid->addPaginator(12);
		$crud->grid->addQuickSearch(['name']);
		$template_model = $this->add('xepan\commerce\Model_Item_Template');
		$template_model->addCondition(
							$template_model
								->dsql()
								->orExpr()
								->where('to_customer_id',$customer->id)
								->where('to_customer_id',null)
							);

		$form = $left->add('Form',null,null,['form/stacked']);
		$tem_field=$form->addField('xepan\commerce\DropDown','item_template','Select a template to duplicate');
		$tem_field->setModel($template_model);
		$tem_field->setEmptyText('Please Select');
		$form->addSubmit('Duplicate');
		
		$temp_image_model= $right->add('xepan\commerce\Model_Item_Image');
		// $temp_image_model->addCondition('item_id',$_GET['item_image']);

		if($_GET['item_image'])
			$temp_image_model->addCondition('item_id',$_GET['item_image'])->setLimit(1);
		// // $temp_image_model->tryLoadAny();
		
		$view=$right->add('View')->addStyle('width','20%');
		if($temp_image_model->loaded()){
			$view->setElement('img')->setAttr('src',$temp_image_model['file'])->setStyle('width','100%')->addClass('atk-box');
		}else{
			$view->set('No Image Found');
		}
		$tem_field->js('change',$view->js()->reload(['item_image'=>$tem_field->js()->val()]));


		if($form->isSubmitted()){

			$new_item = $template_model
						->load($form['item_template'])
						->duplicate(
								$template_model['name']." - new",
								$template_model['sku']." - new",
								$customer->id,
								false,
								false,
								$template_model->id,
								$create_default_design_also=true,
								$this->app->auth->model->id
							);
			
			$form->js(null,$crud->js()->reload())->univ()->successMessage('Design Duplicated')->execute();
		}

		$customer_template_model = $this->add('xepan\commerce\Model_Item');
		$customer_template_model->addCondition(
					$customer_template_model
						->dsql()
						->orExpr()
						->where('to_customer_id',$customer->id)
						->where('designer_id',$customer->id)
				);

		$customer_template_model->setOrder('id','desc');
		$crud->setModel($customer_template_model,array('name','sku','short_description','description','is_party_publish','duplicate_from_item_id'),array('name','sku','designs','is_ordered','is_party_publish'));
		
		if(!$crud->isEditing()){
			$g = $crud->grid;
			$this->count = 1;
			$g->addHook('formatRow',function($g)use($designer_page ,$paginator){
				// $template_thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',['xsnb_design_item_id'=>$g->model['id'],'width'=>'300']);
				// $g->current_row['template_thumb_url'] = $template_thumb_url;

				$template_edit_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['id'],'xsnb_design_template'=>'true'));
				$g->current_row['template_edit'] = $template_edit_url;

				$template_new_design_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['id']));
				$g->current_row['new_design'] = $template_new_design_url;
				$g->current_row['s_no'] = ($this->count++) + $paginator->skip;

				$design = json_decode($g->model['designs'],true);

				if(!$design['design']) return;
				
				$specification = $g->model->getSpecification();

				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['width'],$temp);
				$specification['width']= $temp[1][0];
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['height'],$temp);
				$specification['height']= $temp[1][0];
				$specification['unit']=$temp[2][0];
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['trim'],$temp);
				$specification['trim']= $temp[1][0];

				$g->js(true)->_selector('#canvas-workspace-'.$g->model->id)->xepan_xshopdesigner(
											array(
													'width'=> $specification['width'],
													'height'=>$specification['height'],
													'trim'=> $specification['trim'],
													'unit'=> $specification['unit'],
													'designer_mode'=> false,
													'design'=>json_encode($design['design']),
													'show_cart'=>'0',
													'selected_layouts_for_print'=>$design['selected_layouts_for_print'],
													'item_id'=>$g->model->id,
													'item_member_design_id' => null,
													'item_name' => $g->model['name'],
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
		}
		

	}
}