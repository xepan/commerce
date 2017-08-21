<?php

namespace xepan\commerce;

class page_designer_exportpdf extends \xepan\base\Page{
	
	function init(){
		parent::init();

		// $download_pdf = $this->add('Button')->set('Download PDf');
		// if($download_pdf->isClicked()){
		// 	//var canvas = document.getElementById("canvas");
		// 	// var dataURL = canvas.toDataURL();
		// 	// console.log(dataURL);
		// }
		
		$this->item_member_design_id = $item_member_design_id = $this->api->stickyGET('item_member_design_id');
		$this->target = $target = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($item_member_design_id);
		
		$this->item = $item = $target->ref('item_id');
		$this->item_id = $item_id = $target['item_id'];
		
		if(!($this->specification['width'] = $item->specification('width')) OR !($this->specification['height'] = $item->specification('height')) OR !($this->specification['trim'] = $item->specification('trim'))){
			$this->add('View_Error')->set('Item Does not have \'width\' and/or \'height\' and/or \'trim\' specification(s) set');
			return;
		}else{
			// width and hirght might be like '51mm' and '91 mm' so get digit and unit sperated
			// print_r($this->specification);
			preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $this->specification['width'],$temp);
			$this->specification['width']= $temp[1][0];
			preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $this->specification['height'],$temp);
			$this->specification['height']= $temp[1][0];
			$this->specification['unit']=$temp[2][0];

			preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $this->specification['trim'],$temp);
			$this->specification['trim']= $temp[1][0];
		}

		$this->js(true)
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/webfont.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fabric.min.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/customiseControls.min.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/aligning_guidelines.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/designer.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jquery.colorpicker.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/cropper.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/pace.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/addtocart.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/slick.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fontfaceobserver.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fontfaceobserver.standalone.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jspdf.min.js')
				;
		
		$view_font = $this->add('xepan\commerce\View_Designer_FontCSS');
		$font_family_config_array = json_encode($view_font->getFontList());

		$saved_design = $design = json_decode($this->target['designs'],true);
		$selected_layouts_for_print = $design['selected_layouts_for_print']; // trimming other array values like px_width etc
		$design = $design['design']; // trimming other array values like px_width etc
		$design = json_encode($design);
		$cart_options = "{}";
		// $selected_layouts_for_print ="front_layout";
		$currency ="INR";
		
		//generating_file name
		$file_name = "";
		if($_GET['order_no']){
			$sale_order = $this->add('xepan\commerce\Model_SalesOrder')->load($_GET['order_no']);
			$file_name = $_GET['order_no']."_".$sale_order['contact']."_".$sale_order['created_at']."_".$item['name']."_".$item_member_design_id;
		}	


		$this->js(true)->xepan_xshopdesigner(array('width'=>$this->specification['width'],
														'height'=>$this->specification['height'],
														'trim'=>$this->specification['trim'],
														'unit'=>$this->specification['unit'],
														'designer_mode'=> false,
														'design'=>$design,
														'show_cart'=>'0',
														// 'cart_options' => $cart_options,
														'selected_layouts_for_print' => $selected_layouts_for_print,
														'item_id'=>$this->item_id,
														'item_member_design_id' => $this->item_member_design_id,
														'item_name' => $this->item['name'] ." ( ".$this->item['sku']." ) ",
														'item_sale_price'=>$this->item['sale_price'],
														'item_original_price'=>$this->item['original_price'],
														'currency_symbole'=>$currency,
														'base_url'=>$this->api->url()->absolute()->getBaseURL(),
														'calendar_starting_month'=>$saved_design['calendar_starting_month'],
														'calendar_starting_year'=>$saved_design['calendar_starting_year'],
														'calendar_event'=>$saved_design['calendar_event'],
														'printing_mode'=>true,
														'show_canvas'=>0,
														'is_start_call'=>1,
														'show_tool_bar'=>0,
														'show_pagelayout_bar'=>true,
														'file_name'=>$file_name,
														'generating_image'=>true,
														'font_family_list'=>$font_family_config_array
												));

	}
}