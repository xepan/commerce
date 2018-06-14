<?php

namespace xepan\commerce;

class Tool_Item_Detail extends \xepan\cms\View_Tool{
	public $options = [
				'layout'=>'primary',/*flat,collapse,tab*/
				'specification_layout'=>'specification',
				'show_item_upload'=>false,
				'show_addtocart'=>true,
				'show_multi_step_form'=>false,
				'multi_step_form_layout'=>"stacked",
				'custom_template'=>"",
				'personalized_page'=>"",
				'personalized_button_label'=>"Personalized",
				'addtocart_button_label'=>'Add To Cart',
				'show_addtowishlist'=>false,
				'wishlist_button_name'=>"add to wish list",
				'wishlist_not_login_error_message'=>'login first to add in your wish',
				'wishlist_not_customer_error_message'=>'you are not a customer',
				'wishlist_success_message'=>'added in your wish list',

				'show_price_or_amount'=>false,
				"show_original_price"=>true, // sale Price, sale/Original Price
				"show_shipping_charge"=>false,
				"shipping_charge_with_item_amount"=>false,
				"checkout_page"=>"",
				'continue_shopping_page'=>"index",
				'amount_group_in_multistepform'=>null,
				"show_qty_input"=>true,
				"qty_label"=>"Qty",
				"show_review"=>false,
				"show_review_form"=>true
			];
	public $item;
	function init(){
		parent::init();

		// added for price changing
		$this->addClass('xshop-item');

		$item_id = $this->api->stickyGET('commerce_item_id');
		$item_slug_url = $this->api->stickyGET('commerce_item_slug_url');

		$this->item = $this->add('xepan\commerce\Model_Item');
		if($this->app->enable_sef){
			$this->item->tryLoadBy('slug_url',$item_slug_url);
		}else
			$this->item->tryLoad($item_id?:-1);

		if(!$this->item->loaded()){
			$this->add('View')->set('No Record Found');
			$this->template->tryDel("xepan_commerce_itemdetail_wrapper");
			return;
		}

		$this->setModel($this->item);
	}
	
	function addToolCondition_show_addtowishlist($value){
		
		if(!$value){
			$this->current_row_html['add_to_wishlist_wrapper'] = " ";
			return;
		}
		
		if(!$this->template->hasTag('add_to_wishlist')){
			$this->add('View')->set('Spot(add_to_wishlist) not found, please add to tool template');
			return;
		}

		$wish_options = [
				'show_add_button'=>$this->options['show_addtowishlist'],
				'button_name'=> $this->options['wishlist_button_name'],
				'not_login_error_message'=>$this->options['wishlist_not_login_error_message'],
				'not_customer_error_message'=>$this->options['wishlist_not_customer_error_message'],
				'success_message'=>$this->options['wishlist_success_message']
			];

		$tool_wish_list = $this->add('xepan\commerce\View_Item_AddToWishList',
				[
					'name'=>'a_'.$this->getModel()->id,
					'options'=>$wish_options
			],'add_to_wishlist');
		$tool_wish_list->setModel($this->getModel());

		$this->current_row_html['add_to_wishlist'] = $tool_wish_list->getHtml();
	}


