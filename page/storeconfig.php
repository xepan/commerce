<?php
namespace xepan\commerce;

class page_storeconfig extends \xepan\commerce\page_configurationsidebar{
	public $title="Store Configuration";

	function init(){
		parent::init();

		$tab = $this->add('Tabs');
		$tab1 = $tab->addTab('Adjustment Subtype');
		$tab2 = $tab->addTab('Dispatch');

		$store_config = $tab1->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'subtype'=>'text',
							],
					'config_key'=>'ADJUSTMENT_SUBTYPE',
					'application'=>'commerce'
			]);
		$store_config->tryLoadAny();

		$form = $tab1->add('Form');
		$form->setModel($store_config);
		$form->getElement('subtype');
		$form->add('View')->set('comma separated multiple values');

		$form->addSubmit('Save');

		if($form->isSubmitted()){
			$form->save();
			$form->js()->univ()->successMessage('saved successfully')->execute();
		}
		
		// Dispatch Subtype
		$dispatch_config = $tab2->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'disable_partial_dispatch'=>'checkbox',
						],
					'config_key'=>'PARTIAL_DISPATCH',
					'application'=>'commerce'
			]);
		$dispatch_config->tryLoadAny();
		$form = $tab2->add('Form');
		$form->setModel($dispatch_config);
		$form->addSubmit('save');
		if($form->isSubmitted()){
			$form->save();
			$form->js()->univ()->successMessage('Dispatch Config saved')->execute();
		}

	}
}