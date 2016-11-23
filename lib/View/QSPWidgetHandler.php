<?php

namespace xepan\commerce;

class View_QSPWidgetHandler extends \xepan\base\Widget{
	public $heading;

	function init(){
		parent::init();
	}

	function setModel($model){
		$counts = $model->_dsql()->del('fields')->field('status')->field('count(*) counts')->group('Status')->get();
		$counts_redefined =[];
		$total=0;
		foreach ($counts as $cnt) {
			$counts_redefined[$cnt['status']] = $cnt['counts'];
			$total += $cnt['counts'];
		}

		$this->setTitle($counts_redefined);
		$this->setValue($counts_redefined);
	}

	function recursiveRender(){
		$this->setHeading($this->heading);
		return parent::recursiveRender();
	}

	function setHeading($heading){
		$this->template->trySet('heading',$heading);
	}

	function setTitle($array){
		$title = array_keys($array);
		$count = 1; 
		foreach ($title as $key => $val) {
			$this->template->trySet('title'.$count,$val.' : ');
			$count+=1;
		}
	}

	function setValue($array){
		$count = 1; 
		foreach ($array as $key => $val) {
			$this->template->trySet('val'.$count,$val);
			$count+=1;
		}
	}
	
	function defaultTemplate(){
		return ['view\dashboard\bigbox'];
	}
}