<?php

namespace xepan\commerce;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_commerce';

	function init(){
		parent::init();
		$this->addAppfunction();

		define ('K_PATH_FONTS', getcwd().'/vendor/xepan/commerce/templates/fonts/');
	}


	function setup_admin(){		
		$this->routePages('xepan_commerce');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('../vendor/xepan/commerce/');
		if(!$this->app->isAjaxOutput() && !$this->app->getConfig('hidden_xepan_commerce',false)){
			$m = $this->app->top_menu->addMenu('Commerce');
			// $m->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_dashboard');
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
			$m->addItem(['Warehouse Material Management','icon'=>'fa fa-building'],'xepan_commerce_store_warehouse');
			$m->addItem(['Stock Transaction','icon'=>'fa fa-random'],'xepan_commerce_store_transaction');
			$m->addItem(['Stock Item','icon'=>'fa fa-shopping-cart'],'xepan_commerce_store_item');
			$m->addItem(['Dispatch Request / Item','icon'=>'fa fa-truck'],'xepan_commerce_store_dispatchrequest');
			$m->addItem(['Bar Code List','icon'=>'fa fa-barcode'],'xepan_commerce_barcode');
			$m->addItem(['Store Activities','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_store_activity_all');
			$m->addItem(['Commerce Reports','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_reports_customer');
			$m->addItem(['Store Reports','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_store_reports_itemstock');
			$m->addItem(['Configuration','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_customfield');

			/*Store Top Menu & Items*/
			// $store->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_store_dashboard');

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
		$logment_m = $this->add('xepan\commerce\Model_Lodgement');
		$this->app->addHook('deleteTransaction',[$logment_m,'transactionRemoved']);
		$this->app->addHook('widget_collection',[$this,'exportWidgets']);
        $this->app->addHook('entity_collection',[$this,'exportEntities']);
        $this->app->addHook('sef-config-form-layout',[$this,'sefConfigFormLayout']);
        $this->app->addHook('sef-config-form',[$this,'sefConfigForm']);
		// $purchase_inv = $this->add('xepan\commerce\Model_PurchaseInvoice');
		// $this->app->addHook('deleteTransaction',[$purchase_inv,'transactionRemoved']);
		
		return $this;
	}

	function exportWidgets($app,&$array){
        $array[] = ['xepan\commerce\Widget_FavouriteItem','level'=>'Global','title'=>'Favourite Items'];
        $array[] = ['xepan\commerce\Widget_UnpaidInvoices','level'=>'Global','title'=>'Unpaid Invoice Count'];
        $array[] = ['xepan\commerce\Widget_OnlineUnpaidOrders','level'=>'Global','title'=>'Online Unpaid Orders Count'];
        $array[] = ['xepan\commerce\Widget_OnlineUnpaidCustomer','level'=>'Global','title'=>'Online Unpaid Customer Count'];
        $array[] = ['xepan\commerce\Widget_DueInvoices','level'=>'Global','title'=>'Due Invoices'];
        $array[] = ['xepan\commerce\Widget_DueOrders','level'=>'Global','title'=>'Due Orders'];       
        $array[] = ['xepan\commerce\Widget_SaleOrderStatus','level'=>'Global','title'=>'Sale Order Count'];
        $array[] = ['xepan\commerce\Widget_SaleInvoiceStatus','level'=>'Global','title'=>'Sale Invoice Count'];
        $array[] = ['xepan\commerce\Widget_PurchaseOrderStatus','level'=>'Global','title'=>'Purchase Order Count'];
        $array[] = ['xepan\commerce\Widget_PurchaseInvoiceStatus','level'=>'Global','title'=>'Purchase Invoice Count'];
        $array[] = ['xepan\commerce\Widget_MonthlyInvoices','level'=>'Global','title'=>'Monthly Invoice Count'];
    }

    function exportEntities($app,&$array){
    	$array['Customer'] = ['caption'=>'Customer','type'=>'DropDown','model'=>'xepan\commerce\Model_Customer'];
    	$array['Supplier'] = ['caption'=>'Supplier','type'=>'DropDown','model'=>'xepan\commerce\Model_Supplier'];
    	$array['Quotation'] = ['caption'=>'Quotation','type'=>'DropDown','model'=>'xepan\commerce\Model_Quotation'];
    	$array['Category'] = ['caption'=>'Category','type'=>'DropDown','model'=>'xepan\commerce\Model_Category'];
    	$array['Item'] = ['caption'=>'Item','type'=>'DropDown','model'=>'xepan\commerce\Model_Item'];
    	$array['TNC'] = ['caption'=>'TNC','type'=>'DropDown','model'=>'xepan\commerce\Model_TNC'];
    	$array['Item_Specification'] = ['caption'=>'Item_Specification','type'=>'DropDown','model'=>'xepan\commerce\Model_Item_Specification'];
    	$array['SalesOrder'] = ['caption'=>'SalesOrder','type'=>'DropDown','model'=>'xepan\commerce\Model_SalesOrder'];
    	$array['SalesInvoice'] = ['caption'=>'SalesInvoice','type'=>'DropDown','model'=>'xepan\commerce\Model_SalesInvoice'];
    	$array['PurchaseOrder'] = ['caption'=>'PurchaseOrder','type'=>'DropDown','model'=>'xepan\commerce\Model_PurchaseOrder'];
    	$array['PurchaseInvoice'] = ['caption'=>'PurchaseInvoice','type'=>'DropDown','model'=>'xepan\commerce\Model_PurchaseInvoice'];
    	$array['Item_CustomField'] = ['caption'=>'Item_CustomField','type'=>'DropDown','model'=>'xepan\commerce\Model_Item_CustomField'];
    	$array['Taxation'] = ['caption'=>'Taxation','type'=>'DropDown','model'=>'xepan\commerce\Model_Taxation'];
    	$array['TaxationRule'] = ['caption'=>'TaxationRule','type'=>'DropDown','model'=>'xepan\commerce\Model_TaxationRule'];
    	$array['ShippingRule'] = ['caption'=>'ShippingRule','type'=>'DropDown','model'=>'xepan\commerce\Model_Model_ShippingRule'];
    	$array['Store_Transaction'] = ['caption'=>'Store_Transaction','type'=>'DropDown','model'=>'xepan\commerce\Model_Store_Transaction'];
    	$array['Store_DispatchRequest'] = ['caption'=>'Store_DispatchRequest','type'=>'DropDown','model'=>'xepan\commerce\Model_Store_DispatchRequest'];
    	$array['QUOTATION_LAYOUT'] = ['caption'=>'QUOTATION_LAYOUT','type'=>'DropDown','model'=>'xepan\commerce\Model_QUOTATION_LAYOUT'];
    	$array['SALESORDER_LAYOUT'] = ['caption'=>'SALESORDER_LAYOUT','type'=>'DropDown','model'=>'xepan\commerce\Model_SALESORDER_LAYOUT'];
    	$array['SALESINVOICE_LAYOUT'] = ['caption'=>'SALESINVOICE_LAYOUT','type'=>'DropDown','model'=>'xepan\commerce\Model_SALESINVOICE_LAYOUT'];
    	$array['PURCHASEORDER_LAYOUT'] = ['caption'=>'PURCHASEORDER_LAYOUT','type'=>'DropDown','model'=>'xepan\commerce\Model_PURCHASEORDER_LAYOUT'];
    	$array['PURCHASEINVOICE_LAYOUT'] = ['caption'=>'PURCHASEINVOICE_LAYOUT','type'=>'DropDown','model'=>'xepan\commerce\Model_PURCHASEINVOICE_LAYOUT'];
    	$array['CHALLAN_LAYOUT'] = ['caption'=>'CHALLAN_LAYOUT','type'=>'DropDown','model'=>'xepan\commerce\Model_CHALLAN_LAYOUT'];
    	$array['COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG'] = ['caption'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG','type'=>'DropDown','model'=>'xepan\commerce\Model_COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG'];
    	$array['CustomerCredit'] = ['caption'=>'CustomerCredit','type'=>'DropDown','model'=>'xepan\commerce\Model_CustomerCredit'];
    	$array['Warehouse'] = ['caption'=>'Warehouse','type'=>'DropDown','model'=>'xepan\commerce\Model_Warehouse'];
    	$array['UnitGroup'] = ['caption'=>'UnitGroup','type'=>'DropDown','model'=>'xepan\commerce\Model_UnitGroup'];
    	$array['Unit'] = ['caption'=>'Unit','type'=>'DropDown','model'=>'xepan\commerce\Model_Unit'];
    	$array['UnitConversion'] = ['caption'=>'UnitConversion','type'=>'DropDown','model'=>'xepan\commerce\Model_UnitConversion'];
    	$array['DiscountVoucher'] = ['caption'=>'DiscountVoucher','type'=>'DropDown','model'=>'xepan\commerce\Model_DiscountVoucher'];
    }

    function setup_pre_frontend(){
    	$this->app->addHook('sef-router',[$this,'addSEFRouter']);
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
			$this->app->exportFrontEndTool('xepan\commerce\Tool_FreelancerListing','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_FreelancerCategory','Commerce');
		}

		$this->app->addHook('cron_executor',function($app){
			
			$now = \DateTime::createFromFormat('Y-m-d H:i:s', $this->app->now);
			echo "Running Cron in Commerce <br/>";
			var_dump($now);

			$job2 = new \Cron\Job\ShellJob();
			$job2->setSchedule(new \Cron\Schedule\CrontabSchedule('0 0 * * *'));
			if(!$job2->getSchedule() || $job2->getSchedule()->valid($now)){	
				echo " Executing Recurring Invoice code <br/>";
				$this->add('xepan\commerce\Controller_GenerateRecurringInvoice')->run();
			}

		});
		
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

	function addAppfunction(){

		$this->app->addMethod('round',function($app,$amount,$digit_after_decimal=2){
			
			$round_amount_standard = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'round_amount_standard'=>'DropDown'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
			$round_amount_standard->tryLoadAny();

			switch($round_amount_standard['round_amount_standard']){
	         case 'Standard' :
	            $rounded_net_amount =  round($amount);
	            break; 
	         case 'Up' :
	            $rounded_net_amount =  ceil($amount);
	            break; 
	         case 'Down' :
	            $rounded_net_amount =  floor($amount);
	            break;
	         default: 
	         	$rounded_net_amount = $amount;
	        }
			return number_format($rounded_net_amount,2);
		});



		// $item_qty_unit_id = $to_become_unit_id
		// $qsp_detail_item_unit_id = $one_of_unit_id
		$this->app->addMethod('getUnitMultiplier',function($app,$to_become_unit_id,$one_of_unit_id){
			if($to_become_unit_id == $one_of_unit_id)
				return 1;
			
			$uc_model = $this->add('xepan\commerce\Model_UnitConversion');
			$uc_model->addCondition('to_become_id',$to_become_unit_id);
			$uc_model->addCondition('one_of_id',$one_of_unit_id);
			// $uc_model->addCondition('one_of_unit_group_id',$item_unit_group_id);
			// $uc_model->addCondition('to_become_unit_group_id',$item_unit_group_id);
			$uc_model->tryLoadAny();
			if(!$uc_model->loaded()){
				throw new \Exception("unit conversion not found", 1);
			}
			return $uc_model['multiply_with'];
		});

		$this->app->addMethod('getConvertedQty',function($app,$to_become_unit_id,$one_of_unit_id,$qty){
			return $qty * $this->app->getUnitMultiplier($to_become_unit_id,$one_of_unit_id);
		});
	}
	
	function resetDB(){
		// Clear DB

		// if(!isset($this->app->old_epan)) $this->app->old_epan = $this->app->epan;
  //       if(!isset($this->app->new_epan)) $this->app->new_epan = $this->app->epan;
        
		// $this->app->epan=$this->app->old_epan;
		// $truncate_models = ['Store_TransactionRow','Store_Transaction','Store_Warehouse',
		// 					'Item_Taxation_Association','Taxation',
		// 					'Item_CustomField_Association','Item_Specification','Filter','Category',
		// 					'Item_Image',
		// 					'Designer_Image_Category','Designer_Images','Item_Template_Design','Item_Department_Association',
		// 					'Item_CustomField_Value','Item_CustomField_Association','Item_Quantity_Set','CategoryItemAssociation','TNC',
		// 					'QSP_Detail','QSP_Master','Item','Item_CustomField_Generic','Item_Department_Consumption','Customer','Supplier','PaymentGateway'];
  //       foreach ($truncate_models as $t) {
  //           $m=$this->add('xepan\commerce\Model_'.$t);
  //           foreach ($m as $mt) {
  //               $mt->delete();
  //           }
  //       }

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
        
		// $this->app->epan=$this->app->new_epan;

	}

	function sefConfigForm($app,$form, $values){
		$form->addField('commerce_category_detail_page')->setFieldHint('Commerce Category Detail Page in front website')
			->set($values['commerce_category_detail_page']);
		$form->addField('commerce_product_list_page')->setFieldHint('Commerce Product List Page in front website')
			->set($values['commerce_product_list_page']);
		$form->addField('commerce_product_detail_page')->setFieldHint('Commerce Product Detail Page in front website')
			->set($values['commerce_product_detail_page']);
		$form->addField('commerce_freelancer_list_page')->setFieldHint('Commerce FreeLancer List Page in front website')
			->set($values['commerce_freelancer_list_page']);

	}

	function sefConfigFormLayout($app,&$layout){
		$layout ['commerce_category_detail_page']='Commerce~c2~3'; 
		$layout ['commerce_product_list_page']='c3~3'; 
		$layout ['commerce_product_detail_page']='c4~3'; 	
		$layout ['commerce_freelancer_list_page']='c5~3'; 	
	}

	function addSEFRouter($app, $value){
		$this->app->app_router->addRule($value['commerce_category_detail_page']."\/(.*)", $value['commerce_category_detail_page'], ['xsnb_category_sef_url']);
		$this->app->app_router->addRule($value['commerce_product_list_page']."\/(.*)", $value['commerce_product_list_page'], ['xsnb_category_sef_url']);
		$this->app->app_router->addRule($value['commerce_product_detail_page']."\/(.*)", $value['commerce_product_detail_page'], ['commerce_item_slug_url']);
		$this->app->app_router->addRule($value['commerce_freelancer_list_page']."\/(.*)", $value['commerce_freelancer_list_page'], ['freelancercategory_slug_url']);
	}

}
