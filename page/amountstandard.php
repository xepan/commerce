<?php

namespace xepan\commerce; 

class page_amountstandard extends \xepan\base\Page{
	public $title = "Amount Standard";
	
	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$form->addField('DropDown','round_amount')->setValueList(['None'=>'None','Standard'=>'Standard','Up'=>'Up','Down'=>'Down'])->set($this->app->epan->config->getConfig('AMOUNT_ROUNDING_STANDARD'));
		$form->addSubmit('Save');
		
		if($form->isSubmitted()){
			$this->app->epan->config->setConfig('AMOUNT_ROUNDING_STANDARD',$form['round_amount'],'commerce');
			return $form->js()->univ()->successMessage('Saved')->execute();
		}
	}
}