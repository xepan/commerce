<?php

namespace xepan\commerce;

class Grid_QSP extends \xepan\base\Grid{


	function render(){
		if($_GET['action']!='view'){
			$this->js(true)->_load('xepan-QSIP')->univ()->calculateQSIP();
		}
		parent::render();
	}

	function formatRow(){

		$array = json_decode($this->model['extra_info']?:"[]",true);

		// echo "<pre>";
		// var_dump($array);
		// exit;
		$cf_html = " "; 

		foreach ($array as $department_id => &$details) {
			$department_name = $details['department_name'];
			$cf_list = $this->add('CompleteLister',null,'extra_info',['view\qsp\extrainfo']);
			$cf_list->template->trySet('department_name',$department_name);
			unset($details['department_name']);
			
			$cf_list->setSource($details);

			$cf_html  .= $cf_list->getHtml();	
		}


		$this->current_row_html['extra_info'] = $cf_html . $this->model['narration'];
		
		$export_design = "";
		$design = $this->add('xepan\commerce\Model_Item_Template_Design')
					->addCondition('item_id',$this->model['item_id'])
					->addCondition('contact_id',$this->model['customer_id'])
					;
		$design->tryLoadAny();
		if($design->loaded()){
			$url =  $this->api->url('xepan_commerce_designer_pdf',array('item_id'=>"not-defined",'item_member_design_id'=>$design->id,'xsnb_design_template'=>false,'print_ratio'=>10,'cut_page'=>0));
			$url = str_replace("admin/", "", $url);
			$export_design = '<a class="btn btn-primary" href="'.$url.'" target="_blank">Export Design</a>';
		}

		$this->current_row_html['export_design'] = $export_design;

		
		parent::formatRow();
	}

}