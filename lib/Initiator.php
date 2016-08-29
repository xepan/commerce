<?php

namespace xepan\commerce;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_commerce';

	function init(){
		parent::init();
		$this->addAppRoundAmount();

		define ('K_PATH_FONTS', getcwd().'/vendor/xepan/commerce/templates/fonts/');
	}


	function setup_admin(){		
		$this->routePages('xepan_commerce');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('../vendor/xepan/commerce/');
		if(!$this->app->isAjaxOutput()){
			$m = $this->app->top_menu->addMenu('Commerce');
			$m->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_dashboard');
			$m->addItem(['Item Category','icon'=>'fa fa-sitemap'],'xepan_commerce_category');
			$m->addItem(['Item','icon'=>'fa fa-cart-plus'],$this->app->url('xepan_commerce_item',['status'=>'Published']));
			$m->addItem(['Customer','icon'=>'fa fa-male'],'xepan_commerce_customer');
			$m->addItem(['Supplier','icon'=>'fa fa-male'],'xepan_commerce_supplier');
			$m->addItem(['Quotation','icon'=>'fa fa-file-text-o'],'xepan_commerce_quotation');
			$m->addItem(['Sales Order','icon'=>'fa fa-pencil-square-o'],'xepan_commerce_salesorder');
			$m->addItem(['Sales Invoice','icon'=>'fa fa-list-ul'],'xepan_commerce_salesinvoice');
			$m->addItem(['Purchase Order','icon'=>'fa fa-pencil-square-o'],'xepan_commerce_purchaseorder');
			$m->addItem(['Purchase Invoice','icon'=>'fa fa-list-ul'],'xepan_commerce_purchaseinvoice');
			$m->addItem(['Discount Vouchers','icon'=>'fa fa-tag'],'xepan_commerce_discountvoucher');
			// $m->addItem(['Tax','icon'=>'fa fa-percent'],'xepan_commerce_tax');
			// $m->addItem(['Terms And Condition','icon'=>'fa fa-check-square'],'xepan_commerce_tnc');
			$m->addItem(['Lodgement Management','icon'=>'fa fa-adjust'],'xepan_commerce_lodgement');
			$m->addItem(['Configuration','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_customfield');

			/*Store Top Menu & Items*/
			$store = $this->app->top_menu->addMenu('Store');
			$store->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_store_dashboard');
			$store->addItem(['Warehouse','icon'=>'fa fa-building'],'xepan_commerce_store_warehouse');
			$store->addItem(['Stock Transaction','icon'=>'fa fa-random'],'xepan_commerce_store_transaction');
			$store->addItem(['Stock Item','icon'=>'fa fa-shopping-cart'],'xepan_commerce_store_item');
			$store->addItem(['Dispatch Request / Item','icon'=>'fa fa-truck'],'xepan_commerce_store_dispatchrequest');


			$this->app->status_icon["xepan\commerce\Model_Category"] = ['All'=>' fa fa-globe','Active'=>"fa fa-circle text-success",'InActive'=>'fa fa-circle text-danger'];
			$this->app->status_icon["xepan\commerce\Model_Item"] = ['All'=>' fa fa-globe','Published'=>"fa fa-file-text-o text-success",'UnPublished'=>'fa fa-file-o text-success'];
			$this->app->status_icon["xepan\commerce\Model_Customer"] = ['All'=>' fa fa-globe','Active'=>"fa fa-circle text-success",'InActive'=>'fa fa-circle text-danger'];
			$this->app->status_icon["xepan\commerce\Model_Supplier"] = ['All'=>' fa fa-globe','Active'=>"fa fa-circle text-success",'InActive'=>'fa fa-circle text-danger'];
			$this->app->status_icon["xepan\commerce\Model_Quotation"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Approved'=>'fa fa-thumbs-up text-success','Redesign'=>'fa fa-refresh ','Rejected'=>'fa fa-times text-danger','Converted'=>'fa fa-check text-success'];
			$this->app->status_icon["xepan\commerce\Model_SalesOrder"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Redesign'=>'fa fa-refresh ','Approved'=>'fa fa-thumbs-up text-success','InProgress'=>'fa fa-spinner','Canceled'=>'fa fa-ban text-danger','Completed'=>'fa fa-check text-success','Dispatched'=>'fa fa-truck ','OnlineUnpaid'=>'fa fa-globe text-danger'];
			$this->app->status_icon["xepan\commerce\Model_SalesInvoice"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Redesign'=>'fa fa-refresh ','Due'=>'fa fa-money text-danger','Paid'=>'fa fa-money text-success','Canceled'=>'fa fa-ban text-danger'];
			$this->app->status_icon["xepan\commerce\Model_PurchaseOrder"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Approved'=>'fa fa-thumbs-up text-success','InProgress'=>'fa fa-spinner','Redesign'=>'fa fa-refresh ','Canceled'=>'fa fa-ban text-danger','Rejected'=>'fa fa-times text-danger','PartialComplete'=>'fa  fa-hourglass-half text-warning','Completed'=>'fa fa-check text-success'];
			$this->app->status_icon["xepan\commerce\Model_PurchaseInvoice"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Redesign'=>'fa fa-refresh ','Due'=>'fa fa-money text-danger','Paid'=>'fa fa-money text-success','Canceled'=>'fa fa-ban text-danger'];
		}
		
		$search_itemcategory = $this->add('xepan\commerce\Model_Category');
		$this->app->addHook('quick_searched',[$search_itemcategory,'quickSearch']);
		return $this;
	}

	function setup_frontend(){
		$this->routePages('xepan_commerce');
			$this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>'templates/css'))
			->setBaseURL('./vendor/xepan/commerce/');

		if($this->app->isEditing){
			
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Cart','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Category','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_CategoryDetail','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Designer','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Filter','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_ItemList','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_ItemImage','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Item_Detail','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Checkout','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_MyAccount','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_Search','Commerce');
		}
		
		$customer=$this->add('xepan\commerce\Model_Customer');
		$this->app->addHook('userCreated',[$customer,'createNewCustomer']);
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
		$truncate_models = ['Store_TransactionRow','Store_Transaction','Store_Warehouse',
							'Item_Taxation_Association','Taxation',
							'Item_CustomField_Association','Item_Specification','Filter','Category',
							'Item_Image',
							'Designer_Image_Category','Designer_Images','Item_Template_Design','Item_Department_Association',
							'Item_CustomField_Value','Item_CustomField_Association','Item_Quantity_Set','CategoryItemAssociation','TNC',
							'QSP_Detail','QSP_Master','Item','Item_CustomField_Generic','Item_Department_Consumption','Customer','Supplier','PaymentGateway'];
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

        // orphan custome field associations
        $d = $this->app->db->dsql();
        $d->sql_templates['delete'] = "delete [table] from  [table] [join] [where]";
        $d->table('customfield_association')->where('item.id is null')->join('item',null,'left')->delete();

        // orphan custome field assos values
        $d = $this->app->db->dsql();
        $d->sql_templates['delete'] = "delete [table] from  [table] [join] [where]";
        $d->table('customfield_association')->where('customfield_generic.id is null')->join('customfield_generic',null,'left')->delete();

        $this->app->db->dsql()->table('designer_images')->where('epan_id',null)->delete();
        
		$this->app->epan=$this->app->new_epan;

	}

}
