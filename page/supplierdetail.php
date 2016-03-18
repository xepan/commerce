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
		
		$contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		$contact_view->setModel($supplier);
		$d = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'contact_id'],'basic_info',['page/supplier/detail','basic_info']);
		$d->setModel($supplier,['tin_no','address','pan_no','organization','city','state','country','currency','pin_code'],['tin_no','address','pan_no','organization','city','state','country','currency_id','pin_code']);
		
	}

	function defaultTemplate(){
		return ['page/supplier/detail'];
	}
}



 


















// <?php
//  namespace xepan\commerce;
//  class page_supplierdetail extends \Page{

//  	public $title='Supplier Detail';


// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/supplierdetail'];
// 	}
// }