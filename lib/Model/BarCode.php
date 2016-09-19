<?php

namespace xepan\commerce;

class Model_BarCode extends \xepan\base\Model_Table{
	public $table='dispatch_barcode';
	public $acl = false;
	// public $status = ['Active','DeActive'];
	// public $actions = [
	// 				'Active'=>['view','edit','delete','deactivate','used'],
	// 				'DeActive'=>['view','edit','delete','activate']
	// 			];

	function init(){
		parent::init();

		$this->addField('name')->caption('BarCode Number');
		$this->addField('is_used')->type('boolean');

		$this->addField('related_document_id');
		$this->addField('related_document_type');

		// $this->addField('status')->enum(['Active','DeActive'])->defaultValue('Active');
		// $this->addCondition('type','BarCode');
		$this->is([
				'name|required|unique'
				]);
	}

	//activate BarCode
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Bar Code '".$this['name']."' is now active, available for use", $this['related_document_id']/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_barcode")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	//deactivate BarCode
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Bar Code '".$this['name']."' is has been deactived, not available for use", $this['related_document_id']/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_barcode")
            ->notifyWhoCan('activate','InActive',$this);
		return $this->save();
	}

	//Used BarCode
	function used(){
		$this['status']='Used';
		$this->app->employee
            ->addActivity("Bar Code '".$this['name']."' have been used", $this['related_document_id']/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_barcode")
            ->notifyWhoCan('activate','InActive',$this);
		return $this->save();
	}
}

