<?php

namespace xepan\commerce;

class Tool_Item_Detail extends \xepan\cms\View_Tool{
	public $options = [
				// 'display_layout'=>'item-description',/*flat*/
					 
				];
	public $item;
	function init(){
		parent::init();

		$item_id = $this->api->stickyGET('commerce_item_id');

		$this->item = $this->add('xepan\commerce\Model_Item')->load($item_id);
		$this->setModel($this->item);
	}

	function setModel($model){
		//tryset html for description 
		$this->template->trySetHtml('item_description', $model['description']);

		//specification
		$spec_grid = $this->add('xepan\base\Grid',null,'specification',["view/tool/item/detail/".$this->options['specification_layout']]);
		$spec_grid->setModel($model->specification(),['name','value']);

		//add personalized button
		if($model['is_designable']){
			// add Personalioze View
			$personalized_page_url = $this->app->url(
										$this->options['personalized_page'],
										['xsnb_design_item_id'=>$model['id']]
									);
			$this->add('Button',null,'personalized_button')
					->set(
							$this->options['personalized_button_label']?:"personalized"
						)
					->js('click',
							$this->js()->univ()->location($personalized_page_url)
						);
		}

		//price calculation or add to cart button setup
		//if item is designable than hide "AddToCart" button
		if($model['is_saleable']){
			$options = [
						'button_name'=>$this->options['addtocart_button_label'],
						'show_addtocart_button'=>$model['is_designable']?0:1

						];

			$cart_btn = $this->add('xepan\commerce\Tool_Item_AddToCartButton',
				[
					'name' => "addtocart_view_".$model->id,
					'options'=>$options
				],'Addtocart'
				);
			$cart_btn->setModel($model);
		}

		//add Item Uploadable		
		if($model['is_allowuploadable'] and $this->options['show_item_upload']){			
			$contact = $this->add('xepan/base/Model_Contact');
      		if(!$contact->loadLoggedIn()){
      			$this->add('View_Error',null,'item_upload')->set('Login First');
      		}
		}

		parent::setModel($model);
	}

	function defaultTemplate(){
		return ['view/tool/'.$this->options['layout']];
	}
	
}