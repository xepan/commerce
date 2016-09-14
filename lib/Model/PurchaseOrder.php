<?php

namespace xepan\commerce;

class Model_PurchaseOrder extends \xepan\commerce\Model_QSP_Master{

   public $status = ['Draft','Submitted','Approved','InProgress','Redesign','Canceled','Rejected','PartialComplete','Completed'];

   public $actions = [
   'Draft'=>['view','edit','delete','submit','manage_attachments'],
   'Submitted'=>['view','edit','delete','reject','approve','manage_attachments','createInvoice','print_document'],
   'Approved'=>['view','edit','delete','reject','markinprogress','manage_attachments','createInvoice','print_document','send'],
   'InProgress'=>['view','edit','delete','cancel','markhascomplete','manage_attachments','sendToStock','send'],
   'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
   'Canceled'=>['view','edit','delete','manage_attachments'],
   'Rejected'=>['view','edit','delete','submit','manage_attachments'],
   'PartialComplete'=>['view','edit','delete','markhascomplete','manage_attachments','send'],
   'Completed'=>['view','edit','delete','manage_attachments','print_document','send']
   ];
   
   function init(){
      parent::init();

      $this->addCondition('type','PurchaseOrder');
      $this->getElement('document_no')->defaultValue($this->newNumber());

  }

  function print_document(){
    $this->print_QSP();
  }
  
  function page_send($page){
    $this->send_QSP($page,$this);
  }

