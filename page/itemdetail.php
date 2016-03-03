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

		$action = $this->api->stickyGET('action')?:'view';
	
		$item = $this->add('xepan\commerce\Model_Item')->tryLoadBy('id',$this->api->stickyGET('document_id'));

		$basic_item = $this->add('xepan\base\View_Document',['action'=>'view','id_field_on_reload'=>'document_id'],'view_info',['page/item/detail','view_info']);
		$basic_item->setModel($item,['name','total_sale','total_orders','created_at','stock_availability'],
									['name','total_sale','total_orders','created_at','stock_availability']);
		
		$basic_item = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],'basic_info',['page/item/detail','basic_info']);
		$basic_item->setModel($item,['name','sku','display_sequence','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions'],

								['name','sku','display_sequence','expiry_date',
								'is_saleable','is_allowuploadable','is_purchasable','is_productionable',
								'website_display','maintain_inventory','allow_negative_stock',
								'is_enquiry_allow','is_template',
								'show_detail','show_price','is_visible_sold',
								'is_new','is_feature','is_mostviewed',
								'enquiry_send_to_admin','item_enquiry_auto_reply',
								'is_comment_allow','comment_api',
								'add_custom_button','custom_button_label','custom_button_url',
								'description','terms_and_conditions']);

		if($item->loaded()){
			$seo_item = $this->add('xepan\base\View_Document',['action'=>$action],'seo',['page/item/detail','seo']);
			$seo_item->setModel($item,['meta_title','meta_description','tags'],
									  ['meta_title','meta_description','tags']);

			$cat_item = $this->add('xepan\base\View_Document',['action'=>$action],'catg',['page/item/detail','catg']);
			$cat_item->setModel($item,['category_name'],
										['category_name']);

			$qty_detail = $this->add('xepan\base\View_Document',['action'=>$action],'qty_price_detail',['page/item/detail','qty_price_detail']);
			$qty_detail->setModel($item,['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit','qty_from_set_only'],
										['sale_price','original_price','minimum_order_qty','maximum_order_qty','qty_unit','qty_from_set_only']);			
		}else{
			// $this->add('View_Error',null,'attribute')->set('First Add Item');
		}

	}

	function defaultTemplate(){
		return ['page/item/detail'];

	}
}


