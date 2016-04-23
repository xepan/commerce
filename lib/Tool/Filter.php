<?php

namespace xepan\commerce;

class Tool_Filter extends \xepan\cms\View_Tool{
	function init(){
		parent::init();

		$model_specification = $this->add('xepan\commerce\Model_Item_CustomField_Generic');
		$model_specification->addCondition('is_filterable',true);
		
		
						
		$form = $this->add('Form');
		foreach ($model_specification as $specification) {
			$form->addField('checkbox',$specification['name']);			
		}		
	}
}