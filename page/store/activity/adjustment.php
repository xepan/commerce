<?php

namespace xepan\commerce;

class page_store_activity_adjustment extends \xepan\base\Page{
	public $title="Adjustment Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible()
			->addContentSpot()
			->layout([
				'adjustment_type'=>"Stock Adjustment~c1~3",
				'subtype'=>"c2~3",
				'warehouse'=>"c3~3",
				'date'=>"c4~3",
				'item'=>"c11~4",
				'extra_info~'=>"c11~4",
				'extra_info_btn~&nbsp;'=>"c12~2",
				'serial_nos'=>'c11~4',

				'quantity'=>"c13~2",
				'narration'=>"c14~4",
				'FormButtons~'=>"c21~12"
			]);

		$form->addField('dropdown','adjustment_type')->setValueList(['Adjustment_Add'=>'Adjustment_Add','Adjustment_Removed'=>'Adjustment_Removed'])->setEmptyText('Please select adjustment type');
		// $warehouse_model = $this->add('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field = $form->addField('xepan\commerce\Warehouse','warehouse');
		// $warehouse_field->setModel($warehouse_model);
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Store_Item');
		$item_field->other_field->validate('required');

		$form->layout->add('Button',null,'extra_info_btn')->set('Extra-Info')->setClass('btn btn-warning extra-info');
		
		$adjust_subtype = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'subtype'=>'text',
							],
				'config_key'=>'ADJUSTMENT_SUBTYPE',
				'application'=>'commerce'
			]);

		$adjust_subtype->tryLoadAny();
		$adjust_subtype = explode(",",$adjust_subtype['subtype']);

		$subtype_field = $form->addField('xepan\base\DropDown','subtype');
		$subtype_field->setValueList(array_combine($adjust_subtype,$adjust_subtype));
		$subtype_field->setEmptyText('Please Select');

		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addField('text','narration')->addClass('height-60');
		$form->addField('DatePicker','date')->validate('required')->set($this->app->today);
		$form->addField('text','serial_nos')->addClass('height-60');

		$form->addSubmit('Adjust Now')->addClass('btn btn-primary');

		$view = $this->add('View');

		$tab = $view->add('Tabs');
		$tab1 = $tab->addTab('Item Stock');
		$tab2 = $tab->addTab('Adjustment Add');
		$tab3 = $tab->addTab('Adjustment Removed');

		$grid = $tab1->add('xepan\base\Grid');
		$item_stock_model = $tab1->add('xepan\commerce\Model_Item_Stock')
							->addCondition([['adjustment_add','>',0],['adjustment_removed','>',0]]);
		$grid->setModel($item_stock_model,['name','adjustment_add','adjustment_removed','subtype','purchase','consumed','consumption_booked','received','net_stock','qty_unit']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['name']);
		$grid->addSno();

		$transaction_row_m = $tab2->add('xepan\commerce\Model_Store_TransactionRow'); 
		$grid2 = $tab2->add('xepan\base\Grid');
		$grid2->setModel($transaction_row_m,['item_name','quantity','transaction_narration','from_warehouse','to_warehouse','subtype'])->addCondition('status','Adjustment_Add');
		$grid2->addPaginator($ipp=25);
		$grid2->addQuickSearch(['item_name']);
		$grid2->addSno();

		$transaction_row_m = $tab3->add('xepan\commerce\Model_Store_TransactionRow');
		$grid3 = $tab3->add('xepan\base\Grid');
		$grid3->setModel($transaction_row_m,['item_name','quantity','transaction_narration','from_warehouse','to_warehouse','subtype'])->addCondition('status','Adjustment_Removed');
		$grid3->addPaginator($ipp=25);
		$grid3->addQuickSearch(['item_name']);
		$grid3->addSno();

		if($form->isSubmitted()){
			
			if($form['adjustment_type'] == '')
				$form->displayError('adjustment_type','Please select adjustment type');

			
			try{
				$this->app->db->beginTransaction();

				$oi = $this->add('xepan\commerce\Model_Item')->load($form['item']);
				$cf_key  = $oi->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
				$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);

				$serial_no_array = [];
				if($oi['is_serializable']){
		          $code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$form['serial_nos'])));
		          $serial_no_array = explode("\n",$code);
		          if($form['quantity'] != count($serial_no_array))
		            $form->displayError('serial_nos','count of serial nos must be equal to receive quantity');
					
					// checking user/warehouse having serial_no or not
					$serial_model = $this->add('xepan\commerce\Model_Item_Serial')
						->addCondition('item_id',$form['item'])
						->addCondition('contact_id',$form['warehouse'])
						;
					$all_serial_no = array_column($serial_model->getRows(), 'serial_no');
					$all_serial_no = array_combine($all_serial_no, $all_serial_no);

					// finding all value exist or not
					$serial_nos_not_available = array_diff($serial_no_array, $all_serial_no);
					$serial_nos_available = array_diff($serial_no_array,$serial_nos_not_available);
					
					if($form['adjustment_type'] == "Adjustment_Add" AND count($serial_nos_available) != 0){
						$form->displayError('serial_nos','serial no ('.implode(",", $serial_nos_available).') already added to warehouse '.$warehouse['name']);
					}

					// serial_no not belong to this warehouse
					if($form['adjustment_type'] == "Adjustment_Removed" AND count($serial_nos_not_available) != 0){
						$form->displayError('serial_nos','serial no ('.implode(",", $serial_nos_not_available).') must belong to warehouse '.$warehouse['name']);
					}
		        }

				$serial_data = [
		        		'is_available'=>true,
		        		'is_return'=>false,
		        		'contact_id'=>$form['warehouse']
		        	];

				$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],$form['adjustment_type'],null,null,$form['narration'],$form['subtype']);
				$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,$form['adjustment_type'],$oi['qty_unit_id'],null,true,$serial_no_array,null,null,$serial_data);

				if( $oi['is_serializable'] AND $form['adjustment_type'] == "Adjustment_Removed"){
					$serial_model = $this->add('xepan\commerce\Model_Item_Serial')
						->addCondition('item_id',$form['item'])
						->addCondition('contact_id',$form['warehouse'])
						->addCondition('serial_no','in',$serial_no_array)
						;
					$serial_model->deleteAll();
				}

				$this->app->db->commit();
			}catch(\Exception $e){
				$this->app->db->rollback();
				throw $e;
			}

			
			$js = [$view->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}