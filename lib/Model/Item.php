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
		'Published'=>['view','edit','delete','unpublish','duplicate','other_info'],
		'UnPublished'=>['view','edit','delete','publish','duplicate','other_info']
	];

	public $addOtherInfo=true;
	public $document_type="Item";

	function init(){
		parent::init();

		$this->getElement('created_by_id')->defaultValue(@$this->app->employee->id);
		$item_j=$this->join('item.document_id');

		$item_j->hasOne('xepan\base\Contact','designer_id')->defaultValue(0);
		$item_j->hasOne('xepan\commerce\Model_Item_Template','duplicate_from_item_id')->defaultValue(0);
		$item_j->hasOne('xepan\commerce\Model_Unit','qty_unit_id');
		$item_j->hasOne('xepan\accounts\Ledger','nominal_id');
		$item_j->hasOne('xepan\accounts\Ledger','purchase_nominal_id');

		$item_j->addField('name')->mandatory(true)->sortable(true);
		$item_j->addField('sku')->PlaceHolder('Insert Unique Referance Code')->caption('Code')->hint('Insert Unique Referance Code')->mandatory(true);
		$item_j->addField('display_sequence')->hint('descending wise sorting');
		$item_j->addField('description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		$item_j->addField('slug_url');
		// gst related field
		$item_j->addField('hsn_sac')->sortable(true)->caption('HSN/SAC');

		$item_j->addField('original_price')->type('money')->mandatory(true)->defaultValue(0);
		$item_j->addField('sale_price')->type('money')->mandatory(true)->defaultValue(0)->sortable(true);
		$item_j->addField('treat_sale_price_as_amount')->type('boolean')->defaultValue(0);
		
		$item_j->addField('expiry_date')->type('date')->defaultValue(null);
		
		$item_j->addField('minimum_order_qty')->type('int')->defaultValue(1);
		$item_j->addField('maximum_order_qty')->type('int')->defaultValue(null);
		// $item_j->addField('qty_unit')->defaultValue(null);
		$item_j->addField('qty_from_set_only')->type('boolean')->defaultValue(true);
		
		// Item renewable fields
		$item_j->addField('is_renewable')->type('boolean')->defaultValue(0);
		$item_j->addField('remind_to')->display(['form'=>'xepan\base\DropDown'])->setValueList(['Both'=>'Both','Customer'=>'Customer','Admin'=>'Admin']);
		$item_j->addField('renewable_value')->type('number');
		$item_j->addField('renewable_unit')->setValueList(['DAYS'=>'Day','WEEKS'=>'Week','MONTHS'=>'Month','YEARS'=>'Year']);

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
		$item_j->addField('is_teller_made_item')->type('boolean')->defaultValue(false);
		
		$item_j->addField('is_servicable')->type('boolean')->defaultValue(false);
		$item_j->addField('is_productionable')->type('boolean')->hint('used in Production')->defaultValue(false);
		$item_j->addField('is_production_phases_fixed')->type('boolean')->hint('used in Production')->defaultValue(false);
		$item_j->addField('website_display')->type('boolean')->hint('Show on Website')->defaultValue(false);
		$item_j->addField('is_downloadable')->type('boolean')->defaultValue(false);
		$item_j->addField('is_rentable')->type('boolean')->defaultValue(false);
		$item_j->addField('is_designable')->type('boolean')->hint('item become designable and customer customize the design')->defaultValue(false);
		$item_j->addField('is_template')->type('boolean')->hint('blueprint/layout of designable item')->defaultValue(false);
		$item_j->addField('is_attachment_allow')->type('boolean')->hint('by this option you can attach the item information pdf/doc etc. to be available on website')->defaultValue(false);
		$item_j->addField('is_serializable')->type('boolean')->defaultValue(false);
		
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
		//Item Package Option to added multiple item in a package
		$item_j->addField('is_package')->type('boolean')->hint('Create Package Used Multiple Item`s')->defaultValue(false);

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
		$item_j->addField('minimum_stock_limit');
		
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
		$item_j->hasMany('xepan\commerce\PackageItemAssociation','package_item_id',null,'MyPackageItems');
		$item_j->hasMany('xepan\commerce\PackageItemAssociation','item_id',null,'InPackages');
		
		
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

		$this->addExpression('qty_unit_group_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('qty_unit_id')->fieldQuery('unit_group_id')]);
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

		foreach ($this->ref('MyPackageItems') as $package_item) {
			$package_item->delete();
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
		
		// update slug_url
		if(!strlen(trim($this['slug_url']))){
			$this['slug_url'] = $this->app->normalizeSlugUrl($this['name']."-".$this['sku']);
		}else
			$this['slug_url'] = $this->app->normalizeSlugUrl($this['slug_url']);

		// check slug is exist or not
		$oi = $this->add('xepan\commerce\Model_Item');
		$oi->addCondition('slug_url',$this['slug_url']);
		$oi->addCondition('id','<>',$this->id);
		$oi->tryLoadAny();
		if($oi->loaded()){
			throw $this->Exception('slug Already Exist '.$this['name'],'ValidityCheck')->setField('slug_url');
		}

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
		$form->addField('slug_url');
		$form->addField('Line','hsn_sac','HSN/SAC')->set($this['hsn_sac']);
		$form->addField('xepan\base\DropDown','qty_unit')->setModel('xepan\commerce\Unit');
		$customer_field = $form->addField('DropDown','to_customer_id','To Customer');
		$customer_field->setEmptyText('Please Select customer if this item belongs to a specific customer');
		$customer_field->setModel('xepan\commerce\Model_Customer');
		$customer_field->set($this['to_customer_id']);

		if($this['is_designable']){
			$field_designer = $form->addField('DropDown','designer');
			$field_designer->setModel($designer);
			$field_designer->set($this->app->employee->id);
		}

		$form->addField('checkbox','create_as_child')->set(true);
		$form->addField('checkbox','continue_duplicating');
		$form->addSubmit('Duplicate');

		if($form->isSubmitted()){
			// $item = $this->add('xepan\commerce\Model_Item');
			// $item->addCondition('name',$form['name']);
			// $item->tryLoadAny();

			// if($item->loaded()){
			// 	$form->displayError('name','Item with this name already exist, please choose a different name');
			// }

			$sku_item = $this->add('xepan\commerce\Model_Item');
			$sku_item->addCondition('sku',$form['sku']);
			$sku_item->tryLoadAny();
			
			if($sku_item->loaded()){
				$form->displayError('sku','sku already exist, please choose a different sku');
			}

			$designer->loadLoggedIn('Customer');

			try{
				$this->api->db->beginTransaction();

				$name = $form['name']; 
				$sku = $form['sku'];
				$slug_url = $form['slug_url'];
				$designer_id = $form['designer'];
				$qty_unit = $form['qty_unit'];
				$hsn_sac = $form['hsn_sac'];
				$is_template = false;
				$is_published = false;
				$create_default_design_also  = false;
				$to_customer_id = $form['to_customer_id'];

				if($form['create_as_child'])
					$duplicate_from_item_id = $this->id;     		
				else
					$duplicate_from_item_id = null;     		

				$new_item = $this->duplicate($name, $sku, $designer_id, $is_template, $is_published, $duplicate_from_item_id,$create_default_design_also,$to_customer_id,$qty_unit,$hsn_sac,$slug_url);
				$this->app->employee
				->addActivity("Item : '".$this['name']."' Duplicated as New Item : '".$name."'", $this->id/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_itemdetail&document_id=".$this->id."")
				->notifyWhoCan('unpublish,duplicate','Published');
				$this->api->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
	            throw $e;
			}
			if(!$form['continue_duplicating'])
				return $this->api->js()->univ()->location($this->app->url('xepan_commerce_itemdetail',['document_id'=>$new_item->id, 'action'=>'edit']));
			else
				return $this->api->js(null,$form->js()->reload())->univ()->successMessage('wait ... ');
		}
	}

	function duplicate($name, $sku, $designer_id, $is_template, $is_published, $duplicate_from_item_id, $create_default_design_also,$to_customer_id=null,$qty_unit=null,$hsn_sac="",$slug_url=""){
		if(!$qty_unit)
			$qty_unit = $this['qty_unit_id'];
		

		$model_item = $this->add('xepan\commerce\Model_Item');

		$fields=$this->getActualFields();
		$fields = array_diff($fields,array('id','name','sku','slug_url','designer_id', 'is_published', 'created_at','is_template','duplicate_from_item_id','qty_unit_id'));

		foreach ($fields as $fld) {
			$model_item[$fld] = $this[$fld];
		}

		// $model_item->save();

		$model_item['name'] = $name;
		$model_item['sku'] = $sku;
		$model_item['designer_id'] = $designer_id;
		$model_item['qty_unit_id'] = $qty_unit;
		$model_item['slug_url'] = $slug_url;
		// $model_item['created_at'] = $created_at;
		$model_item['is_template'] = $is_template;
		$model_item['is_published'] = $is_published;
		$model_item['duplicate_from_item_id'] = $duplicate_from_item_id;
		$model_item['created_at'] = $this->app->now;
		$model_item['to_customer_id'] = $to_customer_id;
		$model_item['hsn_sac'] = $hsn_sac;
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
					$cf_val_query .= " ('$nid' , '".$v['status']."','".str_replace("'", "\'",$v['name'])."','".$v['highlight_it']."' ),";
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
		$has_custom_field = false;
		foreach ($old_cf_asso_rows as $qr) {
			foreach ($child_item_id_array as $index => $item) {
				if(!isset($old_cf_asso_values[$j])) continue;
				foreach ($old_cf_asso_values[$j] as $v) {
					$has_custom_field = true;
					$nid= $new_cf_asso_id[$i];
					$cf_val_query .= " ('$nid' , '".$v['status']."','".str_replace("'", "\'",$v['name'])."','".$v['highlight_it']."' ),";
				}
				$i++;
			}
			$j++;
		}

		// exit();
		if($has_custom_field){
			$cf_val_query = trim($cf_val_query,',');
			// echo $cf_val_query .'<br/><br/><br/><br/>';
			$this->app->db->dsql()->expr($cf_val_query)->execute();
		}
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
					$cf_val_query .= " ('$nid' , '".$v['status']."','".str_replace("'", "\'",$v['name'])."','".$v['highlight_it']."' ),";
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
		->addCondition([['can_effect_stock',false],['can_effect_stock',null]])
		;
		$asso->addCondition('CustomFieldType','Specification');
		if(!$with_filter){
			$asso->addCondition([['is_filterable', false],['is_filterable', null]]);
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

	function associateCustomField($department_phase_id=false,$stock_effect_cf_only=false){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");

		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$this->id)
		;
		if($department_phase_id)
			$asso->addCondition('department_id',$department_phase_id);
		if($stock_effect_cf_only)
			$asso->addCondition('can_effect_stock',true);

		// $asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addExpression('sequence_order')->set($asso->refSQL('customfield_generic_id')->fieldQuery('sequence_order'));
		$asso->addCondition('CustomFieldType','CustomField');
		$asso->setOrder('name','asc');
		$asso->setOrder('sequence_order','asc');
		$asso->tryLoadAny();
		
		return $asso;
	}

	function getAssociatedCustomFields($department_id,$stock_effect_cf_only=false){
		$associated_cf = $this->associateCustomField($department_id,$stock_effect_cf_only)->_dsql()->del('fields')->field('customfield_generic_id')->getAll();
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

	function isInCategory($category_name){
		if(!$this->loaded()) throw new \Exception("item model must loaded");
		
						
		$cat = $this->add('xepan\commerce\Model_Category');
		$cat->addCondition('name',$category_name);
		$cat->tryLoadAny();
		if(!$cat->loaded()){return false;};

		$asso_cat_ids = $this->getAssociatedCategories();

		if(in_array($cat->id, $asso_cat_ids)) return true;
		return false;
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

	function noneDepartmentAssociateCustomFields($stock_effect_custom_field_only=false){
		if(!$this->loaded())
			throw new \Exception("Item Model Must Loaded before getting noneDepartmentAssociateCustomFields");
		
		$cf = $this->add('xepan\commerce\Model_Item_CustomField_Association');
		$cf->addCondition('item_id',$this->id)
			->addCondition(
					$cf->dsql()->orExpr()
								->where($cf->getElement('department_id'),null)
								->where($cf->getElement('department_id'),0)
					)
			->addCondition('CustomFieldType','CustomField');
		if($stock_effect_custom_field_only)
			$cf->addCondition('can_effect_stock',true);

		$cf->tryLoadAny();
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

			//  return tax included price
			function getAmount($custom_field_values_array, $qty, $rate_chart='retailer'){
				if($this['treat_sale_price_as_amount'])
					$qty = 1;
				
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
							'shipping_duration_days'=>isset($shipping_detail_array['shipping_duration_days'])?$shipping_detail_array['shipping_duration_days']:0,
							'express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
							'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
							'express_shipping_duration_days'=>isset($shipping_detail_array['express_shipping_duration_days'])?$shipping_detail_array['express_shipping_duration_days']:0,
							'raw_shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
							'raw_express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
							'taxation'=>$applicable_taxation,
							'raw_sale_price'=>$price['sale_price'],
							'raw_original_price'=>$price['original_price']
						);
				}
				
				$tax_percentage = trim($applicable_taxation['percentage']);
				$original_amount_include_tax = round($original_amount + (($tax_percentage*$original_amount) / 100),2);
				$sale_amount_include_tax = round($sale_amount + (($tax_percentage*$sale_amount) / 100),2);

				
				$shipping_charge_include_tax = $shipping_detail_array['shipping_charge'];
				$express_shipping_charge_include_tax = $shipping_detail_array['express_shipping_charge'];

				// echo "<pre>";
				// echo $original_amount_include_tax."<br/>";
				// echo $sale_amount_include_tax."<br/>";
				// print_r($price);
				// echo "</pre>";
				// die();

				if($misc_tax_on_shipping){
					$shipping_charge_include_tax = round($shipping_charge_include_tax + ($tax_percentage*$shipping_charge_include_tax / 100),2);
					
					$express_shipping_charge_include_tax = round($express_shipping_charge_include_tax + ($tax_percentage*$express_shipping_charge_include_tax / 100),2);
				}
				
				return array(
							'original_amount'=>$original_amount_include_tax?:0,
							'sale_amount'=>$sale_amount_include_tax?:0,
							'shipping_charge'=>$shipping_charge_include_tax?:0,
							'shipping_duration'=>isset($shipping_detail_array['shipping_duration'])?$shipping_detail_array['shipping_duration']:"",
							'shipping_duration_days'=>isset($shipping_detail_array['shipping_duration_days'])?$shipping_detail_array['shipping_duration_days']:0,
							'express_shipping_charge'=>$express_shipping_charge_include_tax?:0,
							'express_shipping_duration'=>isset($shipping_detail_array['express_shipping_duration'])?$shipping_detail_array['express_shipping_duration']:"",
							'express_shipping_duration_days'=>isset($shipping_detail_array['express_shipping_duration_days'])?$shipping_detail_array['express_shipping_duration_days']:0,
							'raw_shipping_charge'=>isset($shipping_detail_array['shipping_charge'])?$shipping_detail_array['shipping_charge']:0,
							'raw_express_shipping_charge'=>isset($shipping_detail_array['express_shipping_charge'])?$shipping_detail_array['express_shipping_charge']:0,
							'taxation'=>$applicable_taxation,
							'raw_sale_price'=>$price['sale_price'],
							'raw_original_price'=>$price['original_price']
							);
			}

		function applyTax(){
			return $this->ref('Tax')->setOrder('priority','desc')->tryLoadAny()->setLimit(1);
		}	

		// return taxation model if found else false
		function applicableTaxation($country_id=null,$state_id=null){
			if(!$this->loaded())
				return false;
			
			$current_country_id = null;
			if( isset($this->app->country) and ($this->app->country instanceof \xepan\base\Model_Country))
				$current_country_id = $this->app->country->id;	

			$current_state_id = null;
			if(isset($this->app->state) and ($this->app->state instanceof \xepan\base\Model_State))
				$current_state_id = $this->app->state->id;

			if($country_id) $current_country_id = $country_id;
			if($state_id) $current_state_id = $state_id;

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

		function shippingCharge($sale_amount,$selected_qty, $per_item_weight=null,$use_this_country_id=0,$use_this_state_id=0){
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
			if($use_this_state_id) $state_id = $use_this_state_id;
			if($use_this_country_id) $country_id = $use_this_country_id;

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
	
	/**custom field array{ [ "23":
								[	"department_name":"Digital Press",
									"4":
									[
										"custom_field_name":"Paper GSM",
										"custom_field_value_id":"3803",
										"custom_field_value_name":"300"
									]
								}]
	*/
	function getConsumption($order_qty,$custom_field=[],$item_id=null,$qsp_qty_unit_id=null){
		
		if(!$this->loaded())
			throw new \Exception("model must loaded or item_id pass");

		if(!$item_id){
			$item_id = $this->id;
		}


		$consumption_array = ['total'=>[]];
		$dept_asso_model = $this->add('xepan\commerce\Model_Item_Department_Association')
							->addCondition('item_id',$item_id);

		if($qsp_qty_unit_id){
			$order_qty = $this->app->getConvertedQty($this['qty_unit_id'],$qsp_qty_unit_id,$order_qty);
		}
		
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

	//  [cf_name1 => cf_value_name1,cf_name2 => cf_value_name2]

	function filterStockEffectedCustomField($custom_field){
		$cf_array = json_decode($custom_field,true)?:[];
		$convert_cf_array=[];

		
		ksort($cf_array);
		foreach ($cf_array as $department_id => $dept_array) {
				unset($dept_array['department_name']);				
				
				ksort($dept_array);
				foreach ($dept_array as $customfield_id => $cf_values) {

					$dept_stock_effect_cf_array = $this->getAssociatedCustomFields($department_id,true);
					if(!in_array($customfield_id, $dept_stock_effect_cf_array))
						continue;

					$convert_cf_array[$cf_values['custom_field_name']] = $cf_values['custom_field_value_name'];
				}
		}
		return $convert_cf_array;
	}

	// $custom_field = json;
	// required in param is qty that you check in stock
	// required in return array is qty that you required to purchase or production
	function getStockAvalibility($custom_fields,$required,&$result,$warehouse=null,$qsp_item_unit_id=null,$serial_no_array=null){
		if(!$this->loaded())
			throw new \Exception("model item must loaded");

		
		
		// 1. filter for stock effected custom filed only from given custom field
		// 2. Convert CF to  StokItem required CF array ie $cf_value = ['Custom_fiel'=>'Value','Custom_fueld'=>'Value']
		// 2. StockItem model object with $si=add('StiockItem',['cf_value'=>$cf_value,'warehouse'=>$ware_house])
		// 4  $si->load($this->id);
		// $si['net_stock'] == this fields available net stock
		// if isset($result[$this['name']][cf_string]) then plus values ()required and available both 
		// or just set $result[$this['name']][cf_string] = ['required'=>$required,'available'=>$si['net_stock']];
		// if($si['is_productionable'] && ($required - $si['net_stock'])>0 )
		// 		$cons = $this->getConsuption($required - $si['net_stock'],$custom_field=[],$item_id=null)
		// 		foreach($cons[total] as $c){
					// $this->getStockAvalibality($c[custom_fields],$c['required_quantity'],$result);
		//		 }

		if($qsp_item_unit_id){
			$required = $this->app->getConvertedQty($this['qty_unit_id'],$qsp_item_unit_id,$required);	
		}

		$custom_field_array = json_decode($custom_fields,true);

		$se_cf = $this->filterStockEffectedCustomField($custom_fields);

		$item_stock = $this->add('xepan\commerce\Model_Item_Stock',['item_custom_field'=>$se_cf,'warehouse_id'=>$warehouse]);
		$item_stock->load($this->id);
		$pre_made_net_stock = $item_stock['net_stock']?:0;
		
		$required = $required - $pre_made_net_stock;
		if($required < 0) $required = 0;

		$cf_key = $this->convertCustomFieldToKey( $custom_field_array,true);

		if(isset($result[$this['name']][$cf_key])){
			$result[$this['name']][$cf_key]['required']	+= $required;
			$result[$this['name']][$cf_key]['available'] += $pre_made_net_stock;
		}else{
			$result[$this['name']][$cf_key] = [
												'required'=>$required?:0,
												'available'=>$pre_made_net_stock?:0,
												'unit'=>$item_stock['qty_unit']
											];
		}

		if($required > 0){

			if($item_stock['is_productionable'] && $required > 0){

				$consumption_item_array = $this->getConsumption($required,$custom_field_array,$this->id);

				$consumption_item_array = $consumption_item_array['total'];
				/*NESTED CONSUMPTION ITEM TEMPRARORY STOPPED */
				foreach ($consumption_item_array as $item_id => $array) {
								
					foreach ($array as $key => $data_array) { // $key = $cf_key
						$item_model = $this->add('xepan\commerce\Model_Item_Stock')->load($item_id);
							if(isset($result[$item_model['name']][$key])){
								$result[$item_model['name']][$key]['required']	+= $data_array['qty'];
								$result[$item_model['name']][$key]['available'] += ($item_model['net_stock']?:0);
							}else{
								$result[$item_model['name']][$key] = [
																	'required'=>$data_array['qty'],
																	'available'=>$item_model['net_stock']?:0,
																	'unit'=>$data_array['unit']
																];
							}
						// $this->getStockAvalibility($item_model->createOrderExtraInfo($key),$data_array['qty'],$result,$warehouse);
					}
				}
			}
		}

		// check serial no
		if(is_array($serial_no_array)){
			
			$is = $this->add('xepan\commerce\Model_Item_Serial');
			if($warehouse)
				$is->addCondition('contact_id',$warehouse);
			$is->addCondition('serial_no','in',$serial_no_array);
			$serial_available = array_column($is->getRows(), 'serial_no');
			$result[$this['name']][$cf_key]['serial']= [
													'available'=>$serial_available,
													'unavailable'=>array_diff($serial_no_array,$serial_available)
												];
		}

		return $result;

	}


	/** 

	$cf_key = cf_id~cf_name<=>cf_val_id~cf_val_name|| ... ";

	return  = custom field array{ [ "23":
								[	"department_name":"Digital Press",
									"4":
									[
										"custom_field_name":"Paper GSM",
										"custom_field_value_id":"3803",
										"custom_field_value_name":"300"
									]
								}]
	*/

	function createOrderExtraInfo($cf_key,$mode="json"){	

		if(!$this->loaded()){
			throw new \Exception("Error Processing Request", 1);
		}

		$array = $this->convertCFKeyToArray($cf_key);


		$cf_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association');
		$cf_asso->addCondition('item_id',$this->id);
		$cf_asso->addCondition('can_effect_stock',true);
		$cf_asso->addCondition('customfield_generic_id',array_keys($array)); // custome_field_ids

		$result = []; 

		foreach ($cf_asso as $cf) {
			if(!isset($result[$cf['department_id']])) $result[$cf['department_id']] =[];

			$result[$cf['department_id']] = [ 'department_name'=>$cf['department'] ];
			
			if(isset($result[$cf['department_id']][$cf['customfield_generic_id']])) continue;
			
				$result[$cf['department_id']][$cf['customfield_generic_id']] = [
																			'custom_field_name' => $array[$cf['customfield_generic_id']]['custom_field_name'],
																			'custom_field_value_id' => $array[$cf['customfield_generic_id']]['custom_field_value_id'],
																			'custom_field_value_name' => $array[$cf['customfield_generic_id']]['custom_field_value_name']
																		];
		}

		if($mode == "json")
			return json_encode($result);

		return $result;
		
	}



	function convertCFKeyToArray($key){
		$return_array = [];
		$temp = explode("||", $key);
		
		foreach ($temp as $cf_v_str) {
			if(!$cf_v_str)
				continue;

			$cf_v_array = explode("<=>", $cf_v_str);
			
			if(!$cf_v_array)
				continue;

			$cf_str = $cf_v_array[0];
			$cf_value_str = $cf_v_array[1];
			
			$cf_array = explode("~", $cf_str);
			$cf_value_array = explode("~", $cf_value_str);

			$return_array[$cf_array[0]] = [
										'custom_field_id'=>$cf_array[0],
										'custom_field_name'=>$cf_array[1],
										'custom_field_value_id'=>$cf_value_array[0],
										'custom_field_value_name'=>$cf_value_array[1]
									];
		}

		return $return_array;				
	}



	//order_item_custom_field =  {"department_name":"Digital Press",
								// "4":
									// {"custom_field_name":"Paper GSM",
									// "custom_field_value_id":"3803",
									// "custom_field_value_name":"300"
									// }
	// custom field is actual order item custom field
	function convertCustomFieldToKey($custom_field=[],$use_only_stock_effect_cf=false){
		
		if(!$this->loaded())
			throw $this->exception('item model must loaded');
		
		if(!$custom_field) $custom_field = [];

		if(!is_array($custom_field)){
			throw new \Exception("must pass array of custom field");
		}

		ksort($custom_field);
		$key = "";
		foreach ($custom_field as $dept_id => $cf_array) {
			if(isset($cf_array['department_name'])){
				unset($cf_array['department_name']);
			}

			ksort($cf_array);
			foreach ($cf_array as $cf_key => $data) {
				// get stock_effect_custom_field
				$dept_stock_effect_cf_array = $this->getAssociatedCustomFields($dept_id,true);
				
				if(!in_array($cf_key, $dept_stock_effect_cf_array))
					continue;

				$key .= $cf_key."~".trim($data['custom_field_name'])."<=>".$data['custom_field_value_id']."~".trim($data['custom_field_value_name'])."||";
			}
		}

		return $key?:0;
	}

	function getReadOnlyCustomField($use_only_stock_effect_cf = false){
		if(!$this->loaded()) throw new \Exception("item model must loaded", 1);
		$data = [];
		
		$item = $this;
		$preDefinedPhase = [];
		foreach ($item->getAssociatedDepartment() as $key => $value) {
			$preDefinedPhase[$value] = [];
		}

		$none_dept_cf = $item->noneDepartmentAssociateCustomFields($use_only_stock_effect_cf);

		// none department
		$data[0] = ['department_name'=>'No Department','pre_selected'=>1,'production_level'=>0];
		foreach ($none_dept_cf as $cf_asso) {
			$data[0][$cf_asso['customfield_generic_id']] = $this->getCustomFieldAndValue($cf_asso);
		}

		//[department_id] = ['depart_name'=>,'cf'=>[]];
		//Department Associated CustomFields
		$phases = $this->add('xepan\hr\Model_Department')
					->setOrder('production_level','asc');
		foreach ($phases as $phase) {
			$custom_fields_asso = $item->ref('xepan\commerce\Item_CustomField_Association')
									->addCondition('department_id',$phase->id);
			$pre_selected = 0;
			if(isset($preDefinedPhase[$phase->id]))
				$pre_selected=1;
			$data[$phase->id] = ['department_name'=>$phase['name'],'pre_selected'=>$pre_selected,'production_level'=>$phase['production_level']];

			// showing only stock effected cf with department
			if($use_only_stock_effect_cf){
				$custom_fields_asso->addCondition('can_effect_stock',true);
				if(!$custom_fields_asso->count()->getOne())
					continue;
			}

			// if item has custome fields for phase & set if editing
			foreach ($custom_fields_asso as $cfassos) {
				$data[$phase->id][$cfassos['customfield_generic_id']] = $this->getCustomFieldAndValue($custom_fields_asso);
			}
		}

		return $data;		
	}

	function getCustomFieldAndValue($custom_fields_asso){
		
		$cf = $this->add('xepan\commerce\Model_Item_CustomField_Generic')
					->load($custom_fields_asso['customfield_generic_id']);
		
		//[cf_id => ['name'=>,'value'=>[]]
		$temp = [
					'custom_field_name'=>$custom_fields_asso['name'],
					'custom_field_value_id'=>"",
					'custom_field_value_name'=>"",
					'display_type'=>$cf['display_type'],
					'mandatory'=>false,
					'value'=>[]
				];

		switch($cf['display_type']){
			case "DropDown":
				$values = $this->add('xepan\commerce\Model_Item_CustomField_Value');
				$values->addCondition('customfield_association_id',$custom_fields_asso->id);
				$values_array=array();
				foreach ($values as $value) {
					$values_array[$value['id']]=$value['name'];
				}
				$temp['value'] = $values_array;
			break;
			case "Color":
			break;
		}

		return $temp;
	}

	function getURL($page){
		if(!$this->loaded()) throw new \Exception("item model must loaded for url");

		if($this->app->enable_sef){
			$url = $this->api->url($page."/".$this['slug_url']);
			$url->arguments = [];
		}else
			$url = $this->api->url($page,['commerce_item_id'=>$this->id]);
		return $url;			
	}

	function getProductionDepartment(){
		$asso = $this->add('xepan\commerce\Model_Item_Department_Association');
		$asso->addCondition('item_id',$this->id);

		$data = [];
		foreach ($asso as $m) {
			$data[$m['department_id']] = ['department_name'=>$m['department']];
		}
		
		return $data;
	}
}