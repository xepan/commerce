<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','partial_complete','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','inprogress','manage_attachments'],
				'InProgress'=>['view','edit','delete','cancel','complete','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments'],
				// 'Returned'=>['view','edit','delete','manage_attachments']
				];


	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');
		
		$this->addExpression('days_left')->set(function($m,$q){
			$date=$m->add('\xepan\base\xDate');
			$diff = $date->diff(
						date('Y-m-d H:i:s',strtotime($m['created_at'])
							),
						date('Y-m-d H:i:s',strtotime($m['due_date'])),'Days'
					);

			return "'".$diff."'";
		});
	}

	function inprogress(){
		$this['status']='InProgress';
        $this->app->employee
            ->addActivity("InProgress QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('cancel','Approved');
        $this->saveAndUnload();
    }

    function cancel(){
		$this['status']='Canceled';
        $this->app->employee
            ->addActivity("Canceled QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();
    }

    function complete(){
		$this['status']='Completed';
        $this->app->employee
            ->addActivity("Completed QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();
    }

	function page_approve($page){

		$page->add('View_Info')->setElement('H2')->setHTML('Approving Job Card will move this order to approved status and create JobCards to receive in respective FIRST Departments for EACH Item');

		$form = $page->add('Form_Stacked');
		$form->addField('text','comments');
		$form->addSubmit('Approve & Create Jobcards');

		if($form->isSubmitted()){
			$this->approve();
			
			$this['status']='InProgress';
        	$this->app->employee
            	->addActivity("SaleOrder Jobcard created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            	->notifyWhoCan('','InProgress');
            $this->saveAndUnload();
            return true;
		}
		return false;
	}

	function approve(){
		$this->app->hook('sales_order_approved',[$this]);
	}

	function orderItems(){
		return $this->items();
	}

	function customer(){
		return $this->ref('contact_id');
	}

	function invoice(){
		if(!$this->loaded());
			throw new \Exception("Model Must Loaded, SaleOrder");
			
		$inv = $this->add('xepan\commerce\Model_SalesInvoice')
					->addCondition('related_qsp_master_id',$this->id);

		$inv->tryLoadAny();
		if($inv->loaded()) return $inv;
		return false;
	}

	function page_sendToStock($page){

        $page->add('View_Info')->set('Please Select Item to send to Stock');
        
        $form = $page->add('Form',null,null,['form/empty']);
        foreach ($this->items() as  $item_row) {
            $form->addField('CheckBox',$item_row['item_id'],$item_row['item']);
            $form->addField('hidden','qsp_detail_'.$item_row->id)->set($item_row->id);
            
            $form->addField('Number','qty_'.$item_row->id,'qty');
            $warehouse_f=$form->addField('DropDown','warehouse_'.$item_row->id,'warehouse');
            $warehouse=$page->add('xepan\commerce\Model_Store_Warehouse');
        	$warehouse_f->setModel($warehouse);
        }

        $form->addSubmit('Send');
   
    	if($form->isSubmitted()){

            $warehouse=[];
            $transaction=[];

            foreach ($this->items() as  $item_row) {

                if(!isset($warehouse[$form['warehouse_'.$item_row->id]] )){
                    $w = $warehouse[$form['warehouse_'.$item_row->id]] = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse_'.$item_row->id]);
                    $transaction[$form['warehouse_'.$item_row->id]] = $w->newTransaction($this,"Sale");
                }

                // throw new \Exception($form['item_'.$item_row->id]);
                if($form[$item_row['item_id']]){
                    $transaction[$form['warehouse_'.$item_row->id]]
                            ->addItem($form['qsp_detail_'.$item_row->id],$form['qty_'.$item_row->id],null,null);
                }
            }       
            $this['status']='partial_complete';
            $this->saveAndUnload();
            $form->js()->univ()->successMessage('Item Send To Store')->closeDialog();
            return true;
        }
        
    }

}
