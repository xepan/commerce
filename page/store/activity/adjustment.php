<?php


namespace xepan\commerce;

class page_store_activity_adjustment extends \xepan\base\Page{
	public $title="Adjustment Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$form->addField('dropdown','adjustment_type')->setValueList(['Adjustment_Add'=>'Adjustment_Add','Adjustment_Removed'=>'Adjustment_Removed'])->setEmptyText('Please select adjustment type');

		$warehouse_field = $form->addField('dropdown','warehouse');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addSubmit('save');

		$tab = $this->add('Tabs');
		$tab1 = $tab->addTab('Item Stock');
		$tab2 = $tab->addTab('Adjustment Add');
		$tab3 = $tab->addTab('Adjustment Removed');

		$grid = $tab1->add('xepan\base\Grid');
		$item_stock_model = $tab1->add('xepan\commerce\Model_Item_Stock')
							->addCondition([['adjustment_add','>',0],['adjustment_removed','>',0]]);
		$grid->setModel($item_stock_model,['name','adjustment_add','adjustment_removed','purchase','consumed','consumption_booked','received','net_stock']);
		$grid->addPaginator($ipp=30);	
		
		$transaction_row_m = $tab2->add('xepan\commerce\Model_Store_TransactionRow'); 
		$transaction_row_m->addCondition('status','Adjustment_Add');
		$grid2 = $tab2->add('Grid');
		$grid2->setModel($transaction_row_m);
		$grid2->addPaginator($ipp=30);	

		$transaction_row_m = $tab3->add('xepan\commerce\Model_Store_TransactionRow'); 
		$transaction_row_m->addCondition('status','Adjustment_Removed');
		$grid3 = $tab3->add('Grid');
		$grid3->setModel($transaction_row_m);
		$grid3->addPaginator($ipp=30);


		if($form->isSubmitted()){
			if($form['adjustment_type'] == '')
				$form->displayError('adjustment_type','Please select adjustment type');

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],$form['adjustment_type']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,$form['adjustment_type']);
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}