<?php


namespace xepan\commerce;

class page_store_activity_issue extends \xepan\base\Page{
	// public $title="Dispatch Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible()
			->addContentSpot()
			->layout([
				'warehouse~From Warehouse'=>"Issue Stock To Department/Employee~c1~3",
				'department~To Department'=>"c2~3",
				'employee~To Employee'=>"c3~3",
				'date'=>"c4~3",
				'item'=>"c5~4",
				'extra_info~'=>"c5~4",
				'extra_info_btn~&nbsp;'=>"c6~2",
				'quantity'=>'c7~2',
				'narration'=>'c8~4',
				'FormButtons~&nbsp;'=>"c9~3"
			]);

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
		
		$form->layout->add('Button',null,'extra_info_btn')->set('Extra-Info')->setClass('btn btn-warning extra-info');
		$form->addField('text','extra_info');
		$form->addField('Number','quantity');
		$form->addField('text','narration')->addClass('height-60');
		$form->addField('DatePicker','date')->Validate('required')->set($this->app->today);

		$form->addSubmit('Issue Now')->addClass('btn btn-primary');

		$this->add('View')->setElement('H2')->set("Stock Issue Record");
		$grid= $this->add('xepan\base\Grid');
		$issue_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Issue');
		$grid->setModel($issue_model,['item','quantity','transaction_narration']);
		$grid->addPaginator($ipp=25);
		$grid->addSno();

		if($form->isSubmitted()){
			if(!$form['department'] && !$form['employee'])
				$form->error('employee','please select either Department or Employee');

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Issue',$form['department'],$form['employee'],$form['narration'],null,'ToReceived',$form['date']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'ToReceived');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('stock issue successfully')->execute();
		}

	}
}