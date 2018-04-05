<?php
namespace xepan\commerce;

class page_storeconfig extends \xepan\commerce\page_configurationsidebar{
	public $title="Store Configuration";

	function init(){
		parent::init();

		$tab = $this->add('Tabs');
		$tab1 = $tab->addTab('Adjustment Subtype');
		$tab2 = $tab->addTab('Dispatch');
		$layout_tab = $tab->addTab('Issue Layout');

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

		// layout format
		$m = $layout_tab->add('xepan\commerce\Model_Store_Transaction');
		$master_hint = "";
		foreach ($m->getActualFields() as $key => $fields) {
			$master_hint .= '{$'.$fields.'},';
		}

		$m = $layout_tab->add('xepan\commerce\Model_Store_TransactionRow');
		$detail_hint = "";
		foreach ($m->getActualFields() as $key => $fields) {
			$detail_hint .= '{$'.$fields.'},';
		}

		$issue_layout_m = $layout_tab->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'STORE_ISSUE_LAYOUT',
					'application'=>'commerce'
			]);
		$issue_layout_m->add('xepan\hr\Controller_ACL');
		$issue_layout_m->tryLoadAny();

		$form_issue_layout = $layout_tab->add('Form');
		$form_issue_layout->setModel($issue_layout_m);

		$form_issue_layout->getElement('master')->setFieldHint($master_hint);
		$form_issue_layout->getElement('detail')->setFieldHint($detail_hint);

		$save = $form_issue_layout->addSubmit('Save')->addClass('btn btn-primary')->setStyle('margin-right','20px');
		$reset = $form_issue_layout->addSubmit('Reset Default Layout')->addClass('btn btn-danger');


		if($form_issue_layout->isSubmitted()){
			if($form_issue_layout->isClicked($save)){
				$form_issue_layout->save();

				$issue_layout_m->app->employee
					->addActivity("Issue Printable Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_storeconfig")
					->notifyWhoCan(' ',' ',$issue_layout_m);
				return $form_issue_layout->js()->univ()->successMessage('Layout Saved successfully')->execute();
			}

			if($form_issue_layout->isClicked($reset)){
				$master = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/default-issue-master.html"));
				$detail = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/default-issue-detail.html"));
				
				$issue_layout_m['master'] = $master;
				$issue_layout_m['detail'] = $detail;
				$issue_layout_m->save();
				$issue_layout_m->app->employee
			    	->addActivity("Issue Printable Layout Default Reset", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_storeconfig")
					->notifyWhoCan(' ',' ',$issue_layout_m);
				return $form_issue_layout->js(null,$form_issue_layout->js()->reload())->univ()->successMessage('Layout Updated successfully')->execute();
			}	
		}
	}
}