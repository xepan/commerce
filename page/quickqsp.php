<?php

namespace xepan\commerce;

class page_quickqsp extends \Page{
	public $title = "xEpan POS";
	public $document_type = "Quotation";
	function init(){
		parent::init();
		
		//load saved design and pass it to widget
		$this->template->trySet('document_type',$this->document_type);
		
		$qsp_data=[];
		if($_GET['document_id']){
			$document = $this->add('xepan\commerce\Model_QSP_Master');
			$document->addCondition('id',$_GET['document_id']);
			$document->tryLoadAny();

			if($document->loaded()){
				$master_data = $document->getRows();
				unset(
						$master_data[0]['sub_type'],
						$master_data[0]['search_string']
					);

				$qsp_data = $master_data[0];
				$qsp_data['created_at'] = date('Y-m-d',strtotime($qsp_data['created_at']));
				$qsp_data['due_date'] = $qsp_data['due_date']?(date('Y-m-d',strtotime($qsp_data['due_date']))):"";
				// get all qsp_detail
				$qsp_details_model = $this->add('xepan\commerce\Model_QSP_Detail')
						->addCondition('qsp_master_id',$document->id);
				$detail_data = $qsp_details_model->getRows();

				// add read_only_custom_field_values
				foreach ($detail_data as $key => &$qsp_item) {
					$item = $this->add('xepan\commerce\Model_Item')->load($qsp_item['item_id']);
					$item_read_only_cf = $item->getReadOnlyCustomField();

					// merge QSP_DETAIL into ITEM_READ_ONLY_CF
					$updated_cf = $this->updateReadOnlyDeptCF($item_read_only_cf,$qsp_item['extra_info']);
					$qsp_item['read_only_custom_field_values'] = $updated_cf;
				}

				$qsp_data['details'] = $detail_data;

			}
		}else{
			// set data of guest customer or default value
			$qsp_data['document_no'] = $document = $this->add('xepan\commerce\Model_QSP_Master')->newNumber();
		}


		$all_tax = $this->add('xepan\commerce\Model_Taxation')->getRows();
		$taxation = [];
		foreach ($all_tax as $tax) {
			$taxation[$tax['id']] = [
									'name'=>$tax['name'],
									'percentage'=>$tax['percentage']
								];
		}

		// country
		$country = $this->add('xepan\base\Model_Country');
		$country->addCondition('status','Active');
		$country_list = $country->getRows();

		// state
		$state = $this->add('xepan\base\Model_State');
		$state->addCondition('status','Active');
		$state_list = $state->getRows();

		// tnc list
		$tnc_list = $this->add('xepan\commerce\Model_TNC')->getRows();

		// currency list
		$currency_list = $this->add('xepan\accounts\Model_Currency')->addCondition('status','Active')->getRows();
		// nominal list
		$nominal_list = $this->add('xepan\accounts\Model_Ledger')->getRows();
		$unit_list = $this->add('xepan\commerce\Model_Unit')->getRows();

		// round amount standard
		$round_amount_standard = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'round_amount_standard'=>'DropDown'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$round_amount_standard->tryLoadAny();
		
		// echo "<pre>";
		// print_r($tnc_list);
		// echo "</pre>";
		// exit;
		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('pos')->xepan_pos([
								'show_custom_fields'=>true,
								'qsp'=>$qsp_data,
								'taxation'=>$taxation,
								'document_type'=>$this->document_type,
								'country'=>$country_list,
								'state'=>$state_list,
								'tnc'=>$tnc_list,
								'currency'=>$currency_list,
								'nominal'=>$nominal_list,
								'unit_list'=>$unit_list,
								'round_standard'=>$round_amount_standard['round_amount_standard']
							]);

		$this->js(true)->_selector('#page-wrapper')->addClass('container nav-small');
	}

	function updateReadOnlyDeptCF($read_only_cf_array,$qsp_extra_info_json){
		
		$qsp_extra_info_array = json_decode($qsp_extra_info_json,true);
		
		foreach ($read_only_cf_array as $dept_id => &$dept_cf_detail) {
			// first remove pre_selected department
			$dept_cf_detail['pre_selected'] = 0;
			if(isset($qsp_extra_info_array[$dept_id])){
				$dept_cf_detail['pre_selected'] = 1;
				
				foreach ($dept_cf_detail as $cf_id => &$cf_array) {
					if(in_array($cf_id, ['department_name','pre_selected','production_level']) OR !is_int($cf_id)) continue;
					
					if(isset($qsp_extra_info_array[$dept_id][$cf_id])){
						$qsp_cf_detail = $qsp_extra_info_array[$dept_id][$cf_id];

						$cf_array['custom_field_name'] = $qsp_cf_detail['custom_field_name'];
						$cf_array['custom_field_value_id'] = $qsp_cf_detail['custom_field_value_id'];
						$cf_array['custom_field_value_name'] = $qsp_cf_detail['custom_field_value_name'];											
					}
				}
			}
		}

		return $read_only_cf_array;
	}

	function defaultTemplate(){
		return ['page\pos'];
	}

}