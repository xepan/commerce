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
				'serial_nos'=>'c14~6',
				'FormButtons~&nbsp;'=>"c15~6"
			]);

		$warehouse_field = $form->addField('xepan\commerce\Warehouse','warehouse')->validate('required');

		$item_model = $this->add('xepan\commerce\Model_Store_Item');
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel($item_model);
		$form->layout->add('Button',null,'extra_info_btn')->set('Extra-Info')->setClass('btn btn-warning extra-info');
		$form->addField('text','extra_info');
		
		$form->addField('Number','quantity')->validate('required');
		$form->addField('DatePicker','date')->validate('required')->set($this->app->today);

		$form->addField('text','narration')->addClass('height-60');
		$form->addField('text','serial_nos')->addClass('height-60');

		$grid = $this->add('xepan\base\Grid');
		$opening_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Opening');
		$opening_model->getElement('from_warehouse')->caption('warehouse');
		$opening_model->setOrder('created_at','asc');

		// delete related transaction record
		$opening_model->addHook('afterDelete',function($m){
			$m->add('xepan\commerce\Model_Store_Transaction')
				->load($m['store_transaction_id'])
				->delete();
		});
		$grid->setModel($opening_model,['item_name','quantity','transaction_narration','from_warehouse','created_at']);
		// $grid = $crud->grid;
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['item_name']);
		$grid->addSno();
		// $grid->removeColumn('action');
		// $grid->removeAttachment();

		$form->addSubmit('Add Opening Stock')->addClass('btn btn-primary');
		if($form->isSubmitted()){

			try{
				$this->app->db->beginTransaction();

				$oi = $this->add('xepan\commerce\Model_Item')->load($form['item']);
				$serial_no_array = [];
				if($oi['is_serializable']){
		          $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$form['serial_nos'])));
		          $serial_no_array = explode("\n",$code);
		          if($form['quantity'] != count($serial_no_array))
		            $form->displayError('serial_nos','count of serial nos must be equal to quantity');
		        }

				$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);

		        $serial_data = [
		        		'is_available'=>true,
		        		'is_return'=>false,
		        		'contact_id'=>$form['warehouse']
		        	];

				$cf_key = $oi->convertCustomFieldToKey(json_decode(($form['extra_info']?:'{}'),true));

				$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Opening',null,null,$form['narration'],null,'ToReceived',$form['date']);
				$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Opening',$oi['qty_unit_id'],null,true,$serial_no_array,null,null,$serial_data);

				$this->app->db->commit();
			}catch(\Exception $e){
				$this->app->db->rollback();

				throw $e;
			}

			$js = [$crud->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}