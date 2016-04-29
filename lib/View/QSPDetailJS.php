<?php

namespace xepan\commerce;

class View_QSPDetailJS extends \View {
	function render(){
		$tax_id=$this->api->stickyGET('tax_id');
		if($tax_id){
			$tax = $this->add('xepan\commerce\Model_Taxation');
			$tax->load($tax_id);
			$js=[];
			
			// tax pecentage
			$js[] = $this->js()->_selector('.tax_percentage')->find('input')->val($tax['percentage']);
			
			$this->js(true,$js);
		}

		parent::render();
	}
}