	function setModel($model){
		//tryset html for description 
		$this->template->trySetHtml('item_description', $model['description']);
		$this->template->trySetHtml('name', $model['name']);

		//specification
		$spec_grid = $this->add('xepan\base\Grid',null,'specification',["view/tool/item/detail/".$this->options['specification_layout']]);
		$spec_grid->setModel($model->specification()->addCondition('is_system','<>',true),['name','value']);

		//add personalized button

		if($model['is_designable']){
			// add Personalioze View
			$personalized_page_url = $this->app->url(
										$this->options['personalized_page'],
										['commerce_item_id'=>$model['id'],'xsnb_design_item_id'=>$model['id']]
									);
			$this->add('Button',null,'personalizedbtn')
					->addClass("xepan-commerce-item-personalize btn btn-primary btn-block")
					->set(
							$this->options['personalized_button_label']?:"Personalize"
						)
					->js('click',
							$this->js()->univ()->location($personalized_page_url)
						);
		}else{
			$this->current_row_html['personalizedbtn'] = "";
			$this->current_row_html['personalizedbtn_wrapper'] = "";
		}

		//price calculation or add to cart button setup
		//if item is designable than hide "AddToCart" button
		if($model['is_saleable']){
			$options = [
						'button_name'=>$this->options['addtocart_button_label'],
						'show_addtocart_button'=>$model['is_designable']?0:1,
						'show_price'=>$this->options['show_price_or_amount'],
						'show_multi_step_form'=>$this->options['show_multi_step_form'],
						'form_layout'=>$this->options['multi_step_form_layout'],
						'show_shipping_charge'=>$this->options['show_shipping_charge'],
						'shipping_charge_with_item_amount'=>$this->options['shipping_charge_with_item_amount'],
						'checkout_page' => $this->options['checkout_page'],
						'continue_shopping_page'=>$this->options['continue_shopping_page'],
						'amount_group_in_multistepform'=>$this->options['amount_group_in_multistepform'],
						"show_qty_input"=>$this->options['show_qty_input'],
						"qty_label"=>$this->options['qty_label']
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
		// if($model['is_allowuploadable'] and $this->options['show_item_upload']){
		// 	// $contact = $this->add('xepan/base/Model_Contact');
  //  //    		if(!$contact->loadLoggedIn()){
  //  //  			//Todo add login panle here
		// 	// 	$this->add('View_Error',null,'item_upload')->set('add Login Panel Here');
		// 	// 	return;
  //  //    		}

  //  //    		$member_image=$this->add('xepan/commerce/Model_Designer_Images');
		// 	// $images_count = 1;
		// 	// if($model['upload_file_label']){
		// 	// 	$upload_array=explode(',', $model['upload_file_label']);
		// 	// 	$images_count = count($upload_array);
		// 	// }

		// 	$v = $this->add('View',null,'item_upload');

		// 	$this->api->stickyGET('show_cart');

		// 	if($_GET['show_cart']){
		// 		$v->add('Button')->setLabel('Back')->js('click',$v->js()->reload(array('show_cart'=>0)));

		// 		$options = [
		// 				'button_name'=>$this->options['addtocart_button_label'],
		// 				'show_addtocart_button'=>$model['is_designable']?0:1,
		// 				'show_price'=>$this->options['show_price_or_amount'],
		// 				'form_layout'=>$this->options['multi_step_form_layout'],
		// 				'show_original_price'=>$this->options['show_original_price']
		// 				];

		// 		$cart_btn = $v->add('xepan\commerce\Tool_Item_AddToCartButton',
		// 			[
		// 				'name' => "addtocart_view_".$model->id,
		// 				'options'=>$options
		// 			]);
		// 		$cart_btn->setModel($model);

		// 	}else{
				
		// 		$v->add('View')->setHTML($model['item_specific_upload_hint']);
		// 		$up_form = $v->add('Form');
		// 		$multi_upload_field = $up_form->addField('Upload','upload',"")
		// 				->allowMultiple($images_count)
		// 				->setFormatFilesTemplate('view/tool/item/detail/file_upload');
		// 		$multi_upload_field->setAttr('accept','.jpeg,.png,.jpg');
		// 		$multi_upload_field->setModel('filestore/Image');

		// 		$up_form->addSubmit('Next');
				
		// 		if($up_form->isSubmitted()){
		// 			//check for the image count
		// 			$upload_images_array = explode(",",$up_form['upload']);

		// 			if($images_count != count($upload_images_array))
		// 				$up_form->error('upload','upload all images');

		// 			$image_cat_model = $this->add('xepan\commerce\Model_Designer_Image_Category')->loadCategory($model['name']);

		// 			foreach ($upload_images_array as $file_id) {
		// 			    $image_model = $this->add('xepan/commerce/Model_Designer_Images');
		// 				$image_model['file_id'] = $file_id;
		// 				$image_model['designer_category_id'] = $image_cat_model->id;
		// 				$image_model->saveAndUnload();
		// 			}

		// 			$up_form->js(null,$v->js()->reload(array('show_cart'=>1,'file_upload_ids'=>$up_form['upload'])))->execute();
		// 		}
		// 	}

		// }

		if($this->options['show_review']){
			$this->add('xepan\commerce\View_Review',[
								'related_model'=>$model,
								'related_document_type'=>'xepan\commerce\Model_Item',
								'show_review_form'=>$this->options['show_review_form']
							]
						,'review');
		}else{
			$this->template->tryDel('review_wrapper');
			$this->template->tryDel('review');
		}

		parent::setModel($model);
	}

	function defaultTemplate(){
		$layout = $this->options['layout'];

		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/item/detail/layout/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				throw new \Exception($path);
				$this->add('View_Warning')->set('template not found');
				return;
			}else{
				$layout = $this->options['custom_template'];
			}
		}
		
		return ['view/tool/item/detail/layout/'.$layout];
	}
	
}