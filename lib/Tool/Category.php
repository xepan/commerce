<?php
namespace xepan\commerce;

class Tool_Category extends \xepan\cms\View_Tool{
	public $options = [
		'url_page' =>'index'
	];

	function init(){
		parent::init();

		$lister = $this->add('xepan\commerce\View_CategoryLister',['options'=>$this->options]);
	}
}