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

 class page_itemdetail extends \xepan\base\Page {
	public $title='Item Details';
	public $breadcrumb=['Home'=>'index','Items'=>'xepan_commerce_item','Detail'=>'#'];

	function init(){
		parent::init();
	}

	function page_index(){

		$action = $this->api->stickyGET('action')?:'view';
	
		$item = $this->add('xepan\commerce\Model_Item')->tryLoadBy('id',$this->api->stickyGET('document_id'));

		$basic_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'view_info',['page/item/detail','view_info']);
		$basic_item->setModel($item,['name','total_sales','total_orders','created_at','stock_available'],
									['name','created_at']);

		if(!$item['maintain_inventory']){
			$basic_item->effective_template->tryDel('stock_available');
		}elseif($item['available_stock']>0){
			$basic_item->effective_template->setHTML('stock_available','<i style="color:orange;"> In Stock</i>');
		}else{
			if($item['allow_negative_stock'])
				$basic_item->effective_template->setHTML('stock_available','<i style="color:orange;"> PreOrder</i>');
			else
				$basic_item->effective_template->setHTML('stock_available','<i style="color:red;"> Out Of Stock</i>');
		}

		$basic_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'basic_info',['page/item/detail','basic_info']);
		$basic_item->setModel($item,['name','sku','display_sequence','status','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock','is_dispatchable',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions','is_designable'],

								['name','sku','display_sequence','expiry_date','status',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock','is_dispatchable',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions','is_designable']);
		

		if(!$item['website_display']) $this->js(true)->_selector('#website_display')->hide();
		$basic_item->form->getElement('website_display')->js('change',$this->js()->_selector('#website_display')->toggle());

		
		if($item->loaded()){
		
		/**
		
		specification

		*/	
			$crud_spec = $this->add('xepan\hr\CRUD',null,'specification',['view/item/associate/specification']);
			$crud_spec->setModel($item->associateSpecification(),['customfield_generic_id','can_effect_stock','status'],['customfield_generic','can_effect_stock','status']);
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
			$crud_cf->grid->addColumn('value');

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

			$crud_cf->grid->addMethod('format_value',function($grid,$field){
				$data = $grid->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$grid->model->id);
				$l = $grid->add('Lister',null,'Values');
				$l->setModel($data);
				
				$grid->current_row_html[$field] = $l->getHTML();
			});
			$crud_cf->grid->addFormatter('value','value');
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
			$form_asso_model->addCondition('is_filterable',true);
			$crud_filter->grid->addQuickSearch(['custom_field']);

		/**

		Extra

		*/

			$media_m = $item->ref('ItemImages');
			$crud_media = $this->add('xepan\hr\CRUD',null,'media',['view/item/media']);
			$crud_media->setModel($media_m);
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
					$model_asso->addCondition('category_id',$cat_id);
					$model_asso->addCondition('item_id',$item->id);
					$model_asso->tryLoadAny();
					$model_asso->saveAndUnload();
				}
				$form->js(null,$this->js()->univ()->successMessage('Category Associated'))->reload()->execute();
			}

		/**

		QuantitySet Condition

		*/
			$basic_price = $this->add('xepan\base\View_Document',[
									'action'=>$action,
									'id_field_on_reload'=>'document_id'],
									'qty_price_detail',['page/item/detail','qty_price_detail']
									);

			$basic_price->setModel(
									$item,
									['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit','qty_from_set_only'],
									['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit','qty_from_set_only']
								);

			//Quantity set Condition/Rate Chart
			$crud_qty_set_condition = $this->add('xepan\hr\CRUD',null,'qtysetcondition',['view/item/qtysetcondition']);
			$model_qtyset = $this->add('xepan\commerce\Model_Item_Quantity_Set');
			$model_qtyset->addCondition('item_id',$item->id);

			$crud_qty_set_condition->setModel($model_qtyset);
			$crud_qty_set_condition->grid->addQuickSearch(['name','qty','price']);

			$crud_qty_set_condition->grid->addColumn('Button','Condition');

			$crud_qty_set_condition->grid
								->add('VirtualPage')
								->addColumn('condition')
								->set(function($page){

								$id = $_GET[$page->short_name.'_id'];
								$model_qty_condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')
															->addCondition('quantity_set_id', $id);
								$crud_condition = $page->add('xepan\hr\CRUD',null,null,['view/item/associate/quantitycondition']);
								$crud_condition->setModel($model_qty_condition);

							});
			//CSV Uploader
			
			$grid = $crud_qty_set_condition->grid;
			$upl_btn=$grid->addButton('Upload Data');
			$upl_btn->setIcon('ui-icon-arrowthick-1-n');
			$item_id = $item->id;

			$upl_btn->js('click')
				->univ()
				->frameURL(
						'Data Upload',
						$this->app->url('./upload',
										array(
												'item_id'=>$item_id,
												'cut_page'=>1
											)
										)
						);


		/**

		Production Phase

		*/
		$grid_dept_asso = $this->add('xepan\base\Grid',
										null,
										'department',
										['view/item/associate/department']
									);

		$model_department = $this->add('xepan\hr\Model_Department')->addCondition('status','Active');

		$form_dept_asso = $this->add('Form',null,'item_dept_asso_form');
		$item_dept_asso_field = $form_dept_asso->addField('hidden','ass_dept')->set(json_encode($item->getAssociatedDepartment()));
		$form_dept_asso->addSubmit('Update');

		$grid_dept_asso->setModel($model_department,array('name'));
		$grid_dept_asso->addSelectable($item_dept_asso_field);

		if($form_dept_asso->isSubmitted()){
			$item->ref('xepan\commerce\Item_Department_Association')->deleteAll();

			$selected_department = array();
			$selected_department = json_decode($form_dept_asso['ass_dept'],true);
			foreach ($selected_department as $dept_id) {
				$model_asso = $this->add('xepan\commerce\Model_Item_Department_Association');
				$model_asso->addCondition('department_id',$dept_id);
				$model_asso->addCondition('item_id',$item->id);
				$model_asso->tryLoadAny();
				$model_asso->saveAndUnload();
			}
			$form_dept_asso->js(null,$this->js()->univ()->successMessage('Department Added to this Item'))->reload()->execute();
		}

		$grid_dept_asso->add('VirtualPage')
 				->addColumn('consumption')
				->set(function($page)use($item){

					$department_id = $_GET[$page->short_name.'_id'];
					// $page->add('Text')->set('ID='.$department_id);

					$dept_assos = $page->add('xepan\commerce\Model_Item_Department_Association')
								->addCondition('department_id',$department_id)
								->addCondition('item_id',$item->id)
								->tryLoadAny();
					
					if(!$dept_assos->loaded()){
						$page->add('View_Error')->set('Please define item\'s association with this department first');
						return;
					}

					$form = $page->add('Form');
					$form->addField('Checkbox','can_redefine_qty')->set($dept_assos['can_redefine_qty']);
					$form->addField('Checkbox','can_redefine_item')->set($dept_assos['can_redefine_item']);

					$form->addSubmit('Update');
					if($form->isSubmitted()){
						$dept_assos['can_redefine_item'] = $form['can_redefine_item'];
						$dept_assos['can_redefine_qty'] = $form['can_redefine_qty'];
						$dept_assos->save();
						$form->js()->univ()->successMessage('Information Saved')->execute();
					}

					$model_item_consumption = $this->add('xepan\commerce\Model_Item_Department_Consumption')
											->addCondition('item_department_association_id',$dept_assos->id);

					$crud_dept_item_consumption = $page->add('xepan\base\CRUD',null,null,['view\item\associate\departmentconsumption']);
					$crud_dept_item_consumption->setModel($model_item_consumption,['composition_item_id','quantity','unit','custom_fields','composition_item']);

				});

		}

/**

		Accounts

*/		
	$act = $this->add('xepan\commerce\Model_Item_Taxation_Association')
				->addCondition('item_id',$item->id);
	$crud_ac = $this->add('xepan\hr\CRUD',null,'taxation',['view/item/accounts/tax']);
	$crud_ac->setModel($act);
	
	$crud_ac->grid->addQuickSearch(['taxation']);

	}

	function format_created_at($value,$m){
		return date('d M Y',strtotime($value));
	}

	function defaultTemplate(){
		return ['page/item/detail'];

	}

}


