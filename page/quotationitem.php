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

class page_quotationitem extends \Page {
	public $title='Quatation Details';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$supplier= $this->add('xepan\commerce\Model_Quotation')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		$contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		$contact_view->setModel($quotation);
		$d = $this->add('xepan\base\View_Document',['action'=>$action],'quot_info',['page/quotationitem','quot_info']);
		$d->setModel($quotation,['tin_no','company_address'],['tin_no','company_address']);
		
	}

	function defaultTemplate(){
		return ['page/quotationitem'];
	}
}



 



















