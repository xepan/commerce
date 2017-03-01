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
		$data = [];
		$item = $this->add('xepan\commerce\Model_Item')
						->load($item_id);
		$preDefinedPhase = [];
		foreach ($item->getAssociatedDepartment() as $key => $value) {
			$preDefinedPhase[$value] = [];
		}

		$none_dept_cf = $item->noneDepartmentAssociateCustomFields($this->show_only_stock_effect_customField);

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
			if($this->show_only_stock_effect_customField){
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
	
	// save qsp
	function page_save(){

	}

}