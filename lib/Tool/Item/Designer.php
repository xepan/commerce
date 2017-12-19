<?php

namespace xepan\commerce;
class Tool_Item_Designer extends \View{
	public $options = [];
	public $item=null;
	public $target=null;
	public $render_designer=true;
	public $designer_mode=false;
	public $load_designer_tool = true;
	public $specification=array('width'=>false,'height'=>false,'trim'=>false,'unit'=>false);
	public $item_member_design_id;
	public $item_id;

	public $printing_mode = false;
	public $show_canvas = true;
	public $is_start_call = 1;
	public $show_tool_bar = true;
	public $show_pagelayout_bar = true;
	public $show_layout_bar = true;
	public $show_paginator = true;
	public $is_preview_mode = false;
	public $generating_image = false;
	public $show_safe_zone = 1;


	function init(){
		parent::init();

		//Load Associate Designer Item
		if(!$this->item_member_design_id){
			$this->item_member_design_id = $this->api->stickyGET('item_member_design');
		}

		$item_member_design_id = $this->item_member_design_id;

		if(!$this->item_id){
			$this->item_id = $this->api->stickyGET('xsnb_design_item_id');
		}

		$item_id = $this->item_id;
		
		$want_to_edit_template_item = $this->api->stickyGET('xsnb_design_template');
				
		$this->addClass('xshop-designer-tool xshop-item');

		if(isset($this->api->xepan_xshopdesigner_included)){
			// throw $this->exception('Designer Tool Cannot be included twise on same page','StopRender');
		}else{
			$this->api->xepan_xshopdesigner_included = true;
		}


		$designer = $this->add('xepan\base\Model_Contact');
		$designer_loaded = $designer->loadLoggedIn("Customer"); // return true of false
		
		// 3. Design own in-complete design again
		if($item_member_design_id and $designer_loaded){
			
			$target = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($item_member_design_id);
			if(!$target->loaded()) return;
				
			if($target['contact_id'] != $designer->id){
				$target->unload();
				unset($target);	
			}else{
				$this->item = $item = $target->ref('item_id');
			}
		}
			

		// 1. Designer wants edit template
		if($item_id and $want_to_edit_template_item=='true'  and $designer_loaded){
			$target = $this->item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			
			if(!$target->loaded()){
				return;	
			} 
			$item = $target;

			if($target['designer_id'] != $designer->id){
				return;
			}
			$this->designer_mode = true;
		}
		
		// 2. New personalized item
		if($item_id and is_numeric($item_id) and $want_to_edit_template_item !='true' and !isset($target)){

			$this->item = $item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			if(!$item->loaded()) {
				return;
			}

			$target = $this->add('xepan\commerce\Model_Item_Template_Design')->addCondition('item_id',$item->id);
			// $target = $item->ref('xepan\commerce\Item_Template_Design');
			$target['designs'] = $item['designs'];
		}


		
		if(!isset($target)){
			$this->render_designer = false;
			$this->add('View_Warning')->set('Insufficient Values, Item unknown or Not Authorised');
			$this->load_designer_tool = false;			
			return;
		}
		
		$this->target = $target;
		
		// check for required specifications like width / height
		if(!($this->specification['width'] = $item->specification('width')) OR !($this->specification['height'] = $item->specification('height')) OR !($this->specification['trim'] = $item->specification('trim'))){
			$this->add('View_Error')->set('Item Does not have \'width\' and/or \'height\' and/or \'trim\' specification(s) set');
			$this->load_designer_tool = false;
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

		$this->font_view = $this->add('xepan\commerce\View_Designer_FontCSS');
	}

	function render(){
		if($this->load_designer_tool){
		
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/designer.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/flat_top_orange.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/jquery.colorpicker.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/cropper.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/addtocart.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/slick.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/slick-theme.css" />');
		
		$this->js(true)->_css('tool/jquery.fancybox');
		$this->js(true)->_css('fontello');
		$this->js(true)->_css('jquery-ui');
		$this->js(true)->_css('tool/designer/jquery.colorpicker');

		$this->app->jquery->addStaticInclude('tool/designer/fabric.min');
		// $this->app->jquery->addStaticInclude('tool/designer/customiseControls.min');

		$this->js(true)
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/webfont.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fabric.min.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/aligning_guidelines.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/designer.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jquery.colorpicker.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/cropper.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/pace.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/addtocart.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/slick.js')
				;
		
		

		$font_family_config_array = json_encode($this->font_view->getFontList());

		// $this->js(true)->_load('item/addtocart');
		$saved_design = $design = json_decode($this->target['designs'],true);
		$selected_layouts_for_print = $design['selected_layouts_for_print']; // trimming other array values like px_width etc
		$design = $design['design']; // trimming other array values like px_width etc
		$design = json_encode($design);
		$cart_options = "{}";
		// $selected_layouts_for_print ="front_layout";
		$currency ="INR";
		
		// $cart_options = $this->item->getBasicCartOptions();
		// $cart_options['item_member_design'] = $_GET['item_member_design']?:'0';
		// $cart_options['show_qty'] = '1'; // ?????????????  from options
		// $cart_options['show_price'] = '1'; //$this->show_price;
		// $cart_options['show_custom_fields'] = '1'; //$this->show_custom_fields;
		// $cart_options['is_designable'] = $this->item['is_designable']; //$this->show_custom_fields;
				
		// echo "<pre>";
		// print_r ($saved_design);
		// echo "</pre>";
		// exit;
		// var_dump($this->specification);
		// exit;

		// used for show designer tool is in preview mode and only page layout bar is show
		$show_pagelayout_bar = $saved_design['show_pagelayout_bar']?$saved_design['show_pagelayout_bar']:$this->show_pagelayout_bar;
		$show_canvas = $saved_design['show_canvas']?$saved_design['show_canvas']:$this->show_canvas;
		$show_layout_bar = $this->show_layout_bar;
		if($this->is_preview_mode){
			$show_pagelayout_bar = true;
			$show_canvas = false;
			$show_layout_bar = false;
		}

			$this->js(true)->xepan_xshopdesigner(array('width'=>$this->specification['width'],
														'height'=>$this->specification['height'],
														'trim'=>$this->specification['trim'],
														'unit'=>$this->specification['unit'],
														'designer_mode'=> $this->designer_mode,
														'design'=>$design,
														'show_cart'=>'1',
														// 'cart_options' => $cart_options,
														'selected_layouts_for_print' => $selected_layouts_for_print,
														'item_id'=>$this->item_id,
														'item_member_design_id' => $this->item_member_design_id,
														'item_name' => $this->item['name'],
														'item_sale_price'=>$this->item['sale_price'],
														'item_original_price'=>$this->item['original_price'],
														'currency_symbole'=>$currency,
														'base_url'=>$this->api->url()->absolute()->getBaseURL(),
														'watermark_text'=>$this->options['watermark_text'],
														'calendar_starting_month'=>$saved_design['calendar_starting_month'],
														'calendar_starting_year'=>$saved_design['calendar_starting_year'],
														'calendar_event'=>$saved_design['calendar_event'],

														'is_start_call'=>'1',
														'show_tool_bar'=>$this->show_tool_bar,
														'show_pagelayout_bar'=>$show_pagelayout_bar,
														'show_canvas'=>$show_canvas,
														'printing_mode'=>$this->printing_mode,
														'show_layout_bar'=>$show_layout_bar,
														'show_paginator'=>$this->show_paginator,
														'mode'=>$saved_design['mode'],
														'ComponentsIncludedToBeShow'=>$saved_design['ComponentsIncludedToBeShow'],
														'BackgroundImage_tool_label'=>$saved_design['BackgroundImage_tool_label'],
														'show_tool_calendar_starting_month'=>$saved_design['show_tool_calendar_starting_month'],
														'is_preview_mode'=>$this->is_preview_mode,
														'generating_image'=>$this->generating_image,
														'font_family_list'=>$font_family_config_array,
														'show_safe_zone'=>$this->show_safe_zone
												));
			// ->slick(array("dots"=>false,"slidesToShow"=>3,"slidesToScroll"=>2));
		}
		parent::render();
	}

}