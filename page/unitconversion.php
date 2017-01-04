<?php 
 namespace xepan\commerce;
 class page_unitconversion extends \xepan\base\Page{

	public $title='Unit Conversion';

	function init(){
		parent::init();

		$model_unit_conversion = $this->add('xepan\commerce\Model_UnitConversion');

		$form = $this->add('Form');
		$form->setModel($model_unit_conversion);

		if($_GET['to_become_id']){
			$form->getElement('to_become_id')->set($_GET['to_become_id']);
		}
		if($_GET['one_of_id']){
			$form->getElement('one_of_id')->set($_GET['one_of_id']);
		}

		$form->addSubmit();
		if($form->isSubmitted()){
			$form->save();
			$form->js()->univ()->closeDialog()->execute();
		}

	}
} 