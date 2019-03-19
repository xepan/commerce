<?php


namespace xepan\commerce;

class page_store_activity_movement extends \xepan\base\Page{
	public $title="Purchase Return Order Item";

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
		$session_item->addField('serial_nos')->type('text')->hint('Enter Seperated');

		$session_item->addHook('beforeSave',function($m){
			$oi = $this->add('xepan\commerce\Model_Item')->load($m['item']);
			$serial_no_array = [];
			if($oi['is_serializable']){
	          $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$m['serial_nos'])));
	          $serial_no_array = explode("\n",$code);
	          if($m['quantity'] != count($serial_no_array))
	          	throw $this->exception('count of serial nos must be equal to receive quantity','ValidityCheck')->setField('serial_nos');
	        }
		});

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible()
			->addContentSpot()
			->layout([
				'warehouse~From Warehouse'=>"Move Stock To Contact~c1~3",
				'contact~To Warehouse'=>"c3~3",
				'date'=>"c4~3",
				'items~'=>'c5~12',
				'narration'=>'c6~6',
				'FormButtons~&nbsp;'=>"c9~3"
			]);

		$contact_field = $form->addField('xepan\base\Basic','contact');
		$contact_model = $this->add('xepan\base\Model_Contact');
		$contact_field->setModel($contact_model);
		
		$warehouse_field = $form->addField('xepan\commerce\Warehouse','warehouse')->Validate('required');
		// $warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		// $warehouse_field->setEmptyText("Please Select");

		$form->layout->add('View',null,'items');
		$crud = $form->layout->add('CRUD',['entity_name'=>'Movabele Item'],'items')->setStyle('margin-top','10px');
		$crud->setModel($session_item,['item','quantity','extra_info','narration','serial_nos'],['item_name','quantity','extra_info','narration','serial_nos']);
		if($crud->isEditing()){
			$crud->form->add('Button')->set('Extra Info')->addClass('extra-info btn btn-primary');
		}

		$form->addField('text','narration')->addClass('height-60');
		$form->addField('DatePicker','date')->Validate('required')->set($this->app->today);

		$form->addSubmit('Move Now')->addClass('btn btn-primary');

		$this->add('View')->setElement('H2')->set("Stock Movement Record");

		$grid = $this->add('xepan\base\Grid');
		$movement_model = $this->add('xepan\commerce\Model_Store_TransactionRow')
						->addCondition('type','Movement');
		$movement_model->setOrder('id','desc');
		$grid->setModel($movement_model,['item_name','quantity','transaction_narration','from_warehouse','to_warehouse']);
		$grid->addPaginator($ipp=25);
		$grid->addSno();

		$print_btn = $grid->addColumn('Button','Print_Challan');

		if($transaction_id = $_GET['Print_Challan']){
			$model = $this->add('xepan\commerce\Model_Store_TransactionRow')
						->addCondition('type','Movement')
						->load($transaction_id);

			$this->app->js(true)->univ()->newWindow($this->app->url('xepan_commerce_printstoretransaction',['transaction_id'=>$model['store_transaction_id']]),'PrintChallan')->execute();
		}

		if($form->isSubmitted()){

			if(!$form['department'] && !$form['contact'])
				$form->error('contact','please select either Department or contact');

			if(!$session_item->count()){
				$form->js()->univ()->errorMessage('please add issuable item')->execute();
			}
			
			if(!$form['date']) $form->error('date','date must not be empty');
			try{
				$this->app->db->begintransaction();	
				$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);

				$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Movement',$form['department'],$form['contact'],$form['narration'],null,'ToReceived',$form['date']);
				foreach ($session_item as $model){
					$item_model = $this->add('xepan\commerce\Model_Item')
							->load($model['item']);

					// check serial no exist or not in department
					$result_data = [];
					$senitized_serial_nos = $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$model['serial_nos'])));
					$stock_data = $item_model->getStockAvalibility(($model['extra_info']?:'{}'),$model['quantity'],$result_data,$form['warehouse'],$item_model['qty_unit_id'],explode("\n",$senitized_serial_nos));
					$cf_key = $item_model->convertCustomFieldToKey(json_decode($model['extra_info']?:'{}',true));
					if($item_model['is_serializable'] && isset($stock_data[$item_model['name']][$cf_key]['serial']) && count($stock_data[$item_model['name']][$cf_key]['serial']['unavailable']) ){
						$form->js()->univ()->errorMessage('Serial nos not found in '.$warehouse['name'] . ' => '. implode(",", $stock_data[$item_model['name']][$cf_key]['serial']['unavailable']))->execute();
					}
					$serial_fields=[
						'contact_id'=>$form['contact'],
						'transaction_id'=>$transaction->id
					];

					$transaction->addItem(null,$model['item'],$model['quantity'],null,$cf_key,'ToReceived',null,null,null,$senitized_serial_nos,null,$model['narration'],$serial_fields);
				}
				$this->app->db->commit();	
			}catch(\Exception $e){
				$this->app->db->rollback();
				throw $e;	

			}

			$session_item->deleteAll();
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('stock moved successfully')->execute();
		}

	}
}