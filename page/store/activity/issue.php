<?php


namespace xepan\commerce;

class page_store_activity_issue extends \xepan\base\Page{
	// public $title="Dispatch Order Item";
	public $contact_model = "xepan\base\Model_Contact";

	function init(){
		parent::init();
		
		// session model
		$session_item = $this->add('Model',['table'=>'item']);
		$session_item->setSource('Session');

		$session_item->addField('item')->display(['form'=>'xepan\commerce\Item'])->setModel('xepan\commerce\Model_Store_Item');
		$session_item->addField('item_name');
		$session_item->addHook('afterLoad',function($m){$m['item_name'] = $this->add('xepan\commerce\Model_Store_Item')->load($m['item'])->get('name'); });
	
		$session_item->addField('quantity')->type('number');
		$session_item->addField('extra_info')->type('text');
		$session_item->addField('narration')->type('text');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible()
			->addContentSpot()
			->layout([
				'warehouse~From Warehouse'=>"Issue Stock To Department/Employee~c1~3",
				'department~To Department'=>"c2~3",
				'contact~To Contact'=>"c3~3",
				'date'=>"c4~3",
				'items~'=>'c5~12',
				'narration'=>'c6~6',
				'FormButtons~&nbsp;'=>"c9~3"
			]);

		$department_field = $form->addField('dropdown','department');
		$department_field->setModel('xepan\hr\Department');
		$department_field->setEmptyText('Please Select');

		$contact_field = $form->addField('xepan\base\Basic','contact');

		$contact_model = $this->add($this->contact_model);
		$contact_field->setModel($contact_model);

		$warehouse_field = $form->addField('dropdown','warehouse')->Validate('required');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field->setEmptyText("Please Select");

		$form->layout->add('View',null,'items');
		$crud = $form->layout->add('CRUD',['entity_name'=>'Issuable Item'],'items')->setStyle('margin-top','10px');
		$crud->setModel($session_item,['item','quantity','extra_info','narration'],['item_name','quantity','extra_info','narration']);
		if($crud->isEditing()){
			$crud->form->add('Button')->set('Extra Info')->addClass('extra-info btn btn-primary');
		}

		$form->addField('text','narration')->addClass('height-60');
		$form->addField('DatePicker','date')->Validate('required')->set($this->app->today);

		$form->addSubmit('Issue Now')->addClass('btn btn-primary');

		$this->add('View')->setElement('H2')->set("Stock Issue Record");

		$grid = $this->add('xepan\base\Grid');
		$issue_model = $this->add('xepan\commerce\Model_Store_TransactionAbstract')
					->addCondition('type','Issue');
		$grid->setModel($issue_model,['from_warehouse','to_contact_name','item_quantity','created_at']);

		$grid->addPaginator($ipp=25);
		$grid->addSno();
		$print_btn = $grid->addColumn('Button','Print_Document');

		if($transaction_id = $_GET['Print_Document']){
			$this->app->js(true)->univ()->newWindow($this->app->url('xepan_commerce_printstoretransaction',['transaction_id'=>$transaction_id,'']),'PrintIssueChallan')->execute();
		}

		if($form->isSubmitted()){

			if(!$form['department'] && !$form['contact'])
				$form->error('contact','please select either Department or contact');

			if(!$session_item->count()){
				$form->js()->univ()->errorMessage('please add issuable item')->execute();
			}
			
			if(!$form['date']) $form->error('date','date must not be empty');

			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Issue',$form['department'],$form['contact'],$form['narration'],null,'ToReceived',$form['date']);

			foreach ($session_item as $model){
				$cf_key = $this->add('xepan\commerce\Model_Item')
						->load($model['item'])
						->convertCustomFieldToKey(json_decode($model['extra_info']?:'{}',true));

				$transaction->addItem(null,$model['item'],$model['quantity'],null,$cf_key,'ToReceived',null,null,null,null,null,$model['narration']);
			}

			$session_item->deleteAll();
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('stock issue successfully')->execute();
		}

	}
}