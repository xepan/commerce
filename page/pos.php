<?php

namespace xepan\commerce;

class page_pos extends \Page{
	public $show_only_stock_effect_customField = false;

	function page_item(){
		$item = $this->add('xepan\commerce\Model_Item');
		$item->addCondition('status','Published');

		$acl = $item->add('xepan\hr\Controller_ACL');
		if($acl->isBranchRestricted() AND @$this->app->branch->id){
			$item->addCondition('branch_id',$this->app->branch->id);
		}
		
		if(isset($_GET['term'])){
			$term = htmlspecialchars($_GET['term']);
			$item->addCondition('name','like',"%".$term."%");
			$item->setLimit(20);
		}

		$item = $item->getRows();

		$data = [];
		// if(isset($_GET['term'])){
		foreach ($item as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['name']."::".$value['sku'];
			$temp['value'] = $value['name']."::".$value['sku'];
			$temp['price'] = $value['sale_price'];
			$temp['sku'] = $value['sku'];
			$temp['description'] = $value['description'];
			$temp['custom_field'] = '{}';
			$temp['read_only_custom_field'] = '{}';
			// $temp['read_only_custom_field'] = json_encode($this->getReadOnlyCustomField($value['id']));
			$temp['qty_unit_id'] = $value['qty_unit_id']?:0;
			$temp['qty_unit_group_id'] = $value['qty_unit_group_id']?:0;
			$temp['tax_id'] = 0;
			$temp['hsn_sac'] = $value['hsn_sac'];
			$temp['treat_sale_price_as_amount'] = $value['treat_sale_price_as_amount'];
			$temp['is_productionable'] = $value['is_productionable'];
			$temp['is_production_phases_fixed'] = $value['is_production_phases_fixed'];
			
			// $taxation = $value->applicableTaxation($_GET['country_id'],$_GET['state_id']);

			// if($taxation){
			// 	$temp['tax_id'] = $taxation['taxation_id'];
			// 	$temp['tax_percentage'] = $taxation['percentage'];
			// }
			$data[$key] = $temp;
		}

		echo json_encode($data);
		exit;
		// }
	}

	function page_contact(){

		$document_type = $_GET['document_type'];

		if(in_array($document_type, ['PurchaseOrder','PurchaseInvoice'])){
			$contact_model = $this->add('xepan\commerce\Model_Supplier');
		}elseif(in_array($document_type, ['SalesOrder','SalesInvoice'])){
			$contact_model = $this->add('xepan\commerce\Model_Customer');
		}else{
			$contact_model = $this->add('xepan\base\Model_Contact');
		}

		if(isset($_GET['term'])){
			$term = htmlspecialchars($_GET['term']);
			$contact_model->addCondition([['effective_name','like',"%".$term."%"],['user','like','%'.$term.'%']]);
		}

		$acl = $contact_model->add('xepan\hr\Controller_ACL');
		if($acl->isBranchRestricted() AND @$this->app->branch->id)
			$contact_model->addCondition("branch_id",$this->app->branch->id);

		$contact_model->setLimit(30);

		$data = [];
		foreach ($contact_model->getRows() as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['organization']." ".$value['first_name']." ".$value['last_name']." ".$value['user'];
			
			$temp['first_name'] = $value['first_name'];
			$temp['last_name'] = $value['last_name'];
			$temp['organization'] = $value['organization'];
			$temp['address'] = $value['address'];
			$temp['city'] = $value['city'];
			$temp['pin_code'] = $value['pin_code'];
			$temp['code'] = $value['code'];
			
			// if(in_array($document_type, ['SalesOrder','SalesInvoice'])){
				$temp['billing_country_id'] = $value['billing_country_id']?:$value['country_id'];
				$temp['billing_state_id'] = $value['billing_state_id']?:$value['state_id'];
				$temp['billing_name'] = $value['billing_name'];
				$temp['billing_address'] = $value['billing_address']?:$value['address'];
				$temp['billing_city'] = $value['billing_city']?:$value['city'];
				$temp['billing_pincode'] = $value['billing_pincode']?:$value['pin_code'];

				$temp['shipping_country_id'] = $value['shipping_country_id']?:$value['country_id'];
				$temp['shipping_state_id'] = $value['shipping_state_id']?:$value['state_id'];
				$temp['shipping_name'] = $value['shipping_name'];
				$temp['shipping_address'] = $value['shipping_address']?:$value['address'];
				$temp['shipping_city'] = $value['shipping_city']?:$value['city'];
				$temp['shipping_pincode'] = $value['shipping_pincode']?:$value['pin_code'];

				$temp['same_as_billing_address'] = $value['same_as_billing_address'];
			// }
			$data[$key] = $temp;
		}

		echo json_encode($data);
		exit;
	}

