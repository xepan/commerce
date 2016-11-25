<?php

namespace xepan\commerce;

class Model_Reports_Customer extends \xepan\commerce\Model_Customer{
	public $from_date;
	public $to_date;
	public $customer;
	public $from_amount;
	public $to_amount;

	function init(){
		parent::init();

		if($this->customer)
			$this->addCondition('id',$this->customer);

		$this->addExpression('draft_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','Draft');

			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('submitted_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','Submitted');

			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('approved_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','Approved');
			
			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('inprogress_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','InProgress');
			
			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('canceled_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','Canceled');
			
			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('completed_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','Completed');
			
			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('onlineunpaid_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','OnlineUnpaid');
			
			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('redesign_odr_count')->set(function($m,$q){
			$so = $this->add('xepan\commerce\Model_SalesOrder');
			$so->addCondition('contact_id',$m->getElement('id'));
			$so->addCondition('status','Redesign');
			
			if($this->from_date){
				$so->addCondition('created_at','>',$this->from_date);
				$so->addCondition('created_at','<',$this->to_date);
			}

			return $so->count();
		});

		$this->addExpression('draft_inv_count')->set(function($m,$q){
			$si = $this->add('xepan\commerce\Model_SalesInvoice');
			$si->addCondition('contact_id',$m->getElement('id'));
			$si->addCondition('status','Draft');
			
			if($this->from_date){
				$si->addCondition('created_at','>',$this->from_date);
				$si->addCondition('created_at','<',$this->to_date);
			}

			return $si->count();
		});

		$this->addExpression('submitted_inv_count')->set(function($m,$q){
			$si = $this->add('xepan\commerce\Model_SalesInvoice');
			$si->addCondition('contact_id',$m->getElement('id'));
			$si->addCondition('status','Submitted');
				
			if($this->from_date){
				$si->addCondition('created_at','>',$this->from_date);
				$si->addCondition('created_at','<',$this->to_date);
			}

			return $si->count();
		});

		$this->addExpression('redesign_inv_count')->set(function($m,$q){
			$si = $this->add('xepan\commerce\Model_SalesInvoice');
			$si->addCondition('contact_id',$m->getElement('id'));
			$si->addCondition('status','Redesign');
			
			if($this->from_date){
				$si->addCondition('created_at','>',$this->from_date);
				$si->addCondition('created_at','<',$this->to_date);
			}

			return $si->count();
		});

		$this->addExpression('due_inv_count')->set(function($m,$q){
			$si = $this->add('xepan\commerce\Model_SalesInvoice');
			$si->addCondition('contact_id',$m->getElement('id'));
			$si->addCondition('status','Due');
			
			if($this->from_date){
				$si->addCondition('created_at','>',$this->from_date);
				$si->addCondition('created_at','<',$this->to_date);
			}

			return $si->count();
		});

		$this->addExpression('paid_inv_count')->set(function($m,$q){
			$si = $this->add('xepan\commerce\Model_SalesInvoice');
			$si->addCondition('contact_id',$m->getElement('id'));
			$si->addCondition('status','Paid');
			
			if($this->from_date){
				$si->addCondition('created_at','>',$this->from_date);
				$si->addCondition('created_at','<',$this->to_date);
			}

			return $si->count();
		});

		$this->addExpression('canceled_inv_count')->set(function($m,$q){
			$si = $this->add('xepan\commerce\Model_SalesInvoice');
			$si->addCondition('contact_id',$m->getElement('id'));
			$si->addCondition('status','Canceled');
			
			if($this->from_date){
				$si->addCondition('created_at','>',$this->from_date);
				$si->addCondition('created_at','<',$this->to_date);
			}
			
			return $si->count();
		});	
	}
} 