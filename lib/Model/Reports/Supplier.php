<?php

namespace xepan\commerce;

class Model_Reports_Supplier extends \xepan\commerce\Model_Supplier{
	public $from_date;
	public $to_date;
	public $supplier;
	public $from_amount;
	public $to_amount;

	function init(){
		parent::init();
		
		if($this->supplier)
			$this->addCondition('id',$this->supplier);

		$this->addExpression('draft_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Draft');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('submitted_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Submitted');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('approved_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Approved');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('inprogress_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','InProgress');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('redesign_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Redesign');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('canceled_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Canceled');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('rejected_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Rejected');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('partialcomplete_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','PartialComplete');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('completed_odr_count')->set(function($m,$q){
			$po = $this->add('xepan\commerce\Model_PurchaseOrder');
			$po->addCondition('contact_id',$m->getElement('id'));
			$po->addCondition('status','Completed');
			
			if($this->from_date){
				$po->addCondition('created_at','>',$this->from_date);
				$po->addCondition('created_at','<',$this->to_date);
			}

			return $po->count();
		});

		$this->addExpression('draft_inv_count')->set(function($m,$q){
			$pi = $this->add('xepan\commerce\Model_PurchaseInvoice');
			$pi->addCondition('contact_id',$m->getElement('id'));
			$pi->addCondition('status','Draft');
			
			if($this->from_date){
				$pi->addCondition('created_at','>',$this->from_date);
				$pi->addCondition('created_at','<',$this->to_date);
			}

			return $pi->count();
		});

		$this->addExpression('submitted_inv_count')->set(function($m,$q){
			$pi = $this->add('xepan\commerce\Model_PurchaseInvoice');
			$pi->addCondition('contact_id',$m->getElement('id'));
			$pi->addCondition('status','Submitted');
			
			if($this->from_date){
				$pi->addCondition('created_at','>',$this->from_date);
				$pi->addCondition('created_at','<',$this->to_date);
			}

			return $pi->count();
		});

		$this->addExpression('due_inv_count')->set(function($m,$q){
			$pi = $this->add('xepan\commerce\Model_PurchaseInvoice');
			$pi->addCondition('contact_id',$m->getElement('id'));
			$pi->addCondition('status','Due');
			
			if($this->from_date){
				$pi->addCondition('created_at','>',$this->from_date);
				$pi->addCondition('created_at','<',$this->to_date);
			}

			return $pi->count();
		});

		$this->addExpression('canceled_inv_count')->set(function($m,$q){
			$pi = $this->add('xepan\commerce\Model_PurchaseInvoice');
			$pi->addCondition('contact_id',$m->getElement('id'));
			$pi->addCondition('status','Canceled');
			
			if($this->from_date){
				$pi->addCondition('created_at','>',$this->from_date);
				$pi->addCondition('created_at','<',$this->to_date);
			}

			return $pi->count();
		});

		$this->addExpression('paid_inv_count')->set(function($m,$q){
			$pi = $this->add('xepan\commerce\Model_PurchaseInvoice');
			$pi->addCondition('contact_id',$m->getElement('id'));
			$pi->addCondition('status','Paid');
			
			if($this->from_date){
				$pi->addCondition('created_at','>',$this->from_date);
				$pi->addCondition('created_at','<',$this->to_date);
			}

			return $pi->count();
		});		
	}
}