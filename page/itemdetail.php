<?php  

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/ 

 namespace xepan\commerce;

 class page_itemdetail extends \Page {
	public $title='View Item';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$item = $this->add('xepan\commerce\Model_Item')->tryLoadBy('id',$this->api->stickyGET('document_id'));

		$basic_item = $this->add('xepan\base\View_Document',['action'=>'view','id_field_on_reload'=>'document_id'],'view_info',['page/item/detail','view_info']);
		$basic_item->setModel($item,['name','total_sale','total_orders','created_at','stock_availability'],
									['name','total_sale','total_orders','created_at','stock_availability']);
		
		$basic_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'basic_info',['page/item/detail','basic_info']);
		$basic_item->setModel($item,['name','sku','display_sequence','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions'],

								['name','sku','display_sequence','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions']);

		if($item->loaded()){
		
		/**
		
		specification

		*/	
			$crud_spec = $this->add('xepan\hr\CRUD',null,'specification',['view/item/associate/specification']);
			$crud_spec->setModel($item->associateSpecification());
			$crud_spec->grid->addColumn('Button','Value');
			$crud_spec->grid->addQuickSearch(['custom_field']);

			$crud_spec->grid
					->add('VirtualPage')
					->addColumn('Values')
					->set(function($page){

					$id = $_GET[$page->short_name.'_id'];
					$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id', $id);					
					$crud_value = $page->add('xepan\hr\CRUD',null,null,['view/item/associate/value']);
					$crud_value->setModel($model_cf_value);

				});

			$crud_spec->form->getElement('customfield_generic_id')->getModel()->addCondition('type','Specification');

		/**

		Custom Field

		*/
			$crud_cf = $this->add('xepan\hr\CRUD',null,'customfield',['view/item/associate/customfield']);
			$crud_cf->setModel($item->associateCustomField());
			$crud_cf->grid->addColumn('Button','Value');
			$crud_cf->grid->addQuickSearch(['custom_field']);

			$crud_cf->grid
					->add('VirtualPage')
					->addColumn('Values')
					->set(function($page){

					$id = $_GET[$page->short_name.'_id'];
					$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id', $id);
					$crud_value = $page->add('xepan\hr\CRUD',null,null,['view/item/associate/value']);
					$crud_value->setModel($model_cf_value);

				});			
			$crud_cf->form->getElement('customfield_generic_id')->getModel()->addCondition('type','CustomField');

		/**

		Stock Effect Custom Field/ User Choice

		*/
			$crud_uc = $this->add('xepan\hr\CRUD',null,'userchoice',['view/item/associate/userchoice']);
			$crud_uc->setModel($item->associateUserChoice());
			$crud_uc->grid->addColumn('Button','Value');
			$crud_uc->grid->addQuickSearch(['custom_field']);

			$crud_uc->grid
					->add('VirtualPage')
					->addColumn('Values')
					->set(function($page){

					$id = $_GET[$page->short_name.'_id'];
					$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id', $id);
					$crud_value = $page->add('xepan\hr\CRUD',null,null,['view/item/associate/value']);
					$crud_value->setModel($model_cf_value);

				});			
			$crud_uc->form->getElement('customfield_generic_id')->getModel()->addCondition('type','CustomField');

		/**

		Filters

		*/
			$crud_filter = $this->add('xepan\hr\CRUD',null,'filter',['view/item/filter']);
			$model_filter = $this->add('xepan\commerce\Model_Filter');
			//Join Filter Model with CustomField Association
			$cf_asso_j = $model_filter->join('customfield_association');
			$cf_asso_j->addField('item_id');
			$model_filter->addCondition('item_id',$item->id);

			$crud_filter->setModel($model_filter);

			$form_asso_model = $crud_filter->form->getElement('customfield_association_id')->getModel();
			$cf_generic_j = $form_asso_model->join('customfield_generic');
			$cf_generic_j->addField('is_filterable');
			// $form_asso_model->addCondition('CustomFieldType',"Specification");
			$form_asso_model->addCondition('is_filterable',true);

			// $crud_uc->grid
			// 		->add('VirtualPage')
			// 		->addColumn('Values')
			// 		->set(function($page){

			// 		$id = $_GET[$page->short_name.'_id'];
			// 		$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
			// 						->addCondition('customfield_association_id', $id);
			// 		$crud_value = $page->add('xepan\hr\CRUD',null,null,['view/item/associate/value']);
			// 		$crud_value->setModel($model_cf_value);

			// 	});			
			// $crud_uc->form->getElement('customfield_generic_id')->getModel()->addCondition('type','CustomField');


		/**

		SEO

		*/
			$seo_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'seo',['page/item/detail','seo']);
			$seo_item->setModel($item,['meta_title','meta_description','tags'],
									  ['meta_title','meta_description','tags']);

		/**

		Category Item Association

		*/	
			$crud_cat_asso = $this->add('xepan\base\Grid',
										null,
										'category',
										['view/item/associate/category']
									);

			$model_active_category = $this->add('xepan\commerce\Model_Category')->addCondition('status','Active');

			$form = $this->add('Form',null,'cat_asso_form');
			$ass_cat_field = $form->addField('hidden','ass_cat')->set(json_encode($item->getAssociatedCategories()));
			$form->addSubmit('Update');

			$crud_cat_asso->setModel($model_active_category,array('name'));
			$crud_cat_asso->addSelectable($ass_cat_field);

			if($form->isSubmitted()){
				$item->ref('xepan\commerce\CategoryItemAssociation')->deleteAll();

				$selected_categories = array();
				$selected_categories = json_decode($form['ass_cat'],true);
				foreach ($selected_categories as $cat_id) {
					$model_asso = $this->add('xepan\commerce\Model_CategoryItemAssociation');
					$model_asso['category_id'] = $cat_id;
					$model_asso['item_id'] = $item->id;
					$model_asso->saveAndUnload();
				}
				$form->js(null,$this->js()->univ()->successMessage('Category Associated'))->reload()->execute();
			}

		/**

		QuantitySet Condition

		*/
			$qty_detail = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'qty_price_detail',['page/item/detail','qty_price_detail']);
			$qty_detail->setModel($item,['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit','qty_from_set_only'],
										['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit','qty_from_set_only']);			
		}

	}

	function format_created_at($value,$m){
		return date('d M Y',strtotime($value));
	}

	function defaultTemplate(){
		return ['page/item/detail'];

	}
}


