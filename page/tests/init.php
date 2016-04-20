<?php

namespace xepan\commerce;

class page_tests_init extends \AbstractController{
	public $title = "Commerce Test Init";

	function init(){
		parent::init();

		$this->app->xepan_app_initiators['xepan\commerce']->resetDB();
	}

	function createCateories(){
		// ADDING Categories
		$model_category=[];
		for ($i=0; $i <4 ; $i++) { 
			$model_category[$i] = $this->add('xepan\commerce\Model_Category');
			
			if($i==0){
				$model_category[$i]['parent_category_id'] = '';
			}else{
				$model_category[$i]['parent_category_id'] = $model_category[$i-1]->id;
			}

			$model_category[$i]['name'] = "Test Category".$i;
			$model_category[$i]['display_sequence'] = $i;
			$model_category[$i]['alt_text'] = "Test".$i;
			$model_category[$i]['description'] = "Test".$i;
			$model_category[$i]['custom_link'] = "Test".$i;
			$model_category[$i]['meta_title'] = "Test".$i;
			$model_category[$i]['meta_description'] = "Test".$i;
			$model_category[$i]['meta_keywords'] = "Test".$i;
			$model_category[$i]->save();
		}

		return $this;
	}

	function createItems(){

		// ADDING items
		$model_item=[];
		for($i=0; $i<4; $i++){
			$model_item[$i] = $this->add('xepan\commerce\Model_Item');

			$model_item[$i]['name'] = 'Test'.$i;			
			$model_item[$i]['sku'] = 'Test'.$i;
			$model_item[$i]['original_price'] ='00'.$i;
			$model_item[$i]['sale_price'] ='00'.$i;
			$model_item[$i]['expiry_date'] ="2016-04-20";
			$model_item[$i]['description'] ='Test'.$i;
			$model_item[$i]['show_detail'] =true;
			$model_item[$i]['show_price'] =true;
			$model_item[$i]['is_new'] =true;
			$model_item[$i]['is_mostviewed'] =true;
			$model_item[$i]['Item_enquiry_auto_reply'] =true;
			$model_item[$i]['is_comment_allow'] =true;
			$model_item[$i]['comment_api'] ='Test'.$i;
			$model_item[$i]['add_custom_button'] =true;
			$model_item[$i]['custom_button_url'] ="Test".$i;
			$model_item[$i]['meta_title'] ="Test".$i;
			$model_item[$i]['meta_description'] ="Test".$i;
			$model_item[$i]['tags'] ="Test".$i;
			$model_item[$i]['is_designable'] =true;
			$model_item[$i]['is_party_publish'] =true;
			$model_item[$i]['minimum_order_qty'] =$i;
			$model_item[$i]['maximum_order_qty'] =$i;
			$model_item[$i]['qty_unit'] =$i;
			$model_item[$i]['is_attachment_allow'] =true;
			$model_item[$i]['is_saleable'] =true;
			$model_item[$i]['is_downloadable'] =true;
			$model_item[$i]['is_rentable'] =true;
			$model_item[$i]['is_enquiry_allow'] =true;
			$model_item[$i]['is_template'] =true;
			$model_item[$i]['negative_qty_allowed'] =$i;
			$model_item[$i]['enquiry_send_to_admin'] =true;
			$model_item[$i]['watermark_position'] ='TopLeft';
			$model_item[$i]['watermark_opacity'] =$i;
			$model_item[$i]['qty_from_set_only'] =true;
			$model_item[$i]['custom_button_label'] ="Test".$i;
			$model_item[$i]['is_servicable'] =true;
			$model_item[$i]['is_purchasable'] =true;
			$model_item[$i]['maintain_inventory'] =true;
			$model_item[$i]['website_display'] =true;
			$model_item[$i]['allow_negative_stock'] =true;
			$model_item[$i]['is_productionable'] =true;
			$model_item[$i]['warranty_days'] =$i;
			$model_item[$i]['terms_and_conditions'] ="Test".$i;
			$model_item[$i]['watermark_text'] ="Test".$i;
			$model_item[$i]['duplicate_from_item_id'] ="Test".$i;
			$model_item[$i]['is_allowuploadable'] =true;
			$model_item[$i]['designer_id'] =$this->app->employee->id;
			$model_item[$i]['is_dispatchable'] =true;
			$model_item[$i]['upload_file_label'] ="Test".$i;
			$model_item[$i]['item_specific_upload_hint'] ="Test".$i;
			$model_item[$i]->save();

		} 

		return $this;
	}

}