<?php


namespace xepan\commerce;

class page_store_activity_packageitem extends \xepan\base\Page{
	// public $title="Dispatch Order Item";

	function init(){
		parent::init();

		// $department_id = $this->api->stickyGET('department_id')?:0;
		
		$form = $this->add('Form');
		
		$item_field = $form->addField('dropdown','package_item');
		$item_field->setModel('xepan\commerce\Item')->addCondition('is_package',true);

		$form->addField('line','no_of_package')->addClass('xepan-push-large');
		
		$open_package_btn = $form->addSubmit('Open Package')->addClass('btn btn-primary');
		$close_package_btn = $form->addSubmit('close Package')->addClass('btn btn-danger');

		if($form->isSubmitted()){
			if($form->isClicked($open_package_btn)){}
			if($form->isClicked($close_package_btn)){}

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Issue',$form['department'],$form['employee'],$form['narration']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'ToReceived');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}