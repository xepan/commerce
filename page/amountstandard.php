<?php

namespace xepan\commerce; 

class page_amountstandard extends \xepan\commerce\page_configurationsidebar{
	public $title = "Amount Standard";
	
	function init(){
		parent::init();
		
		$round_amount = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'round_amount'=>'DropDown'
							],
					'config_key'=>'ROUNDING_STANDARD_FOR_AMOUNT',
					'application'=>'commerce'
			]);
		$round_amount->add('xepan\hr\Controller_ACL');
		$round_amount->tryLoadAny();		

		$form = $this->add('Form');
		$form->setModel($round_amount);

		$default_round_standard = $form->getElement('round_amount')->setValueList(['None'=>'None','Standard'=>'Standard','Up'=>'Up','Down'=>'Down'])->set($round_amount['round_amount']);
		$form->addSubmit('Save')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Rounding Amount Standard Updated')->execute();
		}
	}
}