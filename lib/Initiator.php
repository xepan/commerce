<?php

namespace xepan\commerce;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_commerce';

	function init(){
		parent::init();
		$this->addAppRoundAmount();
	}


	function setup_admin(){		
		if($this->app->is_admin){
			$m = $this->app->top_menu->addMenu('Commerce');
			$m->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_commercedashboard');
			$m->addItem(['Item Category','icon'=>'fa fa-sitemap'],'xepan_commerce_category');
			$m->addItem(['Item','icon'=>'fa fa-cart-plus'],'xepan_commerce_item');
			$m->addItem(['Customer','icon'=>'fa fa-male'],'xepan_commerce_customer');
			$m->addItem(['Supplier','icon'=>'fa fa-male'],'xepan_commerce_supplier');
			$m->addItem(['Quotation','icon'=>'fa fa-file-text-o'],'xepan_commerce_quotation');
			$m->addItem(['Sales Order','icon'=>'fa fa-pencil-square-o'],'xepan_commerce_salesorder');
			$m->addItem(['Sales Invoice','icon'=>'fa fa-list-ul'],'xepan_commerce_salesinvoice');
			$m->addItem(['Purchase Order','icon'=>'fa fa-pencil-square-o'],'xepan_commerce_purchaseorder');
			$m->addItem(['Purchase Invoice','icon'=>'fa fa-list-ul'],'xepan_commerce_purchaseinvoice');
			$m->addItem(['Configuration','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_customfield');
			$m->addItem(['Tax','icon'=>'fa fa-percent'],'xepan_commerce_tax');
			$m->addItem(['Terms And Condition','icon'=>'fa fa-check-square'],'xepan_commerce_tnc');
			$m->addItem(['Lodgement Management','icon'=>'fa fa-adjust'],'xepan_commerce_lodgement');

			/*Store Top Menu & Items*/
			$store = $this->app->top_menu->addMenu('Store');
			$store->addItem(['Warehouse','icon'=>'fa fa-building'],'xepan_commerce_store_warehouse');
			$store->addItem(['Stock Transaction','icon'=>'fa fa-random'],'xepan_commerce_store_transaction');
			$store->addItem(['Stock Item','icon'=>'fa fa-shopping-cart'],'xepan_commerce_store_item');
			$store->addItem(['Dispatch Request / Item','icon'=>'fa fa-truck'],'xepan_commerce_store_dispatchrequest');
			
			$this->routePages('xepan_commerce');
			$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
			->setBaseURL('../vendor/xepan/commerce/');
		}
		return $this;
		// $lodgement = $this->add('xepan\commerce\Model_Lodgement');
		// $this->app->addHook('deleteTransactionRow',[$lodgement,'deleteLodgement']);
	}

	function setup_frontend(){
		$this->routePages('xepan_commerce');
			$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
			->setBaseURL('./vendor/xepan/commerce/');

		return $this;
	}

	function deleteLodgement(){
		$lodgement = $this->add('xepan\commerce\Model_Lodgement');
		foreach ($deletelodgement as $lodgement) {
			
			$id = $deletelodgement['account_transaction_id'];
		}
		if($id==null){
			throw new \Exception("Error Processing Request", 1);
			
		}
	}

	function addAppRoundAmount(){

		$this->app->addMethod('round',function($app,$amount,$digit_after_decimal=2){
			
			return number_format($amount,2);
		});
	}

	function resetDB(){
		// Clear DB

		if(!isset($this->app->old_epan)) $this->app->old_epan = $this->app->epan;
        if(!isset($this->app->new_epan)) $this->app->new_epan = $this->app->epan;
        
		$this->app->epan=$this->app->old_epan;
		$truncate_models = ['Store_TransactionRow','Store_Transaction','Store_Warehouse','Store_TransactionRow',
							'Item_Taxation_Association','Taxation',
							'Item_CustomField_Association','Item_Specification','Filter','Category',
							'Item_Image',
							'Designer_Image_Category','Designer_Images','Item_Template_Design','Item_Department_Association',
							'Item_CustomField_Value','Item_CustomField_Association','Item_Quantity_Set','CategoryItemAssociation','TNC',
							'QSP_Detail','QSP_Master','Item','Item_CustomField_Generic','Item_Department_Consumption','Customer','Supplier'];
        foreach ($truncate_models as $t) {
            $m=$this->add('xepan\commerce\Model_'.$t);
            foreach ($m as $mt) {
                $mt->delete();
            }
        }

        // orphan items
        $d = $this->app->db->dsql();
        $d->sql_templates['delete'] = "delete [table] from  [table] [join] [where]";
        $d->table('item')->where('document.id is null')->join('document',null,'left')->delete();

        $this->app->db->dsql()->table('designer_images')->where('epan_id',null)->delete();
        
		$this->app->epan=$this->app->new_epan;

	}

}
