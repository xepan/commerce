<?php

 namespace xepan\commerce;

 class Model_Item extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$item_j=$this->join('item.document_id');
		//$item_j->hasone('xepan\commerce\itemdetail');

		$item_j->addField('name')->mandatory(true);
		$item_j->addField('image')->mandatory(true);
		$item_j->addField('sku')->PlaceHolder('Insert Unique Referance Code')->caption('Code')->hint('Place your unique Item code ')->mandatory(true);
		$item_j->addField('original_price')->type('money')->mandatory(true);
		$item_j->addField('sale_price')->type('money')->mandatory(true);
		$item_j->addField('total_sale')->mandatory(true);

		
		$item_j->addField('is_publish')->type('boolean');
		$item_j->addField('is_party_publish')->type('boolean');

		$item_j->addField('rank_weight');
		$item_j->addField('expiry_date')->type('date');
		$item_j->addField('description')->type('text');
		
		// Price and Qtuanitity Management
		$item_j->addField('minimum_order_qty')->type('int');
		$item_j->addField('maximum_order_qty')->type('int');
		$item_j->addField('qty_unit');
		$item_j->addField('qty_from_set_only')->type('boolean');
		
		//Item Allow Optins
		$item_j->addField('is_saleable')->type('boolean');
		$item_j->addField('allow_uploadedable')->type('boolean');
		$item_j->addField('is_purchasable')->type('boolean');
		$item_j->addField('mantain_inventory')->type('boolean');
		$item_j->addField('allow_negative_stock')->type('boolean');
		$item_j->addField('is_servicable')->type('boolean');
		$item_j->addField('is_productionable')->type('boolean');
		$item_j->addField('website_display')->type('boolean');
		$item_j->addField('is_downloadable')->type('boolean');
		$item_j->addField('is_rentable')->type('boolean');
		$item_j->addField('is_designable')->type('boolean');
		$item_j->addField('is_template')->type('boolean');
		$item_j->addField('is_enquiry_allow')->type('boolean');
		$item_j->addField('is_attachment_allow')->type('boolean');
		$item_j->addField('is_fixed_asset')->type('boolean');
		$item_j->addField('warranty_days')->type('int');
		
		//Item Display Options
		$item_j->addField('show_detail')->type('boolean');
		$item_j->addField('show_price')->type('boolean');
		$item_j->addField('is_visible_sold')->type('boolean');

		//Marked
		$item_j->addField('new')->type('boolean')->caption('New');
		$item_j->addField('feature')->type('boolean')->caption('Featured');
		$item_j->addField('latest')->type('boolean')->caption('Latest');
		$item_j->addField('mostviewed')->type('boolean')->caption('Most Viewed');

		//Enquiry Send To
		$item_j->addField('enquiry_send_to_admin')->type('boolean');
		$item_j->addField('Item_enquiry_auto_reply')->caption('Item Enquiry Auto Reply')->type('boolean');
		
		//Item Comment Options
		$item_j->addField('allow_comments')->type('boolean');
		$item_j->addField('comment_api')->setValueList(array('disqus'=>'Disqus'));

		//Item Other Options
		$item_j->addField('add_custom_button')->type('boolean');
		$item_j->addField('custom_button_label');
		$item_j->addField('custom_button_url')->placeHolder('subpage name like registration etc.');
		$item_j->addField('theme_code')->hint('To club same theme code items in one');
		$item_j->addField('reference')->PlaceHolder('Any Referance')->hint('Use URL for external link');
		
		//Item Stock Options	
		$item_j->addField('negative_qty_allowed')->type('number');

		// Item WaterMark
		//$item_j->add('filestore/Field_Image','watermark_image_id');
		$item_j->addField('watermark_text')->type('text');
		$item_j->addField('watermark_position')->enum(array('TopLeft','TopRight','BottomLeft','BottomRight','Center','Left Diagonal','Right Diagonal'));
		$item_j->addField('watermark_opacity');
		
		//Item Designs
		$item_j->addField('designs')->type('text');

		//Item SEO
		$item_j->addField('meta_title');
		$item_j->addField('meta_description')->type('text');
		$item_j->addField('tags')->type('text')->PlaceHolder('Comma Separated Value');

		//others
		$item_j->addField('terms_condition')->type('text');
		$item_j->addField('duplicate_from_item_id');
		
		//Blog
		$item_j->addField('is_blog')->type('boolean');

	}
} 
 
	

