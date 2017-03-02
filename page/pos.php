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

		$data = $this->getReadOnlyCustomField($item_id);
		
		if($_GET['debug']){
			echo "<pre>";
			print_r($data);
			exit;
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
	function getReadOnlyCustomField($item_id){
		return $this->add('xepan\commerce\Model_Item')
				->load($item_id)
				->getReadOnlyCustomField($this->show_only_stock_effect_customField);
	}
	
	// save qsp
	function page_save(){

	}

}