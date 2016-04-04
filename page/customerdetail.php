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

class page_customerdetail extends \xepan\base\Page {
	public $title='Customer Details';
	public $breadcrumb=['Home'=>'index','Customers'=>'xepan_commerce_customer','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$customer= $this->add('xepan\commerce\Model_Customer')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		$contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		$contact_view->setModel($customer);

		$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
		$d->setIdField('contact_id');
		$d->setModel($customer,['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
								'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency'],
								['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
								'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency_id']);
		
		/**

		Orders

		*/

			$media_m = $item->ref('ItemImages');
			$crud_media = $this->add('xepan\hr\CRUD',null,'media',['view/item/media']);
			$crud_media->setModel($media_m);
			$seo_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'seo',['page/item/detail','seo']);
			$seo_item->setModel($item,['meta_title','meta_description','tags'],
									  ['meta_title','meta_description','tags']);


/**

		Accounts

*/		
	$act = $this->add('xepan\commerce\Model_Item_Taxation_Association')
				->addCondition('item_id',$item->id);
	$crud_ac = $this->add('xepan\hr\CRUD',null,'taxation',['view/item/accounts/tax']);
	$crud_ac->setModel($act);
	$crud_ac->grid->addQuickSearch(['taxation']);

	}

	function defaultTemplate(){
		return ['page/customer/detail'];
	}
}
