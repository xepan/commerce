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

		if($this->app->inConfigurationMode)
            $this->populateConfigurationMenus();
        else
            $this->populateApplicationMenus();

		$this->app->status_icon["xepan\commerce\Model_Category"] = ['All'=>' fa fa-globe','Active'=>"fa fa-circle text-success",'InActive'=>'fa fa-circle text-danger'];
		$this->app->status_icon["xepan\commerce\Model_Item"] = ['All'=>' fa fa-globe','Published'=>"fa fa-file-text-o text-success",'UnPublished'=>'fa fa-file-o text-success'];
		$this->app->status_icon["xepan\commerce\Model_Customer"] = ['All'=>' fa fa-globe','Active'=>"fa fa-circle text-success",'InActive'=>'fa fa-circle text-danger'];
		$this->app->status_icon["xepan\commerce\Model_Supplier"] = ['All'=>' fa fa-globe','Active'=>"fa fa-circle text-success",'InActive'=>'fa fa-circle text-danger'];
		$this->app->status_icon["xepan\commerce\Model_Quotation"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Approved'=>'fa fa-thumbs-up text-success','Redesign'=>'fa fa-refresh ','Rejected'=>'fa fa-times text-danger','Converted'=>'fa fa-check text-success'];
		$this->app->status_icon["xepan\commerce\Model_SalesOrder"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Redesign'=>'fa fa-refresh ','Approved'=>'fa fa-thumbs-up text-success','InProgress'=>'fa fa-spinner','Canceled'=>'fa fa-ban text-danger','Completed'=>'fa fa-check text-success','Dispatched'=>'fa fa-truck ','OnlineUnpaid'=>'fa fa-globe text-danger'];
		$this->app->status_icon["xepan\commerce\Model_SalesInvoice"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Redesign'=>'fa fa-refresh ','Due'=>'fa fa-money text-danger','Paid'=>'fa fa-money text-success','Canceled'=>'fa fa-ban text-danger'];
		$this->app->status_icon["xepan\commerce\Model_PurchaseOrder"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Approved'=>'fa fa-thumbs-up text-success','InProgress'=>'fa fa-spinner','Redesign'=>'fa fa-refresh ','Canceled'=>'fa fa-ban text-danger','Rejected'=>'fa fa-times text-danger','PartialComplete'=>'fa  fa-hourglass-half text-warning','Completed'=>'fa fa-check text-success'];
		$this->app->status_icon["xepan\commerce\Model_PurchaseInvoice"] = ['All'=>'fa fa-globe','Draft'=>"fa fa-sticky-note-o ",'Submitted'=>'fa fa-check-square-o text-primary','Redesign'=>'fa fa-refresh ','Due'=>'fa fa-money text-danger','Paid'=>'fa fa-money text-success','Canceled'=>'fa fa-ban text-danger'];
		
		$search_itemcategory = $this->add('xepan\commerce\Model_Category');
		$this->app->addHook('quick_searched',[$search_itemcategory,'quickSearch']);
		$logment_m = $this->add('xepan\commerce\Model_Lodgement');
		$this->app->addHook('deleteTransaction',[$logment_m,'transactionRemoved']);
		$this->app->addHook('widget_collection',[$this,'exportWidgets']);
        $this->app->addHook('entity_collection',[$this,'exportEntities']);
        $this->app->addHook('sef-config-form-layout',[$this,'sefConfigFormLayout']);
        $this->app->addHook('sef-config-form',[$this,'sefConfigForm']);
        $this->app->addHook('collect_shortcuts',[$this,'collect_shortcuts']);
		// $purchase_inv = $this->add('xepan\commerce\Model_PurchaseInvoice');
		// $this->app->addHook('deleteTransaction',[$purchase_inv,'transactionRemoved']);
		
		return $this;
	}

	function populateConfigurationMenus(){
		$m = $this->app->top_menu->addMenu('Commerce');
		$m->addItem(['Tax & Tax Rule Configurations','icon'=>'fa fa-percent'],$this->app->url('xepan_commerce_tax'));
		$m->addItem(['Custom Fields','icon'=>'fa fa-cog'],$this->app->url('xepan_commerce_customfield'));
		$m->addItem(['Specifications','icon'=>'fa fa-magic xepan-effect-yellow'],$this->app->url('xepan_commerce_specification'));
		$m->addItem(['Payment Gate Ways','icon'=>'fa fa-cc-mastercard'],$this->app->url('xepan_commerce_paymentgateway'));
		$m->addItem(['Layouts','icon'=>'fa fa-th'],$this->app->url('xepan_commerce_layouts'));
		$m->addItem(['Designer Library','icon'=>'fa fa-th'],$this->app->url('xepan_commerce_designerlibraryimages'));
		$m->addItem(['Fonts','icon'=>'fa fa-font'],$this->app->url('xepan_commerce_font'));
		$m->addItem(['Terms And Condition','icon'=>'fa fa-check-square'],$this->app->url('xepan_commerce_tnc'));
		$m->addItem(['Shipping Rule','icon'=>'fa fa-truck'],$this->app->url('xepan_commerce_shippingrule'));
		$m->addItem(['Amount Standard','icon'=>'fa fa-dollar'],$this->app->url('xepan_commerce_amountstandard'));
		$m->addItem(['Customer Credits','icon'=>'fa fa-dollar'],$this->app->url('xepan_commerce_customercredit'));
		$m->addItem(['Freelancer Category','icon'=>'fa fa-users'],$this->app->url('xepan_commerce_freelancategory'));
		$m->addItem(['Unit Conversion','icon'=>'fa fa-exchange'],$this->app->url('xepan_commerce_unit'));
		$m->addItem(['Warehouse Management','icon'=>'fa fa-building'],$this->app->url('xepan_commerce_warehousemanagment'));
		$m->addItem(['QSP Config','icon'=>'fa fa-building'],$this->app->url('xepan_commerce_qspconfig'));
		$m->addItem(['Store Config','icon'=>'fa fa-building'],$this->app->url('xepan_commerce_storeconfig'));

	}

	function populateApplicationMenus(){
		if(!$this->app->isAjaxOutput() && !$this->app->getConfig('hidden_xepan_commerce',false)){
			// $m = $this->app->top_menu->addMenu('Commerce');
			// $m->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_dashboard');
			// $m->addItem(['Item Category','icon'=>'fa fa-sitemap'],'xepan_commerce_category');
			// $m->addItem(['Item','icon'=>'fa fa-cart-plus'],$this->app->url('xepan_commerce_item',['status'=>'Published']));
			// $m->addItem(['Customer','icon'=>'fa fa-male'],'xepan_commerce_customer');
			// $m->addItem(['Supplier','icon'=>'fa fa-male'],'xepan_commerce_supplier');
			// $m->addItem(['Quotation','icon'=>'fa fa-file-text-o'],'xepan_commerce_quotation');
			// $m->addItem(['Sales Order','icon'=>'fa fa-pencil-square-o'],'xepan_commerce_salesorder');
			// $m->addItem(['Sales Invoice','icon'=>'fa fa-list-ul'],'xepan_commerce_salesinvoice');
			// $m->addItem(['Purchase Order','icon'=>'fa fa-pencil-square-o'],'xepan_commerce_purchaseorder');
			// $m->addItem(['Purchase Invoice','icon'=>'fa fa-list-ul'],'xepan_commerce_purchaseinvoice');
			// $m->addItem(['Discount Vouchers','icon'=>'fa fa-tag'],'xepan_commerce_discountvoucher');
			// // $m->addItem(['Warehouse Material Management','icon'=>'fa fa-building'],'xepan_commerce_store_warehouse');
			// // $m->addItem(['Stock Transaction','icon'=>'fa fa-random'],'xepan_commerce_store_transaction');
			// $m->addItem(['Stock Item','icon'=>'fa fa-shopping-cart'],'xepan_commerce_store_item');
			// $m->addItem(['Dispatch Request / Item','icon'=>'fa fa-truck'],'xepan_commerce_store_dispatchrequest');
			// $m->addItem(['Bar Code List','icon'=>'fa fa-barcode'],'xepan_commerce_barcode');
			// $m->addItem(['Store Activities','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_store_activity_all');
			// $m->addItem(['Commerce Reports','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_reports_customer');
			// // $m->addItem(['Store Reports','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_store_reports_itemstock');
			// $m->addItem(['Configuration','icon'=>'fa fa-cog fa-spin'],'
			// 	xepan_commerce_customfield');
			// $m->addItem(['Wishlist','icon'=>'fa fa-heart'],$this-> app-> url('xepan_commerce_wishlist'));
			// $m->addItem(['Review','icon'=>'fa fa-star'],$this->app->url('xepan_commerce_review'));
			

			// $this->app->user_menu->addItem(['My Stocks','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_mystocks');
			/*Store Top Menu & Items*/
			// $store->addItem(['Dashboard','icon'=>'fa fa-dashboard'],'xepan_commerce_store_dashboard');

			// $this->app->report_menu->addItem(['Employee Comm. & Sales Report','icon'=>'fa fa-users'],'xepan_commerce_reports_salesreport');

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
        $this->app->addHook('collect_shortcuts',[$this,'collect_shortcuts']);
		// $purchase_inv = $this->add('xepan\commerce\Model_PurchaseInvoice');
		// $this->app->addHook('deleteTransaction',[$purchase_inv,'transactionRemoved']);
		
		return $this;
	}

	function getTopApplicationMenu(){
		if($this->app->getConfig('hidden_xepan_commerce',false)){return [];}

        return [
                'Commerce'=>[
            		[ 
            			'name'=>'Item Category',
            			'icon'=>'fa fa-sitemap',
            			'url'=>'xepan_commerce_category'
            		],
					[	'name'=>'Item',
						'icon'=>'fa fa-cart-plus',
						'url'=>'xepan_commerce_item',
						'url_param'=>['status'=>'Published']
					],
					[	'name'=>'Customer',
						'icon'=>'fa fa-male',
						'url'=>'xepan_commerce_customer'
					],
					[	'name'=>'Supplier',
						'icon'=>'fa fa-male',
						'url'=>'xepan_commerce_supplier'
					],
					[	'name'=>'Quotation',
						'icon'=>'fa fa-file-text-o',
						'url'=>'xepan_commerce_quotation'
					],
					[	'name'=>'Sales Order',
						'icon'=>'fa fa-pencil-square-o',
						'url'=>'xepan_commerce_salesorder'
					],
					[	'name'=>'Sales Invoice',
						'icon'=>'fa fa-list-ul',
						'url'=>'xepan_commerce_salesinvoice'
					],
					[	'name'=>'Purchase Order',
						'icon'=>'fa fa-pencil-square-o',
						'url'=>'xepan_commerce_purchaseorder'
					],
					[	'name'=>'Purchase Invoice',
						'icon'=>'fa fa-list-ul',
						'url'=>'xepan_commerce_purchaseinvoice'
					],
					[	'name'=>'Discount Vouchers',
						'icon'=>'fa fa-tag',
						'url'=>'xepan_commerce_discountvoucher'
					],					
					[	'name'=>'Stock Item',
						'icon'=>'fa fa-shopping-cart',
						'url'=>'xepan_commerce_store_item'
					],
					[	'name'=>'Dispatch Request / Item',
						'icon'=>'fa fa-truck',
						'url'=>'xepan_commerce_store_dispatchrequest'
					],
					[	'name'=>'Bar Code List',
						'icon'=>'fa fa-barcode',
						'url'=>'xepan_commerce_barcode'
					],
					[	'name'=>'Store Activities',
						'icon'=>'fa fa-cog fa-spin',
						'url'=>'xepan_commerce_store_activity_all'
					],
					[	'name'=>'Commerce Reports',
						'icon'=>'fa fa-cog fa-spin',
						'url'=>'xepan_commerce_reports_customer'
					],
					[	'name'=>'Configuration',
						'icon'=>'fa fa-cog fa-spin',
						'url'=>'xepan_commerce_customfield'
					],
					[	'name'=>'Wishlist',
						'icon'=>'fa fa-heart',
						'url'=>'xepan_commerce_wishlist'
					],
					[	'name'=>'Review',
						'icon'=>'fa fa-star',
						'url'=>'xepan_commerce_review'
					],
					[	'name'=>'My Stocks',
						'icon'=>'fa fa-cog fa-spin',
						'url'=>'xepan_commerce_mystocks',
						'skip_default'=>true
					]
                ],
                'Reports'=>[
                	[	'name'=>'Employee Comm. & Sales Report',
						'icon'=>'fa fa-users',
						'url'=>'xepan_commerce_reports_salesreport'
                	]
                ]
            ];
			// $m->addItem(['Warehouse Material Management','icon'=>'fa fa-building'],'xepan_commerce_store_warehouse');
			// $m->addItem(['Stock Transaction','icon'=>'fa fa-random'],'xepan_commerce_store_transaction');
			// $m->addItem(['Store Reports','icon'=>'fa fa-cog fa-spin'],'xepan_commerce_store_reports_itemstock');
	}

	function getConfigTopApplicationMenu(){
		if($this->app->getConfig('hidden_xepan_commerce',false)){return [];}
		
		return [
				'Commerce_Config'=>[
					[	'name'=>'Tax & Tax Rule Configurations',
						'icon'=>'fa fa-percent',
						'url'=>'xepan_commerce_tax'
					],
					[	'name'=>'Custom Fields',
						'icon'=>'fa fa-cog',
						'url'=>'xepan_commerce_customfield'
					],
					[	'name'=>'Specifications',
						'icon'=>'fa fa-magic xepan-effect-yellow',
						'url'=>'xepan_commerce_specification'
					],
					[	'name'=>'Payment Gate Ways',
						'icon'=>'fa fa-cc-mastercard',
						'url'=>'xepan_commerce_paymentgateway'
					],
					[	'name'=>'Layouts',
						'icon'=>'fa fa-th',
						'url'=>'xepan_commerce_layouts'
					],
					[	'name'=>'Designer Library',
						'icon'=>'fa fa-th',
						'url'=>'xepan_commerce_designerlibraryimages'
					],
					[	'name'=>'Fonts',
						'icon'=>'fa fa-font',
						'url'=>'xepan_commerce_font'
					],
					[	'name'=>'Terms And Condition',
						'icon'=>'fa fa-check-square',
						'url'=>'xepan_commerce_tnc'
					],
					[	'name'=>'Shipping Rule',
						'icon'=>'fa fa-truck',
						'url'=>'xepan_commerce_shippingrule'
					],
					[	'name'=>'Amount Standard',
						'icon'=>'fa fa-dollar',
						'url'=>'xepan_commerce_amountstandard'
					],
					[	'name'=>'Customer Credits',
						'icon'=>'fa fa-dollar',
						'url'=>'xepan_commerce_customercredit'
					],
					[	'name'=>'Freelancer Category',
						'icon'=>'fa fa-users',
						'url'=>'xepan_commerce_freelancategory'
					],
					[	'name'=>'Unit Conversion',
						'icon'=>'fa fa-exchange',
						'url'=>'xepan_commerce_unit'
					],
					[	'name'=>'Warehouse Management',
						'icon'=>'fa fa-building',
						'url'=>'xepan_commerce_warehousemanagment'
					],
					[	'name'=>'QSP Config',
						'icon'=>'fa fa-building',
						'url'=>'xepan_commerce_qspconfig'
					],
					[	'name'=>'Store Config',
						'icon'=>'fa fa-building',
						'url'=>'xepan_commerce_storeconfig'
					]
				]
			];
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
    	$array['ReviewAndRating'] = ['caption'=>'ReviewAndRating','type'=>'DropDown','model'=>'xepan\commerce\Model_Review'];
    	$array['WISHLIST'] = ['caption'=>'Wishlist','type'=>'DropDown','model'=>'xepan\commerce\Model_Wishlist'];
    	$array['Discount_Voucher'] = ['caption'=>'Discount Voucher','type'=>'DropDown','model'=>'xepan\commerce\Model_DiscountVoucher'];
    }

    function collect_shortcuts($app,&$shortcuts){
		$shortcuts[]=["title"=>"Item Category","keywords"=>"commerce item category","description"=>"Manage Your Items Category","normal_access"=>"Commerce -> Item Category","url"=>$this->app->url('xepan_commerce_category'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Item/Product","keywords"=>"commerce item product goods","description"=>"Manage Your Company Products","normal_access"=>"Commerce -> Item","url"=>$this->app->url('xepan_commerce_item'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Coustomer","keywords"=>"customer client website user","description"=>"Manage Your Customer","normal_access"=>"Commerce -> Customer","url"=>$this->app->url('xepan_commerce_customer'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Supplier","keywords"=>"supplier distributor vendor provider merchant","description"=>"Manage Your Supplier","normal_access"=>"Commerce -> Supplier","url"=>$this->app->url('xepan_commerce_supplier'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Quotation","keywords"=>"quotation","description"=>"Manage Your Quotation","normal_access"=>"Commerce -> Quotation","url"=>$this->app->url('xepan_commerce_quotation'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Sales Order","keywords"=>"sales order","description"=>"Manage Your Company Sales Order","normal_access"=>"Commerce -> Sales Order","url"=>$this->app->url('xepan_commerce_salesorder'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Sales Invoice","keywords"=>"sales invoice bill","description"=>"Manage Your Company Sales invoice","normal_access"=>"Commerce -> Sales Invoice","url"=>$this->app->url('xepan_commerce_salesinvoice'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Purchase Order","keywords"=>"purchase order","description"=>"Manage Your Customers Purchase Order","normal_access"=>"Commerce -> Purchase Order","url"=>$this->app->url('xepan_commerce_purchaseorder'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Purchase Invoice","keywords"=>"purchase invoice bill","description"=>"Manage Your Customers Purchase Invoice","normal_access"=>"Commerce -> Purchase Invoice","url"=>$this->app->url('xepan_commerce_purchaseinvoice'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Discount Voucher","keywords"=>"discount voucher coupon offers","description"=>"Manage Your Discount Vouchers & Coupons","normal_access"=>"Commerce -> Discount Voucher","url"=>$this->app->url('xepan_commerce_discountvoucher'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Warehouse Material Management","keywords"=>"store management warehouse management godown","description"=>"Manage Your Store Warehouse Management","normal_access"=>"Commerce -> Warehouse Material Management","url"=>$this->app->url('xepan_commerce_store_warehouse'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Stock Transaction","keywords"=>"stock transaction store activity store log stock log stock history","description"=>"Manage Your Product/Store Transaction","normal_access"=>"Commerce -> Store Transaction","url"=>$this->app->url('xepan_commerce_store_transaction'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Stock Item","keywords"=>"stock item current stock value stock report","description"=>"Manage Your Stock Report","normal_access"=>"Commerce -> Stock Item","url"=>$this->app->url('xepan_commerce_store_item'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Dispatch Request Item","keywords"=>"dispatch request item delivery management shippment product current shipping item pending shipping item shipping management","description"=>"Product Delivery Management","normal_access"=>"Commerce -> Dispatch Request Item","url"=>$this->app->url('xepan_commerce_store_dispatchrequest'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Bar Code List","keywords"=>"bar code list bar code management serialze number management","description"=>"Product Bar Code Management","normal_access"=>"Commerce -> Bar Code List","url"=>$this->app->url('xepan_commerce_barcode'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Store Activities","keywords"=>"store activities opening store management warehouse management store adjustment store movement store issue store issue submitted store package item","description"=>"Store Activity Management","normal_access"=>"Commerce -> Store Activities","url"=>$this->app->url('xepan_commerce_store_activity_all'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Commerce Reports","keywords"=>"customer sales purchase report","description"=>"Customer Sale Purchase Report Management","normal_access"=>"Commerce -> Customer Report","url"=>$this->app->url('xepan_commerce_reports_customer'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Taxation Configuration","keywords"=>"tax subtax management taxation rule","description"=>"Taxation Management Define All Type Of Taxes","normal_access"=>"Commerce -> Configuration / SideBar -> Taxation Configuration","url"=>$this->app->url('xepan_commerce_tax'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Custom Field Configuration","keywords"=>"item product custom field","description"=>"Product Specific Customer Options","normal_access"=>"Commerce -> Configuration / SideBar -> Custom Fields","url"=>$this->app->url('xepan_commerce_customfield'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Specification Configuration","keywords"=>"Specification product item properties attributes","description"=>"Product Item Attributes & Specification","normal_access"=>"Commerce -> Configuration / SideBar -> Specification Configuration","url"=>$this->app->url('xepan_commerce_specification'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Payment Gateway Configuration","keywords"=>"third party payment gateway integration online payment gateway","description"=>"Management Of Third Party Payment Gateway API's","normal_access"=>"Commerce -> Configuration / SideBar -> Payment Gateway","url"=>$this->app->url('xepan_commerce_paymentgateway'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Layouts Configuration","keywords"=>"sales order formate design purchase order formate design sales invoice formate design purchase invoice formate design quotation formate design challan formate design","description"=>"Custom Design Formate For Sales Order Sales Invoice Purchase Order Purchase Invoice In Printable Formate","normal_access"=>"Commerce -> Layouts Configuration","url"=>$this->app->url('xepan_commerce_layouts'),'mode'=>'frame'];
		
		$shortcuts[]=["title"=>"Quotation Layout","keywords"=>"quotation formate design print layout","description"=>"Customise Quotation layout","normal_access"=>"Commerce -> Configuration / SideBar -> Layouts Configuration / Quotation","url"=>$this->app->url('xepan_commerce_layouts',['cut_object'=>'admin_layout_cube_layouts_tabs_quotation']),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Sales Order Layout","keywords"=>"sales order salesorder so formate design print layout","description"=>"Customise Sales Order layout","normal_access"=>"Commerce -> Configuration / SideBar -> Layouts Configuration / Sales Order","url"=>$this->app->url('xepan_commerce_layouts',['cut_object'=>'admin_layout_cube_layouts_tabs_sales_order']),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Sales Invoice Layout","keywords"=>"sales invoice salesinvoice si formate design print layout","description"=>"Customise Sales Invoice layout","normal_access"=>"Commerce -> Configuration / SideBar -> Layouts Configuration / Sales Invoice","url"=>$this->app->url('xepan_commerce_layouts',['cut_object'=>'admin_layout_cube_layouts_tabs_sales_invoice']),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Purchase Order Layout","keywords"=>"purhcase order purchaseorder po formate design print layout","description"=>"Customise Purchase Order layout","normal_access"=>"Commerce -> Configuration / SideBar -> Layouts Configuration / Purchase Order","url"=>$this->app->url('xepan_commerce_layouts',['cut_object'=>'admin_layout_cube_layouts_tabs_po']),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Purchase Invoice Layout","keywords"=>"purhcase invoice purchaseinvoice pi formate design print layout","description"=>"Customise Purchase Invoice layout","normal_access"=>"Commerce -> Configuration / SideBar -> Layouts Configuration / Purchase Invoice","url"=>$this->app->url('xepan_commerce_layouts',['cut_object'=>'admin_layout_cube_layouts_tabs_pi']),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Challan Layout","keywords"=>"challan billty formate design print layout","description"=>"Customise Challan layout","normal_access"=>"Commerce -> Configuration / SideBar -> Layouts Configuration / Challan","url"=>$this->app->url('xepan_commerce_layouts',['cut_object'=>'admin_layout_cube_layouts_tabs_challan']),'mode'=>'frame'];
		
		$shortcuts[]=["title"=>"Designer Library Configuration","keywords"=>"designer image library front designer tool printing designer tool image library","description"=>"Predefine Image Library For Frontend Printing Designer Tool","normal_access"=>"Commerce -> Configuration / SideBar -> Designer Tool Library Configuration","url"=>$this->app->url('xepan_commerce_designerlibraryimages'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Fonts Configuration","keywords"=>"designer font list printing font list configuration google font intergration custom font .ttf integration","description"=>"Google Font And Custom Font Family Configuration","normal_access"=>"Commerce -> Configuration / SideBar -> Font Family Configuration","url"=>$this->app->url('xepan_commerce_font'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Terms & Conditions Configuration","keywords"=>"company terms & condition custom T&C","description"=>"Define Terms & Conditions For Sales Order, Sales Invoice, Purchase Order, Purchase Invoice, Quotation","normal_access"=>"Commerce -> Configuration / SideBar -> Terms & Conditions Configuration","url"=>$this->app->url('xepan_commerce_tnc'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Shipping Configuration","keywords"=>"product shipping configuration shipping rates configuration","description"=>"Product/Item Shipping Configuration","normal_access"=>"Commerce -> Configuration / SideBar -> Shipping Configuration","url"=>$this->app->url('xepan_commerce_shippingrule'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Round Amount Standard Configuration","keywords"=>"amount standard formate amount up formate amount down formate","description"=>"Manage Round Amount Formate For Sales Order, Purchase Order, Sales Invoice, Purchase Invoice, Quotation","normal_access"=>"Commerce -> Configuration / SideBar -> Round Amount Configuration","url"=>$this->app->url('xepan_commerce_amountstandard'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Customer Credit","keywords"=>"customer credit","description"=>"Manage customer credit wallet system","normal_access"=>"Commerce -> Configuration / SideBar -> Customer Credit","url"=>$this->app->url('xepan_commerce_customercredit'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Freelancer Designers Category","keywords"=>"freelancer designer category","description"=>"Manage freelancer designers","normal_access"=>"Commerce -> Configuration / SideBar -> Freelancer Category","url"=>$this->app->url('xepan_commerce_freelancategory'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Unit Management and Conversions","keywords"=>"unit conversion","description"=>"Manage units and their inter conversion","normal_access"=>"Commerce -> Configuration / SideBar -> Unit Conversion","url"=>$this->app->url('xepan_commerce_unit'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Warehouse Management","keywords"=>"store house godown warehouse","description"=>"Manage warehouses","normal_access"=>"Commerce -> Configuration / SideBar -> Warehouse","url"=>$this->app->url('xepan_commerce_warehousemanagment'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"QSP Configurations","keywords"=>"discount on item tax on discounted amount serial series quotation sales invoice purchase order","description"=>"Config tax discount relations and serieses","normal_access"=>"Commerce -> Configuration / SideBar -> QSP Config","url"=>$this->app->url('xepan_commerce_qspconfig'),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Stock Adjustment Types","keywords"=>"stock adjustment subtypes","description"=>"Subtypes to mark adjustments","normal_access"=>"Commerce -> Configuration / SideBar -> Store Config","url"=>$this->app->url('xepan_commerce_storeconfig',['cut_object'=>'admin_layout_cube_storeconfig_tabs_view_htmlelement']),'mode'=>'frame'];
		$shortcuts[]=["title"=>"Dispatch Config","keywords"=>"dispacth partial config","description"=>"Configure if partial dispatch is allowed","normal_access"=>"Commerce -> Configuration / SideBar -> Store Config","url"=>$this->app->url('xepan_commerce_storeconfig',['cut_object'=>'admin_layout_cube_storeconfig_tabs_view_htmlelement_2']),'mode'=>'frame'];

	}

    function setup_pre_frontend(){
    	$this->app->customer = $this->add('xepan\commerce\Model_Customer');
        $this->app->customer->loadLoggedIn("Customer");
    	$this->app->addHook('sef-router',[$this,'addSEFRouter']);
    	$this->app->addHook('sitemap_generation',[$this,'addSiteMapEntries']);
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
			$this->app->exportFrontEndTool('xepan\commerce\Tool_MyWishlist','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_FreelancerListing','Commerce');
			$this->app->exportFrontEndTool('xepan\commerce\Tool_FreelancerCategory','Commerce');
			/*$this->app->exportFrontEndTool('
				xepan\commerce\Tool_MyWishlist','Commerce');*/
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

		if($value['commerce_category_detail_page'])
			$this->app->app_router->addRule('\/'.$value['commerce_category_detail_page']."\/(.*)", $value['commerce_category_detail_page'], ['xsnb_category_sef_url']);
		if($value['commerce_product_list_page'])
			$this->app->app_router->addRule('\/'.$value['commerce_product_list_page']."\/(.*)", $value['commerce_product_list_page'], ['xsnb_category_sef_url']);
		if($value['commerce_product_detail_page'])
			$this->app->app_router->addRule('\/'.$value['commerce_product_detail_page']."\/(.*)", $value['commerce_product_detail_page'], ['commerce_item_slug_url']);
		if($value['commerce_freelancer_list_page'])
			$this->app->app_router->addRule('\/'.$value['commerce_freelancer_list_page']."\/(.*)", $value['commerce_freelancer_list_page'], ['freelancercategory_slug_url']);
	}

	function addSiteMapEntries($app,&$urls,$sef_config_page_lists){
		// categories and products list prepare
			// category/active
				// based on sef_enabled
					// /$sef_config_pagelist/$foreachvalue
				// or no sef
					// /?page=$sef_config_pagelist&category_id/xsnb_category_sef_url=$foreachvalue
			// product/that are webdisplay/active
				// based on sef_enabled
					// /$sef_config_pagelist/$foreachvalue
				// or no sef
					// /?page=$sef_config_pagelist&commerce_item_id_id/commerce_item_slug_url=$foreachvalue

		if($page = $sef_config_page_lists['commerce_category_detail_page']){
			$category = $this->add('xepan\commerce\Model_Category')
				->addCondition('status','Active');
			foreach ($category as $cat) {
				$urls[] = (string)$cat->getURL($page);
			}
		}
		
		if($page = $sef_config_page_lists['commerce_product_detail_page']){
			$product = $this->add('xepan\commerce\Model_Item_WebsiteDisplay')
						->addCondition('status','Published')
						;
			foreach ($product as $pro) {
				$urls[] = (string)$pro->getURL($page);
			}
		}
	}


	function documentActionData(){

		$sale_order = $this->add('xepan\commerce\Model_SalesOrder');
		$sale_order_fields = $sale_order->getActualFields();

		$sale_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$sale_invoice_fields = $sale_invoice->getActualFields();

		$quotation = $this->add('xepan\commerce\Model_Quotation');
		$quotation_fields = $quotation->getActualFields();

		$po = $this->add('xepan\commerce\Model_PurchaseOrder');
		$po_fields = $po->getActualFields();

		$pi = $this->add('xepan\commerce\Model_PurchaseInvoice');
		$pi_fields = $pi->getActualFields();

		return [
				'SalesOrder'=>[
							'model_class'=>'xepan\commerce\Model_SalesOrder',
							'status'=> array_combine($sale_order->status, $sale_order->status),
							'fields'=>array_combine($sale_order_fields, $sale_order_fields),
							'related_contact_field'=>'contact_id',
						],
				'SalesInvoice'=>[
							'model_class'=>'xepan\commerce\Model_SalesInvoice',
							'status'=> array_combine($sale_invoice->status, $sale_invoice->status),
							'fields'=>array_combine($sale_invoice_fields, $sale_invoice_fields),
							'related_contact_field'=>'contact_id',
						],
				'Quotation'=>[
							'model_class'=>'xepan\commerce\Model_Quotation',
							'status'=> array_combine($quotation->status, $quotation->status),
							'fields'=>array_combine($quotation_fields, $quotation_fields),
							'related_contact_field'=>'contact_id'
						],
				'PurchaseOrder'=>[
							'model_class'=>'xepan\commerce\Model_PurchaseOrder',
							'status'=> array_combine($po->status, $po->status),
							'fields'=>array_combine($po_fields, $po_fields),
							'related_contact_field'=>'contact_id'
						],
				'PurchaseInvoice'=>[
							'model_class'=>'xepan\commerce\Model_PurchaseInvoice',
							'status'=> array_combine($pi->status, $pi->status),
							'fields'=>array_combine($pi_fields, $pi_fields),
							'related_contact_field'=>'contact_id'
						],

			];
	}
}
