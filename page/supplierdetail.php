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

class page_supplierdetail extends \Page {
	public $title='Supplier Details';

	function init(){
		parent::init();

		$supplier= $this->add('xepan\commerce\Model_Supplier')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		$contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		$contact_view->setModel($supplier);
		$d = $this->add('xepan\base\View_Document',
				[
					'action'=>$this->api->stickyGET('action')?:'view', // add/edit
					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
					'allow_many_on_add' => false, // Only visible if editinng,
					'view_template' => ['page/supplierdetail','basic_info']
				],
				'basic_info'
			);
		$d->setModel($supplier,null,['tin_no','company_address']);
		
	}

	function defaultTemplate(){
		return ['page/supplierdetail'];
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