<?php

namespace xepan\commerce;

class Grid_Quotation extends \xepan\base\Grid{

	function render(){
		if($_GET['action']!='view'){
			$this->js(true)->_load('xepan-QSIP')->univ()->calculateQSIP();
		}
		parent::render();
	}

}