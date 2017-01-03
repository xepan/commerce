<?php

namespace xepan\commerce;

class Model_PurchaseOrder extends \xepan\commerce\Model_QSP_Master{

   public $status = ['Draft','Submitted','Approved','InProgress','Redesign','Canceled','Rejected','PartialComplete','Completed'];

   public $actions = [
     'Draft'=>['view','edit','delete','submit','manage_attachments'],
     'Submitted'=>['view','edit','delete','reject','approve','createInvoice','print_document','manage_attachments'],
     'Approved'=>['view','edit','delete','reject','redesign','complete','inprogress','createInvoice','print_document','manage_attachments','send'],
     'InProgress'=>['view','edit','delete','complete','manage_attachments','send','cancel','sendToStock'],
     'Redesign'=>['view','edit','delete','submit','manage_attachments'],
     'Canceled'=>['view','edit','delete','redraft','manage_attachments'],
     'Rejected'=>['view','edit','delete','submit','redesign','manage_attachments'],
     'PartialComplete'=>['view','edit','delete','complete','manage_attachments','send'],
     'Completed'=>['view','edit','delete','createInvoice','manage_attachments','print_document','send']
   ];
   
   function init(){
      parent::init();

      $this->addCondition('type','PurchaseOrder');
      $this->getElement('document_no')->defaultValue($this->newNumber());

      $this->is([
      'document_no|required|number|unique_in_epan_for_type'
      ]);
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
      ->addActivity("Purchase Order No : '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('reject,approve,createInvoice','Submitted');
      $this->save();
  }

  function redesign(){
    $this['status']='Redesign';
    $this->app->employee
    ->addActivity("Purchase Order No : '".$this['document_no']."' proceed for redesign", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('submit','Redesign',$this);
    $this->save();
  }
  
  function redraft(){
    $this['status']='Draft';
    $this->app->employee
    ->addActivity("Purchase Order No : '".$this['document_no']."' redraft", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('submit','Draft',$this);
    $this->save();
  }

  function reject(){
      $this['status']='Rejected';
      $this->app->employee
      ->addActivity("Purchase Order No : '".$this['document_no']."' rejected", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('submit,redesign','Rejected');
      $this->save();
  }

  function approve(){
      $this['status']='Approved';
      $this->app->employee
      ->addActivity("Purchase Order No : '".$this['document_no']."' approved, so it's invoice can be created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('reject,markinprogress,createInvoice','Approved');
      $this->save();
  }

  function inprogress(){
    $this['status']='InProgress';
    $this->app->employee
    ->addActivity("Purchase Order No : '".$this['document_no']."' proceed for dispatching", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('markhascomplete,sendToStock','InProgress');
    $this->save();
  }

  function cancel(){
    $this['status']='Canceled';
    $this->app->employee
    ->addActivity("Purchase Order No : '".$this['document_no']."' canceled", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
    ->notifyWhoCan('delete','Canceled');
    $this->save();
  }

  function page_complete($page){

    $qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail');
    $qsp_detail->addCondition('qsp_master_id',$this->id);
    $qsp_detail->addExpression('received_qty')->set(function($m,$q){
      return $m->add('xepan\commerce\Model_Store_TransactionRow')
          ->addCondition('qsp_detail_id',$m->getElement('id'))
          ->sum('quantity');
    })->type('int');

    $form = $page->add('Form_Stacked',null,null,array('form/minimal'));
    $th = $form->add('Columns')->addClass('row');
    $th_name =$th->addColumn(4)->addClass('col-md-4');
    $th_name->add('H4')->set('Items');
    $th_qty =$th->addColumn(2)->addClass('col-md-2');
    $th_qty->add('H4')->set('Order Qty');
    $th_received_qty = $th->addColumn(2)->addClass('col-md-2');
    $th_received_qty->add('H4')->set('Pre Received Qty');
    $th_receive_qty = $th->addColumn(2)->addClass('col-md-2');
    $th_receive_qty->add('H4')->set('Receive Qty');
    $th_receive_qty = $th->addColumn(2)->addClass('col-md-2');
    $th_receive_qty->add('H4')->set('Warehouse');
    // $form = $page->add('Form');
    // $item = $this->add('xepan\commerce\Model_Item')->addCondition('id',$qsp_detail['item_id']);
    foreach ($qsp_detail as $oi) {
      $c = $form->add('Columns')->addClass('row');
      $c1 = $c->addColumn(4)->addClass('col-md-4');
      $c1->addField('line','item_name_'.$oi->id)->set($oi['name'])->setAttr('disabled','disabled');
      $array = json_decode($oi['extra_info']?:"[]",true);
      $cf_html = " ";
      // var_dump($array); 
      $v = $c1->add('View',null,null,['view\order\purchase\extrainfo']);
      $v->template->trySet('item_id',$oi->id);
      foreach ($array as $department_id => &$details) {
        $department_name = $details['department_name'];
        $cf_list = $v->add('CompleteLister',null,'extra_info',['view\order\purchase\extrainfo','extra_info']);
        $cf_list->template->trySet('department_name',$department_name);
        $cf_list->template->trySet('narration',$details['narration']);
        unset($details['department_name']);
        
        $cf_list->setSource($details);

        // $cf_html  .= $cf_list->getHtml();
      }
        $cf_html  .= $v->getHtml();

      if($cf_html != " "){
        $cf_html = "<br/>".$cf_html;
      }

      $c1->add('View')->setHTML($cf_html)->addClass('pocitem-extrainfo');

      $c2 = $c->addColumn(2)->addClass('col-md-2');
      $c2->addField('line','item_qty_'.$oi->id)->set($oi['quantity'])->setAttr('disabled','disabled');
      $c2->addField('hidden','item_qty_hidden'.$oi->id)->set($oi['quantity']);
      $c3 = $c->addColumn(2)->addClass('col-md-2');
      $c3->addField('line','item_received_before_qty_'.$oi->id)->set($oi['received_qty']?:0)->setAttr('disabled','disabled');
      $c3->addField('hidden','item_received_before_qty_hidden'.$oi->id)->set($oi['received_qty']?:0);
      $c4 = $c->addColumn(2)->addClass('col-md-2');
      $c4->addField('Number','item_received_qty_'.$oi->id)->set(0);
      $c4 = $c->addColumn(2)->addClass('col-md-2');
      $c4->addField('Dropdown','item_warehouse_'.$oi->id)->validate('required')->setModel('xepan\commerce\Store_Warehouse');
      
    }
    $form->addField('CheckBox','force_complete_order');
    $form->addSubmit('Complete Purchase Order')->addClass('btn btn-primary');

    if($form->isSubmitted()){

      $check = 1;
      $this['status']='Completed';

      // check validation
      foreach ($qsp_detail as $oi) {
        $qty_to_receive = $oi['quantity'] - $oi['received_qty'];
        
        if($form['item_received_qty_'.$oi->id] > $qty_to_receive){
            $form->displayError('item_received_qty_'.$oi->id,'received qty must be smaller then order qty');
        }
      }      

      foreach ($qsp_detail as $oi) {

        $warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['item_warehouse_'.$oi->id]);
        $transaction = $warehouse->newTransaction($this->id,$jobcard_id=null,$from_warehouse_id=$this['contact_id'],"Purchase",$department_id=null,$to_warehouse_id=$form['item_warehouse_'.$oi->id]);
        $transaction->addItem($oi->id,$item_id=$oi['item_id'],$form['item_received_qty_'.$oi->id],$jobcard_detail_id=null,$custom_field_combination=$oi->convertCustomFieldToKey(json_decode($oi['extra_info'],true)),$status="ToReceived");
        $total_received = $form['item_received_qty_'.$oi->id] + $form['item_received_before_qty_hidden'.$oi->id];
        
        if($form['force_complete_order']){
            $oi['quantity'] = $total_received;
            $oi->save();      
        }
        
        if($check && $total_received < $oi['quantity']){
          $check = 0;
          $this['status']='PartialComplete';
        }

      }

    $this->app->employee
      ->addActivity("Purchase Order No : '".$this['document_no']."' successfully Added to Warehouse", $this->id /*Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseorderdetail&document_id=".$this->id."")
      ->notifyWhoCan('delete,send','Completed');
    $this->save();
    $this->app->page_action_result = $form->js(null,$form->js()->closest('.dialog')->dialog('close'))->univ()->successMessage('Item Send To Warehouse');

    }

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
    $invoice['due_date'] = $this->app->now;
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

        $form->addField('Number','qty_'.$item_row->id,'qty')->set($item_row['quantity']);
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
                // var_dump(json_decode($item_row['extra_info']));
                $transaction[$form['warehouse_'.$item_row->id]]
                ->addItem($form['qsp_detail_'.$item_row->id],$item_row['item_id'],$form['qty_'.$item_row->id],null,$item_row['extra_info'],null);
            }
        }       
        $this['status']='PartialComplete';
        $this->app->employee
          ->addActivity("Purchase Order No : '".$this['document_no']."' related products successfully send to stock", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
          ->notifyWhoCan('markhascomplete,send','PartialComplete');
        $this->save();
        $this->app->page_action_result = $form->js(null,$form->js()->closest('.dialog')->dialog('close'))->univ()->successMessage('Item Send To Store');
        // $form->js()->univ()->successMessage('Item Send To Store')->closeDialog();
        return true;
    }

}

}
