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


		parent::formatRow();
	}

}