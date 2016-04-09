<?php

namespace xepan\commerce;

class Tool_Cart extends \xepan\cms\View_Tool{
	public $options = [
					// 'show_name'=>true
					// 'template'=>'short'
					//'show_customfield'=>'true'
					//'image="yes"'
					//show_qtyform="true"
				];
	public $total_count=0;
	function init(){
		parent::init();
		
		$this->addClass('xshop-cart');
		$this->js('reload')->reload();

		$cart = $this->add('xepan\commerce\Model_Cart');
		
		$lister=$this->add('CompleteLister',null,'lister',["view/tool/".$this->options['layout'],'lister']);
		$lister->setModel($cart);

		$sum_amount_excluding_tax=0;
		$gross_amount=0;
		$total_tax_amount=0;
		$total_shipping_charge = 0;
		$discount_amount = 0;
		$net_amount=0;
		$count = 0;
		
		foreach ($cart as $item) {
			$sum_amount_excluding_tax += $item['amount_excluding_tax'];
			$total_tax_amount += $item['tax_amount'];
			$gross_amount += $item['total_amount'];
			$total_shipping_charge += $item['shipping_charge'];
			$count++;
		}

		$net_amount = $gross_amount + $total_shipping_charge - $discount_amount;

		$this->total_count = $count;

		$this->template->trySet('sum_amount_excluding_tax',$sum_amount_excluding_tax);
		$this->template->trySet('tax_amount',$total_tax_amount);
		$this->template->trySet('net_amount',$net_amount);
		$this->template->trySet('gross_amount',$gross_amount);
		$this->template->trySet('total_shipping_amount',$total_shipping_charge);
		$this->template->set('total_count',$this->total_count);
		
		$count = $this->total_count;
		$this->on('click','.xepan-commerce-cart-item-delete',function($js,$data)use($count){
			$count = $count - 1;
			$this->add('xepan\commerce\Model_Cart')->deleteItem($data['cartid']);
			$js_event = [
				$this->js()->find('.xepan-commerce-cart-item-count')->html($count),
				$js->closest('.xepan-commerce-tool-cart-item-row')->hide(),
				$this->js()->univ()->successMessage('removed successfully')
			];
			return $js_event;
		});

		$cart_detail_url = $this->api->url($this->options['cart-detail-url']);
		$this->template->trySet('cart_detail_url',$cart_detail_url)	;
		
		$place_order_button = $this->add('Button',null,'place_order')->set($this->options['place_order_button_name']);
		$place_order_button->js('click')->redirect($this->api->url($this->options['checkout_page']));

		$lister->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$cart]);
	}

	function defaultTemplate(){
		return ["view/tool/".$this->options['layout']];
	}

	function addToolCondition_row_image($value,$l){
			//Image
			$model = $l->model;			
			// get preview image of editable items
			if($model['item_member_design_id']){
				$img_url='index.php?page=xepan_commerce_designer_thumbnail&item_member_design_id='.$model['item_member_design_id'];
			
			}else if($model['file_upload_id']){
				$img_url = $model['file_upload_id'];
			
			}else
				$img_url = $model->getImageUrl();

			$l->current_row_html['image_url'] = $img_url;
	}

	function addToolCondition_row_show_customfield($value,$l){
		$lister = $l->add('Lister',null,'custom_field',["view/tool/".$this->options['layout'],'custom_field']);
		$lister->setSource($l->model['custom_fields']);
		$l->current_row_html['custom_field'] = $lister->getHtml();
	}

	function addToolCondition_row_show_qtyform($value,$l){
		$form = $l->add('Form',null,'qty_form',['form/empty']);

		$form->addField('Hidden','cartid')->set($l->model->id);

		$qty_field = $form->addField('Number','qty','')->set($l->model['qty']);
		
		$qty_field->js('change',$form->js()->submit());

		if($form->isSubmitted()){
			$cart = $this->add('xepan\commerce\Model_Cart')->load($form['cartid']);			
			$cart->updateCart($form['cartid'],$form['qty']);
				
			$js = [$form->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')];
			$form->js(null,$js)->execute();

			// $form->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')->execute();
		}

		$l->current_row_html['qty_form'] = $form->getHtml();
	}	


}