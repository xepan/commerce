<?php
namespace xepan\commerce;

class page_store_transaction extends \xepan\base\Page{
	public $title="Store Transaction";
	function init(){
		parent::init();

		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$activity_type = $this->app->stickyGET('activity_type');
		$branch = $this->app->stickyGET('branch');
		$from_warehouse = $this->app->stickyGET('from_warehouse');
		$to_warehouse = $this->app->stickyGET('to_warehouse');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
					'from_date'=>'Filter~c1~2',
					'to_date'=>'c2~2',
					'activity_type'=>'c3~2',
					'branch'=>'c4~2',
					'from_warehouse'=>'c5~2',
					'to_warehouse'=>'c6~2',
					'FormButtons~&nbsp;'=>'b1~4'
				]);

		$form->addField('DatePicker','from_date')->set($from_date);
		$form->addField('DatePicker','to_date')->set($to_date);
		$type_field = $form->addField('DropDown','activity_type');
		$type_field->setValueList([
				'Opening'=>'Opening',
				'Adjustment_Add'=>'Adjustment Add',
				'Adjustment_Removed'=>'Adjustment Removed',
				'Issue'=>'Issue',
				'Issue_Submitted'=>'Issue Submitted',
				'Movement'=>'Movement',
				'PackageCreated'=>'PackageCreated',
				'ConsumedInPackage'=>'ConsumedInPackage',
				'PackageOpened'=>'PackageOpened',
				'ReleaseFromPackage'=>'ReleaseFromPackage',
				'Consumption_Booked'=>'Consumption_Booked'
			]);
		$type_field->setEmptyText('Please Select');
		$type_field->set($activity_type);

		$branch_field = $form->addField('DropDown','branch');
		$branch_field->setModel('xepan\base\Branch');
		$branch_field->setEmptyText('Please Select');
		$branch_field->set($branch);

		$from_warehouse_field = $form->addField('DropDown','from_warehouse');
		$from_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$from_warehouse_field->setEmptyText('Please Select');
		$from_warehouse_field->set($from_warehouse);

		$to_warehouse_field = $form->addField('DropDown','to_warehouse');
		$to_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$to_warehouse_field->setEmptyText('Please Select');
		$to_warehouse_field->set($to_warehouse);

		$form->addSubmit('Filter');

		$transaction = $this->add('xepan\commerce\Model_Store_Transaction');
		$transaction->addCondition([['jobcard_id',null],['jobcard_id',0]]);
		$transaction->setOrder('id','desc');
		$transaction->actions = [
				'ToReceived'=>['view','details','delete'],
				'Received'=>['view','details','delete']
			];

		if($branch)
			$transaction->addCondition('branch_id',$branch);
		if($from_warehouse)
			$transaction->addCondition('from_warehouse_id',$from_warehouse);
		if($to_warehouse)
			$transaction->addCondition('to_warehouse_id',$to_warehouse);
		if($activity_type)
			$transaction->addCondition('type',$activity_type);
		if($from_date)
			$transaction->addCondition('created_at','>=',$from_date);
		if($to_date)
			$transaction->addCondition('created_at','<',$this->app->nextDate($to_date));
		
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false]);
		$crud->setModel($transaction,null,['branch','from_warehouse','to_warehouse','created_by','type','created_at','status','narration','item_quantity','toreceived','received']);
		$crud->grid->addPaginator(25);
		$crud->grid->addQuickSearch(['from_warehouse','to_warehouse','type','narration']);
		$crud->add('xepan\base\Controller_MultiDelete');
		$crud->grid->removeAttachment();


		if($form->isSubmitted()){

			$crud->js()->reload([
					'from_date'=>$form['from_date'],
					'to_date'=>$form['to_date'],
					'branch'=>$form['branch'],
					'activity_type'=>$form['activity_type'],
					'from_warehouse'=>$form['from_warehouse'],
					'to_warehouse'=>$form['to_warehouse'],
				])->execute();

		}

	}
}