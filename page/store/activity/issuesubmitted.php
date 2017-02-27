<?php


namespace xepan\commerce;

class page_store_activity_issuesubmitted extends \xepan\base\Page{
	// public $title="Dispatch Order Item";

	function init(){
		parent::init();

		// $department_id = $this->api->stickyGET('department_id')?:0;
		
		$form = $this->add('Form');
		
		$department_field = $form->addField('dropdown','department');
		$department_field->setModel('xepan\hr\Department');

		$employee_field = $form->addField('xepan\base\DropDown','employee');
		$emp_model = $this->add('xepan\hr\Model_Employee');

		$employee_field->setModel($emp_model);
		
		$warehouse_field = $form->addField('dropdown','warehouse');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('text','extra_info');
		$form->addField('Number','quantity');
		$form->addField('text','narration');

		$form->addSubmit('Save')->addClass('btn btn-primary');
		
		$this->add('View')->setElement('H2')->set("Stock Issue Submitted Record");
		$grid= $this->add('xepan\base\Grid');
		$issue_submitted_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Movement');
		$grid->setModel($issue_submitted_model,['item','quantity','narration']);
		$grid->addPaginator($ipp=30);

		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['employee'],'Issue_Submitted',$form['department'],$form['warehouse'],$form['narration']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Received');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}