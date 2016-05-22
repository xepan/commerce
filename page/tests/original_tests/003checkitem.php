<?php

namespace xepan\commerce;

class page_tests_003checkitem extends \xepan\base\Page_Tester{
	public $title = 'Item Tests';

	function init(){
		$this->add('xepan\commerce\page_tests_init');
		parent::init();
	}

	function prepare_itemCheck(){
        $this->proper_responses['test_itemCheck']=[
			'name'=> 'Test0',			
			'sku'=> 'Test0',
			'original_price'=>"0.00",
			'sale_price'=>"0.00",
			'expiry_date'=>"2016-04-20",
			'description'=>'Test0',
			'show_detail'=>1,
			'show_price'=>1,
			'is_new'=>1,
			'is_mostviewed'=>1,
			'Item_enquiry_auto_reply'=>1,
			'is_comment_allow'=>1,
			'comment_api'=>'Test0',
			'add_custom_button'=>1,
			'custom_button_url'=>"Test0",
			'meta_title'=>"Test0",
			'meta_description'=>"Test0",
			'tags'=>"Test0",
			'is_designable'=>1,
			'is_party_publish'=>1,
			'minimum_order_qty'=>"0.00",
			'maximum_order_qty'=>"0.00",
			'qty_unit'=>"0.00",
			'is_attachment_allow'=>1,
			'is_saleable'=>1,
			'is_downloadable'=>1,
			'is_rentable'=>1,
			'is_enquiry_allow'=>1,
			'is_template'=>1,
			'negative_qty_allowed'=>"0.00",
			'enquiry_send_to_admin'=>1,
			'watermark_position'=>"TopLeft",
			'watermark_opacity'=>"0.00",
			'qty_from_set_only'=>1,
			'custom_button_label'=>"Test0",
			'is_servicable'=>1,
			'is_purchasable'=>1,
			'maintain_inventory'=>1,
			'website_display'=>1,
			'allow_negative_stock'=>1,
			'is_productionable'=>1,
			'warranty_days'=>"0.00",
			'terms_and_conditions'=>"Test0",
			'watermark_text'=>"Test0",
			'duplicate_from_item_id'=>"Test0",
			'is_allowuploadable'=>1,
			'designer_id'=>$this->app->employee->id,
			'is_dispatchable'=>1,
			'upload_file_label'=>"Test0",
			'item_specific_upload_hint'=>"Test0",

        ];
    }

    function test_itemCheck(){
    	$this->item = $this->add('xepan\commerce\Model_Item')
                                    ->loadBy('name','Test0');                                                             
        $result=[];
        foreach ($this->proper_responses
            ['test_itemCheck'] as $field => $value) {
            $result[$field] = $this->item[$field];            
        }
        return $result;   
    }
}