<?php

namespace xepan\commerce;

class Model_DiscountVoucher extends \xepan\base\Model_Table{
	public $table='discount_voucher';
	public $status = ['Active','DeActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','condition','used'],
					'DeActive'=>['view','edit','delete','activate']
				];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Epan','epan_id');
		$this->addCondition('epan_id',$this->app->epan->id);

		$this->hasOne('xepan\base\Contact','created_by_id')->defaultValue($this->app->epan->id)->system(true);
		$this->hasOne('xepan\base\Contact','updated_by_id')->defaultValue($this->app->epan->id)->system(true);
		$this->hasOne('xepan\commerce\Category','on_category_id');

		$this->addField('name')->caption('Voucher Number');
		$this->addField('start_date')->caption('Strating Date')->type('date')->defaultValue(date('Y-m-d'))->mandatory(true);
		$this->addField('expire_date')->type('date');
		$this->addField('no_of_person')->type('Number')->defaultValue(1)->hint('how many person');
		$this->addField('one_user_how_many_time')->type('Number')->defaultValue(1);
		$this->addField('on')->setValueList(['price'=>"Price",'shipping'=>"Shipping"]);
		$this->addField('include_sub_category')->type('boolean');
		$this->addField('based_on')->setValueList([
							'weight'=>'Weight',
							'volume'=>'Volume',
							'quantity'=>'Quantity',
							'price'=>"Price",
							'gross_amount'=>"Gross Amount"
							]);

		$this->addField('created_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('updated_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);

		$this->hasMany('xepan/commerce/DiscountVoucherCondition','discountvoucher_id');
		$this->hasMany('xepan/commerce/DiscountVoucherUsed','discountvoucher_id');

		$this->addField('type');
		$this->addField('status')->enum(['Active','DeActive']);
		$this->addCondition('type','Discount_Voucher');
		
		$this->is([
				'name|to_trim|required|unique_in_epan',
				'start_date|required',
				'expire_date|required',
				'no_of_person|number|>0',
				'one_user_how_many_time|number|>0'
				]);

		$this->addHook('beforeDelete',$this);	
	}

	//activate Voucher
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Voucher '".$this['name']."' is now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	//deactivate Voucher
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Voucher '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('deactivate','Active',$this);
		return $this->save();
	}

	function beforeDelete($m){	
		if($m->ref('xepan/commerce/DiscountVoucherUsed')->count()->getOne())
			throw new \Exception("First Delete the Related Orders/Invoices");
  	}

  	function discountVoucherUsed($discount_voucher){

		$this->addCondition('name',$discount_voucher);
		$this->tryLoadAny();
		$discountvoucherused=$this->add('xepan/commerce/Model_DiscountVoucherUsed');
		$discountvoucherused['contact_id']=$this->app->auth->model->id;
		$discountvoucherused['discountvoucher_id']=$this['id'];
		$discountvoucherused['qsp_master_id']=$this[''];
		$discountvoucherused->save();
	}


  	function isVoucherExpired(){
		$current_date = $this->app->now;
		if( strtotime($current_date) > strtotime($this['expire_date']))
			return true;
		else
			return false;
	}

	function isVoucherUsable($voucher_no){
		$voucher=$this->add('xepan/commerce/Model_DiscountVoucher');
		$voucher->addCondition('name',$voucher_no);
		$voucher->tryLoadAny();
		if(!$voucher->loaded()){
			return "coupon not found";
		}
		// if voucher expired then give error message
		if($voucher->isVoucherExpired()){
			return "coupon expired";
		}
	 	// if voucher is not expired, how many used it
		else{
			$person_used = $voucher->ref('xepan/commerce/DiscountVoucherUsed')->count()->getOne();
			if($voucher['no_of_person'] > $person_used){
				return true;
			}
			// if no of allowed person already consumed it then, error message 
			else{
				return "coupon limit number of person exit";
			}
							
		}
	}

	function page_condition($page){
		if(!$this->loaded())
			throw new \Exception("model discount voucher must loaded");
		
		$condition_model = $page->add('xepan\commerce\Model_DiscountVoucherCondition');
	    $condition_model->addCondition('discountvoucher_id',$this->id);
	    $crud = $page->add('xepan\hr\CRUD');
	    $crud->setModel($condition_model);
	}
	
	function page_used($page){
		if(!$this->loaded())
			throw new \Exception("model discount voucher must loaded");

	    $used = $page->add('xepan\commerce\Model_DiscountVoucherUsed');
	    $used->addCondition('discountvoucher_id',$this->id);
	    $crud = $page->add('xepan\hr\CRUD');
	    $crud->setModel($used);
	}

}

