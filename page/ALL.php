<?php 
 namespace xepan\commerce;
 class Model_Item extends \xepan\hr\Model_Document{
	function init(){
		parent::init();

	1.  in Category

	$cat_j->hasOne('xepan\commerce\ParentCategory','parent_category_id')->defaultValue('Null');

	$cat_j->hasMany('xepan\commerce\Filter','category_id');
	$cat_j->hasMany('xepan\commerce\CategoryItemAssociation','category_id');
	$cat_j->hasMany('xepan\commerce/Category','parent_category_id',null,'SubCategories');

	2.  in Category Item Association

		$this->hasOne('xepan/commerce/Item','item_id');
		$this->hasOne('xepan/commerce/Category','category_id');

	3.  in Customer 

		$cust_j->hasOne('xepan\accounts\Currency','currency_id');

		$this->hasMany('xepan/commerce/Model_QSP_Master',null,null,'QSPMaster');

	4.  in Supplier

		$cust_j->hasOne('xepan\accounts\Currency','currency_id');

		$this->hasMany('xepan/commerce/Model_QSP_Master',null,null,'QSPMaster');

	5.  in Filter

		$this->hasOne('xepan\commerce\Category','category_id')->mandatory(true);
		$this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_association_id')->mandatory(true);

	6.  in Item

		$item_j->hasOne('xepan\base\Contact','designer_id');

		$item_j->hasMany('xepan\commerce\Item_Quantity_Set','item_id');
		$item_j->hasMany('xepan\commerce\Item_CustomField_Association','item_id');
		$item_j->hasMany('xepan\commerce\Item_Department_Association','item_id',null);
		
		//Category Item Association
		$item_j->hasMany('xepan\commerce\CategoryItemAssociation','item_id');
		
		//Member Design
		$item_j->hasMany('xepan\commerce\Item_Template_Design','item_id');
		$this->hasMany('xepan\commerce\Store_TransactionRow','item_id',null,'StoreTransactionRows');
		$this->hasMany('xepan\commerce\QSP_Detail','item_id',null,'QSPDetail');
		$item_j->hasMany('xepan\commerce\Item_Image','item_id',null,'ItemImages');
		$item_j->hasMany('xepan\commerce\Item_Taxation_Association','item_id',null,'Tax');

	7. in Lodgement

		$this->hasOne('xepan\commerce\Model_SalesInvoice','salesinvoice_id');
		$this->hasOne('xepan\accounts\Model_Transaction','account_transaction_id');

	8. in OrderItemDepartment

		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
		$this->hasOne('xepan\hr\Department','department_id');
		
		$this->hasMany('xepan\production\Jobcard','order_item_departmental_status_id');

	9. in TNC

		$document_j->hasMany('xepan/commerce/QSP_Master','tnc_id');

	10. in Taxation
 		
 		$this->hasMany('xepan\commerce\Item_Taxation_Association','taxation_id');

 	11. in Store_Transaction 

 		$this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','from_warehouse_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','to_warehouse_id');
		$this->hasOne('xepan\production\Jobcard','jobcard_id');
		
		$this->hasMany('xepan\commerce\Store_TransactionRow','store_transaction_id',null,'StoreTransactionRows');
	
	12 . in Store_TransactionRow

		$this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\commerce\Store_Transaction','store_transaction_id');
		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_generic_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		$this->hasOne('xepan\production\Jobcard_Detail','jobcard_detail_id');


	13. in Store_Warehouse

		$this->hasMany('xepan\commerce\Store_Transaction','from_warehouse_id',null,'FromTransactions');
		$this->hasMany('xepan\commerce\Store_Transaction','to_warehouse_id',null,'ToTransactions');
	
	14. in QSP_Detail

		$this->hasOne('xepan\commerce\QSP_Master','qsp_master_id');
		$this->hasOne('xepan\commerce\Item','item_id')->display(array('form'=>'xepan\commerce\Item'));
		$this->hasOne('xepan\commerce\Taxation','taxation_id');

	15. in QSP_Master

		$qsp_master_j->hasOne('xepan/base/Contact','contact_id')->sortable(true);
		$qsp_master_j->hasOne('xepan/accounts/Currency','currency_id');
		$qsp_master_j->hasOne('xepan/accounts/Group','nominal_id');
		$qsp_master_j->hasOne('xepan/commerce/TNC','tnc_id');
		$qsp_master_j->hasOne('xepan/commerce/PaymentGateway','paymentgateway_id');

		$qsp_master_j->hasMany('xepan\commerce\QSP_Detail','qsp_master_id',null,'Details');
		$qsp_master_j->hasMany('xepan\commerce\QSP_Master','related_qsp_master_id',null,'RelatedQSP');

	16. in Model_Designer_Image_Category

		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\base\Epan','epan_id');
		
		$this->hasMany('xepan\commerce\Designer_Images','designer_category_id',null,'DesignerAttachments');
	
	17. in Model_Designer_Images 

		$this->hasOne('xepan\base\Epan','epan_id');
 		$this->hasOne('xepan\commerce\Designer_Image_Category','designer_category_id');
 		
 	18. in Model_Item_CustomField_Association

 		$this->hasOne('xepan\commerce\Item_CustomField_Generic','customfield_generic_id');//->display(array('form'=>'autocomplete/Plus'));
		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\hr\Department','department_id')->mandatory(true);
		
		$this->hasMany('xepan\commerce\Item_CustomField_Value','customfield_association_id');
		$this->hasMany('xepan\commerce\Filter','customfield_association_id');

	19. in Model_Item_CustomField_Value

		$this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_association_id');

		$this->hasMany('xepan\commerce\Item_Image','customfield_value_id');
		$this->hasMany('xepan\commerce\Item_Quantity_Condition','customfield_value_id');

	20. in Model_Item_Department_Association

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\hr\Model_Department','department_id')->defaultValue(0);
		
		$this->hasMany('xepan\commerce\Item_Department_Consumption','item_department_association_id');
	
	21. in Model_Item_Department_Consumption

		$this->hasOne('xepan\commerce\Item_Department_Association','item_department_association_id');
		$this->hasOne('xepan\commerce\Item','composition_item_id');
		
	22. in Model_Item_Quantity_Condition

		$this->hasOne('xepan\commerce\Item_Quantity_Set','quantity_set_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		
	23. in Model_Item_Quantity_Set

		$this->hasOne('xepan\commerce\Item','item_id');

		$this->hasMany('xepan\commerce\Item\Quantity\Condition','quantity_set_id');

	24. in Model_Item_Taxation_Association

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\commerce\Taxation','taxation_id');

	25. in Model_Item_Template_Design

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\base\Contact','contact_id');
	
	26. in Model_Item_CustomField

		$this->hasMany('xepan/commerce/Item/CustomField_Association','customfield_generic_id');

	27. in Model_Item_Image

		$this->hasOne('xepan\commerce\Item','item_id');
		
	28. in Model_Item_Specification

		$this->hasMany('xepan/commerce/Item/CustomField_Association','customfield_generic_id');
	

	}
} 
 
	