	// get item detail
	function page_itemcustomfield(){

		$item_id = $_GET['item_id'];

		$item_model = $this->add('xepan\commerce\Model_Item')->load($item_id);
		
		$data['cf'] = $this->getReadOnlyCustomField($item_id,$item_model);
		
		if($_GET['debug']){
			echo "<pre>";
			print_r($data);
			exit;
		}

		$data['id'] = $item_model['id'];
		$data['name'] = $item_model['name'];
		$data['value'] = $item_model['name'];
		$data['price'] = $item_model['sale_price'];
		$data['sku'] = $item_model['sku'];
		$data['description'] = $item_model['description'];
		$data['custom_field'] = '{}';
		$data['qty_unit_id'] = $item_model['qty_unit_id']?:0;
		$data['qty_unit_group_id'] = $item_model['qty_unit_group_id']?:0;
		$data['tax_id'] = 0;
		$data['hsn_sac'] = $item_model['hsn_sac'];
		$data['is_productionable'] = $item_model['is_productionable'];
		$data['is_production_phases_fixed'] = $item_model['is_production_phases_fixed'];
		
		$taxation = $item_model->applicableTaxation($_GET['country_id'],$_GET['state_id']);
		if($taxation instanceof \xepan\commerce\Model_TaxationRuleRow && $taxation->loaded()){
			$data['tax_id'] = $taxation['taxation_id'];
			$data['tax_percentage'] = $taxation['percentage'];
		}

		echo  json_encode($data);
		exit;
	}

	function page_getamount(){
		$item_id = $_GET['item_id'];
		$cf = json_decode($_GET['custom_field'],true);
		$qty = $_GET['qty'];

		$item_model = $this->add('xepan\commerce\Model_Item')->load($item_id);
		$price = $item_model->getPrice($cf,$qty);
		// $price_amount = $item_model->getAmount($cf,$qty);
		
		echo json_encode($price);
		// echo "amount".json_encode($price_amount);
		exit;
	}

	function page_shippingamount(){
		$item_id = $_GET['item_id'];
		$item_model = $this->add('xepan\commerce\Model_Item')->load($item_id);

		$data = $item_model->shippingCharge($_GET['sale_amout'],$_GET['qty'],null,$_GET['country_id'],$_GET['state_id']);
		echo  json_encode($data);
		exit;
	}

	// 	department wise Read Only Custom Fields
	// {
	// 	"0":{
	// 			"name":"nonedepartment",
	// 			"pre_selected":1,
	// 			"cf":[]
	// 		},
	// 	"21":{
	// 			"name":"Designing",
	// 			"2380":{
	// 					"custom_field_name":"Designing Sides",
	// 					'custom_field_value_id':,
	// 					'custom_field_value_name':,
						
	// 					"display_type":"DropDown",
	// 					"mandatory":false,
	// 					"value":{
	// 								"3176":"Front",
	// 								"3178":"Front Back"
	// 							}
	// 					},
	// 	}
	function getReadOnlyCustomField($item_id,$item_model=null){
		if(!$item_model){
			$item_model = $this->add('xepan\commerce\Model_Item')
				->load($item_id);
		}
		return $item_model->getReadOnlyCustomField($this->show_only_stock_effect_customField);
	}
	
