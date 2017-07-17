<?php
namespace xepan\commerce;

class page_storeconfig extends \xepan\commerce\page_configurationsidebar{
	public $title="Store Configuration";

	function init(){
		parent::init();

		$store_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'subtype'=>'text',
							],
					'config_key'=>'ADJUSTMENT_SUBTYPE',
					'application'=>'commerce'
			]);
		$store_config->tryLoadAny();

		$form = $this->add('Form');
		$form->setModel($store_config);
		$form->getElement('subtype');
		$form->add('View')->set('comma separated multiple values');

		$form->addSubmit('Save');

		if($form->isSubmitted()){
			$form->save();
			$form->js()->univ()->successMessage('saved successfully')->execute();
		}
		

	}
}