  function submit(){
      $this['status']='Submitted';
      $this->app->employee
      ->addActivity("Purchase Order no. '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('reject,approve,createInvoice','Submitted');
      $this->save();
  }

  function reject(){
      $this['status']='Rejected';
      $this->app->employee
      ->addActivity("Purchase Order no. '".$this['document_no']."' rejected", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('submit','Rejected');
      $this->save();
  }

  function approve(){
      $this['status']='Approved';
      $this->app->employee
      ->addActivity("Purchase Order no. '".$this['document_no']."' approved, invoice can be created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('reject,markinprogress,createInvoice','Approved');
      $this->save();
  }

  function markinprogress(){
    $this['status']='InProgress';
    $this->app->employee
    ->addActivity("Purchase Order no. '".$this['document_no']."' proceed for dispatching", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('markhascomplete,sendToStock','InProgress');
    $this->save();
  }

  function cancel(){
    $this['status']='Canceled';
    $this->app->employee
    ->addActivity("Purchase Order no. '".$this['document_no']."' canceled", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('delete','Canceled');
    $this->save();
  }

  function markhascomplete(){
    $this['status']='Completed';
    $this->app->employee
    ->addActivity("Purchase Order no. '".$this['document_no']."' successfully dispatched", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('delete','Completed');
    $this->save();

  }

  function orderItems(){
    if(!$this->loaded())
      throw new \Exception("loaded sale order required");

    return $order_details = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
  }

  function invoice(){
    if(!$this->loaded())
      throw new \Exception("Model Must Loaded, PurchaseOrder");
    
    $inv = $this->add('xepan\commerce\Model_PurchaseInvoice')
    ->addCondition('related_qsp_master_id',$this->id);

    $inv->tryLoadAny();
    if($inv->loaded()) return $inv;
    
    return false;
  }

  function page_createInvoice($page){
    $page->add('View')->set('Order No: '.$this['document_no']);
    if(!$this->loaded()){
      $page->add('View_Error')->set("model must loaded");
      return;
    }

    $inv = $this->invoice();
    if(!$inv){
      $page->add('View')->set("You have successfully created invoice of this order, you can edit too ");
      $new_invoice = $this->createInvoice();
      $form = $page->add('Form');
      $form->addSubmit('Edit Invoice');
      if($form->isSubmitted()){
        return $form->js()->univ()->location($this->api->url('xepan_commerce_purchaseinvoicedetail',['action'=>'edit','document_id'=>$new_invoice->id]));
      }
      $page->add('xepan\commerce\View_QSP',['qsp_model'=>$new_invoice]);
    }else{

      $page->add('View')->set("You already created invoice of this order");
      $form = $page->add('Form');
      $form->addSubmit('Edit Invoice');
        if($form->isSubmitted()){
          return $form->js()->univ()->location($this->api->url('xepan_commerce_purchaseinvoicedetail',['action'=>'edit','document_id'=>$inv->id]));
        }
      $page->add('xepan\commerce\View_QSP',['qsp_model'=>$inv]);
    }
  }


  function createInvoice($status='Due'){
    
    if(!$this->loaded())
      throw new \Exception("model must loaded before creating invoice", 1);
    
    $customer = $this->customer();
    
    $invoice = $this->add('xepan\commerce\Model_PurchaseInvoice');

    $invoice['contact_id'] = $customer->id;
    $invoice['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
    $invoice['related_qsp_master_id'] = $this->id;
    $invoice['tnc_id'] = $this['tnc_id'];
    $invoice['tnc_text'] = $this['tnc_text']?$this['tnc_text']:"not defined";
    
    $invoice['status'] = $status;
    $invoice['due_date'] = null;
    $invoice['exchange_rate'] = $this['exchange_rate'];

    $invoice['document_no'] = $invoice['document_no'];

    $invoice['billing_address'] = $this['billing_address'];
    $invoice['billing_city'] = $this['billing_city'];
    $invoice['billing_state_id'] = $this['billing_state_id'];
    
    $invoice['billing_country_id'] = $this['billing_country_id'];
    $invoice['billing_pincode'] = $this['billing_pincode'];
    
    $invoice['shipping_address'] = $this['shipping_address'];
    $invoice['shipping_city'] = $this['shipping_city'];
    $invoice['shipping_state_id'] = $this['shipping_state_id'];
    $invoice['shipping_country_id'] = $this['shipping_country_id'];
    $invoice['shipping_pincode'] = $this['shipping_pincode'];
    
    $invoice['is_shipping_inclusive_tax'] = $this['is_shipping_inclusive_tax'];
    $invoice['from'] = $this['from'];

    $invoice['discount_amount'] = $this['discount_amount']?:0;
    $invoice['is_express_shipping'] = $this['is_express_shipping']?:0;
    $invoice->save();
    
    //here this is current order
    $ois = $this->orderItems();
    foreach ($ois as $oi) { 
        //todo check all invoice created or not
      $invoice->addItem(
        $oi->item(),
        $oi['price'],
        $oi['quantity'],
        $oi['sale_amount'],
        $oi['original_amount'],
        $oi['shipping_charge'],
        $oi['shipping_duration'],
        $oi['express_shipping_charge'],
        $oi['express_shipping_duration'],
        $oi['narration'],
        $oi['extra_info'],
        $oi['taxation_id'],
        $oi['tax_percentage']
        );
    }

    return $invoice;
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
                $transaction[$form['warehouse_'.$item_row->id]] = $w->newTransaction($this->id,null,$this['contact_id'],"Store_Transaction");
            }

                        // throw new \Exception($form['item_'.$item_row->id]);
            if($form[$item_row['item_id']]){
                $transaction[$form['warehouse_'.$item_row->id]]
                ->addItem($form['qsp_detail_'.$item_row->id],$form['qty_'.$item_row->id],null,null,null);
            }
        }       
        $this['status']='PartialComplete';
        $this->app->employee
          ->addActivity("Purchase Order no. '".$this['document_no']."' related products successfully send to stock", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
          ->notifyWhoCan('delete','Completed');
        $this->save();
        $this->app->page_action_result = $form->js(null,$form->js()->closest('.dialog')->dialog('close'))->univ()->successMessage('Item Send To Store');
        // $form->js()->univ()->successMessage('Item Send To Store')->closeDialog();
        return true;
    }

}

}
