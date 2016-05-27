<?php
namespace xepan\commerce;

class Model_Item extends \xepan\hr\Model_Document{
	public $status = ['Published','UnPublished'];

	// draft
		// Item are not published or is_party published off
	//submitted 
		//item status unpublished and and is_paty published
	//published 
		// Item is published true

	public $actions = [
	'Published'=>['view','edit','delete','unpublish','duplicate'],
	'UnPublished'=>['view','edit','delete','publish','duplicate']
	];

	function init(){
		parent::init();

		$this->getElement('created_by_id')->defaultValue($this->app->employee->id);
		$item_j=$this->join('item.document_id');

		$item_j->hasOne('xepan\base\Contact','designer_id')->defaultValue(0);

		$item_j->addField('name')->mandatory(true)->sortable(true);
		$item_j->addField('sku')->PlaceHolder('Insert Unique Referance Code')->caption('Code')->hint('Insert Unique Referance Code')->mandatory(true);
		$item_j->addField('display_sequence')->hint('descending wise sorting');
		$item_j->addField('description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		
		$item_j->addField('original_price')->type('money')->mandatory(true)->defaultValue(0);
		$item_j->addField('sale_price')->type('money')->mandatory(true)->defaultValue(0)->sortable(true);
		
		$item_j->addField('expiry_date')->type('date')->defaultValue(null);
		
		$item_j->addField('minimum_order_qty')->type('int')->defaultValue(1);
		$item_j->addField('maximum_order_qty')->type('int')->defaultValue(null);
		$item_j->addField('qty_unit')->defaultValue(null);
		$item_j->addField('qty_from_set_only')->type('boolean')->defaultValue(true);
		
		//Item Allow Optins
		$item_j->addField('is_party_publish')->type('boolean')->hint('Freelancer Item Design/Template to be Approved')->defaultValue(false);
		$item_j->addField('is_saleable')->type('boolean')->hint('Make Item Becomes Saleable')->defaultValue(false);
		$item_j->addField('is_allowuploadable')->type('boolean')->hint('on website customer can upload a degin for designable item')->defaultValue(false);
		$item_j->addField('is_purchasable')->type('boolean')->hint('item display only at purchase Order/Invoice')->defaultValue(false);
		//Item Stock Options
		// $item_j->addField('available_stock')->type('boolean')->hint('Stock Availability ');
		$item_j->addField('maintain_inventory')->type('boolean')->hint('Manage Inventory ')->defaultValue(false);
		$item_j->addField('allow_negative_stock')->type('boolean')->hint('show item on website apart from stock is available or not')->defaultValue(false);
		$item_j->addField('is_dispatchable')->type('boolean')->hint('show item on website apart from stock is is dispatchable or not')->defaultValue(false);
		$item_j->addField('negative_qty_allowed')->type('boolean')->hint('allow the negative stock until this quantity')->defaultValue(false);
		$item_j->addField('is_visible_sold')->type('boolean')->hint('display item on website after out of stock/all sold')->defaultValue(false);
		
		$item_j->addField('is_servicable')->type('boolean')->defaultValue(false);
		$item_j->addField('is_productionable')->type('boolean')->hint('used in Production')->defaultValue(false);
		$item_j->addField('website_display')->type('boolean')->hint('Show on Website')->defaultValue(false);
		$item_j->addField('is_downloadable')->type('boolean')->defaultValue(false);
		$item_j->addField('is_rentable')->type('boolean')->defaultValue(false);
		$item_j->addField('is_designable')->type('boolean')->hint('item become designable and customer customize the design')->defaultValue(false);
		$item_j->addField('is_template')->type('boolean')->hint('blueprint/layout of designable item')->defaultValue(false);
		$item_j->addField('is_attachment_allow')->type('boolean')->hint('by this option you can attach the item information pdf/doc etc. to be available on website')->defaultValue(false);
		
		$item_j->addField('warranty_days')->type('int')->defaultValue(null);
		$item_j->addField('weight')->defaultValue(0);
		
		//Item Display Options
		$item_j->addField('show_detail')->type('boolean');
		$item_j->addField('show_price')->type('boolean');

		//Marked
		$item_j->addField('is_new')->type('boolean')->caption('New');
		$item_j->addField('is_feature')->type('boolean')->caption('Featured');
		$item_j->addField('is_mostviewed')->type('boolean')->caption('Most Viewed');

		//Enquiry Send To
		$item_j->addField('is_enquiry_allow')->type('boolean')->hint('display enquiry form at item detail on website')->defaultValue(false);
		$item_j->addField('enquiry_send_to_admin')->type('boolean')->hint('send a copy of enquiry form to admin')->defaultValue(false);
		$item_j->addField('item_enquiry_auto_reply')->type('boolean')->caption('Item Enquiry Auto Reply')->defaultValue(false);
		
		//Item Comment Options
		$item_j->addField('is_comment_allow')->type('boolean')->defaultValue(false);
		$item_j->addField('comment_api')->setValueList(array('disqus'=>'Disqus'))->defaultValue('');

		//Item Other Options
		$item_j->addField('add_custom_button')->type('boolean');
		$item_j->addField('custom_button_label')->defaultValue(null);
		$item_j->addField('custom_button_url')->placeHolder('subpage name like registration etc.');
		
		// Item WaterMark
		// $item_j->add('xepan/filestore/Field_Image','watermark_image_id');
		$item_j->addField('watermark_text')->type('text')->defaultValue('');
		$item_j->addField('watermark_position')->enum(array('TopLeft','TopRight','BottomLeft','BottomRight','Center','Left Diagonal','Right Diagonal'))->defaultValue('Center');
		$item_j->addField('watermark_opacity')->defaultValue(50);
		
		//Item SEO
		$item_j->addField('meta_title')->defaultValue(null);
		$item_j->addField('meta_description')->type('text')->defaultValue(null);
		$item_j->addField('tags')->type('text')->PlaceHolder('Comma Separated Value')->defaultValue(null);

		//Item Designs
		$item_j->addField('designs')->type('text')->hint('used for internal, design saved')->defaultValue(null);

		//others
		$item_j->addField('terms_and_conditions')->type('text')->defaultValue(null);
		$item_j->addField('duplicate_from_item_id')->hint('internal used saved its parent')->defaultValue(null);

		$item_j->addField('upload_file_label')->type('text')->hint('comma separated multiple file name');;
		$item_j->addField('item_specific_upload_hint')->type('text')->hint('Hint for upload images')->defaultValue(null);

		$item_j->addField('to_customer_id')->hint('Specific to customer/organization')->defaultValue(null);

		$this->addCondition('type','Item');

		$this->getElement('status')->defaultValue('UnPublished');

		//Quantity set condition just for relation
		$item_j->hasMany('xepan\commerce\Item_Quantity_Set','item_id');
		$item_j->hasMany('xepan\commerce\Item_CustomField_Association','item_id');
		$item_j->hasMany('xepan\commerce\Item_Department_Association','item_id',null);
		//Category Item Association
		$item_j->hasMany('xepan\commerce\CategoryItemAssociation','item_id');
		//Member Design
		$item_j->hasMany('xepan\commerce\Item_Template_Design','item_id');
		$this->hasMany('xepan\commerce\Store_TransactionRow','item_id',null,'StoreTransactionRows');
		$this->hasMany('xepan\commerce\QSP_Detail','item_id',null,'QSPDetail');
		$item_j->hasMany('xepan\commerce\Item_Image','item_id',null,'ItemImages');
		$item_j->hasMany('xepan\commerce\Item_Taxation_Association','item_id',null,'Tax');
		
		
		//Stock Availability

		$this->addExpression('available_stock')->set(function($m){
			return "'0'";
		});
		//Image

		$this->addExpression('first_image')->set(function($m){
			return $m->refSQL('ItemImages')->setLimit(1)->fieldQuery('thumb_url');
		});

		//Total Sales And Total Orders

		$this->addExpression('total_orders')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_orders']);
			$qsp_details->addExpression('document_type')->set($qsp_details->refSQL('qsp_master_id')->fieldQuery('type'));
			$qsp_details->addCondition('document_type','SalesOrder');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			return $qsp_details->_dsql()->del('fields')->field($q->expr('SUM([0])',[$qsp_details->getElement('quantity')]));
		})->sortable(true);

