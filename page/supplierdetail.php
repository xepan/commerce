<?php
 
/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class page_supplierdetail extends \xepan\base\Page {
	public $title='Supplier Details';
	public $breadcrumb=['Home'=>'index','Supplier'=>'xepan_commerce_supplier','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$supplier= $this->add('xepan\commerce\Model_Supplier')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		$contact_view = $this->add('xepan\base\View_Contact',['acl'=>"xepan\commerce\Model_Supplier"],'contact_view');
		$contact_view->setModel($supplier);
		$d = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'contact_id'],'basic_info',['page/supplier/detail','basic_info']);
		$d->setModel($supplier,['tin_no','address','pan_no','organization','city','state','country','currency','pin_code'],['tin_no','address','pan_no','organization','city','state','country','currency_id','pin_code']);


/**

		Orders

*/

			$ord = $this->add('xepan\commerce\Model_PurchaseOrder')
			->addCondition('contact_id',$supplier->id);
			$crud_ord = $this->add('xepan\hr\CRUD',null,'orders',['view/supplier/order/grid']);
			$crud_ord->setModel($ord);
			$crud_ord->grid->addQuickSearch(['orders']);

/**

		Invoices

*/
			$inv = $this->add('xepan\commerce\Model_PurchaseInvoice')
			->addCondition('contact_id',$supplier->id);
			$crud_inv = $this->add('xepan\hr\CRUD',null,'invoices',['view/supplier/invoice/grid']);
			$crud_inv->setModel($inv);
			$crud_inv->grid->addQuickSearch(['invoices']);		

		
	}

	function defaultTemplate(){
		return ['page/supplier/detail'];
	}
}