<?php

namespace xepan\commerce;

class Model_PurchaseOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','partial_complete','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','reject','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','reject','markinprogress','manage_attachments'],
				'InProgress'=>['view','edit','delete','cancel','markhascomplete','manage_attachments','sendToStock'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Rejected'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments']
				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','PurchaseOrder');

	}

	function markinprogress(){
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

    function markhascomplete(){
		$this['status']='Completed';
        $this->app->employee
            ->addActivity("Completed QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();

    }

    function page_sendToStock($page){
    	$form = $page->add('Form');
        $form->add('View_Info')->set('Please Select Item to send to Stock');
        foreach ($this->items() as  $item_row) {
            $form->addField('CheckBox',$item_row['item_id'],$item_row['item']);
            $form->addField('hidden','item_'.$item_row->id)->set($item_row->id);
            
            $form->addField('Number','qty_'.$item_row->id,'qty');
            $warehouse_f=$form->addField('DropDown','warehouse_'.$item_row->id,'warehouse');
            $warehouse=$page->add('xepan\commerce\Model_Store_Warehouse');
        	$warehouse_f->setModel($warehouse);
        }

        $form->addSubmit('Send');
   
    	if($form->isSubmitted()){
            $warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
            $transaction = $warehouse->newPurchaseReceive($this,$form['warehouse_'.$this->items()->id]);

            foreach ($this->items() as  $item_row) {
                // throw new \Exception($form['item_'.$item_row->id]);
                if($form[$item_row['item_id']]){
                    $transaction->addItem($form['item_'.$item_row->id],$form['qty_'.$item_row->id],null,null);
                }
            }
            
            $this['status']='partial_complete';
            $this->saveAndUnload();
            $form->js()->univ()->successMessage('Item Send To Store')->closeDialog();
            return true;
        }
        
    }

}