	// save qsp
	function page_save(){
		$return = ['status'=>'failed','message'=>'failed'];
		
		$type = $_POST['qsp_type'];
		$qsp_data = json_decode($_POST['qsp_data'],true);
		
		$qsp_type = ['Quotation','SalesOrder','SalesInvoice','PurchaseOrder','PurchaseInvoice'];
		if(!in_array($type, $qsp_type)){
			$return['message'] = "type not defined";
			echo json_encode($return);
			exit;
		}

		$taxation_list = $this->add('xepan\commerce\Model_Taxation')->getRows();

		$master_data = $qsp_data['master'];
		$detail_data = $qsp_data['detail'];		
		$document_other_info = $qsp_data['document_other_info'];		

		try{
			$this->api->db->beginTransaction();

			$this->app->qsp_saving_from_pos = true; // used for bypassing the double calling of function update transaction at after save
			$qsp_master = $this->add('xepan\commerce\Model_QSP_Master');
			$master_model = $qsp_master->createQSPMaster($master_data,$type);
			// details
			// get all qsp_detail id array
			$detail_id_array = $master_model->getDetailIds();
			$detail_id_array = array_flip($detail_id_array);

			foreach($detail_data as $key => $row) {
				if(!$row['item_id']) continue;

				$qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail');
				$qsp_detail->addCondition('qsp_master_id',$master_model->id);
				if($row['qsp-detail-id']){
					$qsp_detail->addCondition('id',$row['qsp-detail-id']);
					$qsp_detail->tryLoadAny();
					if(isset($detail_id_array[$row['qsp-detail-id']])){
						unset($detail_id_array[$row['qsp-detail-id']]);
					}
				}

				$qsp_detail['item_id'] = $row['item_id'];
				$qsp_detail['price'] = $row['price'];
				$qsp_detail['quantity'] = $row['quantity'];
				
				$qsp_detail['taxation_id'] = $row['taxation_id'];
				$tax_percentage = 0;
				foreach ($taxation_list as $key => $tax) {
					if($tax['id'] == $row['taxation_id']){
						$tax_percentage = $tax['percentage'];
						break;
					}
				}
				$qsp_detail['tax_percentage'] = $tax_percentage;
				$qsp_detail['narration'] = $row['narration'];
				$qsp_detail['extra_info'] = $row['extra_info'];
				$qsp_detail['shipping_charge'] = $row['shipping_charge'];
				$qsp_detail['shipping_duration'] = $row['shipping_duration'];
				$qsp_detail['express_shipping_charge'] = $row['express_shipping_charge'];
				$qsp_detail['express_shipping_duration'] = $row['express_shipping_duration'];
				$qsp_detail['qty_unit_id'] = $row['qty_unit_id'];
				$qsp_detail['discount'] = $row['discount']?:0;
				$qsp_detail['treat_sale_price_as_amount'] = $row['treat_sale_price_as_amount']?:0;
				$qsp_detail->save();
			}

			if(count($detail_id_array)){
				foreach ($detail_id_array as $key => $row) {
					$this->add('xepan\commerce\Model_QSP_Detail')->load($key)->delete();
				}
			}

			// if document type is salesinvoice then update transaction
			if($type == "SalesInvoice" || $type == "PurchaseInvoice"){
				$master_model->reload();
				$master_model->updateTransaction();
			}

			// update document other info
			foreach ($document_other_info as $field_name => $value) {
				$existing = $this->add('xepan\base\Model_Document_Other')
					->addCondition('document_id',$master_model->id)
					->addCondition('head',$field_name)
					->tryLoadAny();
				$existing['value'] = $value;
				$existing->save();
			}
			
			$this->api->db->commit();

		}catch(\Exception $e){
			$this->api->db->rollback();
			
			$return['status'] = "failed";
			$return['message'] = $e->getMessage();
			echo json_encode($return);
			exit;
		}

		$return['status'] = "success";
		$return['message'] = "saved";
		$return['master_data'] = $master_model->get();
		echo json_encode($return);
		exit;
	}

}