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
		
		$col = $this->add('Columns')->addClass('atk-box');
		$left = $col->addColumn(7)->addClass('col-md-12');
		$right = $col->addColumn(5)->addClass('col-md-12');
		$form = $left->add('Form');
		$crud = $this->add('xepan\base\CRUD',array('allow_add'=>false,'allow_edit'=>false),null,["view\\tool\\grid\\".$this->options['customer-design-grid-template']]);

		$template_model = $this->add('xepan\commerce\Model_Item_Template');
		$template_model->addCondition(
							$template_model
								->dsql()
								->orExpr()
								->where('to_customer_id',$customer->id)
								->where('to_customer_id',null)
							);

		$tem_field=$form->addField('xepan\commerce\DropDown','item_template');
		$tem_field->setModel($template_model);
		$tem_field->setEmptyText('Please Select');

		$form->addSubmit('Duplicate');
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

		$customer_designs_model = $this->add('xepan\commerce\Model_Item_Template_Design');
		$customer_designs_model->addCondition('contact_id',$customer->id);

		$customer_designs_model->setOrder('id','desc');
		$crud->setModel($customer_designs_model,array('name','sku','short_description','description','is_party_publish','duplicate_from_item_id'),array('name','sku','designs','is_ordered','is_party_publish'));
		
		if(!$crud->isEditing()){
			$g = $crud->grid;
			$g->addHook('formatRow',function($g)use($designer_page){
				
				//templates
				$template_thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',['xsnb_design_item_id'=>$g->model['item_id']]);
				$g->current_row['template_thumb_url'] = $template_thumb_url;

				$template_edit_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['item_id'],'xsnb_design_template'=>'true'));
				$g->current_row['template_edit'] = $template_edit_url;
						
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