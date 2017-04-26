<?php

namespace xepan\commerce;

class page_pos extends \Page{
	public $show_only_stock_effect_customField = false;

	function page_item(){
		$item = $this->add('xepan\commerce\Model_Item');
		$item->addCondition('status','Published');
		
		if(isset($_GET['term'])){
			$term = htmlspecialchars($_GET['term']);
			$item->addCondition('name','like',"%".$term."%");
		}

		$item->setLimit(20);

		$data = [];
		// if(isset($_GET['term'])){
		foreach ($item as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['name'];
			$temp['value'] = $value['name'];
			$temp['price'] = $value['sale_price'];
			$temp['sku'] = $value['sku'];
			$temp['description'] = $value['description'];
			$temp['custom_field'] = '{}';
			$temp['read_only_custom_field'] = json_encode($this->getReadOnlyCustomField($value['id']));
			$temp['qty_unit_id'] = $value['qty_unit_id']?:0;
			$temp['qty_unit_group_id'] = $value['qty_unit_group_id']?:0;
			
			$taxation = $value->applicableTaxation($_GET['country_id'],$_GET['state_id']);

			$temp['tax_id'] = 0;
			if($taxation){
				$temp['tax_id'] = $taxation['taxation_id'];
				$temp['tax_percentage'] = $taxation['percentage'];
			}
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
		}elseif(in_array($document_type, ['SaleOrder','SaleInvoice'])){
			$contact_model = $this->add('xepan\commerce\Model_Customer');
		}else{
			$contact_model = $this->add('xepan\base\Model_Contact');
		}

		if(isset($_GET['term'])){
			$term = htmlspecialchars($_GET['term']);
			$contact_model->addCondition('organization','like',"%".$term."%");
		}

		$contact_model->setLimit(20);

		$data = [];
		foreach ($contact_model->getRows() as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['organization']." ".$value['first_name']." ".$value['last_name'];
			
			$temp['first_name'] = $value['first_name'];
			$temp['last_name'] = $value['last_name'];
			$temp['organization'] = $value['organization'];
			$temp['address'] = $value['address'];
			$temp['city'] = $value['city'];
			$temp['pin_code'] = $value['pin_code'];
			$temp['code'] = $value['code'];
			
			if(in_array($document_type, ['SaleOrder','SaleInvoice'])){
				$temp['billing_country_id'] = $value['billing_country_id'];
				$temp['billing_state_id'] = $value['billing_state_id'];
				$temp['billing_name'] = $value['billing_name'];
				$temp['billing_address'] = $value['billing_address'];
				$temp['billing_city'] = $value['billing_city'];
				$temp['billing_pincode'] = $value['billing_pincode'];

				$temp['shipping_country_id'] = $value['shipping_country_id'];
				$temp['shipping_state_id'] = $value['shipping_state_id'];
				$temp['shipping_name'] = $value['shipping_name'];
				$temp['shipping_address'] = $value['shipping_address'];
				$temp['shipping_city'] = $value['shipping_city'];
				$temp['shipping_pincode'] = $value['shipping_pincode'];

				$temp['same_as_billing_address'] = $value['same_as_billing_address'];
			}
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

		// $applicable_tax = $item_model->applicableTaxation();
		// $data['tax_id'] = 0;
		// if($applicable_tax instanceof \xepan\commerce\Model_TaxationRuleRow && $applicable_tax->loaded()){
		// 	$data['tax_id'] = $applicable_tax['taxation_id'];
		// }

		echo  json_encode($data);
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
		
		$qsp_type = ['Quotation','SaleOrder','SaleInvoice','PurchaseOrder','PurchaseInvoice'];
		if(!in_array($type, $qsp_type)){
			$return['message'] = "type not defined";
			echo json_encode($return);
			exit;
		}

		// echo "<pre>";
		// print_r($qsp_data);
		// echo "</pre>";
		// exit;
		$master_data = $qsp_data['master'];
		$detail_data = $qsp_data['detail'];

		if($master_data['tnc_id'])
			$tnc_model = $this->add('xepan\commerce\Model_TNC')->load($master_data['tnc_id']);

		$master_model = $this->add('xepan\commerce\Model_'.$type);
		$master_model->addCondition('document_no',$master_data['qsp_no']);
		$master_model->tryLoadAny();

		$master_model['contact_id'] = $master_data['contact_id'];
		$master_model['currency_id'] = $master_data['currency_id'];
		$master_model['nominal_id'] = $master_data['nominal_id'];

		$master_model['billing_country_id'] = $master_data['billing_country_id'];
		$master_model['billing_state_id'] = $master_data['billing_state_id'];
		$master_model['billing_name'] = $master_data['billing_name']?:'not defined';
		$master_model['billing_address'] = $master_data['billing_address']?:'not defined';;
		$master_model['billing_city'] = $master_data['billing_city']?:'not defined';
		$master_model['billing_pincode'] = $master_data['billing_pincode']?:'not defined';

		$master_model['shipping_country_id'] = $master_data['shipping_country_id'];
		$master_model['shipping_state_id'] = $master_data['shipping_state_id'];		
		$master_model['shipping_name'] = $master_data['shipping_name']?:'not defined';
		$master_model['shipping_address'] = $master_data['shipping_address']?:'not defined';
		$master_model['shipping_city'] = $master_data['shipping_city']?:'not defined';
		$master_model['shipping_pincode'] = $master_data['shipping_pincode']?:'not defined';

		$master_model['is_shipping_inclusive_tax'] = $master_data['is_shipping_inclusive_tax'];
		$master_model['is_express_shipping'] = $master_data['is_express_shipping'];

		$master_model['due_date'] = $master_data['due_date'];
		$master_model['narration'] = $master_data['narration'];
		
		$master_model['round_amount'] = $master_data['round_amount'];
		$master_model['discount_amount'] = $master_data['discount_amount'];
		$master_model['exchange_rate'] = $master_data['exchange_rate'];
		
		$master_model['tnc_id'] = $master_data['tnc_id'];
		if($master_data['tnc_id'])
			$master_model['tnc_text'] = $tnc_model['content'];
		$master_model->save();

		// details
		// get all qsp_detail id array
		$detail_id_array = $master_model->getDetailIds();

		foreach($detail_data as $key => $row) {
			if(!$row['item_id']) continue;

			$qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail');
			$qsp_detail->addCondition('qsp_master_id',$master_model->id);
			if($row['qsp-detail-id']){
				$qsp_detail->addCondition('id',$row['qsp-detail-id']);
				if(in_array($row['item-qsp-detail-id'], $detail_id_array))
					unset($detail_id_array[$row['qsp-detail-id']]);
			}

			$qsp_detail['item_id'] = $row['item_id'];
			$qsp_detail['taxation_id'] = $row['taxation_id'];
			$qsp_detail['qty_unit_id'] = $row['qty_unit_id'];
			$qsp_detail['price'] = $row['price'];
			$qsp_detail['quantity'] = $row['quantity'];
			$qsp_detail['discount'] = $row['discount'];
			$qsp_detail['shipping_charge'] = $row['shipping_charge'];
			$qsp_detail['shipping_duration'] = $row['shipping_duration'];
			$qsp_detail['express_shipping_charge'] = $row['express_shipping_charge'];
			$qsp_detail['express_shipping_duration'] = $row['express_shipping_duration'];
			$qsp_detail['tax_percentage'] = $row['tax_percentage'];
			$qsp_detail['narration'] = $row['narration'];
			$qsp_detail['extra_info'] = $row['extra_info'];
			$qsp_detail->save();
		}

		if(count($detail_id_array)){
			foreach ($detail_id_array as $key => $row) {
				$this->add('xepan\commerce\Model_QSP_Detail')->load($row['qsp-detail-id'])->delete();
			}
		}

		$return['status'] = "success";
		$return['message'] = "saved";

		echo json_encode($return);
		exit;
	}

}