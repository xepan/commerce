<?php


namespace xepan\commerce;

class page_store_activity_issue extends \xepan\base\Page{
	// public $title="Dispatch Order Item";

	function init(){
		parent::init();

		// $department_id = $this->api->stickyGET('department_id')?:0;
		
		$form = $this->add('Form');
		
		$department_field = $form->addField('dropdown','department');
		$department_field->setModel('xepan\hr\Department');
		$department_field->setEmptyText('Please Select');

		$employee_field = $form->addField('xepan\base\DropDown','employee');
		$emp_model = $this->add('xepan\hr\Model_Employee');

		// if($department_id)
			// $emp_model->addCondition('department_id',$department_id);
		$employee_field->setModel($emp_model);
		$employee_field->setEmptyText('Please Select');

		$warehouse_field = $form->addField('dropdown','warehouse')->Validate('required');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field->setEmptyText("Please Select");

		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Store_Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('text','extra_info');
		$form->addField('Number','quantity');
		$form->addField('text','narration');
		$form->addSubmit('Save')->addClass('btn btn-primary');

		$this->add('View')->setElement('H2')->set("Stock Issue Record");
		$grid= $this->add('xepan\base\Grid');
		$issue_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Issue');
		$grid->setModel($issue_model,['item','quantity','transaction_narration']);
		$grid->addPaginator($ipp=30);

		if($form->isSubmitted()){
			if(!$form['department'] && !$form['employee'])
				$form->error('employee','please select either Department or Employee');

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Issue',$form['department'],$form['employee'],$form['narration']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'ToReceived');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}