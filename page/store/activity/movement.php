<?php


namespace xepan\commerce;

class page_store_activity_movement extends \xepan\base\Page{
	public $title="Purchase Return Order Item";

	public $from_warehouse;
	public $to_warehouse;

	function init(){
		parent::init();
		
		$this->from_warehouse = $this->app->stickyGET('from_warehouse');
		$this->to_warehouse = $this->app->stickyGET('to_warehouse');

		$item_m = $this->add('xepan\commerce\Model_Item');
		// $item_m->addCondition('maintain_inventory',true);
		$item_m->addCondition('status','Published');

		$session_item = $this->add('Model',['table'=>'Items']);
		$session_item->setSource('Session');
		$session_item->addField('from_warehouse_id');
		$session_item->hasOne('xepan\commerce\Model_Item','item_id')
				->display(['form'=>'xepan\commerce\Item']);
		$session_item->addField('quantity')->type('number');
		$session_item->addField('extra_info')->type('text');

		$session_item->addCondition('from_warehouse_id',$this->from_warehouse);

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible()
			// ->addContentSpot()
			->layout([
				'from_warehouse'=>"Transfer Stock Between Warehouse~c1~6",
				'to_warehouse'=>"c2~6",
				'item'=>"Add Stock Movement Item~c1~4",
				'extra_info~'=>"c1~4",
				'extra_info_btn~&nbsp;'=>"c2~2",
				'quantity'=>"c3~3",
				'FormButtons~&nbsp;'=>"c4~3",
				'crud_view~'=>'c5~12',
				'move_selected_item~'=>'c12~12',
				'narration'=>'c6~4',
				'FormButtonsSecond~'=>'c7~4'
			]);

		$from_warehouse_field = $form->addField('dropdown','from_warehouse');
		$from_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$from_warehouse_field->setEmptyText('Please Select');

		$to_warehouse_field = $form->addField('dropdown','to_warehouse');
		$to_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$to_warehouse_field->setEmptyText('Please Select');

		if($this->from_warehouse){
			$from_warehouse_field->set($this->from_warehouse);
			$from_warehouse_field->setAttr('disabled','disabled');
		}

		if($this->to_warehouse){
			$to_warehouse_field->set($this->to_warehouse);
			$to_warehouse_field->setAttr('disabled','disabled');
		}

		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->is_mandatory = false;
		$item_field->setModel($item_m);

		$form->addField('Number','quantity')->set(0);
		$form->addField('text','extra_info');
		$form->layout->add('Button',null,'extra_info_btn')->set('Extra-Info')->setClass('btn btn-warning extra-info');

		$view = $form->layout->add('View',null,'move_selected_item');

		$form->addField('text','narration');
		$btn_2 = $form->layout->add('View',null,'FormButtonsSecond');

		$add_button = $form->addSubmit('Add')->addClass('btn btn-primary btn-block');
		$transfer_button = $form->addSubmit('Transfer Now')->addClass('transfer-button btn btn-primary');

		$transfer_button->js(true)->appendTo($btn_2);

		$item_crud = $form->layout->add('CRUD',['allow_add'=>false,'allow_edit'=>false],'crud_view');
		// $item_crud->addClass('remove-grid-header');
		$item_crud->setModel($session_item);

		$grid = $this->add('xepan\base\Grid');
		$movement_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Movement');
		$grid->setModel($movement_model,['item_name','quantity','transaction_narration','from_warehouse','to_warehouse']);
		$grid->addPaginator($ipp=25);
		$grid->addSno();

		if($form->isSubmitted()){
			
			$from_warehouse = $form['from_warehouse'];

			if(!$from_warehouse){
				if(!$this->from_warehouse)
					$form->error('from_warehouse','must not be empty');
			}
			if($this->from_warehouse)
				$from_warehouse = $this->from_warehouse;

			$to_warehouse = $form['to_warehouse'];
			if(!$to_warehouse){
				if(!$this->to_warehouse)
				$form->error('to_warehouse','must not be empty');
			}
			if($this->to_warehouse)
				$to_warehouse = $this->to_warehouse;

			if($form->isClicked($add_button)){

				if(!$form['item']){
					$form->error('item','please select item');
				}

				// todo check item stock is
				$session_item['from_warehouse_id'] = $from_warehouse;
				$session_item['item_id'] = $form['item'];
				$session_item['quantity'] = $form['quantity'];
				$session_item['extra_info'] = $form['extra_info'];
				$session_item->save();

				$js = [$item_crud->js()->reload(),$form->js()->reload(['from_warehouse'=>$from_warehouse,'to_warehouse'=>$to_warehouse])];
				$form->js(null,$js)->univ()->successMessage('Item Added To Move List')->execute();
			}

			if($form->isClicked($transfer_button)){
				
				if(!$session_item->count()){
					$form->js()->univ()->errorMessage('first add stock movement item')->execute();
				}

				$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')
						->load($this->from_warehouse);

				$transaction = $warehouse->newTransaction(null,null,$this->from_warehouse,'Movement',null,$this->to_warehouse);
				foreach ($session_item as $si) {
					$cf_key = $this->add('xepan\commerce\Model_Item')
							->load($si['item_id'])
							->convertCustomFieldToKey(json_decode($si['extra_info']?:'{}',true));
					$transaction->addItem(null,$si['item_id'],$si['quantity'],null,$cf_key,'ToReceived',$form['narration']);
				}

				$session_item->deleteAll();
				$this->app->stickyForget('from_warehouse');
				$this->app->stickyForget('to_warehouse');

				$js = [
						$grid->js()->reload(),
						$form->js()->reload(),
						$item_crud->js()->reload()
					];
				$form->js(null,$js)->univ()->successMessage('saved')->execute();
			}

		}

		// $form = $this->add('Form');
		// $form->add('xepan\base\Controller_FLC')
		// 	->makePanelsCoppalsible()
		// 	// ->addContentSpot()
		// 	->layout([
		// 		'from_warehouse'=>"Move Selected Item~c1~3",
		// 		'to_warehouse'=>'c2~3',
		// 		'narration'=>'c3~3',
		// 		'FormButtons~&nbsp;'=>"c4~3"
		// 	]);

		// $from_warehouse_field = $form->addField('dropdown','from_warehouse')->validate('required');
		// $from_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		// $from_warehouse_field->setEmptyText('Please Select');

		// $to_warehouse_field = $form->addField('dropdown','to_warehouse')->validate('required');
		// $to_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		// $to_warehouse_field->setEmptyText('Please Select');
		
		// $form->addField('text','narration');
		// $form->addSubmit('Transfer Now');

		// $this->add('View')->setElement('H2')->set("Stock Movement Record");
		// if($form->isSubmitted()){

		// 	if($form['from_warehouse'] == $form['to_warehouse'])
		// 		$form->error('from_warehouse','From and To warehouse must not be same');

		// 	if(!$session_item->count()){
		// 		$form->js()->univ()->errorMessage('first add stock movement item')->execute();
		// 	}

		// 	$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['from_warehouse']);
		// 	$transaction = $warehouse->newTransaction(null,null,$form['from_warehouse'],'Movement',null,$form['to_warehouse']);
		// 	foreach ($session_item as $si) {
		// 		$cf_key = $this->add('xepan\commerce\Model_Item')
		// 				->load($si['item_id'])
		// 				->convertCustomFieldToKey(json_decode($si['extra_info']?:'{}',true));
		// 		$transaction->addItem(null,$si['item_id'],$si['quantity'],null,$cf_key,'ToReceived',$form['narration']);
		// 	}

		// 	$session_item->deleteAll();

		// 	$js = [
		// 			$grid->js()->reload(),
		// 			$form->js()->reload(),
		// 			$item_crud->js()->reload()
		// 		];
		// 	$form->js(null,$js)->univ()->successMessage('saved')->execute();
		// }

	}
}