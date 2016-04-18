<?php

namespace xepan\commerce;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_commerce';

	function init(){
		parent::init();
		
		if($this->app->is_admin){
			$m = $this->app->top_menu->addMenu('Commerce');
			$m->addItem(['Item Category','icon'=>'fa fa-cubes'],'xepan_commerce_category');
			$m->addItem(['Item','icon'=>'fa fa-cube'],'xepan_commerce_item');
			$m->addItem(['Customer','icon'=>'fa fa-male'],'xepan_commerce_customer');
			$m->addItem(['Supplier','icon'=>'fa fa-eye'],'xepan_commerce_supplier');
			$m->addItem(['Quotation','icon'=>'fa fa-copy'],'xepan_commerce_quotation');
			$m->addItem(['Sales Order','icon'=>'fa fa-edit'],'xepan_commerce_salesorder');
			$m->addItem(['Sales Invoice','icon'=>'fa fa-list'],'xepan_commerce_salesinvoice');
			$m->addItem(['Purchase Order','icon'=>'fa fa-edit'],'xepan_commerce_purchaseorder');
			$m->addItem(['Purchase Invoice','icon'=>'fa fa-list'],'xepan_commerce_purchaseinvoice');
			$m->addItem(['Configuration','icon'=>'fa fa-anchor'],'xepan_commerce_customfield');
			$m->addItem(['Tax','icon'=>'fa fa-money'],'xepan_commerce_tax');
			$m->addItem(['Terms And Condition','icon'=>'fa fa-check'],'xepan_commerce_tnc');
			$m->addItem(['Lodgement Management','icon'=>'fa fa-database'],'xepan_commerce_lodgement');

			/*Store Top Menu & Items*/
			$store = $this->app->top_menu->addMenu('Store');
			$store->addItem(['Warehouse','icon'=>'fa fa-database'],'xepan_commerce_store_warehouse');
			$store->addItem(['Stock Transaction','icon'=>'fa fa-th-large'],'xepan_commerce_store_transaction');
			$store->addItem(['Stock Item','icon'=>'fa fa-behance-square'],'xepan_commerce_store_item');
			$store->addItem(['Dispatch Request / Item','icon'=>'fa fa-rocket'],'xepan_commerce_store_dispatchrequest');
			
			$this->routePages('xepan_commerce');
			$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
			->setBaseURL('../vendor/xepan/commerce/');
		}else{
			$this->routePages('xepan_commerce');
			$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
			->setBaseURL('./vendor/xepan/commerce/');
		}

		$this->addAppRoundAmount();
	}

	function addAppRoundAmount(){

		$this->app->addMethod('round',function($app,$amount,$digit_after_decimal=2){
			
			return number_format($amount,2);
		});
	}

	function resetDB(){
		// Clear DB
		$this->app->epan=$this->app->old_epan;
		$truncate_models = ['Store_TransactionRow','Store_Transaction','Store_Warehouse','Store_TransactionRow',
							'Item_Taxation_Association','Taxation',
							'Item_CustomField_Association','Item_Specification','Filter','Category',
							'Item_Image',
							'Designer_Image_Category','Designer_Images','Item_Template_Design','Item_Department_Association',
							'Item_CustomField_Value','Item_CustomField_Association','Item_Quantity_Set','CategoryItemAssociation','TNC',
							'Item','QSP_Detail','QSP_Master'];
        foreach ($truncate_models as $t) {
            $m=$this->add('xepan\commerce\Model_'.$t);
            foreach ($m as $mt) {
                $mt->delete();
            }
        }
		$this->app->epan=$this->app->new_epan;

	}

}
