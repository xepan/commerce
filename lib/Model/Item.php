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
					'Published'=>['view','edit','delete','unpublish'],
					'UnPublished'=>['view','edit','delete','publish']
					];

	function init(){
		parent::init();

		$this->getElement('created_by_id')->defaultValue($this->app->employee->id);

		$item_j=$this->join('item.document_id');

		$item_j->hasOne('xepan\base\Contact','designer_id');

		$item_j->addField('name')->mandatory(true);
		$item_j->addField('sku')->PlaceHolder('Insert Unique Referance Code')->caption('Code')->hint('Insert Unique Referance Code')->mandatory(true);
		$item_j->addField('display_sequence')->hint('descending wise sorting');
		$item_j->addField('description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		
		$item_j->addField('original_price')->type('money')->mandatory(true);
		$item_j->addField('sale_price')->type('money')->mandatory(true);
		
		
		$item_j->addField('expiry_date')->type('date');
		
		$item_j->addField('minimum_order_qty')->type('int');
		$item_j->addField('maximum_order_qty')->type('int');
		$item_j->addField('qty_unit');
		$item_j->addField('qty_from_set_only')->type('boolean');
		
		//Item Allow Optins
		$item_j->addField('is_party_publish')->type('boolean')->hint('Freelancer Item Design/Template to be Approved');
		$item_j->addField('is_saleable')->type('boolean')->hint('Make Item Becomes Saleable');
		$item_j->addField('is_allowuploadable')->type('boolean')->hint('on website customer can upload a degin for designable item');
		$item_j->addField('is_purchasable')->type('boolean')->hint('item display only at purchase Order/Invoice');
		//Item Stock Options
		// $item_j->addField('available_stock')->type('boolean')->hint('Stock Availability ');
		$item_j->addField('maintain_inventory')->type('boolean')->hint('Manage Inventory ');
		$item_j->addField('allow_negative_stock')->type('boolean')->hint('show item on website apart from stock is available or not');
		$item_j->addField('is_dispatchable')->type('boolean')->hint('show item on website apart from stock is is dispatchable or not');
		$item_j->addField('negative_qty_allowed')->type('number')->hint('allow the negative stock until this quantity');
		$item_j->addField('is_visible_sold')->type('boolean')->hint('display item on website after out of stock/all sold');
		
		$item_j->addField('is_servicable')->type('boolean');
		$item_j->addField('is_productionable')->type('boolean')->hint('used in Production');
		$item_j->addField('website_display')->type('boolean')->hint('Show on Website');
		$item_j->addField('is_downloadable')->type('boolean');
		$item_j->addField('is_rentable')->type('boolean');
		$item_j->addField('is_designable')->type('boolean')->hint('item become designable and customer customize the design');
		$item_j->addField('is_template')->type('boolean')->hint('blueprint/layout of designable item');
		$item_j->addField('is_attachment_allow')->type('boolean')->hint('by this option you can attach the item information pdf/doc etc. to be available on website');
		
		$item_j->addField('warranty_days')->type('int');
		
		//Item Display Options
		$item_j->addField('show_detail')->type('boolean');
		$item_j->addField('show_price')->type('boolean');

		//Marked
		$item_j->addField('is_new')->type('boolean')->caption('New');
		$item_j->addField('is_feature')->type('boolean')->caption('Featured');
		$item_j->addField('is_mostviewed')->type('boolean')->caption('Most Viewed');

		//Enquiry Send To
		$item_j->addField('is_enquiry_allow')->type('boolean')->hint('display enquiry form at item detail on website');
		$item_j->addField('enquiry_send_to_admin')->type('boolean')->hint('send a copy of enquiry form to admin');
		$item_j->addField('item_enquiry_auto_reply')->caption('Item Enquiry Auto Reply')->type('boolean');
		
		//Item Comment Options
		$item_j->addField('is_comment_allow')->type('boolean');
		$item_j->addField('comment_api')->setValueList(array('disqus'=>'Disqus'));

		//Item Other Options
		$item_j->addField('add_custom_button')->type('boolean');
		$item_j->addField('custom_button_label');
		$item_j->addField('custom_button_url')->placeHolder('subpage name like registration etc.');
		
		// Item WaterMark
		// $item_j->add('filestore/Field_Image','watermark_image_id');
		$item_j->addField('watermark_text')->type('text');
		$item_j->addField('watermark_position')->enum(array('TopLeft','TopRight','BottomLeft','BottomRight','Center','Left Diagonal','Right Diagonal'));
		$item_j->addField('watermark_opacity');
		
		//Item SEO
		$item_j->addField('meta_title');
		$item_j->addField('meta_description')->type('text');
		$item_j->addField('tags')->type('text')->PlaceHolder('Comma Separated Value');

		//Item Designs
		$item_j->addField('designs')->type('text')->hint('used for internal, design saved');

		//others
		$item_j->addField('terms_and_conditions')->type('text');
		$item_j->addField('duplicate_from_item_id')->hint('internal used saved its parent');

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
		});

		// $this->debug();

		$this->addExpression('total_sales')->set(function($m,$q){
		 	$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales']);
			$qsp_details->addExpression('document_type')->set($qsp_details->refSQL('qsp_master_id')->fieldQuery('type'));
			$qsp_details->addCondition('document_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			return $qsp_details->_dsql()->del('fields')->field($q->expr('SUM([0])',[$qsp_details->getElement('quantity')]));
		});
		 		 		 
	}

	function publish(){
		$this['status']='Published';
        $this->app->employee
            ->addActivity("UnPublish Item", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('publish','UnPublished');
        $this->saveAndUnload();
    }

    function unpublish(){
		$this['status']='UnPublished';
        $this->app->employee
            ->addActivity("Publish Item", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('unpublish','Published');
        $this->saveAndUnload();
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

	function associateCustomField(){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded");
			
		$asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
				->addCondition('item_id',$this->id)
				;

		$asso->addExpression('customfield_type')->set($asso->refSQL('customfield_generic_id')->fieldQuery('type'));
		$asso->addExpression('sequence_order')->set($asso->refSQL('customfield_generic_id')->fieldQuery('sequence_order'));
		$asso->addCondition('customfield_type','CustomField');
		$asso->setOrder('name','asc');
		$asso->setOrder('sequence_order','asc');
		$asso->tryLoadAny();
		
		return $asso;		
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

	function specification($specification=null){
		if(!$this->loaded())
			throw new \Exception("Model must loaded", 1);

		$specs_assos = $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('item_id',$this->id);
		$specs_assos->addExpression('value')->set(function($m,$q){
			return $m->refSQL('xepan\commerce\Item_CustomField_Value')->addCondition('status','Active')->setLimit(1)->fieldQuery('name');
		});

		
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
		return array('original_price'=>$quantitysets['old_price']?:$quantitysets['price'],'sale_price'=>$quantitysets['price'],'shipping_charge'=>$quantitysets['shipping_charge']);
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

		return array('original_amount'=>$price['original_price'] * $qty,'sale_amount'=>$price['sale_price'] * $qty,'shipping_charge'=>$price['shipping_charge']);

	}

	function applyTax(){
		
		return $this->ref('Tax')->tryLoadAny()->setLimit(1);
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

} 
 
	

