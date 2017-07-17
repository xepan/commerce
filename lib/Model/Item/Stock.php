<?php

namespace xepan\commerce;

class Model_Item_Stock extends \xepan\commerce\Model_Item{
	
	/**
	item_custom_field=['custom_field1'=>'value','custome_field_2'=>value]
	**/
	public $item_custom_field = [];
	public $warehouse_id=null;

	function init(){
		parent::init();

		$this->getElement('total_orders')->destroy();
		$this->getElement('total_sales')->destroy();

		$this->addExpression('opening')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Opening');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});		

		$this->addExpression('purchase')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Purchase');
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('purchase_return')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Purchase_Return');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('consumption_booked')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Consumption_Booked');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('consumed')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Consumed');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});


		$this->addExpression('to_received')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				// status not type becuse transaction row has status and we work according to row not transaction
				->addCondition('type','<>',['MaterialRequestDispatch','MaterialRequestSend'])
				->addCondition('status','ToReceived');
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('received')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				// status not type becuse transaction row has status and we work according to row not transaction
				->addCondition('status','Received')
				->addCondition('type','<>',['MaterialRequestDispatch','MaterialRequestSend']);
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('adjustment_add')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Adjustment_Add');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('adjustment_removed')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Adjustment_Removed');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('movement_in')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition([['type','Movement'],['type',"MaterialRequestDispatch"]])
				->addCondition('status','Received');
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('movement_out')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition([['type','Movement'],['type','MaterialRequestDispatch']])
				->addCondition([['status','Received'],['status',"ToReceived"]]);
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});
		
		$this->addExpression('issue')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Issue')
				->addCondition('status','Received');
				if($this->warehouse_id)
					$model->addCondition('from_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});
		
		$this->addExpression('issue_submitted')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Issue_Submitted')
				->addCondition('status','Received');

				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$this->addExpression('sales_return')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Sales_Return');
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});


		// shipped
		$this->addExpression('shipped')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Store_Delivered')
				->addCondition('status','Shipped');
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		// shipped
		$this->addExpression('delivered')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Store_Delivered')
				->addCondition('status','Delivered');
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);

				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		// PackageCreated
		$this->addExpression('package_created')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
					->addCondition('item_id',$m->getElement('id'))
					->addCondition('type','PackageCreated')
					->addCondition('status','Received');

				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);
				
				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});
		
		// PackageOpened
		$this->addExpression('package_opened')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
					->addCondition('item_id',$m->getElement('id'))
					->addCondition('type','PackageOpened')
					->addCondition('status','Received');
				
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);
				
				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});
		
		// ConsumedInPackage
		$this->addExpression('consumed_in_package')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
					->addCondition('item_id',$m->getElement('id'))
					->addCondition('type','ConsumedInPackage')
					->addCondition('status','Received');
				
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);
				
				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		// ReleaseFromPackage
		$this->addExpression('release_from_package')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
					->addCondition('item_id',$m->getElement('id'))
					->addCondition('type','ReleaseFromPackage')
					->addCondition('status','Received');
				
				if($this->warehouse_id)
					$model->addCondition('to_warehouse_id',$this->warehouse_id);
				
				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		// 
		$this->addExpression('net_stock')->set(function($m,$q){
			// $plus=['opening','purchase','received','adjustment_add','movement_in','issue_submitted','sales_return'];
			// $minus=['purchase_return','consumption_booked','consumed','adjustment_removed','movement_out','issue'];
			
			return $q->expr('(([opening]+[purchase]+[received]+[adjustment_add]+[movement_in]+[issue_submitted]+[sales_return]+[package_created])-([purchase_return]+[consumption_booked]+[consumed]+[adjustment_removed]+[movement_out]+[issue]+[shipped]+[delivered]))',
							[
								'opening'  				=>  $m->getElement('opening'),
								'purchase' 				=> 	$m->getElement('purchase'),
								'received' 				=> 	$m->getElement('received'),
								'adjustment_add' 		=> 	$m->getElement('adjustment_add'),
								'movement_in' 			=>	$m->getElement('movement_in'),
								'issue_submitted'		=> 	$m->getElement('issue_submitted'),
								'sales_return'			=> 	$m->getElement('sales_return'),
								'purchase_return'		=>	$m->getElement('purchase_return'),
								'consumption_booked'	=>	$m->getElement('consumption_booked'),
								'consumed' 				=>	$m->getElement('consumed'),
								'adjustment_removed'	=>	$m->getElement('adjustment_removed'),
								'movement_out' 			=>	$m->getElement('movement_out'),
								'issue' 				=>	$m->getElement('issue'),
								'shipped' 				=>	$m->getElement('shipped'),
								'delivered' 			=>	$m->getElement('delivered'),
								'package_created' 		=>	$m->getElement('package_created'),
								// 'package_opened' 		=>	$m->getElement('package_opened'),
								// 'consumed_in_package'	=>	$m->getElement('consumed_in_package'),
								// 'release_from_package'	=>	$m->getElement('release_from_package')
							]);
		});
	}
}