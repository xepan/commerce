<?php

namespace xepan\commerce;

class View_CustomerTemplate extends \View {

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
		
		$col = $this->add('Columns')->addClass('atk-box row');
		$left = $col->addColumn(6)->addClass('col-md-6');
		$right = $col->addColumn(6)->addClass('col-md-6');
		$crud = $this->add('xepan\base\CRUD',array('allow_add'=>false,'allow_edit'=>false,'grid_options'=>['paginator_class'=>'Paginator']),null,["view\\tool\\grid\\".$this->options['customer-template-grid-layout']]);
		$paginator = $crud->grid->addPaginator(10);
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
			$temp_image_model->tryLoad($_GET['item_image']);
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
								$customer->id
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
				$template_thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',['xsnb_design_item_id'=>$g->model['id'],'width'=>'150']);
				$g->current_row['template_thumb_url'] = $template_thumb_url;

				$template_edit_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['id'],'xsnb_design_template'=>'true'));
				$g->current_row['template_edit'] = $template_edit_url;

				$template_new_design_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['id']));
				$g->current_row['new_design'] = $template_new_design_url;

				$g->current_row['s_no'] = ($this->count++) + $paginator->skip;

			});
		}
		

	}
}