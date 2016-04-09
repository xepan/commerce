<?php

namespace xepan\commerce;

class Tool_Cart extends \xepan\cms\View_Tool{
	public $options = [
					// 'show_name'=>true
					// 'template'=>'short'
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
		$sum_tax_amount=0;
		$sum_total_amount=0;
		$sum_shipping_charge=0;
		$count = 0;
		foreach ($cart as $item) {
			$sum_amount_excluding_tax += $item['amount_excluding_tax'];
			$sum_tax_amount += $item['tax_amount'];
			$sum_total_amount += $item['total_amount'];
			$sum_shipping_charge += $item['shipping_charge'];
			$count++;
		}

		$this->total_count = $count;

		$this->template->trySet('sum_amount_excluding_tax',$sum_amount_excluding_tax);
		$this->template->trySet('tax_amount',$sum_tax_amount);
		$this->template->trySet('total_amount',$sum_total_amount);
		$this->template->trySet('shipping_charge',$sum_shipping_charge);
		$this->template->set('total_count',$this->total_count);
		
		$count = $this->total_count;
		$this->on('click','.xepan-commerce-cart-item-delete',function($js,$data)use($count){
			$count = $count - 1;
			$this->add('xepan\commerce\Model_Cart')->deleteItem($data['cartid']);
			$js_event = [
				$this->js()->find('.xepan-commerce-cart-item-count')->html($count),
				$js->closest('li')->hide(),
				$this->js()->univ()->successMessage('removed successfully')
			];
			return $js_event;
		});
	}

	function defaultTemplate(){		
		return ["view/tool/".$this->options['layout']];
	}
}