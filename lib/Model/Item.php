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

		$this->getElement('created_by_id')->defaultValue(@$this->app->employee->id);
		$item_j=$this->join('item.document_id');

		$item_j->hasOne('xepan\base\Contact','designer_id')->defaultValue(0);
		$item_j->hasOne('xepan\commerce\Model_Item_Template','duplicate_from_item_id')->defaultValue(0);

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
		
		// Item renewable fields
		$item_j->addField('is_renewable')->type('boolean')->defaultValue(0);
		$item_j->addField('remind_to')->display(['form'=>'xepan\base\DropDown'])->setValueList(['Both'=>'Both','Customer'=>'Customer','Admin'=>'Admin']);
		$item_j->addField('renewable_value')->type('number');
		$item_j->addField('renewable_unit')->setValueList(['day'=>'Days','months'=>'Months']);

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
		// $item_j->addField('duplicate_from_item_id')->hint('internal used saved its parent')->defaultValue(null);

		$item_j->addField('upload_file_label')->type('text')->hint('comma separated multiple file name, ex. file_name_1:mandatory, file_name_2, file_name_3:mandatory, file_name_4');

		$item_j->addField('item_specific_upload_hint')->type('text')->hint('Hint for upload images')->defaultValue(null)->display(array('form'=>'xepan\base\RichText'));

		$item_j->addField('upload_file_group');

		$item_j->addField('to_customer_id')->hint('Specific to customer/organization')->defaultValue(null);
		$item_j->addField('quantity_group');

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
		$item_j->hasMany('xepan\commerce\Item_Shipping_Association','item_id');
		
		
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
			$categoryfields = $this->add('xepan\commerce\Model_CategoryItemAssociation')->addCondition('item_id',$this->id);
			foreach ($categoryfields as $all_categoryfields) {
				$search_string .=" ". $all_categoryfields['item_id'];
				$search_string .=" ". $all_categoryfields['category_id'];
			}
		}
		
		if($this->loaded()){
			$quantity_set = $this->add('xepan\commerce\Model_Item_Quantity_Set')->addCondition('item_id',$this->id);
			foreach ($quantity_set as $all_quantity_set) {
				$search_string .=" ". $all_quantity_set['name'];
				$search_string .=" ". $all_quantity_set['price'];
			}
		}

		if($this->loaded()){
			$customfields = $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('item_id',$this->id);
			foreach ($customfields as $customfield) {

				$values = $customfield->ref('xepan\commerce\Item_CustomField_Value');
				foreach ($values as $value) {
					$search_string .="". $value['name'];	
				}	
				$search_string .=" ". $customfield['name'];
				$search_string .=" ". $customfield['CustomFieldType'];
			}
		}

		$this['search_string'] = $search_string;
		
	}

	function publish(){
		$this['status']='Published';
		$this->app->employee
		->addActivity("Item : '".$this['name']."' now published", $this->id/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_itemdetail&document_id=".$this->id."")
		->notifyWhoCan('unpublish,duplicate','Published');
		$this->save();
	}

	function unpublish(){
		$this['status']='UnPublished';
		$this->app->employee
		->addActivity("Item : '".$this['name']."' has been unpublished", $this->id/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_itemdetail&document_id=".$this->id."")
		->notifyWhoCan('publish,duplicate','UnPublished');
		$this->save();
	}

	function page_duplicate($page,$acl=true){
		
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
				$this->app->employee
				->addActivity("Item : '".$this['name']."' Duplicated as New Item : '".$name."'", $this->id/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_itemdetail&document_id=".$this->id."")
				->notifyWhoCan('unpublish,duplicate','Published');
				$this->api->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
	            throw $e;
			}
			if($acl)
				return $this->api->js()->univ()->location($this->app->url('xepan_commerce_itemdetail',['document_id'=>$new_item->id, 'action'=>'edit']));
			else
				$this->app->redirect($this->app->url('xepan_commerce_itemdetail',['document_id'=>$new_item->id, 'action'=>'edit']));
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
		// set_time_limit(300);
		$this->duplicateSpecification(array($model_item->id));
		$this->duplicateCustomfields(array($model_item->id));
		$this->duplicateItemDepartmentAssociation(array($model_item->id));
		$this->duplicateQuantitySet(array($model_item->id));
		$this->duplicateCategoryItemAssociation(array($model_item->id));
		// $this->duplicateTemplateDesign($model_item);
		// $this->duplicateImage($model_item);		
		$this->duplicateItemShippingAssociation(array($model_item->id));
		$this->duplicateItemTaxationAssociation(array($model_item->id));
		$this->duplicateItemFilterAssociation(array($model_item->id));

		return $model_item;
	}

	function duplicateItemShippingAssociation($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_shipping_asso = $this->add('xepan\commerce\Model_Item_Shipping_Association')
						->addCondition('item_id',$this->id);

		$shipping_asso_query = "INSERT into shipping_association (item_id,shipping_rule_id,priority) VALUES ";
		
		$old_shipping_rows = $old_shipping_asso->setOrder('id')->getRows();

		if(!count($old_shipping_rows))
			return;

		foreach ($old_shipping_rows as $shipping_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$shipping_asso_query .= "('$chitm','".$shipping_asso_fields['shipping_rule_id']."','".$shipping_asso_fields['priority']."'),";
			}
		}

		$shipping_asso_query = trim($shipping_asso_query,',');
		$this->app->db->dsql()->expr($shipping_asso_query)->execute();

	}

	function duplicateItemTaxationAssociation($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_taxation_asso = $this->add('xepan\commerce\Model_Item_Taxation_Association')
						->addCondition('item_id',$this->id);

		$taxation_asso_query = "INSERT into taxation_association (item_id,taxation_rule_id) VALUES ";
		
		$old_taxation_rows = $old_taxation_asso->setOrder('id')->getRows();

		if(!count($old_taxation_rows))
			return;
		foreach ($old_taxation_rows as $tax_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$taxation_asso_query .= "('$chitm','".$tax_asso_fields['taxation_rule_id']."'),";
			}
		}

		$taxation_asso_query = trim($taxation_asso_query,',');
		$this->app->db->dsql()->expr($taxation_asso_query)->execute();
	}

	function duplicateSpecification($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_cf_association = $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('item_id',$this->id)->addCondition('CustomFieldType','Specification')->addCondition('is_filterable',false);

		$cf_asso_query = "INSERT into customfield_association (customfield_generic_id,item_id,department_id,can_effect_stock,status) VALUES ";
		
		$temp = 0;
		$old_cf_asso_count = 0;
		$old_cf_asso_values = [];
		$cf_asso_number = 0;
		
		$old_cf_asso_rows = $old_cf_association->setOrder('id')->getRows();
		if(!count($old_cf_asso_rows))
			return;

		foreach ($old_cf_asso_rows as $old_cf_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$cf_asso_query .= "('".$old_cf_asso_fields['customfield_generic_id']."','$chitm','".$old_cf_asso_fields['department_id']."','".$old_cf_asso_fields['can_effect_stock']."','".$old_cf_asso_fields['status']."'),";
			}

			$values = $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$old_cf_asso_fields['id'])->setOrder('id')->getRows();
			foreach ($values as $value) {
				if(!isset($old_cf_asso_values[$cf_asso_number])) $old_cf_asso_values[$cf_asso_number] = [];
				$old_cf_asso_values[$cf_asso_number][] = ['name'=>$value['name'],'status'=>$value['status'],'highlight_it'=>$value['highlight_it']];
			}
			$old_cf_asso_count++;
			$cf_asso_number++;
		}

		$cf_asso_query = trim($cf_asso_query,',');
		// echo $cf_asso_query .'<br/><br/><br/><br/>';

		$this->app->db->dsql()->expr($cf_asso_query)->execute();

		$new_cf_asso_id = [];
		$new_cf_asso_id_temp = $this->add('xepan\commerce\Model_Item_CustomField_Association')->setOrder('id')->addCondition('item_id',$child_item_id_array)->addCondition('CustomFieldType','Specification')->addCondition('is_filterable',false)->getRows();
		foreach ($new_cf_asso_id_temp as $t) {
			$new_cf_asso_id[] = $t['id'];
		}

		if(($old_cf_asso_count*count($child_item_id_array)) != count($new_cf_asso_id))
			throw $this->exception('Duplication of Custom Fields value was not perfect, count mismatch')
						->addMoreInfo('Old Item Custom Field Asso count ', $old_cf_asso_count)
						->addMoreInfo('Total items to duplicated ', count($child_item_id_array))
						->addMoreInfo('Found new custom field asso count ', count($new_cf_asso_id))
						;

		$cf_val_query = "INSERT into customfield_value (customfield_association_id,status,name,highlight_it) VALUES ";
		
		$count = count($child_item_id_array);
		$i=0;
		$j=0;
		foreach ($old_cf_asso_rows as $qr) {
			foreach ($child_item_id_array as $index => $item) {
				if(!isset($old_cf_asso_values[$j])) continue;
				foreach ($old_cf_asso_values[$j] as $v) {
					$nid= $new_cf_asso_id[$i];
					$cf_val_query .= " ('$nid' , '".$v['status']."','".$v['name']."','".$v['highlight_it']."' ),";
				}
				$i++;
			}
			$j++;
		}

		// exit();
		$cf_val_query = trim($cf_val_query,',');
		// echo $cf_val_query .'<br/><br/><br/><br/>';
		$this->app->db->dsql()->expr($cf_val_query)->execute();


	}

	function duplicateCustomfields($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_cf_association = $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('item_id',$this->id)->addCondition('CustomFieldType','CustomField');

		$cf_asso_query = "INSERT into customfield_association (customfield_generic_id,item_id,department_id,can_effect_stock,status) VALUES ";
		
		$temp = 0;
		$old_cf_asso_count = 0;
		$old_cf_asso_values = [];
		$cf_asso_number = 0;
		
		$old_cf_asso_rows = $old_cf_association->setOrder('id')->getRows();

		if(!count($old_cf_asso_rows))
			return;

		foreach ($old_cf_asso_rows as $old_cf_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$cf_asso_query .= "('".$old_cf_asso_fields['customfield_generic_id']."','$chitm','".$old_cf_asso_fields['department_id']."','".$old_cf_asso_fields['can_effect_stock']."','".$old_cf_asso_fields['status']."'),";
			}

			$values = $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$old_cf_asso_fields['id'])->setOrder('id')->getRows();
			foreach ($values as $value) {
				if(!isset($old_cf_asso_values[$cf_asso_number])) $old_cf_asso_values[$cf_asso_number] = [];
				$old_cf_asso_values[$cf_asso_number][] = ['name'=>$value['name'],'status'=>$value['status'],'highlight_it'=>$value['highlight_it']];
			}
			$old_cf_asso_count++;
			$cf_asso_number++;
		}

		$cf_asso_query = trim($cf_asso_query,',');
		// echo $cf_asso_query .'<br/><br/><br/><br/>';

		$this->app->db->dsql()->expr($cf_asso_query)->execute();

		$new_cf_asso_id = [];
		$new_cf_asso_id_temp = $this->add('xepan\commerce\Model_Item_CustomField_Association')->setOrder('id')->addCondition('item_id',$child_item_id_array)->addCondition('CustomFieldType','CustomField')->getRows();
		foreach ($new_cf_asso_id_temp as $t) {
			$new_cf_asso_id[] = $t['id'];
		}

		if(($old_cf_asso_count*count($child_item_id_array)) != count($new_cf_asso_id))
			throw $this->exception('Duplication of Custom Fields value was not perfect, count mismatch')
						->addMoreInfo('Old Item Custom Field Asso count ', $old_cf_asso_count)
						->addMoreInfo('Total items to duplicated ', count($child_item_id_array))
						->addMoreInfo('Found new custom field asso count ', count($new_cf_asso_id))
						;

		$cf_val_query = "INSERT into customfield_value (customfield_association_id,status,name,highlight_it) VALUES ";
		
		$count = count($child_item_id_array);
		$i=0;
		$j=0;
		foreach ($old_cf_asso_rows as $qr) {
			foreach ($child_item_id_array as $index => $item) {
				if(!isset($old_cf_asso_values[$j])) continue;
				foreach ($old_cf_asso_values[$j] as $v) {
					$nid= $new_cf_asso_id[$i];
					$cf_val_query .= " ('$nid' , '".$v['status']."','".$v['name']."','".$v['highlight_it']."' ),";
				}
				$i++;
			}
			$j++;
		}

		// exit();
		$cf_val_query = trim($cf_val_query,',');
		// echo $cf_val_query .'<br/><br/><br/><br/>';
		$this->app->db->dsql()->expr($cf_val_query)->execute();
	}


	function duplicateItemFilterAssociation($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);
		
		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_cf_association = $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('item_id',$this->id)->addCondition('CustomFieldType','Specification')->addCondition('is_filterable',true);

		$cf_asso_query = "INSERT into customfield_association (customfield_generic_id,item_id,department_id,can_effect_stock,status) VALUES ";
		
		$temp = 0;
		$old_cf_asso_count = 0;
		$old_cf_asso_values = [];
		$cf_asso_number = 0;
		
		$old_cf_asso_rows = $old_cf_association->setOrder('id')->getRows();

		if(!count($old_cf_asso_rows))
			return;

		foreach ($old_cf_asso_rows as $old_cf_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$cf_asso_query .= "('".$old_cf_asso_fields['customfield_generic_id']."','$chitm','".$old_cf_asso_fields['department_id']."','".$old_cf_asso_fields['can_effect_stock']."','".$old_cf_asso_fields['status']."'),";
			}

			$values = $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$old_cf_asso_fields['id'])->setOrder('id')->getRows();
			foreach ($values as $value) {
				if(!isset($old_cf_asso_values[$cf_asso_number])) $old_cf_asso_values[$cf_asso_number] = [];
				$old_cf_asso_values[$cf_asso_number][] = ['name'=>$value['name'],'status'=>$value['status'],'highlight_it'=>$value['highlight_it']];
			}
			$old_cf_asso_count++;
			$cf_asso_number++;
		}

		$cf_asso_query = trim($cf_asso_query,',');
		// echo $cf_asso_query .'<br/><br/><br/><br/>';

		$this->app->db->dsql()->expr($cf_asso_query)->execute();

		$new_cf_asso_id = [];
		$new_cf_asso_id_temp = $this->add('xepan\commerce\Model_Item_CustomField_Association')->setOrder('id')->addCondition('item_id',$child_item_id_array)->addCondition('CustomFieldType','Specification')->addCondition('is_filterable',true)->getRows();
		foreach ($new_cf_asso_id_temp as $t) {
			$new_cf_asso_id[] = $t['id'];
		}

		if(($old_cf_asso_count*count($child_item_id_array)) != count($new_cf_asso_id))
			throw $this->exception('Duplication of Filter Custom Fields value was not perfect, count mismatch')
						->addMoreInfo('Old Item Filter Asso count ', $old_cf_asso_count)
						->addMoreInfo('Total items to duplicated ', count($child_item_id_array))
						->addMoreInfo('Found new Filter asso count ', count($new_cf_asso_id))
						;

		$cf_val_query = "INSERT into customfield_value (customfield_association_id,status,name,highlight_it) VALUES ";
		
		$count = count($child_item_id_array);
		$i=0;
		$j=0;
		foreach ($old_cf_asso_rows as $qr) {
			foreach ($child_item_id_array as $index => $item) {
				if(!isset($old_cf_asso_values[$j])) continue;
				foreach ($old_cf_asso_values[$j] as $v) {
					$nid= $new_cf_asso_id[$i];
					$cf_val_query .= " ('$nid' , '".$v['status']."','".$v['name']."','".$v['highlight_it']."' ),";
				}
				$i++;
			}
			$j++;
		}

		// exit();
		$cf_val_query = trim($cf_val_query,',');
		// echo $cf_val_query .'<br/><br/><br/><br/>';
		$this->app->db->dsql()->expr($cf_val_query)->execute();
	}

	function duplicateItemDepartmentAssociation($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_dept_asso = $this->add('xepan\commerce\Model_Item_Department_Association')
						->addCondition('item_id',$this->id);

		$dept_asso_query = "INSERT into item_department_association (item_id,department_id,can_redefine_qty,can_redefine_item) VALUES ";
		
		$old_dept_asso_rows = $old_dept_asso->setOrder('id')->getRows();

		if(!count($old_dept_asso_rows))
			return;

		foreach ($old_dept_asso_rows as $dept_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$dept_asso_query .= "('$chitm','".$dept_asso_fields['department_id']."','".$dept_asso_fields['can_redefine_qty']."','".$dept_asso_fields['can_redefine_item']."'),";
			}
		}

		$dept_asso_query = trim($dept_asso_query,',');
		// echo $q_val .'<br/><br/><br/><br/>';
		$this->app->db->dsql()->expr($dept_asso_query)->execute();

	}

	function duplicateQuantitySet($child_item_id_array){

		if(!$this->loaded())
			throw new \Exception("item model must be loaded to duplicate", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_qtyset = $this->add('xepan\commerce\Model_Item_Quantity_Set')->addCondition('item_id',$this->id);

		$q_set = "INSERT into quantity_set (item_id,name,qty,old_price,price,is_default) VALUES ";
		
		$temp =0;

		$old_q_set_count=0;
		$old_q_set_cond_values = [];
		$q_set_number=0;
		
		$old_qty_set_rows = $old_qtyset->setOrder('id')->getRows();

		if(!count($old_qty_set_rows))
			return;

		foreach ($old_qty_set_rows as $old_qty_felds ) {
			foreach ($child_item_id_array as $chitm) {
				$q_set .= "('$chitm','".$old_qty_felds['name']."','".$old_qty_felds['qty']."','".$old_qty_felds['old_price']."','".$old_qty_felds['price']."','".$old_qty_felds['is_default']."'),";
			}
			$conditions = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$old_qty_felds['id'])->setOrder('id')->getRows();
			foreach ($conditions as $cond) {
				if(!isset($old_q_set_cond_values[$q_set_number])) $old_q_set_cond_values[$q_set_number] = [];
				$old_q_set_cond_values[$q_set_number][] = $cond['customfield_value_id'];
			}
			$old_q_set_count++;
			$q_set_number++;
		}

		$q_set = trim($q_set,',');
		// echo $q_set .'<br/><br/><br/><br/>';

		$this->app->db->dsql()->expr($q_set)->execute();

		$new_q_set_id = [];
		$new_qset_id_temp = $this->add('xepan\commerce\Model_Item_Quantity_Set')->setOrder('id')->addCondition('item_id',$child_item_id_array)->getRows();
		foreach ($new_qset_id_temp as $t) {
			$new_q_set_id[] = $t['id'];
		}

		if(($old_q_set_count*count($child_item_id_array)) != count($new_q_set_id))
			throw $this->exception('Duplication of Quantity set was not perfect, count mismatch')
						->addMoreInfo('Old Item Quantity set count ', $old_q_set_count)
						->addMoreInfo('Total items to duplicated ', count($child_item_id_array))
						->addMoreInfo('Found new quantity set count ', count($new_q_set_id))
						;


		$q_val = "INSERT into quantity_condition (quantity_set_id,customfield_value_id) VALUES ";
		
		if(!count($old_q_set_cond_values))
			return;
		$count = count($child_item_id_array);
		$i=0;
		$j=0;
		foreach ($old_qty_set_rows as $qr) {
			foreach ($child_item_id_array as $index => $item) {
				if(!isset($old_q_set_cond_values[$j])) continue;
				foreach ($old_q_set_cond_values[$j] as $v) {
					$nid= $new_q_set_id[$i];
					$q_val .= " ('$nid' , '$v' ),";
				}
				$i++;
			}
			$j++;
		}

		$q_val = trim($q_val,',');
		// echo $q_val .'<br/><br/><br/><br/>';
		$this->app->db->dsql()->expr($q_val)->execute();

	}

	function duplicateCategoryItemAssociation($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		$old_cat_asso = $this->add('xepan\commerce\Model_CategoryItemAssociation')
						->addCondition('item_id',$this->id);

		$cat_asso_query = "INSERT into category_item_association (item_id,category_id) VALUES ";
		
		$old_cat_asso_rows = $old_cat_asso->setOrder('id')->getRows();

		if(!count($old_cat_asso_rows))
			return;
	
		foreach ($old_cat_asso_rows as $old_cat_asso_fields ) {
			foreach ($child_item_id_array as $chitm) {
				$cat_asso_query .= "('$chitm','".$old_cat_asso_fields['category_id']."'),";
			}
		}

		$cat_asso_query = trim($cat_asso_query,',');
		$this->app->db->dsql()->expr($cat_asso_query)->execute();

	}

	function duplicateTemplateDesign($new_item){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		// $old_design = $this->ref('xepan\commerce\Item_Template_Design');
		// foreach ($old_design as $old_design_fields) {
		// 	$model_contact = $this->add('xepan\base\Model_Contact');
		// 	$model_contact->loadLoggedIn();

		// 	$model_itm_template = $this->add('xepan\commerce\Model_Item_Template_Design');
		// 	$model_itm_template['item_id']= $new_item->id;
		// 	$model_itm_template['cotact_id']=$model_contact->id;
		// 	$model_itm_template['name']=$old_design_fields['name'];
		// 	$model_itm_template['last_modified']=$old_design_fields['last_modified'];
		// 	$model_itm_template['is_ordered']=$old_design_fields['is_ordered'];
		// 	$model_itm_template['designes']=$old_design_fields['designes'];
		// 	$model_itm_template->save();
		// 	$model_itm_template->destroy();	    		
		// }	    	
	}

	function duplicateImage($child_item_id_array){
		if(!$this->loaded())
			throw new \Exception("item model must be loaded", 1);

		if(!is_array($child_item_id_array) or !count($child_item_id_array))
			return;

		// $old_image = $this->ref('ItemImages');
		// foreach ($old_image as $old_image_fields) {
		// 	$model_item_Image = $this->add('xepan\commerce\Model_Item_Image');
		// 	$model_item_Image['item_id'] = $new_item->id;
		// 	$model_item_Image['file_id'] = $old_image_fields['file_id'];
		// 	$model_item_Image['customfield_value_id'] = $old_image_fields['customfield_value_id'];
		// 	$model_item_Image->save();
		// 	$model_item_Image->destroy();
		// }	
	}

	function updateChild($fields, $replica_fields){
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
		ini_set("memory_limit","256M");

		$childs = $this->add('xepan\commerce\Model_Item')->addCondition('duplicate_from_item_id',$this->id);
		// todo converted  into insert query
		if(empty(!$replica_fields)){
			foreach ($replica_fields as $field) {
				foreach ($childs as $this_child) {
					$this_child[$field] = $this[$field];
					$this_child->save();
				}
			}
		}

		// Remove fields/value with all item together
		$child_item_array =  $this->getChildItem();
		if(!count($child_item_array))
			return;

		foreach ($fields as $value) {
			// foreach ($childs as  $child_item) {
				switch ($value) {
					case 'Specification':
					$this->removeSpecificationAssociation($child_item_array);
					$this->duplicateSpecification($child_item_array);
					break;
					case 'CustomField':
					$this->removeCustomfields($child_item_array);
					$this->duplicateCustomfields($child_item_array);
					break;	
					case 'Department':
					$this->removeItemDepartmentAssociation($child_item_array);
					$this->duplicateItemDepartmentAssociation($child_item_array);
					break;
					case 'QuantitySet':
					$this->removeQuantitySet($child_item_array);
					$this->duplicateQuantitySet($child_item_array);
					break;
					case 'Category':
					$this->removeCategoryItemAssociation($child_item_array);
					$this->duplicateCategoryItemAssociation($child_item_array);
					break;
					//no need to update or delete if want to delete/update then delete manually
					// case 'Template Design':
					// $child_item->removeTemplateDesign();
					// $this->duplicateTemplateDesign($child_item);
					// break;
					// case 'Image':
					// $child_item->removeImageAssociation();
					// $this->duplicateImage($child_item);
					// break;
					case 'Taxation':
					$this->removeItemTaxationAssociation($child_item_array);
					$this->duplicateItemTaxationAssociation($child_item_array);
					
					break;
					case  'Shipping':																	
					$this->removeItemShippingAssociation($child_item_array);
					$this->duplicateItemShippingAssociation($child_item_array);
					break;
					case  'Filter':
					$this->removeItemFilterAssociation($child_item_array);
					$this->duplicateItemFilterAssociation($child_item_array);
					break;

					default:
					$this->removeSpecificationAssociation($child_item_array);
					$this->duplicateSpecification($child_item_array);

					$this->removeCustomfields($child_item_array);
					$this->duplicateCustomfields($child_item_array);

					$this->removeItemDepartmentAssociation($child_item_array);
					$this->duplicateItemDepartmentAssociation($child_item_array);

					$this->removeQuantitySet($child_item_array);
					$this->duplicateQuantitySet($child_item_array);

					$this->removeCategoryItemAssociation($child_item_array);
					$this->duplicateCategoryItemAssociation($child_item_array);

					// $child_item->removeTemplateDesign();
					// $this->duplicateTemplateDesign($child_item);

					// $child_item->removeImageAssociation();
					// $this->duplicateImage($child_item);

					$this->removeItemTaxationAssociation($child_item_array);
					$this->duplicateItemTaxationAssociation($child_item_array);

					$this->removeItemShippingAssociation($child_item_array);
					$this->duplicateItemShippingAssociation($child_item_array);

					$this->removeItemFilterAssociation($child_item_array);
					$this->duplicateItemFilterAssociation($child_item_array);
					break;
				}
			}
	}

	function associateSpecification($with_filter=true){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");

		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		->addCondition('can_effect_stock',false)
		;
		$asso->addCondition('CustomFieldType','Specification');
		if(!$with_filter){
			$asso->addCondition('is_filterable', false);
		}

		return $asso;

	}

	function associateFilters(){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");

		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		->addCondition('is_filterable',true)
		;
		// $asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addCondition('CustomFieldType','Specification');
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
		
		// $asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addExpression('sequence_order')->set($asso->refSQL('customfield_generic_id')->fieldQuery('sequence_order'));
		$asso->addCondition('CustomFieldType','CustomField');
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

	function getChildItem(){
		if(!$this->loaded())
			throw new \Exception("item model must loaded", 1);
			
		$child_items = $this->add('xepan\commerce\Model_Item')->addCondition('duplicate_from_item_id',$this->id)
					->_dsql()->del('fields')->field('document_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($child_items)),false);
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

		$specs_assos = $this->add('xepan\commerce\Model_Item_CustomField_Association');
		$specs_assos->addCondition('item_id',$this->id)
					->addCondition('CustomFieldType',"Specification")
					->addCondition('is_filterable',false);
		// $specs_assos->addCondition('is_system','<>',true);

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

	function getSpecification($case='both'){	
		$extra_info=[];
		$specifications_model = $this->specification();

		foreach ($specifications_model as $specification) {
			if($case=='both'){
				$extra_info[strtolower($specification['name'])] = $specification['value'];
				$extra_info[ucwords($specification['name'])] = $specification['value'];
			}elseif($case=='exact'){
				$extra_info[$specification['name']] = $specification['value'];
			}
		}
		return $extra_info;
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
		$quantitysets = $this->ref('xepan\commerce\Item_Quantity_Set')
			->setOrder(array('custom_fields_conditioned desc','qty desc'));
		
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
		$data = array('original_price'=>$quantitysets['old_price']?:$quantitysets['price'],'sale_price'=>$quantitysets['price']);
		
		if(!$data['original_price'] and !$data['sale_price'])
			$data = array('original_price'=>$this['original_price'],"sale_price"=>$this['sale_price']);

		return $data;
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
				$sale_amount = $price['sale_price' ]* $qty;

				//get shipping charge
				$shipping_detail_array = $this->shippingCharge($sale_amount,$qty,$this['weight']);
				$applicable_taxation = $this->applicableTaxation();
				
				// get epan config used for taxation with shipping or price
				$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
					[
						'fields'=>[
									'tax_on_shipping'=>'checkbox'
									],
							'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
							'application'=>'commerce'
					]);
				$misc_config->tryLoadAny();

				$misc_tax_on_shipping = $misc_config['tax_on_shipping'];
				
				/*price Calculation according to taxation configuration*/
				//if(item_price_and_shipping_inclusive_tax) return amount
				//else
				//add tax to shipping
				//add tax to amount
				//return
				if(!$applicable_taxation){
					return array(
							'original_amount'=>$original_amount,
							'sale_amount'=>$sale_amount,
							'shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
							'shipping_duration'=>isset($shipping_detail_array['shipping_duration'])?$shipping_detail_array['shipping_duration']:"",
							'express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
							'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
							'raw_shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
							'raw_express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
							'taxation'=>$applicable_taxation,
							'raw_sale_price'=>$price['sale_price'],
							'raw_original_price'=>$price['original_price']
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
							'shipping_charge'=>$shipping_charge_include_tax,
							'express_shipping_charge'=>$express_shipping_charge_include_tax,
							'raw_shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
							'raw_express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
							'shipping_duration'=>isset($shipping_detail_array['shipping_duration'])?$shipping_detail_array['shipping_duration']:"",
							'shipping_duration_days'=>isset($shipping_detail_array['shipping_duration_days'])?$shipping_detail_array['shipping_duration']:"",
							'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
							'express_shipping_duration_days'=>isset($shipping_detail_array['express_shipping_duration_days'])?$shipping_detail_array['express_shipping_duration']:"",
							'taxation'=>$applicable_taxation,
							'raw_sale_price'=>$price['sale_price'],
							'raw_original_price'=>$price['original_price']
							);
			}

		function applyTax(){
			return $this->ref('Tax')->setOrder('priority','desc')->tryLoadAny()->setLimit(1);
		}	

		// return taxation model if found else false
		function applicableTaxation(){
			if(!$this->loaded())
				return false;

			$current_country_id = null;
			if( isset($this->app->country) and ($this->app->country instanceof xepan\base\Model_Country))
				$current_country_id = $this->app->country->id;	

			$current_state_id = null;
			if(isset($this->app->state) and ($this->app->country instanceof xepan\base\Model_State))
				$current_state_id = $this->app->state->id;


			//get first tax rule association :: ITEM DOES NOT HAVE MULTiPLE TAX ASSOS 
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
									->where('country_id',null)
							);
			$taxation_rule_rows_model->addCondition(
							$taxation_rule_rows_model->dsql()->orExpr()
									->where('state_id',$current_state_id)
									->where('state_id',null)
							);
			
			$taxation_rule_rows_model->tryLoadAny();
			if(!$taxation_rule_rows_model->loaded())
				return false;

			return $taxation_rule_rows_model;
		}

		function shippingCharge($sale_amount,$selected_qty, $per_item_weight=null){
			if(!$this->loaded())
				throw new \Exception("item must loaded");
			
			// $misc_config = $this->app->epan->config;
			$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'tax_on_shipping'=>'checkbox'
								],
						'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
						'application'=>'commerce'
				]);
			$misc_config->tryLoadAny();
			
			// $misc_tax_on_shipping = $misc_config->getConfig('TAX_ON_SHIPPING');
			$misc_tax_on_shipping = $misc_config['tax_on_shipping'];

			$country_id = null;
			$state_id = null;

			if( isset($this->app->country) and ($this->app->country instanceof \xepan\base\Model_Country))
				$country_id = $this->app->country->id;	
			// else
			// 	$country_id = $this->add('xepan\base\Model_Country')->tryLoadBy('name','All')->id;

			if(isset($this->app->state) and ($this->app->state instanceof \xepan\base\Model_State))
				$state_id = $this->app->state->id;
			// else
			// 	$state_id = $this->add('xepan\base\Model_State')->tryLoadBy('name','All')->id;

			$shipping_charge = array(
							'shipping_charge'=>0,
							'shipping_duration'=>0,
							'shipping_duration_days'=>0,
							'express_shipping_charge'=>0,
							'express_shipping_duration'=>0,
							'express_shipping_duration_days'=>0
						);

			$shipping_asso = $this->add('xepan\commerce\Model_Item_Shipping_Association')
							->addCondition('item_id',$this->id)
							;

			//if no shiping rule than return 0
			if(!$shipping_asso->count()->getOne()){				
				return $shipping_charge;
			}

			foreach ($shipping_asso as $asso) {
				//check shipping rule exist or not according to country or state id
				$shipping_rule_model = $this->add('xepan\commerce\Model_ShippingRule')
										->addCondition('id',$asso['shipping_rule_id'])
										->addCondition(
												$this->app->db->dsql()->orExpr()
												->where('country_id',$country_id)
												->where('country_id',null)
											)
										->addCondition(
												$this->app->db->dsql()->orExpr()
												->where('state_id',$state_id)
												->where('state_id',null)
											)
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

				$shipping_row = $this->add('xepan\commerce\Model_ShippingRuleRow')
							->addCondition('shipping_rule_id',$shipping_rule_model->id);
				$shipping_row->addCondition('from',"<=",(int)$qty);
				$shipping_row->addCondition('to',">=",(int)$qty);
				$shipping_row->tryLoadAny();
				
				if(!$shipping_row->loaded()){
					return $shipping_charge;
				}

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

			function removeSpecificationAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;
				
				$sql = "
			            DELETE 
							customfield_association, _c
							FROM
							`customfield_association`
							JOIN `customfield_generic` AS `_c_2` ON `_c_2`.`id` = `customfield_association`.`customfield_generic_id`
							LEFT JOIN `customfield_value` AS `_c` ON `_c`.`customfield_association_id` = `customfield_association`.`id`
							WHERE
								`customfield_association`.`item_id` in (".implode (",", $item_array).")
							AND
									_c_2.`type`= 'Specification'
			        ";
			        
			        //removing Quantity set condition because it update from quantity set
					// LEFT JOIN `item_image` AS `_i` ON `_i`.`customfield_value_id` = `_c`.`id`
					// LEFT JOIN `quantity_condition` AS `_q` ON `_q`.`customfield_value_id` = `_c`.`id`

        		$this->app->db->dsql()->expr($sql)->execute();
			}

			function removeCustomfields($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$sql = "
		            DELETE 
						customfield_association, _c, _i
						FROM
						`customfield_association`
						JOIN `customfield_generic` AS `_c_2` ON `_c_2`.`id` = `customfield_association`.`customfield_generic_id`
						LEFT JOIN `customfield_value` AS `_c` ON `_c`.`customfield_association_id` = `customfield_association`.`id`
						LEFT JOIN `item_image` AS `_i` ON `_i`.`customfield_value_id` = `_c`.`id`
						WHERE
							`customfield_association`.`item_id` in (".implode(",", $item_array).")
						AND
								_c_2.`type`= 'CustomField';
		        ";

			    //removing Quantity set condition because it update from quantity set
				// LEFT JOIN `quantity_condition` AS `_q` ON `_q`.`customfield_value_id` = `_c`.`id`

        		$this->app->db->dsql()->expr($sql)->execute();
			}

			function removeItemDepartmentAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$sql = "
		            DELETE 
						item_department_association, consumption
						FROM
						`item_department_association`
						LEFT JOIN `item_department_consumption` AS `consumption` ON `consumption`.`item_department_association_id` = `item_department_association`.`id`
						WHERE
							`item_department_association`.`item_id` in (".implode (",", $item_array).")
		        ";

        		$this->app->db->dsql()->expr($sql)->execute();

			}


			function removeQuantitySet($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				// $item_qty_assoc  = $this->add('xepan\commerce\Model_Item_Quantity_Set');
				// $item_qty_assoc->addCondition('item_id', $this->id);
				// foreach ($item_qty_assoc as $fields) {
				// 	$fields->delete();
				// }

		        $sql = "
		            DELETE 
						quantity_set, _qcondition
						FROM
						`quantity_set`
						LEFT JOIN `quantity_condition` AS `_qcondition` ON `_qcondition`.`quantity_set_id` = `quantity_set`.`id`
						WHERE
							`quantity_set`.`item_id` in (".implode(",", $item_array).")
		        ";

		        $this->app->db->dsql()->expr($sql)->execute();

			}

			function removeCategoryItemAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$model_cat_itm_assoc = $this->add('xepan\commerce\Model_CategoryItemAssociation')->addCondition('item_id',$item_array);
				$model_cat_itm_assoc->deleteAll();
			}


			function removeTemplateDesign($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$model_design = $this->add('xepan\commerce\Model_Item_Template_Design')->addCondition('item_id',$item_array);
				$model_design->deleteAll();
			}

			function removeImageAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$model_image = $this->add('xepan\commerce\Model_Item_Image')->addCondition('item_id',$item_array);
				$model_image->deleteAll();
			}

			function removeItemTaxationAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$model_tax = $this->add('xepan\commerce\Model_Item_Taxation_Association')->addCondition('item_id',$item_array);
				$model_tax->deleteAll();
			}

			function removeItemShippingAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$model_tax = $this->add('xepan\commerce\Model_Item_Shipping_Association')->addCondition('item_id',$item_array);
				$model_tax->deleteAll();
			}

			function removeItemFilterAssociation($item_array){
				if(!is_array($item_array) or !count($item_array))
					return;

				$sql = "
		            DELETE
						customfield_association, _c, _i
						FROM
						`customfield_association`
						JOIN `customfield_generic` AS `_c_2` ON `_c_2`.`id` = `customfield_association`.`customfield_generic_id`
						LEFT JOIN `customfield_value` AS `_c` ON `_c`.`customfield_association_id` = `customfield_association`.`id`
						LEFT JOIN `item_image` AS `_i` ON `_i`.`customfield_value_id` = `_c`.`id`
						WHERE
							`customfield_association`.`item_id` in (".implode(",", $item_array).")
						AND
								_c_2.`is_filterable`= '1'
						AND
								_c_2.`type`= 'Specification';
		        ";

			    //removing Quantity set condition because it update from quantity set
				// LEFT JOIN `quantity_condition` AS `_q` ON `_q`.`customfield_value_id` = `_c`.`id`

        		$this->app->db->dsql()->expr($sql)->execute();
			}

			function updateImageFromDesign($img_data, $delete_previous_image="Yes"){

				$item = $target = $this;
				if(!$this->loaded())
					return('item not found');

				if($delete_previous_image == "Yes"){
					$this->add('xepan/commerce/Model_Item_Image')
									->addCondition('item_id',$this->id)
									->addCondition('auto_generated',true)
									->deleteAll();
				}

				
				foreach($img_data as $page_name => $layouts) {
					foreach ($layouts as $layout_name => $image_data) {
						
						$image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image_data));
						$new_item_image = $this->add('xepan/commerce/Model_Item_Image');
						$image_model = $this->add('xepan/filestore/Model_File',['import_mode'=>'string','import_source'=>$image_data]);
						$image_model['original_filename'] = $this['name']."_".$this->id."_".$page_name."_".$layout_name.".png";
						$image_model->save();

						//First Time Save Image
						$new_item_image['file_id'] = $image_model->id;
						$new_item_image['item_id'] = $this->id;
						$new_item_image['auto_generated'] = true;
						$new_item_image->save();

						// echo $new_item_image->id;
					}
				}
				return "success";
				// $old_item_images = $this->add('xepan/commerce/Model_Item_Image')
				// 				->addCondition('item_id',$this->id)
				// 				->addCondition('auto_generated',true)
				// 				->getRows();

				$count = 0;
				foreach($img_data as $page_name => $layouts) {
					foreach ($layouts as $layout_name => $image_data) {

						$item_image = 0;
						$destination = "";
						if(isset($old_item_images[$count])){
							$item_image = $old_item_images[$count];
							$destination = $item_image['file'];
						}
												
						if($item_image)
							$destination = $_SERVER['DOCUMENT_ROOT'].'/'.$destination;


						$image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image_data));
						if(file_exists($destination) AND !is_dir($destination)){
							$fd = fopen($destination, 'w');
							fwrite($fd, $image_data);
							fclose($fd);
						}else{
							
							// var_dump($this->id);
							// var_dump($this['name']);
							// exit
							$new_item_image = $this->add('xepan/commerce/Model_Item_Image');

							$image_model = $this->add('xepan/filestore/Model_File',['import_mode'=>'string','import_source'=>$image_data]);
							$image_model['original_filename'] = $this['name']."_".$this->id."_".$page_name."_".$layout_name.".png";
							$image_model->save();

							//First Time Save Image
							$new_item_image['file_id'] = $image_model->id;
							$new_item_image['item_id'] = $this->id;
							$new_item_image['auto_generated'] = true;
							$new_item_image->save();
						}
						unset($old_item_images[$count]);
						$count++;
					}
				}

				$to_delete_image_id_array = [];
				foreach ($old_item_images as $key => $value) {
					$to_delete_image_id_array[] = $value['id'];
				}
				if(count($to_delete_image_id_array))
					$this->add('xepan/commerce/Model_Item_Image')
							->addCondition('id',$to_delete_image_id_array)->deleteAll();

				return "success";

			}

			function updateFirstImageFromDesign(){
				$item = $target = $this;
				$design = $target['designs'];
				if(!$design) return;

				$old_item_images = $this->add('xepan/commerce/Model_Item_Image')
								->addCondition('item_id',$this->id)
								->addCondition('auto_generated',true)
								->getRows();

				$design = json_decode($design,true);
				// foreach of layout 
				// check for first layout in with array of total design
				// and unset array value
				// after all layout finish then check if array has count
				//then remove all saved design one by one
				$count = 0;
				foreach ($design['design'] as $page_name => $layout) {
					// print_r($front_page);
					$layout_name_array = array_keys($layout);
					// print_r($layout_name_array);
					foreach ($layout_name_array as $index => $layout_name) {
						// get values from old image array
						$item_image = 0;
						$destination = "";
						if(isset($old_item_images[$count])){
							$item_image = $old_item_images[$count];
							$destination = $item_image['file'];
						}
						
						$cont = $this->add('xepan/commerce/Controller_DesignTemplate',array('item'=>$item,'design'=>$design,'page_name'=>$page_name,'layout'=>$layout_name,'image_ratio'=>2));
						$image_data =  $cont->show($type='png',$quality=3, $base64_encode=false, $return_data=true);
						
						if($item_image)
							$destination = $_SERVER['DOCUMENT_ROOT'].'/'.$destination;

						if(file_exists($destination) AND !is_dir($destination)){
							$fd = fopen($destination, 'w');
							fwrite($fd, $image_data);
							fclose($fd);
						}else{
							
							// var_dump($this->id);
							// var_dump($this['name']);
							// exit
							$new_item_image = $this->add('xepan/commerce/Model_Item_Image');

							$image_model = $this->add('xepan/filestore/Model_File',['import_mode'=>'string','import_source'=>$image_data]);
							$image_model['original_filename'] = 'design_for_item_'. $this->id."_".$this['name'].".png";
							$image_model->save();

							//First Time Save Image
							$new_item_image['file_id'] = $image_model->id;
							$new_item_image['item_id'] = $item->id;
							$new_item_image['auto_generated'] = true;
							$new_item_image->save();
						}
						unset($old_item_images[$count]);
						$count++;
					}
				}

				//Delete extra layout file from item images
				$to_delete_image_id_array = [];
				foreach ($old_item_images as $key => $value) {
					$to_delete_image_id_array[] = $value['id'];
				}
				if(count($to_delete_image_id_array))
					$this->add('xepan/commerce/Model_Item_Image')
							->addCondition('id',$to_delete_image_id_array)->deleteAll();
			}
	
	// custom field array [ "23":
							// {"department_name":"Digital Press",
							// "4":
								// {"custom_field_name":"Paper GSM",
								// "custom_field_value_id":"3803",
								// "custom_field_value_name":"300"
							// }
	function getConsumption($order_qty,$custom_field=[],$item_id=null){
		if(!$item_id){
			if(!$this->loaded())
				throw new \Exception("model must loaded or item_id pass");
			$item_id = $this->id;
		}


		$consumption_array = [];
		$dept_asso_model = $this->add('xepan\commerce\Model_Item_Department_Association')
							->addCondition('item_id',$item_id);

		
		// foreach department association
		foreach ($dept_asso_model as $dept_asso) {
			$dept_id = $dept_asso['department_id'];
			$dept_name = $dept_asso['department'];

			if(!isset($custom_field[$dept_id]))
				continue;

			if(!isset($consumption_array[$dept_id]))
				$consumption_array[$dept_id] = [];

			// foreach department consumption item
			$consumption_model = $this->add('xepan\commerce\Model_Item_Department_Consumption');
			$consumption_model->addCondition('item_department_association_id',$dept_asso->id);
			$consumption_model->addExpression('constraint_count')->set($consumption_model->dsql()->expr('[0]',[$consumption_model->refSQL('xepan\commerce\Item_Department_ConsumptionConstraint')->count()]));
			$consumption_model->setOrder('constraint_count','desc');
			foreach ($consumption_model as $consumption) {

				$consumption_item_id = $consumption['composition_item_id'];
				$unit_consumption_qty = $consumption['quantity'];
				$consumption_qty = $consumption['quantity'] * $order_qty;
							
				if(!$consumption->hasConstraints() or $this->matchConstraints($custom_field[$dept_id],$consumption)){
					$consumption_array[$dept_id]['department_name'] = $dept_name;

					if(!isset($consumption_array[$dept_id][$consumption_item_id]))
						$consumption_array[$dept_id][$consumption_item_id] = [] ;

					$cf_key = $this->convertCustomFieldToKey(json_decode($consumption['custom_fields'],true));
					$constraint_json = $consumption->getConstraint($format = "json");

					// include one item at a time
					if(isset($consumption_array[$dept_id][$consumption_item_id][$cf_key]))
						continue;

					$consumption_array[$dept_id][$consumption_item_id][$cf_key] = [] ;

					$consumption_array[$dept_id][$consumption_item_id][$cf_key]['name'] = $consumption['composition_item'];
					$consumption_array[$dept_id][$consumption_item_id][$cf_key]['qty'] = $consumption_qty; //isset($consumption_array[$dept_id][$consumption_item_id][$cf_key]['qty'])?($consumption_array[$dept_id][$consumption_item_id][$cf_key]['qty'] + $consumption_qty):
					$consumption_array[$dept_id][$consumption_item_id][$cf_key]['unit'] = $consumption['unit'];
					$consumption_array[$dept_id][$consumption_item_id][$cf_key]['constraint_custom_field'] = $constraint_json;

					$consumption_array['total'][$consumption_item_id][$cf_key]['qty'] = isset($consumption_array['total'][$consumption_item_id][$cf_key]['qty'])?($consumption_array['total'][$consumption_item_id][$cf_key]['qty'] + $consumption_qty):$consumption_qty;
					$consumption_array['total'][$consumption_item_id][$cf_key]['name'] = $consumption['composition_item'];
					$consumption_array['total'][$consumption_item_id][$cf_key]['unit'] = $consumption['unit'];
				}

			}

			if(!count($consumption_array[$dept_id]))
				unset($consumption_array[$dept_id]);
		}

		return $consumption_array;
	}

	//order_item_custom_field =  {"department_name":"Digital Press",
								// "4":
									// {"custom_field_name":"Paper GSM",
									// "custom_field_value_id":"3803",
									// "custom_field_value_name":"300"
									// }
								// "7":
									// {"custom_field_name":"Paper GSM",
									// "custom_field_value_id":"3803",
									// "custom_field_value_name":"300"
									// }
	function matchConstraints($cf_array,$consumption_model){
		unset($cf_array['department_name']);
		
		$constraints = $consumption_model->ref('xepan\commerce\Item_Department_ConsumptionConstraint');
		$all_conditions_matched = true;
		foreach ($constraints as $constraint) {
			$item_cf_id = $constraint['item_customfield_id'];
			$item_cfv_name = $constraint['item_customfield_value_name'];

			if(isset($cf_array[$item_cf_id]) and $cf_array[$item_cf_id]['custom_field_value_name'] != $item_cfv_name)
				return false;
		}

		return $all_conditions_matched;
	}

	function stockAvalibility(){
		if(!$this->loaded())
			throw new \Exception("model item must loaded");
	}

	//order_item_custom_field =  {"department_name":"Digital Press",
								// "4":
									// {"custom_field_name":"Paper GSM",
									// "custom_field_value_id":"3803",
									// "custom_field_value_name":"300"
									// }
	function convertCustomFieldToKey($custom_field){
		if(!is_array($custom_field))
			throw new \Exception("must pass array of custom field");

		ksort($custom_field);
		$key = "";
		foreach ($custom_field as $dept_id => $cf_array) {
			if(isset($cf_array['department_name'])){
				unset($cf_array['department_name']);
			}

			ksort($cf_array);
			foreach ($cf_array as $cf_key => $data) {
				$key .= $cf_key."~".trim($data['custom_field_name'])."<=>".$data['custom_field_value_id']."~".trim($data['custom_field_value_name'])."||";
			}
		}

		return $key?:0;
	}

	function getStock($item_custom_fields){

	}

}