<?php


namespace xepan\commerce;

class page_store_activity_opening extends \xepan\base\Page{

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible()
			// ->addContentSpot()
			->layout([
				'item'=>"Add Opening Stock~c1~4",
				'extra_info~'=>"c1~4",
				'extra_info_btn~&nbsp;'=>"c2~2",

				'warehouse'=>"c3~6",
				'quantity'=>"c11~3",
				'date'=>"c12~3",
				'narration'=>"c13~6",
				'FormButtons~&nbsp;'=>"c11~6"
			]);

		$warehouse_field = $form->addField('dropdown','warehouse')->validate('required');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field->setEmptyText('please select');

		$item_model = $this->add('xepan\commerce\Model_Store_Item');
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel($item_model);
		$form->layout->add('Button',null,'extra_info_btn')->set('Extra-Info')->setClass('btn btn-warning extra-info');
		$form->addField('text','extra_info');
		
		$form->addField('Number','quantity')->validate('required');
		$form->addField('DatePicker','date')->validate('required')->set($this->app->today);

		$form->addField('text','narration')->addClass('height-60');

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false]);
		$opening_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Opening');
		$opening_model->getElement('from_warehouse')->caption('warehouse');
		$opening_model->setOrder('created_at','asc');

		// delete related transaction record
		$opening_model->addHook('afterDelete',function($m){
			$m->add('xepan\commerce\Model_Store_TransactionAbstract')
				->load($m['store_transaction_id'])
				->delete();
		});

		$crud->setModel($opening_model,['item_name','quantity','transaction_narration','from_warehouse','created_at']);
		$grid = $crud->grid;
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['item_name']);
		$grid->addSno();
		$grid->removeColumn('action');
		$grid->removeAttachment();

		$form->addSubmit('Add Opening Stock')->addClass('btn btn-primary');
		if($form->isSubmitted()){

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode(($form['extra_info']?:'{}'),true));
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Opening',null,null,$form['narration'],null,'ToReceived',$form['date']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Opening');
			$js = [$crud->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}