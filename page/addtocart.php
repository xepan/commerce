<?php

namespace xepan\commerce;

class page_addtocart extends \Page{
	function init(){
		parent::init();
		
		$cart = $this->add('xepan\commerce\Model_Cart');
		$cart->addItem($_POST['item_id'],$_POST['qty'],$_POST['item_member_design_id'],json_decode($_POST['custome_fields'],true),$otherfield=null,$_POST['file_upload_id']);

		$this->js(null,$this->js()->univ()->successMessage('Item Added to Cart'))->_selector('.xshop-cart')->trigger('reload')->execute();
		
		exit;
	}
}