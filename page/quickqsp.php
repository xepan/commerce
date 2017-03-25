<?php

namespace xepan\commerce;

class page_quickqsp extends \Page{
	public $title = "xEpan POS";

	function init(){
		parent::init();
		
		//load saved design and pass it to widget
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
		}

		// echo "<pre>";
		// print_r($qsp_data);
		// echo "</pre>";
		$all_tax = $this->add('xepan\commerce\Model_Taxation')->getRows();
		$taxation = [];
		foreach ($all_tax as $tax) {
			$taxation[$tax['id']] = [
									'name'=>$tax['name'],
									'percentage'=>$tax['percentage']
								];
		}

		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('pos')->xepan_pos([
								'show_custom_fields'=>true,
								'qsp'=>json_encode($qsp_data),
								'taxation'=>json_encode($taxation)
							]);

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