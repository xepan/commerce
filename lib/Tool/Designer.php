<?php
namespace xepan\commerce;

class Tool_Designer extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$item_member_design_id = $this->api->stickyGET('item_member_design');
		$item_id = $this->api->stickyGET('xsnb_design_item_id');
		$want_to_edit_template_item = $this->api->stickyGET('xsnb_design_template');
		$this->api->stickyGET('show_cart');
		$this->api->stickyGET('show_preview');

		if($_GET['show_cart'] and $item_id){
			//load item model
			// $v= $this;
			$v = $this->add('View')->addClass('xshop-item');
			$v1 = $v->add('View')->addClass('xepan-commerce-tool-item-sale-price')->set('Price');
			$v2 = $v->add('View')->addClass('xepan-commerce-tool-item-original-price')->set('Old Price');

			$cart_tool = $v->add('xepan\commerce\Tool_Item_AddToCartButton',['options',$this->options,'item_member_design'=>$item_member_design_id]);
			$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
			$cart_tool->setModel($item);

		}elseif ($_GET['show_preview']) {

			//previous button redirect to designer
			$previous_btn = $this->add('Button');
			$previous_btn->set('Previous');
			$previous_btn->js('click')->univ()->location($this->api->url(null,['show_preview'=>0]));

			//next button for addto cart button
			$form_design_approved = $this->add('Form');

			//load designs
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
				$v = $form_design_approved->add('View')->setElement('img')->setAttr('src',$thumb_url);
				$v->add('View')->setElement('h2')->set($page." - ".$layout);
			}

			$approved_checkbox = $form_design_approved->addField('checkbox','approved',$this->options['approved_design_checkbox_label']);
			$approved_checkbox->validate('required');
			$form_design_approved->addSubmit('Next');
			if($form_design_approved->isSubmitted()){
				$this->app->stickyForget('show_preview');
				$form_design_approved->js()->univ()->location($this->api->url(['show_cart'=>1]))->execute();
			}

		}
		else{
			
			//check for the designed is saved or not
			$next_btn = $this->add('Button',null,'step1_next_button');
			$next_btn->set('Next');
			$next_btn->js('click')->univ()->location($this->api->url(['show_preview'=>1]));
			$designer_tool = $this->add('xepan\commerce\Tool_Item_Designer',['options',$this->options],'designer_tool');
			
		}
	}

	function defaultTemplate(){
		return ['view\tool\designer'];
	}
}