		// $this->debug();

		$this->addExpression('total_sales')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales']);
			$qsp_details->addExpression('document_type')->set($qsp_details->refSQL('qsp_master_id')->fieldQuery('type'));
			$qsp_details->addCondition('document_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			return $qsp_details->_dsql()->del('fields')->field($q->expr('SUM([0])',[$qsp_details->getElement('quantity')]));
		});

		$this->addHook('beforeDelete', $this);
		$this->addHook('beforeSave',[$this,'updateSearchString']);

		$this->is([
				'name|to_trim|required',
				'sku|to_trim|required|unique_in_epan',
				'display_sequence|int',
				'original_price|number',
				'sale_price|number|>=0',
				'minimum_order_qty|number|>0'
			]);

	}

	function beforeDelete($m){
		$count = $this->ref('QSPDetail')->count()->getOne();
		$customfield_count = $this->ref('xepan\commerce\Item_CustomField_Association');
		
		foreach ($customfield_count as $cf) {
			$cf->delete();
		}

		if($count >0 ){
			throw new \Exception("Please Delete the associated invoice, order, customfields etc. first");
		}
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['name'];
		$search_string .=" ". $this['sku'];
		$search_string .=" ". $this['original_price'];
		$search_string .=" ". $this['sale_price'];
		$search_string .=" ". $this['description'];
		$search_string .=" ". $this['tags'];

		if($this->loaded()){
			$categoryfields = $this->ref('xepan\commerce\CategoryItemAssociation');
			foreach ($categoryfields as $all_categoryfields) {
				$search_string .=" ". $all_categoryfields['item_id'];
				$search_string .=" ". $all_categoryfields['category_id'];
			}
		}
		
		if($this->loaded()){
			$quantity_set = $this->ref('xepan\commerce\Item_Quantity_Set');
			foreach ($quantity_set as $all_quantity_set) {
				$search_string .=" ". $all_quantity_set['name'];
				$search_string .=" ". $all_quantity_set['shipping_charge'];
				$search_string .=" ". $all_quantity_set['price'];
			}
		}

		if($this->loaded()){
			$customfields = $this->ref('xepan\commerce\Item_CustomField_Association');
			foreach ($customfields as $customfield) {

				$values = $customfield->ref('xepan\commerce\Item_CustomField_Value');
				foreach ($values as $value) {
					$search_string .="". $value['name'];	
				}	
				$search_string .=" ". $customfield['name'];
				$search_string .=" ". $customfield['CustomFieldType'];
			}
		}


		// if($this->loaded()){
		// 	$qsp_detail = $this->ref('QSPDetail');
		// 	foreach ($qsp_detail as $all_qsp_detail) {
		// 		$search_string .=" ". $all_qsp_detail['qsp_master_id'];
		// 		$search_string .=" ". $all_qsp_detail['name'];
		// 		$search_string .=" ". $all_qsp_detail['customer'];
		// 		$search_string .=" ". $all_qsp_detail['qsp_type'];
		// 	}
		// }

		$this['search_string'] = $search_string;
		
	}

	function publish(){
		$this['status']='Published';
		$this->app->employee
		->addActivity("Item '".$this['name']."' now published", $this->id/* Related Document ID*/, null /*Related Contact ID*/)
		->notifyWhoCan('publish,duplicate','UnPublished');
		$this->save();
	}

	function unpublish(){
		$this['status']='UnPublished';
		$this->app->employee
		->addActivity("Item '".$this['name']."' has been unpublished", $this->id/* Related Document ID*/, null /*Related Contact ID*/)
		->notifyWhoCan('unpublish,duplicate','Published');
		$this->save();
	}

	function page_duplicate($page){
		$designer = $this->add('xepan\base\Model_Contact');
		$designer->addCondition(
						$designer->dsql()->orExpr()
						->where('type','Employee')
						->where('type','Customer'));

		$form = $page->add('Form');
		$form->addField('name')->set($this['name'].'-copy');
		$form->addField('sku')->set($this['sku'].'-copy');
		if($this['is_designable']){
			$field_designer = $form->addField('DropDown','designer');
			$field_designer->setModel($designer);
			$field_designer->set($this->app->employee->id);
		}
		$form->addSubmit('Duplicate');

		if($form->isSubmitted()){
			$item = $this->add('xepan\commerce\Model_Item');
			$item->addCondition('name',$form['name']);
			$item->tryLoadAny();

			if($item->loaded()){
				$form->displayError('name','Item with this name already exist, please choose a different name');
			}

			$sku_item = $this->add('xepan\commerce\Model_Item');
			$sku_item->addCondition('sku',$form['sku']);
			$sku_item->tryLoadAny();
			
			if($sku_item->loaded()){
				$form->displayError('sku','sku already exist, please choose a different sku');
			}

			$designer->loadLoggedIn();

			try{
				$this->api->db->beginTransaction();

				$name = $form['name']; 
				$sku = $form['sku'];
				$designer_id = $form['designer'];
				$is_template = false;
				$is_published = false;
				$create_default_design_also  = false;
				$duplicate_from_item_id = $this->id;     		
				$new_item = $this->duplicate($name, $sku, $designer_id, $is_template, $is_published, $duplicate_from_item_id,$create_default_design_also);
				$this->api->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
	            throw $e;
			}

			$this->api->redirect($this->app->url('xepan_commerce_itemdetail',['document_id'=>$new_item->id, 'action'=>'edit']));

		}
	}

	function duplicate($name, $sku, $designer_id, $is_template, $is_published, $duplicate_from_item_id, $create_default_design_also,$to_customer_id=null){

		$model_item = $this->add('xepan\commerce\Model_Item');

		$fields=$this->getActualFields();
		$fields = array_diff($fields,array('id','name','sku','designer_id', 'is_published', 'created_at','is_template','duplicate_from_item_id'));

		foreach ($fields as $fld) {
			$model_item[$fld] = $this[$fld];
		}

		// $model_item->save();

		$model_item['name'] = $name;
		$model_item['sku'] = $sku;
		$model_item['designer_id'] = $designer_id;
		// $model_item['created_at'] = $created_at;
		$model_item['is_template'] = $is_template;
		$model_item['is_published'] = $is_published;
		$model_item['duplicate_from_item_id'] = $duplicate_from_item_id;
		$model_item['created_at'] = $this->app->now;
		$model_item['to_customer_id'] = $to_customer_id;

		$model_item->save();

		if($create_default_design_also){
			$new_design = $this->add('xepan\commerce\Model_Item_Template_Design');
			$item_id = $model_item->id;
			$item_design = $model_item['designs'];
			$new_design->duplicate($to_customer_id, $item_id, $item_design);
		}

		//specification duplicate
		$this->duplicateSpecification($model_item);
		$this->duplicateCustomfields($model_item);
		$this->duplicateItemDepartmentAssociation($model_item);
		$this->duplicateQuantitySet($model_item);
		$this->duplicateCategoryItemAssociation($model_item);
		$this->duplicateTemplateDesign($model_item);
		$this->duplicateImage($model_item);
		$this->duplicateItemTaxationAssociation($model_item);

		return $model_item;
	}

	function duplicateSpecification($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);
		
		$old_specification =  $this->specification();
		foreach ($old_specification as $old_spec) {
			$new_spec = $this->add('xepan\commerce\Model_Item_CustomField_Association');
			$new_spec['customfield_generic_id'] = $old_spec['customfield_generic_id'];
			$new_spec['item_id'] = $new_item->id;
			$new_spec['department_id'] = $old_spec['department_id'];
			$new_spec['can_effect_stock'] = $old_spec['can_effect_stock'];
			$new_spec['status'] = $old_spec['status'];
			
			$new_spec->save();
			$old_spec->duplicateValue($new_spec,$new_item);
			$new_spec->unload();
		}
	}

	function duplicateCustomfields($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);
		
		$old_customfields =  $this->associateCustomField();
		foreach ($old_customfields as $old_fields) {
			$new_fields = $this->add('xepan\commerce\Model_Item_CustomField_Association');
			$new_fields['customfield_generic_id'] = $old_fields['customfield_generic_id'];
			$new_fields['item_id'] = $new_item->id;
			$new_fields['department_id'] = $old_fields['department_id'];
			$new_fields['can_effect_stock'] = $old_fields['can_effect_stock'];
			$new_fields['status'] = $old_fields['status'];
			
			$new_fields->save();
			$old_fields->duplicateValue($new_fields,$new_item);
			$new_fields->unload();
		}
	}


	function duplicateItemDepartmentAssociation($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		$old_asso = $this->ref('xepan\commerce\Item_Department_Association');
		foreach ($old_asso as $old_asso_fields ) {
			$model_dept_asso = $this->add('xepan\commerce\Model_Item_Department_Association');
			$model_dept_asso['item_id'] = $new_item->id; 
			$model_dept_asso['department_id'] = $old_asso_fields['department_id'];
			$model_dept_asso['can_redefine_qty'] = $old_asso_fields['can_redefine_qty'];
			$model_dept_asso['can_redefine_item'] = $old_asso_fields['can_redefine_item'];
			$model_dept_asso->saveAndUnload();
		}
	}

	function duplicateQuantitySet($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		$old_qtyset = $this->add('xepan\commerce\Model_Item_Quantity_Set')->addCondition('item_id',$this->id);

		foreach ($old_qtyset as $old_qty_felds ) {
			$model_qty_set = $this->add('xepan\commerce\Model_Item_Quantity_Set');
			$model_qty_set['item_id'] = $new_item->id;
			$model_qty_set['name'] = $old_qty_felds['name'];
			$model_qty_set['qty'] = $old_qty_felds['qty'];
			$model_qty_set['old_price'] = $old_qty_felds['old_price'];
			$model_qty_set['price'] = $old_qty_felds['price'];
			$model_qty_set['is_default'] = $old_qty_felds['is_default'];
			$model_qty_set['shipping_charge'] = $old_qty_felds['shipping_charge'];
			$model_qty_set->save();

			$conditions = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$old_qty_felds->id);
			foreach ($conditions as $itm_qty_conditions) {
				$model_conditions = $this->add('xepan\commerce\Model_Item_Quantity_Condition');
				$model_conditions['quantity_set_id'] = $model_qty_set->id;
				$model_conditions['customfield_value_id'] = $itm_qty_conditions['customfield_value_id'];
				$model_conditions->saveAndUnload();
			}

			$model_qty_set->unload();
		}
	}

	function duplicateCategoryItemAssociation($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		$old_cat = $this->ref('xepan\commerce\CategoryItemAssociation');
		foreach ($old_cat as $old_cat_fields) {
			$model_cat_assoc = $this->add('xepan\commerce\Model_CategoryItemAssociation');
			$model_cat_assoc['item_id'] = $new_item->id;
			$model_cat_assoc['category_id'] =$old_cat_fields['category_id'];
			$model_cat_assoc->saveAndUnload(); 
		}
	}

	function duplicateTemplateDesign($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		$old_design = $this->ref('xepan\commerce\Item_Template_Design');
		foreach ($old_design as $old_design_fields) {
			$model_contact = $this->add('xepan\base\Model_Contact');
			$model_contact->loadLoggedIn();

			$model_itm_template = $this->add('xepan\commerce\Model_Item_Template_Design');
			$model_itm_template['item_id']= $new_item->id;
			$model_itm_template['cotact_id']=$model_contact->id;
			$model_itm_template['name']=$old_design_fields['name'];
			$model_itm_template['last_modified']=$old_design_fields['last_modified'];
			$model_itm_template['is_ordered']=$old_design_fields['is_ordered'];
			$model_itm_template['designes']=$old_design_fields['designes'];
			$model_itm_template->saveAndUnload();	    		
		}	    	
	}

	function duplicateImage($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		$old_image = $this->ref('ItemImages');
		foreach ($old_image as $old_image_fields) {
			$model_item_Image = $this->add('xepan\commerce\Model_Item_Image');
			$model_item_Image['item_id'] = $new_item->id;
			$model_item_Image['file_id'] = $old_image_fields['file_id'];
			$model_item_Image['customfield_value_id'] = $old_image_fields['customfield_value_id'];
			$model_item_Image->saveAndUnload();
		}	
	}

	function duplicateItemTaxationAssociation($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		$old_tax = $this->ref('Tax');
		foreach ($old_tax as $old_tax_fields) {
			$model_tax_assoc = $this->add('xepan\commerce\Model_Item_Taxation_Association');
			$model_tax_assoc['item_id'] = $new_item->id;
			$model_tax_assoc['taxation_id'] = $old_tax_fields['taxation_id'];
			$model_tax_assoc->saveAndUnload();
		}	
	}

	function updateChild($fields, $replica_fields){
		
		$childs = $this->add('xepan\commerce\Model_Item')->addCondition('duplicate_from_item_id',$this->id);

		
		if(empty(!$replica_fields)){
			foreach ($replica_fields as $field) {
				foreach ($childs as $this_child) {
					$this_child[$field] = $this[$field];
					$this_child->save();
				}
			}
		}

		foreach ($fields as $value) {
			foreach ($childs as  $child_item) {
				switch ($value) {
					case 'Specification':

					$child_item->removeSpecificationAssociation();
					$this->duplicateSpecification($child_item);
					break;
					case 'CustomField':
					$child_item->removeCustomfields();
					$this->duplicateCustomfields($child_item);
					break;	
					case 'Department':
					$child_item->removeItemDepartmentAssociation();
					$this->duplicateItemDepartmentAssociation($child_item);
					break;
					case 'QuantitySet':
					$child_item->removeQuantitySet();
					$this->duplicateQuantitySet($child_item);
					break;
					case 'Category':
					$child_item->removeCategoryItemAssociation();
					$this->duplicateCategoryItemAssociation($child_item);
					break;
					case 'Template Design':
					$child_item->removeTemplateDesign();
					$this->duplicateTemplateDesign($child_item);
					break;
					case 'Image':
					$child_item->removeImageAssociation();
					$this->duplicateImage($child_item);
					break;
					case 'Taxation':
					$child_item->removeItemTaxationAssociation();
					$this->duplicateItemTaxationAssociation($child_item);
					break;						
					default:

					$child_item->removeSpecificationAssociation();
					$this->duplicateSpecification($child_item);

					$child_item->removeCustomfields();
					$this->duplicateCustomfields($child_item);

					$child_item->removeItemDepartmentAssociation();
					$this->duplicateItemDepartmentAssociation($child_item);

					$child_item->removeQuantitySet();
					$this->duplicateQuantitySet($child_item);

					$child_item->removeCategoryItemAssociation();
					$this->duplicateCategoryItemAssociation($child_item);

					$child_item->removeTemplateDesign();
					$this->duplicateTemplateDesign($child_item);

					$child_item->removeImageAssociation();
					$this->duplicateImage($child_item);

					$child_item->removeItemTaxationAssociation();
					$this->duplicateItemTaxationAssociation($child_item);
					break;
				}
			}	    
		}    	
	}

	function associateSpecification(){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");

		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		->addCondition('can_effect_stock',false)
		;
		$asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addCondition('customfield_type','Specification');
		$asso->tryLoadAny();
		return $asso;

	}

	function associateFilters(){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");

		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		->addCondition('is_filterable',true)
		;
		$asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addCondition('customfield_type','Specification');
		$asso->tryLoadAny();
		
		return $asso;

	}

	function associateCustomField($department_phase_id=false){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");

		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		;
		if($department_phase_id)
			$asso->addCondition('department_id',$department_phase_id);
		
		$asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addExpression('sequence_order')->set($asso->refSQL('customfield_generic_id')->fieldQuery('sequence_order'));
		$asso->addCondition('customfield_type','CustomField');
		$asso->setOrder('name','asc');
		$asso->setOrder('sequence_order','asc');
		$asso->tryLoadAny();
		
		return $asso;		
	}

	function getAssociatedCustomFields($department_id){
		$associated_cf = $this->associateCustomField($department_id)->_dsql()->del('fields')->field('customfield_generic_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_cf)),false);
	}

	function activeAssociateCustomField(){
		return $this->associateCustomField()->addCondition('status','Active');
		
	}

	function getAssociatedCategories(){

		$associated_categories = $this->ref('xepan\commerce\CategoryItemAssociation')
		->_dsql()->del('fields')->field('category_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_categories)),false);
	}

	function getAssociatedDepartment(){
		$associated_departments = $this->ref('xepan\commerce\Item_Department_Association')
		->_dsql()->del('fields')->field('department_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($associated_departments)),false);
	}

	function stockEffectCustomFields(){
		if(!$this->loaded())
			throw new \Exception("Item Model Must Loaded before getting stockeffectcustomfields");
		
		$stock_effect_cf = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		->addCondition('can_effect_stock',true)
		->tryLoadAny()
		;
		
		return $stock_effect_cf;
	}

	function noneDepartmentAssociateCustomFields(){
		if(!$this->loaded())
			throw new \Exception("Item Model Must Loaded before getting noneDepartmentAssociateCustomFields");
		
		$cf = $this->add('xepan\commerce\Model_Item_CustomField_Association');
		$cf->addCondition('item_id',$this->id)
			->addCondition(
					$cf->dsql()->orExpr()
								->where($cf->getElement('department_id'),null)
								->where($cf->getElement('department_id'),0)
					)
			->addCondition('CustomFieldType','CustomField')
			->tryLoadAny()
			;

		return $cf;
	}

	function specification($specification=null,$highlight_only=false){
		if(!$this->loaded())
			throw new \Exception("Model must loaded", 1);

		$specs_assos = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		->addCondition('CustomFieldType',"Specification")
		;
		
		$value_join = $specs_assos->join('customfield_value.customfield_association_id','id');
		$value_join->addField('highlight_it');
		$value_join->addField('value_status','status');
		$value_join->addField('value','name');
		$specs_assos->addCondition('value_status',"Active");

		if($highlight_only){
			$specs_assos->addCondition('highlight_it',true);
		}
		
		if($specification){
			$specs_assos->addCondition('name',$specification);
			$specs_assos->tryLoadAny();
			if($specs_assos->loaded()) return $specs_assos['value'];
			return false;
		}

		return $specs_assos;
	}

	function getBasicCartOptions(){
		//Get All Item Associated Custom Field_Image
		$custom_field_array = array();

		$stock_effect_custom_fields = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('can_effect_stock',true)
		->addCondition('item_id',$this->id)
							// ->addCondition('department_id',null)->tryLoadAny()
		;
		foreach ($stock_effect_custom_fields as $stock_effect_custom_field){
			$cf_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')->load($stock_effect_custom_field['id']);
			$cf_value_array = $cf_asso->getCustomValue();
			$custom_field_array[$cf_asso['name']] = array(
				'type'=>$cf_asso['display_type'],
				'values' => $cf_value_array
				);
		}

		
		//Get All Item Qnatity Set
		$qty_set_array = array();
		$qty_set_array = $this->getQtySet();

		$options = array();

		$options['item_id'] = $this->id;
		$options['qty_from_set_only'] = $this['qty_from_set_only'];
		$options['qty_set'] = $qty_set_array;
		$options['custom_fields'] = $custom_field_array;

		return $options;
	}

	function getStockEffectCustomFields($department_ids=null){
		if(!$this->loaded())
			return array();

		$stock_effect_custom_field =  $this->ref('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('can_effect_stock',true)
		->addCondition('department_id',null)->tryLoadAny();

		$stock_effect_custom_field = $stock_effect_custom_field->_dsql()->del('fields')->field('customfield_generic_id')->getAll();
		
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($stock_effect_custom_field)),false);
		// return $associate_customfields;
	}

	function getQtySet(){
		if(!$this->loaded())
			throw new \Exception("Item Model Must be Loaded");
		
		/*
		qty_set: {
				Values:{
				value:{
					name:'Default',
					qty:1,
					old_price:100,
					price:90,
					conditions:{
							custom_fields_condition_id:'custom_field_value_id'
						}
				}
			}
		},
		*/
		$qty_added=array();
		$qty_set_array = array();
		//load Associated Quantity Set
		$qty_set_model = $this->add('xepan\commerce\Model_Item_Quantity_Set')->addCondition('item_id',$this->id)->addCondition('is_default',false);
			//foreach qtySet get all Condition
		foreach ($qty_set_model as $junk){
			if(!in_array($junk['qty'], $qty_added)){
				$qty_added[]= $junk['qty'];
			}else{
				continue;
			}
			$qty_set_array[$qty_set_model['id']]['name'] = $qty_set_model['name'];
			$qty_set_array[$qty_set_model['id']]['qty'] = $qty_set_model['qty'];
			$qty_set_array[$qty_set_model['id']]['old_price'] = $qty_set_model['old_price'];
			$qty_set_array[$qty_set_model['id']]['price'] = $qty_set_model['price'];
			$qty_set_array[$qty_set_model['id']]['conditions'] = array();

					//Load QtySet Condition Model
			$condition_model = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$qty_set_model['id']);
						//foreach condition 
			foreach ($condition_model as $junk) {
				$single_condition_array = array();
				$single_condition_array[$condition_model['customfield']] = $condition_model['customfield_name'];
				$qty_set_array[$qty_set_model['id']]['conditions']= array_merge($qty_set_array[$qty_set_model['id']]['conditions'], $single_condition_array);
			}
		}

		return $qty_set_array;		
	}

	function  getPrice($department_wise_custom_field_array, $qty, $rate_chart='retailer'){
		
		$custom_field_values_array = [];
		
		//Note:: Making Custom_field_values_array from Department_custom_field_array
		foreach ($department_wise_custom_field_array as $department) {
			foreach ($department as $cf_id => $values) {
				if($cf_id == "department_name" and !is_numeric($cf_id))
					continue;

				$custom_field_values_array[$values['custom_field_name']] = $values['custom_field_value_name'];
			}
		}

		// throw new \Exception(print_r($custom_field_values_array,true));
		$cf_array = array();
		$cf = array();
		$dept=array();
		if($custom_field_values_array != ""){
			foreach ($custom_field_values_array as $cf_key => $cf_value) {
				$cf[] = trim(str_replace(" ", "","$cf_key::$cf_value"));
			}
		}
		// echo "User Selected Custom Field<br/>";
		// echo implode("<br/>",$cf);
		// echo "<br/>";
		// echo "<br/>Searching....<br/>";
		// exit;
		$quantitysets = $this->ref('xepan\commerce\Item_Quantity_Set')->setOrder(array('custom_fields_conditioned desc','qty desc'));
		
		$i=1;
		foreach ($quantitysets as $qsjunk) {
			// check if all conditioned match AS WELL AS qty
			// echo "Step = ".$i++."<br/>";
			// echo $qsjunk['qty']."==".$qty."<br/>";
			$cond = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$qsjunk->id);
			$all_conditions_matched = true;
			foreach ($cond as $condjunk) {
				$trim_cf_value = trim(str_replace(" ", "", $condjunk['customfield_value']));
				// echo $trim_cf_value."<br/>";
				if(!in_array($trim_cf_value,$cf)){
					$all_conditions_matched = false;
				}
			}

			if($all_conditions_matched && $qty >= $qsjunk['qty']){
				// echo 'breaking at '. $i++. ' '; 
				break;
			}
		}

		// echo ;
		// throw new \Exception(print_r(array('original_price'=>$quantitysets['old_price']?:$quantitysets['price'],'sale_price'=>$quantitysets['price']),true));
		return array('original_price'=>$quantitysets['old_price']?:$quantitysets['price'],'sale_price'=>$quantitysets['price']);
		// return array('original_price'=>rand(1000,9999),'sale_price'=>rand(100,999));

			// return array default_price
		// 1. Check Custom Rate Charts
			/*
				Look $qty >= Qty of rate chart
				get the most field values matched
				having lesser selections of type any or say ...
				when max number of custom fields are having values other than any/%
			*/
		// 2. Custom Field Based Rate Change

		// 3. Quanitity Set

		// 4. Default Price * qty
			}

			function getAmount($custom_field_values_array, $qty, $rate_chart='retailer'){				
				$price = $this->getPrice($custom_field_values_array, $qty, $rate_chart);

				
				$original_amount = $price['original_price'] * $qty;
				$sale_amount = $price['sale_price'] * $qty;

				//get shipping charge
				$shipping_detail_array = $this->shippingCharge($sale_amount,$qty);
				$applicable_taxation = $this->applicableTaxation();

				// get epan config used for taxation with shipping or price
				$misc_config = $this->app->epan->config;
				$misc_tax_on_shipping = $misc_config->getConfig('TAX_ON_SHIPPING');
				$misc_item_price_and_shipping_inclusive_tax = $misc_config->getConfig('ITEM_PRICE_AND_SHIPPING_INCLUSIVE_TAX');
				/*price Calculation according to taxation configuration*/
				//if(item_price_and_shipping_inclusive_tax) return amount
				//else
				//add tax to shipping
				//add tax to amount
				//return
				if($misc_item_price_and_shipping_inclusive_tax){					
					return array(
								'original_amount'=>$original_amount,
								'sale_amount'=>$sale_amount,
								'shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
								'shipping_duration'=>isset($shipping_detail_array['shipping_duration'])?$shipping_detail_array['shipping_duration']:"",
								'express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
								'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
								'taxation'=>$applicable_taxation
							);
				}else{
					
					if(!$applicable_taxation){
						return array(
								'original_amount'=>$original_amount,
								'sale_amount'=>$sale_amount,
								'shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
								'shipping_duration'=>isset($shipping_detail_array['shipping_duration'])?$shipping_detail_array['shipping_duration']:"",
								'express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
								'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
								'taxation'=>$applicable_taxation
							);
					}
					
					$tax_percentage = trim($applicable_taxation['percentage']);
					$original_amount_include_tax = $original_amount + (($tax_percentage*$original_amount) / 100); 
					$sale_amount_include_tax = $sale_amount + (($tax_percentage*$sale_amount) / 100); 

					
					$shipping_charge_include_tax = $shipping_detail_array['shipping_charge'];
					$express_shipping_charge_include_tax = $shipping_detail_array['express_shipping_charge'];

					if($misc_tax_on_shipping){
						$shipping_charge_include_tax = $shipping_charge_include_tax + ($tax_percentage*$shipping_charge_include_tax / 100);
						$express_shipping_charge_include_tax = $express_shipping_charge_include_tax + ($tax_percentage*$express_shipping_charge_include_tax / 100);
					}
					
					return array(
								'original_amount'=>$original_amount_include_tax,
								'sale_amount'=>$sale_amount_include_tax,
								'shipping_charge'=>$shipping_charge_include_tax,
								'express_shipping_charge'=>$express_shipping_charge_include_tax,
								'shipping_duration'=>isset($shipping_detail_array['shipping_duration'])?$shipping_detail_array['shipping_duration']:"",
								'shipping_duration_days'=>isset($shipping_detail_array['shipping_duration_days'])?$shipping_detail_array['shipping_duration']:"",
								'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
								'express_shipping_duration_days'=>isset($shipping_detail_array['express_shipping_duration_days'])?$shipping_detail_array['express_shipping_duration']:"",
								'taxation'=>$applicable_taxation
								);
				}
			}

		function applyTax(){
			return $this->ref('Tax')->setOrder('priority','desc')->tryLoadAny()->setLimit(1);
		}	

		// return taxation model if found else false
		function applicableTaxation(){
			if(!$this->loaded())
				return false;

			$current_country_id = 0;
			if( isset($this->app->country) and ($this->app->country instanceof xepan\base\Model_Country))
				$current_country_id = $this->app->country->id;	
			$country_all_id = $this->add('xepan\base\Model_Country')->tryLoadBy('name','All')->id;

			$current_state_id = 0;
			if(isset($this->app->state) and ($this->app->country instanceof xepan\base\Model_State))
				$current_state_id = $this->app->state->id;
			$state_all_id = $this->add('xepan\base\Model_State')->tryLoadBy('name','All')->id;


			//get first tax rule association
			$first_application_tax_rule_asso = $this->applyTax();
			if(!$first_application_tax_rule_asso->loaded())
				return false;
			$taxation_rule_rows_model = $this->add('xepan\commerce\Model_TaxationRuleRow')->addCondition('taxation_rule_id',$first_application_tax_rule_asso['taxation_rule_id']);
			if(!$taxation_rule_rows_model->count()->getOne())
				return false;
			
			$taxation_rule_rows_model->setOrder('priority','desc');

			$taxation_rule_rows_model->addCondition(
							$taxation_rule_rows_model
									->dsql()->orExpr()
									->where('country_id',$current_country_id)
									->where('country_id',$country_all_id)
							);
			$taxation_rule_rows_model->addCondition(
							$taxation_rule_rows_model->dsql()->orExpr()
									->where('state_id',$current_state_id)
									->where('state_id',$state_all_id)
							);
			
			$taxation_rule_rows_model->tryLoadAny();
			if(!$taxation_rule_rows_model->loaded())
				return false;

			return $taxation_rule_rows_model;
		}

		function shippingCharge($sale_amount,$selected_qty){
			if(!$this->loaded())
				throw new \Exception("item must loaded");
			
			$misc_config = $this->app->epan->config;
			$misc_tax_on_shipping = $misc_config->getConfig('TAX_ON_SHIPPING');

			if( isset($this->app->country) and ($this->app->country instanceof xepan\base\Model_Country))
				$country_id = $this->app->country->id;	
			else
				$country_id = $this->add('xepan\base\Model_Country')->tryLoadBy('name','All')->id;

			if(isset($this->app->state) and ($this->app->country instanceof xepan\base\Model_State))
				$state_id = $this->app->state->id;
			else
				$state_id = $this->add('xepan\base\Model_State')->tryLoadBy('name','All')->id;


			$shipping_charge = [];

			$shipping_asso = $this->add('xepan\commerce\Model_Item_Shipping_Association')
							->addCondition('item_id',$this->id)
							->setOrder('priority','desc')
							;

			//if no shiping rule than return 0
			if(!$shipping_asso->count()->getOne())
				return $shipping_charge;

			foreach ($shipping_asso as $asso) {
				//check shipping rule exist or not according to country or state id
				$shipping_rule_model = $this->add('xepan\commerce\Model_ShippingRule')
										->addCondition('id',$asso['shipping_rule_id'])
										->addCondition('country_id',$country_id)
										->addCondition('state_id',$state_id)
										->tryLoadAny()
										;
				if(!$shipping_rule_model->loaded())
					continue;

				//calculate item qty based on  shipping based on
				$qty = 0;
				switch ($shipping_rule_model['based_on']) {
					case 'amount':
							$qty = $sale_amount;
						break;
					case 'quantity':
							$qty = $selected_qty;
						break;
					case 'weight':
							$qty = $selected_qty * $this['weight'];
						break;
					case 'volume':
						break;
				}

				$shipping_row = $this->add('xepan\commerce\Model_ShippingRuleRow')->addCondition('shipping_rule_id',$shipping_rule_model->id);
				$shipping_row->addCondition('from',"<=",$qty);
				$shipping_row->addCondition('to',">=",$qty);
				$shipping_row->tryLoadAny();
				if(!$shipping_row->loaded())
					return $shipping_charge;

				return array(
							'shipping_charge'=>$shipping_row['shipping_charge'],
							'shipping_duration'=>$shipping_row['shipping_duration'],
							'shipping_duration_days'=>$shipping_row['shipping_duration_days'],
							'express_shipping_charge'=>$shipping_row['express_shipping_charge'],
							'express_shipping_duration'=>$shipping_row['express_shipping_duration'],
							'express_shipping_duration_days'=>$shipping_row['express_shipping_duration_days']
						);
			}

			return $shipping_charge;
		}

	//todo 
			function customFieldsRedableToId($cf_value_json){

		// $cf_value_json = {"Sides":"Single Side"}
						//{"Custom_field_name":"Selected custom Field Value"}
				if(!$this->loaded())
					throw new \Exception("Item Model Must be Loaded");

				if(! (is_string($cf_value_json) && is_object(json_decode($cf_value_json)) && (json_last_error() == JSON_ERROR_NONE)) )
					return "";
		//Load Sales Department
				$sales_department = $this->add('xHR/Model_Department')->loadSales();
				$cart_cf_array = json_decode($cf_value_json,true);
		//Load Item Associated Custom Fields

				$array = array();
				foreach ($cart_cf_array as $cf_name => $cf_value_name) {
					$cf = $this->add('xShop/Model_CustomFields')->addCondition('name',$cf_name)->tryLoadAny();
					$cf_asso = $this->add('xShop/Model_ItemCustomFieldAssos')
					->addCondition('item_id',$this['id'])
					->addCondition('department_phase_id',$sales_department['id'])
					->addCondition('customfield_id',$cf->id)
					->tryLoadAny();

					$cf_value = $cf->ref('xShop/CustomFieldValue')->addCondition('name',$cf_value_name)->tryLoadAny();
					$array[] = array($cf->id => $cf_value->id);

				}

				$json_array[$sales_department->id] = $array;

		//Get Custom Field Id
		//Get Custom Field Value Id
		//{"5":{"2":"10","3":"12"}}
		//and Make json
				return json_encode($json_array);
			}

			function getQuantitySetOnly(){
				if(!$this->loaded())
					throw new \Exception("Error Processing Request", 1);

				$qty_set_model = $this->add('xepan\commerce\Model_Item_Quantity_Set',['id_field'=>'qty']);
				$qty_set_model->addCondition('item_id',$this->id);
				$qty_set_model->setOrder('qty','asc');
				$qty_set_model->_dsql()->group('name');
				return $qty_set_model;

			}

			function removeSpecificationAssociation(){
				if(!$this->loaded())
					throw new \Exception("item_must Loaded", 1);

				$specs  = $this->specification();
				foreach ($specs as $spec) {
					$spec->delete();
				}
			}

			function removeCustomfields(){
				if(!$this->loaded())
					throw new \Exception("item_must Loaded", 1);

				$cf  = $this->associateCustomField();
				foreach ($cf as $fields) {
					$fields->delete();
				}
			}

			function removeItemDepartmentAssociation(){
				if(!$this->loaded()){
					throw new \Exception("Item must be loaded");
				}	
				$item_dept_assoc  = $this->add('xepan\commerce\Model_Item_Department_Association');
				$item_dept_assoc->addCondition('item_id', $this->id);
				foreach ($item_dept_assoc as $fields) {
					$fields->delete();
				}
			}


			function removeQuantitySet(){
				if(!$this->loaded()){
					throw new \Exception("Item must be loaded");
				}	
				$item_qty_assoc  = $this->add('xepan\commerce\Model_Item_Quantity_Set');
				$item_qty_assoc->addCondition('item_id', $this->id);
				foreach ($item_qty_assoc as $fields) {
					$fields->delete();
				}	
			}

			function removeCategoryItemAssociation(){
				if(!$this->loaded()){
					throw new \Exception("Item must be loaded");
				}			
				$model_cat_itm_assoc = $this->add('xepan\commerce\Model_CategoryItemAssociation')->addCondition('item_id',$this->id);
				$model_cat_itm_assoc->deleteAll();
			}

			function removeTemplateDesign(){
				if(!$this->loaded()){
					throw new \Exception("Item must be loaded");
				}	
				$model_design = $this->add('xepan\commerce\Model_Item_Template_Design')->addCondition('item_id',$this->id);
				$model_design->deleteAll();
			}

			function removeImageAssociation(){
				if(!$this->loaded()){
					throw new \Exception("Item must be loaded");
				}	
				$model_image = $this->add('xepan\commerce\Model_Item_Image')->addCondition('item_id',$this->id);
				$model_image->deleteAll();
			}

			function removeItemTaxationAssociation(){
				if(!$this->loaded()){
					throw new \Exception("Item must be loaded");
				}	
				$model_tax = $this->add('xepan\commerce\Model_Item_Taxation_Association')->addCondition('item_id',$this->id);
				$model_tax->deleteAll();
			}

			function updateFirstImageFromDesign(){
				$item = $target = $this;

				$design = $target['designs'];
				if(!$design) return;

				$design = json_decode($design,true);
				$cont = $this->add('xepan/commerce/Controller_DesignTemplate',array('item'=>$item,'design'=>$design,'page_name'=>$_GET['page_name']?:'Front Page','layout'=>$_GET['layout_name']?:'Main Layout'));
				$image_data =  $cont->show($type='png',$quality=3, $base64_encode=false, $return_data=true);

				$item_image = $this->add('xepan/commerce/Model_Item_Image')->addCondition('item_id',$this->id)->tryLoadAny();
				$destination = $item_image['file'];

				if($item_image->count()->getOne())
					$destination = getcwd().DS.$this->add('filestore/Model_File')->tryLoad($destination)->getPath();


				if(file_exists($destination) AND !is_dir($destination)){
					$fd = fopen($destination, 'w');
					fwrite($fd, $image_data);
					fclose($fd);
				}else{
					$image_id = $this->add('filestore/Model_File',['import_mode'=>'string','import_source'=>$image_data]);
					$image_id['original_filename'] = 'design_for_item_'. $this->id;
					$image_id->save();

			//First Time Save Image
					$item_image['file_id'] = $image_id->id;
					$item_image['item_id'] = $item->id;
					$item_image->save();
				}

			}
		}