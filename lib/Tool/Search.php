<?php
namespace xepan\commerce;

class Tool_Search extends \xepan\cms\View_Tool{
	public $option = [];
	function init(){
		parent::init();

		$form = $this->add('Form',null,null,['form/empty']);
		$form->addField('line','content');
	}
}