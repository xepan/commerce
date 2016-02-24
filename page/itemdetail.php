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

class page_itemdetail extends \Page {
	public $title='View Item';

	function init(){
		parent::init();

		$item = $this->add('xepan\commerce\Model_Item')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		// $contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		// $contact_view->setModel($item);

		$d = $this->add('xepan\base\View_Document',
				[
					'action'=>$this->api->stickyGET('action')?:'view', // add/edit
					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
					'allow_many_on_add' => false, // Only visible if editinng,
					'view_template' => ['page/itemdetail','basic_info']
				],
				'basic_info'
			);
		$d->setModel($item,null,['name','sku','rank_weight','expiry_date','is_saleable']);
	}

	function defaultTemplate(){
		return ['page/itemdetail'];
	}
}
























// <?php
//  namespace xepan\commerce;
//  class page_itemdetail extends \Page{

//  	public $title='View Item';


// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/itemdetail'];
// 	}
// }