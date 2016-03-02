<?php 

 namespace xepan\commerce;

 class Model_Item extends \xepan\commerce\Model_Document{
	public $status = ['Draft','Submitted','Published'];
	
	// draft
		// Item are not published or is_party published off
	//submitted 
		//item status unpublished and and is_paty published
	//published 
		// Item is published true

	public $actions = [
					'Draft'=>['view','edit','delete','published','submit'],
					'Submitted'=>['view','edit','delete','published'],
					'Reject'=>['view','edit','delete','draft'],
					'Published'=>['view','edit','delete','unpublished'],
					'UnPublished'=>['view','edit','delete','published'],
					];

	function init(){
		parent::init();

		$item_j=$this->join('item.document_id');

		$item_j->addField('name')->mandatory(true);
		$item_j->addField('sku')->PlaceHolder('Insert Unique Referance Code')->caption('Code')->hint('Insert Unique Referance Code')->mandatory(true);
		$item_j->addField('display_sequence')->hint('descending wise sorting');
		$item_j->addField('description')->type('text');
		
		$item_j->addField('original_price')->type('money')->mandatory(true);
		$item_j->addField('sale_price')->type('money')->mandatory(true);
		

		
		$item_j->addField('expiry_date')->type('date');
		
		$item_j->addField('minimum_order_qty')->type('int');
		$item_j->addField('maximum_order_qty')->type('int');
		$item_j->addField('qty_unit');
		$item_j->addField('qty_from_set_only')->type('boolean');
		
		//Item Allow Optins
		$item_j->addField('is_party_publish')->type('boolean')->hint('Freelancer Item Design/Template to be Approved');
		$item_j->addField('is_saleable')->type('boolean')->hint('Make Item Becomes Saleable');
		$item_j->addField('allow_uploadedable')->type('boolean')->hint('on website customer can upload a degin for designable item');
		$item_j->addField('is_purchasable')->type('boolean')->hint('item display only at purchase Order/Invoice');
		//Item Stock Options
		$item_j->addField('mantain_inventory')->type('boolean')->hint('Manage Inventory ');
		$item_j->addField('allow_negative_stock')->type('boolean')->hint('show item on wensite apart from stock is available or not');
		$item_j->addField('negative_qty_allowed')->type('number')->hint('allow the negative stock until this quantity');
		$item_j->addField('is_visible_sold')->type('boolean')->hint('display item on website after out of stock/all sold');
		
		$item_j->addField('is_servicable')->type('boolean');
		$item_j->addField('is_productionable')->type('boolean')->hint('used in Production');
		$item_j->addField('website_display')->type('boolean')->hint('Show on Website');
		$item_j->addField('is_downloadable')->type('boolean');
		$item_j->addField('is_rentable')->type('boolean');
		$item_j->addField('is_designable')->type('boolean')->hint('item become designable and customer customize the design');
		$item_j->addField('is_template')->type('boolean')->hint('blueprint/layout of designable item');
		$item_j->addField('is_attachment_allow')->type('boolean')->hint('by this option you can attach the item information pdf/doc etc. to be available on website');
		
		$item_j->addField('warranty_days')->type('int');
		
		//Item Display Options
		$item_j->addField('show_detail')->type('boolean');
		$item_j->addField('show_price')->type('boolean');

		//Marked
		$item_j->addField('is_new')->type('boolean')->caption('New');
		$item_j->addField('is_feature')->type('boolean')->caption('Featured');
		$item_j->addField('is_mostviewed')->type('boolean')->caption('Most Viewed');

		//Enquiry Send To
		$item_j->addField('is_enquiry_allow')->type('boolean')->hint('display enquiry form at item detail on website');
		$item_j->addField('enquiry_send_to_admin')->type('boolean')->hint('send a copy of enquiry form to admin');
		$item_j->addField('item_enquiry_auto_reply')->caption('Item Enquiry Auto Reply')->type('boolean');
		
		//Item Comment Options
		$item_j->addField('is_comment_allow')->type('boolean');
		$item_j->addField('comment_api')->setValueList(array('disqus'=>'Disqus'));

		//Item Other Options
		$item_j->addField('add_custom_button')->type('boolean');
		$item_j->addField('custom_button_label');
		$item_j->addField('custom_button_url')->placeHolder('subpage name like registration etc.');
		
		// Item WaterMark
		// $item_j->add('filestore/Field_Image','watermark_image_id');
		$item_j->addField('watermark_text')->type('text');
		$item_j->addField('watermark_position')->enum(array('TopLeft','TopRight','BottomLeft','BottomRight','Center','Left Diagonal','Right Diagonal'));
		$item_j->addField('watermark_opacity');
		
		//Item SEO
		$item_j->addField('meta_title');
		$item_j->addField('meta_description')->type('text');
		$item_j->addField('tags')->type('text')->PlaceHolder('Comma Separated Value');

		//Item Designs
		$item_j->addField('designs')->type('text')->hint('used for internal, design saved');

		//others
		$item_j->addField('terms_and_conditions')->type('text');
		$item_j->addField('duplicate_from_item_id')->hint('internal used saved its parent');

		$this->addCondition('type','Item');

		// $item_j->addExpression('total_sale')->set(" 'TODO' ");
	}

	function submit(){
		$this['status']='Draft';
		$this->saveAndUnload();
	}

	
	function published(){
		$this['status']='Submitted';
		$this->saveAndUnload();
	}
} 
 
	

