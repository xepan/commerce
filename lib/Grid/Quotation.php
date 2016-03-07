<?php

namespace xepan\commerce;

class Grid_Quotation extends \xepan\base\Grid{

	function render(){
		
		$this->js()->_load('document-sum');
		$js=[
			$this->js()->univ()->columnsum('.sum-amount','.gross-amount')
		];
		$this->js(true,$js);
		parent::render();
	}

}