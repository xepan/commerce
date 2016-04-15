<?php
namespace xepan\commerce;

class Tool_Designer extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$item_member_design_id = $this->api->stickyGET('item_member_design');
		$item_id = $this->api->stickyGET('xsnb_design_item_id');
		$want_to_edit_template_item = $this->api->stickyGET('xsnb_design_template');

		if($_GET['show_cart'] and $item_id){
			//load item model
			$v = $this->add('View')->addClass('xshop-item');
			$v1 = $v->add('View')->addClass('xepan-commerce-tool-item-sale-price')->set('Price');
			$v2 = $v->add('View')->addClass('xepan-commerce-tool-item-original-price')->set('Old Price');

			$model_template_design = $this->add('xepan\commerce\Model_Item_Template_Design');
			$model_template_design
					->addCondition('item_id',$item_id)
					->addCondition('id',$item_member_design_id)
					;
			
			$customer = $this->add('xepan\base\Model_Contact');
			$customer_logged_in = $customer->loadLoggedIn();
			
			if(!$model_template_design->count()->getOne() and $customer_logged_in){
				throw new \Exception("some thing happen wrong, design not found");
			}

			$model_template_design->tryLoadAny();
			$design = $model_template_design['designs'];
			
			$design = json_decode($design,true);

			$selected_layouts_for_print = $design['selected_layouts_for_print'];

			foreach ($selected_layouts_for_print as $page => $layout) {
				// http://localhost/xepan2/index.php?page=xepan_commerce_designer_thumbnail
				// &xsnb_design_item_id=2118
				// &page_name=Front Page
				// &layout_name=Main Layout
				// &item_member_design_id=39
				$thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',
								[
									'xsnb_design_item_id'=>$item_id,
									'item_member_design_id'=>$item_member_design_id,
									'page_name'=>$page,
									'layout_name'=>$layout
								]);

				$v->add('View')->setElement('img')->setAttr('src',$thumb_url);
			}

			$cart_tool = $v->add('xepan\commerce\Tool_Item_AddToCartButton',['options',$this->options]);
			$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
			$cart_tool->setModel($item);
		}else{			

			$next_btn = $this->add('Button');
			$next_btn->set('Next');
			$next_btn->js('click')->univ()->location($this->api->url(['show_cart'=>1]));
			$designer_tool = $this->add('xepan\commerce\Tool_Item_Designer',['options',$this->options]);
			
		}
	}
}