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
		foreach ($item->getRows() as $key => $value){
			$temp = [];
			$temp['id'] = $value['id'];
			$temp['name'] = $value['name'];
			$temp['value'] = $value['name'];
			$temp['price'] = $value['sale_price'];
			$temp['sku'] = $value['sku'];
			$temp['description'] = $value['description'];
			$temp['custom_field'] = '{}';
			$temp['read_only_custom_field'] = json_encode($this->getReadOnlyCustomField($value['id']));
			$data[$key] = $temp;
		}

		echo json_encode($data);
		exit;
		// }
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

		$applicable_tax = $item_model->applicableTaxation();
		$data['tax_id'] = 0;
		if($applicable_tax instanceof \xepan\commerce\Model_TaxationRuleRow && $applicable_tax->loaded()){
			$data['tax_id'] = $applicable_tax['taxation_id'];
		}

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
		$qsp_data = json_decode($_POST['qsp_data']);
		
		$qsp_type = ['Quotation','SaleOrder','SaleInvoice','PurchaseOrder','PurchaseInvoice'];
		if(in_array($type, $qsp_type)){
			$return['message'] = "type not defined";
			echo json_encode($return);
			exit;
		}

		$master_data = $qsp_data['master'];
		$detail_data = $qsp_data['detail'];

		$tnc_model = $this->add('xepan\commerce\Model_TNC')->load($master_data['tnc_id']);

		$master_model = $this->add('xepan\commerce\Model_'.$type);
		$master_model->addCondition('document_no',$master_data['qsp_no']);
		$master_model->tryLoadAny();

		$master_model['contact_id'] = $master_data['contact_id'];
		$master_model['currency_id'] = $master_data['currency_id'];
		$master_model['nominal_id'] = $master_data['nominal_id']

		$master_model['billing_country_id'] = $master_data['billing_country_id'];
		$master_model['billing_state_id'] = $master_data['billing_state_id'];
		$master_model['billing_name'] = $master_data['billing_name'];
		$master_model['billing_address'] = $master_data['billing_address'];
		$master_model['billing_city'] = $master_data['billing_city'];
		$master_model['billing_pincode'] = $master_data['billing_pincode'];

		$master_model['shipping_country_id'] = $master_data['shipping_country_id'];
		$master_model['shipping_state_id'] = $master_data['shipping_state_id'];		
		$master_model['shipping_name'] = $master_data['shipping_name'];
		$master_model['shipping_address'] = $master_data['shipping_address'];
		$master_model['shipping_city'] = $master_data['shipping_city'];
		$master_model['shipping_pincode'] = $master_data['shipping_pincode'];

		$master_model['is_shipping_inclusive_tax'] = $master_data['is_shipping_inclusive_tax'];
		$master_model['is_express_shipping'] = $master_data['is_express_shipping'];
		$master_model['due_date'] = $master_data['due_date'];
		$master_model['narration'] = $master_data['narration'];
		
		$master_model['round_amount'] = $master_data['round_amount'];
		$master_model['discount_amount'] = $master_data['discount_amount'];
		$master_model['exchange_rate'] = $master_data['exchange_rate'];
		
		$master_model['tnc_id'] = $master_data['tnc_id'];
		$master_model['tnc_text'] = $tnc_model['content'];
		$master_model->save();

		// details
		// get all qsp_detail id array
		$detail_id_array = $master_model->getDetailIds();
		
		$qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail');
		$qsp_detail->addCondition('qsp_master_id',$master_model->id);

		echo json_encode($return);
		exit;
	}

}