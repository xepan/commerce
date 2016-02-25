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

		$basic_item = $this->add('xepan\base\View_Document',
				[
					'action'=>$this->api->stickyGET('action')?:'view', // add/edit
					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
					'allow_many_on_add' => false, // Only visible if editinng,
					'view_template' => ['page/itemdetail','basic_info']
				],
				'basic_info'
			);
		$basic_item->setModel($item,null,['name','sku','rank_weight','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','alllow_negative_stock',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'is_enquiry_allow','enquiry_send_to_admin','item_enquiry_auto_reply','is_comment_allow','comment_api',
								'add_custom_option','custom_button_label','custom_button_url',
								'description','terms_and_conditions']);

		$seo_item = $this->add('xepan\base\View_Document',
				[
					'action'=>$this->api->stickyGET('action')?:'view', // add/edit
					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
					'allow_many_on_add' => false, // Only visible if editinng,
					'view_template' => ['page/itemdetail','seo`']
				],
				'seo'
			);

		$seo_item->setModel($category,null,['meta_title','meta_description','meta_keywords']);

		$cat_item = $this->add('xepan\base\View_Document',
				[
					'action'=>$this->api->stickyGET('action')?:'view', // add/edit
					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
					'allow_many_on_add' => false, // Only visible if editinng,
					'view_template' => ['page/itemdetail','catg`']
				],
				'catg'
			);
		$cat_item->setModel($category,null,['category_name']);


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