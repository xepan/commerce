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
		$contact_model->title_field = "name_with_type";
		$contact_field->setModel($contact_model);

		$warehouse_field = $form->addField('xepan\commerce\Warehouse','warehouse')->Validate('required');
		// $warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		// $warehouse_field->setEmptyText("Please Select");

		$form->layout->add('View',null,'items');
		$crud = $form->layout->add('CRUD',['entity_name'=>'Issuable Item'],'items')->setStyle('margin-top','10px');
		$crud->setModel($session_item,['item','quantity','extra_info','narration','serial_nos'],['item_name','quantity','extra_info','narration','serial_nos']);
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
		$grid->add('VirtualPage')
			->addColumn('detail')
			->set(function($page){

				$id = $_GET[$page->short_name.'_id'];
				$detail_model = $this->add('xepan\commerce\Model_Store_TransactionRow');
				$detail_model->addCondition('store_transaction_id',$id);
				$crud = $page->add('CRUD');
				$crud->setModel($detail_model,['item_id','quantity','status','extra_info','serial_nos','narration','']);
		});

		if($transaction_id = $_GET['Print_Document']){
			$this->app->js(true)->univ()->newWindow($this->app->url('xepan_commerce_printstoretransaction',['transaction_id'=>$transaction_id]),'PrintIssueChallan')->execute();
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

				$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Issue',$form['department'],$form['contact'],$form['narration'],null,'ToReceived',$form['date']);
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
			$form->js(null,$js)->univ()->successMessage('stock issue successfully')->execute();
		}

	}
}