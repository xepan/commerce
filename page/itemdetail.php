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

		if($this->app->stickyGET('new_template') ){
			$item->addCondition('is_template',true);
		}

		$basic_item_side_info = $this->add('xepan\base\View_Document',['action'=>'view','id_field_on_reload'=>'document_id'],'view_info',['page/item/detail','view_info']);
		$basic_item_side_info->setModel($item,['name','total_sales','total_orders','created_at','stock_available','first_image'],
									['name','created_at']);

		// throw new \Exception($item['first_image']);
		
		if(!$item['maintain_inventory']){
			$basic_item_side_info->effective_template->tryDel('stock_available');
		}elseif($item['available_stock']>0){
			$basic_item_side_info->effective_template->setHTML('stock_available','<i style="color:orange;"> In Stock</i>');
		}else{
			if($item['allow_negative_stock'])
				$basic_item_side_info->effective_template->setHTML('stock_available','<i style="color:orange;"> PreOrder</i>');
			else
				$basic_item_side_info->effective_template->setHTML('stock_available','<i style="color:red;"> Out Of Stock</i>');
		}

		$basic_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id','page_reload'=>true],'basic_info',['page/item/detail','basic_info']);
		$basic_item->setModel($item,['name','sku','display_sequence','status','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock','is_dispatchable',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions','is_designable','upload_file_label','item_specific_upload_hint','upload_file_group','is_renewable','remind_to','renewable_value','renewable_unit','to_customer_id','is_teller_made_item','minimum_stock_limit','is_serializable', 'is_package','hsn_sac','slug_url','is_production_phases_fixed'],

								['name','sku','display_sequence','expiry_date','status',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock','is_dispatchable',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
 								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url','duplicate_from_item_id','designer_id',
								'description','terms_and_conditions','is_designable','upload_file_label','item_specific_upload_hint','upload_file_group','is_renewable','remind_to','renewable_value','renewable_unit','to_customer_id','is_teller_made_item','minimum_stock_limit','is_serializable', 'is_package','hsn_sac','slug_url','is_production_phases_fixed']);

		if(!$item['website_display']) $this->js(true)->_selector('#website_display')->hide();
		$basic_item->form->getElement('website_display')->js('change',$this->js()->_selector('#website_display')->toggle());

		
		if($item->loaded()){

			if(!$item['total_orders']){
				$basic_item_side_info->effective_template->trySet('total_orders','0');
			}
			if(!$item['total_sales']){
				$basic_item_side_info->effective_template->trySet('total_sales','0');
			}
		
		/**
		
		specification

		*/	
				
			$crud_spec = $this->add('xepan\hr\CRUD',['allow_add'=>false,'frame_options'=>['width'=>'600px'],'entity_name'=>'Specification'],'specification',['view/item/associate/specification']);
			$item_spec = $item->associateSpecification($with_filter=false);

			$crud_spec->setModel($item_spec,['customfield_generic_id','can_effect_stock','status','is_filterable'],['customfield_generic','can_effect_stock','status','is_filterable']);
			$crud_spec->grid->addQuickSearch(['customfield_generic']);
			$crud_spec->grid->addColumn('Button','Value');
			$crud_spec->grid->addColumn('value');
			$crud_spec->add('xepan\base\Controller_MultiDelete');
			$crud_spec->grid
					->add('VirtualPage')
					->addColumn('Values','Managing Specification Values',['descr'=>'Values'])
					->set(function($page){

					$id = $_GET[$page->short_name.'_id'];
					$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id', $id);
					
					if($model_cf_value->count()->getOne())
						$val = false;
					else
						$val = true;
													
					$crud_value = $page->add('xepan\hr\CRUD',['allow_add'=>$val,'frame_options'=>['width'=>'600px'],'entity_name'=>'Specification Value'],null,['view/item/associate/value']);
					$crud_value->form->addClass('xepan-admin-input-full-width');
					$crud_value->setModel($model_cf_value);
					$crud_value->grid->addQuickSearch(['customfield_name']);
					$crud_value->add('xepan\base\Controller_MultiDelete');

				});
			
			$crud_spec->form->getElement('customfield_generic_id')->getModel()->addCondition('type','Specification');
			$crud_spec->form->addClass('xepan-admin-input-full-width');

			$crud_spec->grid->addMethod('format_value',function($grid,$field){
				$data = $grid->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$grid->model->id);
				$l = $grid->add('Lister',null,'Values');
				$l->setModel($data);
				
				$grid->current_row_html[$field] = $l->getHTML();
			});
			$crud_spec->grid->addFormatter('value','value');

			if($action == 'add' || $action == 'edit'){
				// specification form 
				$spec_val_form = $this->add('Form',null,'specification_value_form');
				$spec_val_form->setLayout('view\form\specificationvalue');

				// $spec_val_form->setModel($item_spec,['customfield_generic_id']);
				// $spec_val_form->getElement('customfield_generic_id')->getModel()->addCondition('type','Specification');

				$spec_val_form->addField('DropDown','customfield_generic_id')->setModel('xepan\commerce\Item_Specification');
				$spec_val_form->addField('values')->validate('required');
				$spec_val_form->addField('checkbox','highlight','');
				$spec_val_form->addSubmit('Add Specification')->addClass('btn btn-primary btn-sm');

				if($spec_val_form->isSubmitted()){

					$model_cf_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association');
					$model_cf_asso->addCondition('customfield_generic_id',$spec_val_form['customfield_generic_id']);
					$model_cf_asso->addCondition('item_id',$item->id);
					$model_cf_asso->tryLoadAny();
					if($model_cf_asso->loaded()){
						$spec_val_form->error('customfield_generic_id','specification already added');
					}
					$model_cf_asso['status'] = "Active";
					$model_cf_asso->save();


					$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
										->addCondition('customfield_association_id', $model_cf_asso->id);

					$model_cf_value['name'] = $spec_val_form['values'];
					$model_cf_value['highlight_it'] = $spec_val_form['highlight'];
					$model_cf_value['status'] = $spec_val_form['Active'];
					$model_cf_value->save();

					$js=[	
							$spec_val_form->js()->reload(),
							$crud_spec->js()->reload()
						];

					$spec_val_form->js(null,$js)->execute();	
				}
			}

		/**

		Custom Field

		*/
			$crud_cf = $this->add('xepan\hr\CRUD',['frame_options'=>['width'=>'600px'],'entity_name'=>'CustomField'],'customfield',['view/item/associate/customfield']);
			$cf_model = $item->associateCustomField();
			$cf_model->setOrder('id','desc');

			//Add import default value
			if($crud_cf->isEditing('add')){
				$form = $crud_cf->form;
				$form->addField('checkbox','import_default_value');
			}

			$crud_cf->setModel($cf_model);
			$crud_cf->grid->addColumn('Button','Value');
			$crud_cf->grid->addQuickSearch(['customfield_generic']);
			$crud_cf->grid->addColumn('value');
			$crud_cf->add('xepan\base\Controller_MultiDelete');

			$crud_cf->grid
					->add('VirtualPage')
					->addColumn('Values','Managing Custom Field/ Customer Choice Values',['descr'=>'Values'])
					->set(function($page){

					$id = $_GET[$page->short_name.'_id'];
					$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id', $id);
					$crud_value = $page->add('xepan\hr\CRUD',null,null,['view/item/associate/value']);
					$crud_value->form->addClass('xepan-admin-input-full-width');
					$crud_value->setModel($model_cf_value);
					$crud_value->grid->addQuickSearch(['customfield_name']);
					$crud_value->add('xepan\base\Controller_MultiDelete');
				});			
			$crud_cf->form->getElement('customfield_generic_id')->getModel()->addCondition('type','CustomField');
			// $crud_cf->form->addClass('xepan-admin-input-full-width');

			$crud_cf->grid->addMethod('format_value',function($grid,$field){
				$data = $grid->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$grid->model->id);
				$l = $grid->add('Lister',null,'Values');
				$l->setModel($data);
				
				$grid->current_row_html[$field] = $l->getHTML();
			});
			$crud_cf->grid->addFormatter('value','value');
			
			if($crud_cf->isEditing('add')){
				if($form->isSubmitted()){
					if($form['import_default_value']){
						$saved_model = $crud_cf->model;
						$cf_model = $this->add('xepan\commerce\Model_Item_CustomField')->tryLoad($saved_model['customfield_generic_id']);
						if(!$cf_model->loaded())
							$form->js()->univ()->errorMessage('Custom Field Not Found')->execute();

						$default_values = explode(",",trim($cf_model['value']));
						foreach ($default_values as $value) {
							if(!$value)
								continue;
							$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value');
							$model_cf_value->addCondition('customfield_association_id',$saved_model->id);
							$model_cf_value->addCondition('name',$value);
							$model_cf_value->tryLoadany();
							$model_cf_value['status'] = "Active";
							$model_cf_value->save();
						}
						// $form->js()->univ()->successMessage('Default Value Imported')->execute();
					}
				}
			}

		/**

		Filters

		*/
			$crud_filter = $this->add('xepan\hr\CRUD',['frame_options'=>['width'=>'600px'],'entity_name'=>'Filters'],'filter',['view/item/associate/specification']);
			$crud_filter->setModel($item->associateFilters(),['customfield_generic_id','is_filterable','status'],['customfield_generic','is_filterable','status']);
			$crud_filter->grid->addColumn('Button','Value');
			$crud_filter->grid->addQuickSearch(['customfield_generic']);
			$crud_filter->grid->addColumn('value');
			$crud_filter->add('xepan\base\Controller_MultiDelete');
			$crud_filter->grid
				->add('VirtualPage')
				->addColumn('Values','Managing Filter Values',['descr'=>'Values'])
				->set(function($page){

				$id = $_GET[$page->short_name.'_id'];
				$model_cf_value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
								->addCondition('customfield_association_id', $id);
				$crud_value = $page->add('xepan\hr\CRUD',['frame_options'=>['width'=>'600px'],'entity_name'=>'Filter Value'],null,['view/item/associate/value']);
				$crud_value->form->addClass('xepan-admin-input-full-width');
				$crud_value->setModel(
									$model_cf_value,
									['customfield_association_id','name','status','field_name_with_value','customfield_name','customfield_type','type'],
									['customfield_association_id','customfield_association','name','status','field_name_with_value','customfield_name','customfield_type','type']
							);
				$crud_value->grid->addQuickSearch(['customfield_name']);
				$crud_value->add('xepan\base\Controller_MultiDelete');
			});

			$crud_filter->form->getElement('customfield_generic_id')->getModel()->addCondition('type','Specification')->addCondition('is_filterable',true);
			$crud_filter->form->addClass('xepan-admin-input-full-width');

			$crud_filter->grid->addMethod('format_value',function($grid,$field){
				$data = $grid->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$grid->model->id);
				$l = $grid->add('Lister',null,'Values');
				$l->setModel($data);
				$grid->current_row_html[$field] = $l->getHTML();
			});
			$crud_filter->grid->addFormatter('value','value');

		/**

		Extra

		*/
			$media_m = $this->add('xepan\commerce\Model_Item_Image')->addCondition('item_id',$item->id);
			$media_m->setOrder('sequence_no','asc');
			$crud_media = $this->add('xepan\hr\CRUD',null,'media',['view/item/media']);
			$crud_media->setModel($media_m);

			if($crud_media->isEditing()){
				$value_model = $crud_media->form->getElement('customfield_value_id')->getModel();
				$value_model->addCondition('customfield_type',"CustomField");
				$value_model->addCondition('item_id',$item->id);
				// $value_model->setOrder('field_name_with_value','asc');
			}

			$seo_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'seo',['page/item/detail','seo']);
			$seo_item->setModel($item,['meta_title','meta_description','tags'],
									  ['meta_title','meta_description','tags']);

		/**
			Remove Item Empty Design
		*/
			// if($item['is_designable']){
				$empty_design_form = $this->add('Form',null,'misc_content');
				$empty_design_form->addSubmit('Empty Design')->addClass('btn btn-primary');
				$empty_design_form->add('View_Info')->set('hint: reset your design to empty');
				if($empty_design_form->isSubmitted()){
					$item['designs'] = null;
					$item->save();
					$empty_design_form->js(true)->univ()->successMessage('Item Design Reset To Empty')->execute();
				}
			// }


		/**
			Serialize
		*/	
			$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
			$serial_model->addCondition('item_id',$item->id);

			$crud_serialize = $this->add('xepan\hr\CRUD',['frame_options'=>['width'=>'600px']],'serializable');
			$crud_serialize->setModel($serial_model,['serial_no','is_available','is_return','narration','contact','purchase_order_id','purchase_invoice_id','sale_order_id','sale_invoice_id','dispatch_id','transaction_id']);
			$crud_serialize->grid->addPaginator($ipp=50);
			$crud_serialize->grid->addQuickSearch(['serial_no','narration','purchase_order_id','purchase_invoice_id','sale_order_id','sale_invoice_id','dispatch_id','transaction_row_id']);
			
			$view_other_info = $this->add('View',null,'other_info');
			$item->page_other_info($view_other_info);
		/**
		// Update child item
		*/	
			$update_form = $this->add('Form',null,'update_form')->addClass('xepan-admin-input-full-width');
			$update_form->add('View')->addClass('alert alert-info')->set("Total Item to Update: ".$this->add('xepan\commerce\Model_Item')->addCondition('duplicate_from_item_id',$item->id)->count()->getOne());

			$update_form->addField('dropdown','select_fields','Replicate Associated Information')
						->addClass('multiselect-full-width')
						->setAttr(['multiple'=>'multiple'])
						->setValueList(['Specification'=>'Specification','CustomField'=>'CustomField','Department'=>'Department','QuantitySet'=>'QuantitySet','Category'=>'Category','Image'=>'Image', 'Taxation'=>'Taxation' , 'Shipping'=>'Shipping','Filter'=>"Filter" ,'All'=>'All']);
		
			$update_form->addField('dropdown','replicate_fields')
						->addClass('multiselect-full-width')
						->setAttr(['multiple'=>'multiple'])
						->setValueList(['original_price'=>'original_price','sale_price'=>'sale_price', 'expiry_date'=>'expiry_date', 'description'=>'description', 'show_detail'=>'show_detail', 'show_price'=>'show_price', 'is_new'=>'is_new', 'is_mostviewed'=>'is_mostviewed', 'Item_enquiry_auto_reply'=>'Item_enquiry_auto_reply', 'is_comment_allow'=>'is_comment_allow', 'comment_api'=>'comment_api', 'add_custom_button'=>'add_custom_button', 'custom_button_url'=>'custom_button_url', 'meta_title'=>'meta_title', 'meta_description'=>'meta_description', 'tags'=>'tags', 'is_designable'=>'is_designable', 'is_party_publish'=>'is_party_publish', 'minimum_order_qty'=>'minimum_order_qty', 'maximum_order_qty'=>'maximum_order_qty', 'qty_unit_id'=>'qty_unit', 'is_attachment_allow'=>'is_attachment_allow', 'is_saleable'=>'is_saleable', 'is_downloadable'=>'is_downloadable', 'is_rentable'=>'is_rentable', 'is_enquiry_allow'=>'is_enquiry_allow', 'negative_qty_allowed'=>'negative_qty_allowed', 'enquiry_send_to_admin'=>'enquiry_send_to_admin', 'watermark_position'=>'watermark_position', 'watermark_opacity'=>'watermark_opacity', 'qty_from_set_only'=>'qty_from_set_only', 'custom_button_label'=>'custom_button_label', 'is_servicable'=>'is_servicable', 'is_purchasable'=>'is_purchasable', 'maintain_inventory'=>'maintain_inventory', 'website_display'=>'website_display', 'allow_negative_stock'=>'allow_negative_stock', 'is_productionable'=>'is_productionable', 'warranty_days'=>'warranty_days', 'terms_and_conditions'=>'terms_and_conditions', 'watermark_text'=>'watermark_text', 'is_allowuploadable'=>'is_allowuploadable', 'designer_id'=>'designer_id', 'is_dispatchable'=>'is_dispatchable', 'upload_file_label'=>'upload_file_label', 'item_specific_upload_hint'=>'item_specific_upload_hint','hsn_sac'=>'HSN/SAC']);

			$update_form->addSubmit('Update');
		
			if($update_form->isSubmitted()){
				if(!$update_form['select_fields'] and !$update_form['replicate_fields'])
					$update_form->error('select_fields','please select field to update');

				$fields = explode(',', $update_form['select_fields']);
				if($update_form['replicate_fields']){
					$replica_fields = explode(',', $update_form['replicate_fields']);
				}else{
					$replica_fields=[];
				}

				try{
		            $this->app->db->beginTransaction();
					$item->updateChild($fields, $replica_fields);		            
		            $this->app->db->commit();
		        }catch(\Exception_StopInit $e){

		        }catch(\Exception $e){
		            $this->app->db->rollback();
		            throw $e;
		        }

				$update_form->js(true)->univ()->successMessage("All Child Item Updated")->execute();
			}


		/**

		Category Item Association

		*/	
			$crud_cat_asso = $this->add('xepan\base\Grid',
										['fixed_header'=>false],
										'category',
										['view/item/associate/category']
									);

			$model_active_category = $this->add('xepan\commerce\Model_Category')->addCondition('status','Active');

			$form = $this->add('Form',null,'cat_asso_form');
			$ass_cat_field = $form->addField('hidden','ass_cat')->set(json_encode($item->getAssociatedCategories()));
			$form->addSubmit('Update');

			$crud_cat_asso->addQuickSearch(['effective_name']);
			$crud_cat_asso->setModel($model_active_category,array('effective_name'));
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
									['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit_id','qty_unit','qty_from_set_only','weight','quantity_group','treat_sale_price_as_amount'],
									['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit_id','qty_unit','qty_from_set_only','weight','quantity_group','treat_sale_price_as_amount']
								);
			if($basic_price->form){
				$qty_field = $basic_price->form->getElement('qty_unit_id');
				$qty_field->getModel()->title_field = "name_with_group";
			}
			//Quantity set Condition/Rate Chart
			$crud_qty_set_condition = $this->add('xepan\hr\CRUD',['frame_options'=>['width'=>'600px']],'qtysetcondition',['view/item/qtysetcondition']);
			$model_qtyset = $this->add('xepan\commerce\Model_Item_Quantity_Set');
			$model_qtyset->addCondition('item_id',$item->id);
			$model_qtyset->setOrder(array('custom_fields_conditioned desc','qty desc'));

			$crud_qty_set_condition->setModel($model_qtyset);
			$crud_qty_set_condition->grid->addQuickSearch(['name','qty','price']);

			$crud_qty_set_condition->grid->addColumn('Button','Condition');

			$crud_qty_set_condition->grid
								->add('VirtualPage')
								->addColumn('condition','Managing Quantity Set Condition',['descr'=>'Condition'])
								->set(function($page)use($item){

								$id = $_GET[$page->short_name.'_id'];
								$model_qty_condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')
															->addCondition('quantity_set_id', $id);
								$crud_condition = $page->add('xepan\hr\CRUD',['frame_options'=>['width'=>'600px'],'entity_name'=>'Conditions'],null,['view/item/associate/quantitycondition']);
								$crud_condition->setModel($model_qty_condition);
								if($crud_condition->isEditing()){
									$crud_condition->form->getElement('customfield_value_id')
														->getModel()
														->addCondition('customfield_type','CustomField')
														->addCondition('item_id',$item->id)
														;
									$crud_condition->form->addClass('xepan-admin-input-full-width');
								}
								$crud_condition->add('xepan\base\Controller_MultiDelete');
							});
			$crud_qty_set_condition->grid->addPaginator('100');
			$crud_qty_set_condition->add('xepan\base\Controller_MultiDelete');
			/**
			
			CSV Uploader

			*/
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

			$model_department = $this->add('xepan\hr\Model_Department')
					->addCondition('status','Active')
					->setOrder('production_level','asc');

			$form_dept_asso = $this->add('Form',null,'item_dept_asso_form');
			$item_dept_asso_field = $form_dept_asso->addField('hidden','ass_dept')->set(json_encode($item->getAssociatedDepartment()));
			$form_dept_asso->addSubmit('Update');

			$grid_dept_asso->setModel($model_department,array('name'));
			$grid_dept_asso->addSelectable($item_dept_asso_field);
			$grid_dept_asso->addQuickSearch(['name']);
			if($form_dept_asso->isSubmitted()){

				$old_asso_model = $item->ref('xepan\commerce\Item_Department_Association');
				$old_asso_array = [];
				foreach ($old_asso_model as $m) {
					$old_asso_array[$m['department_id']] = $m->id;
				}

				$selected_department = json_decode($form_dept_asso['ass_dept'],true);
				foreach ($selected_department as $dept_id) {

					if(isset($old_asso_array[$dept_id])) unset($old_asso_array[$dept_id]);

					$model_asso = $this->add('xepan\commerce\Model_Item_Department_Association');
					$model_asso->addCondition('department_id',$dept_id);
					$model_asso->addCondition('item_id',$item->id);
					$model_asso->tryLoadAny();
					$model_asso->saveAndUnload();
				}

				if(count($old_asso_array)){
					$ida_model = $this->add('xepan\commerce\Model_Item_Department_Association');
					$ida_model->addCondition('id',$old_asso_array);
					$ida_model->deleteAll();
				}

				$form_dept_asso->js(null,$this->js()->univ()->successMessage('Department Added to this Item'))->reload()->execute();
			}

			$grid_dept_asso->add('VirtualPage')
 				->addColumn('consumption')
				->set(function($page)use($item){

					$department_id = $page->api->stickyGET($page->short_name.'_id');

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

					$this->app->show_only_stock_effect_customField = true;
					$model_item_consumption = $this->add('xepan\commerce\Model_Item_Department_Consumption')
											->addCondition('item_department_association_id',$dept_assos->id);

					$crud = $page->add('xepan\base\CRUD');//,null,null,['view\item\associate\departmentconsumption']);
					$crud->setModel($model_item_consumption,['composition_item_id','quantity','custom_fields'],['composition_item','quantity','unit','custom_fields']);
					$crud->add('xepan\base\Controller_MultiDelete');
					
					if($crud->isEditing()){
						$form = $crud->form;
						$field_consumption_item = $form->getElement('composition_item_id');
						$field_consumption_item->custom_field_element = "custom_fields";
						$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
					}
					$this->app->show_only_stock_effect_customField = false;

					$crud->grid->add('VirtualPage')
		 				->addColumn('constraints')
						->set(function($page)use($item,$department_id){
							$consumption_id = $page->api->stickyGET($page->short_name.'_id');
							
							$model_constraints = $this->add('xepan\commerce\Model_Item_Department_ConsumptionConstraint');
							$model_constraints->addCondition('item_department_consumption_id',$consumption_id);
							$model_constraints->addCondition('item_id',$item->id);

							$crud = $page->add('xepan\base\CRUD');
							$crud->setModel($model_constraints,['item_customfield_asso_id','item_customfield_value_id','item_department_consumption_id','item_customfield_asso','item_customfield_value'],['item_customfield_asso','item_customfield_value']);

							if($crud->isEditing()){
								$form = $crud->form;

								$field_item_cust_asso = $form->getElement('item_customfield_asso_id');
								$model_cust_asso = $field_item_cust_asso->getModel();
								$model_cust_asso
										->addCondition('item_id',$item->id)
										->addCondition('department_id',$department_id);
								$model_cust_asso
										->_dsql()->group($model_cust_asso->dsql()->expr('[0]',[$model_cust_asso->getElement('customfield_generic_id')]));

								$field_item_cust_value = $form->getElement('item_customfield_value_id');
								$model_cust_value = $field_item_cust_value->getModel();
								$model_cust_value->addCondition('item_id',$item->id);

								// autocomplete reload
								$field_item_cust_value->send_other_fields = [$field_item_cust_asso];
								if($cust_asso_id = $_GET['o_'.$field_item_cust_asso->name]){
									$field_item_cust_value->getModel()->addCondition('customfield_association_id',$cust_asso_id);
								}
								
							}


						});
			});

	/**

		Accounts

	*/		
			$act = $this->add('xepan\commerce\Model_Item_Taxation_Association')
						->addCondition('item_id',$item->id);
			$crud_ac = $this->add('xepan\hr\CRUD',null,'taxation',['view/item/accounts/tax']);
			$crud_ac->setModel($act);
			$crud_ac->add('xepan\base\Controller_MultiDelete');

			$crud_ac->grid->addQuickSearch(['taxation_rule']);

			// nominal
			$form = $this->add('Form',null,'nominal');
			$nominal_field = $form->addField('xepan\base\DropDown','nominal','Sales Nominal')->setFieldHint("item/product is saleable");
			$nominal_field->setModel($this->add('xepan\accounts\Model_Ledger'));
			$nominal_field->setEmptyText('Please Select');
			$nominal_field->set($item['nominal_id']);

			$p_nominal_field = $form->addField('xepan\base\DropDown','pnominal','Purchase Nominal')->setFieldHint("item/product is purchasable");
			$p_nominal_field->setModel($this->add('xepan\accounts\Model_Ledger'));
			$p_nominal_field->setEmptyText('Please Select');
			$p_nominal_field->set($item['purchase_nominal_id']);

			$form->addSubmit();
			if($form->isSubmitted()){
				$item['nominal_id'] = $form['nominal'];
				$item['purchase_nominal_id'] = $form['pnominal'];
				$item->save();
				$form->js(null,$form->js()->reload())->univ()->successMessage('Nominal Saved Successfully')->execute();
			}

	
	/**

		Shipping Association

	*/	
			$shipping_asso = $this->add('xepan\commerce\Model_Item_Shipping_Association')
						->addCondition('item_id',$item->id);
			$crud_shipping = $this->add('xepan\hr\CRUD',null,'shippingassociation',['view/item/associate/shippingrule']);
			$crud_shipping->setModel($shipping_asso);
			$crud_shipping->grid->addQuickSearch(['shipping_rule']);
			$crud_shipping->add('xepan\base\Controller_MultiDelete');
	/**

		Package Item Association

		*/	
			if($item['is_package'] == true){
				$c = $this->add('xepan\base\CRUD',
										null,
										'package'/*,
										['view/item/associate/category']*/
									);					
				$c->setModel($item->ref('MyPackageItems'));
				if($c->isEditing()){
					$form = $c->form;
					$form->getElement('item_id')->getModel()->addCondition([['is_package',false],['is_package',null]]);
					$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
				}

				// $form = $this->add('Form',null,'package_item_asso_form');
				// $package_item = $form->addField('xepan\base\DropDown','package_item')->addClass('xepan-push');
				// $package_item->validate_values=false;
				// $package_item->setAttr(['multiple'=>'multiple']);
				// $package_item->setModel($item_asso);

				// $form->addSubmit('Update');

				// if($form->isSubmitted()){
				// 	$item->ref('xepan\commerce\PackageItemAssociation')->deleteAll();
				// 	$package_item_array=[];
				// 	if(!$form['package_item']){
				// 		$f->displayError('package_item',"Please Select Package Item");
				// 	}
				// 	foreach (explode(',', $form['package_item']) as $name => $id) {
				// 		$model_asso = $this->add('xepan\commerce\Model_PackageItemAssociation');
				// 		$model_asso['package_item_id'] = $item->id;
				// 		$model_asso['item_id'] = $id;
				// 		$model_asso->save();
				// 	}

				// 	$form->js(null,$this->js()->univ()->successMessage('Items Associated'))->reload()->execute();
				// }

			}else{
				$this->add('View',null,'package_item_asso_form')->set('This is not a Package Item');
			}
					


		}
	

	}

	function format_created_at($value,$m){
		return date('d M Y',strtotime($value));
	}

	function defaultTemplate(){
		return ['page/item/detail'];

	}

}


