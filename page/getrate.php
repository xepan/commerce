<?php

namespace xepan\commerce;

class page_getrate extends \Page{
	
	function init(){
		parent::init();
		
		//TODO temporary added currency symbole
		$this->api->currency['symbole'] = "Rs-"; 

		$required='amount';
		if($_GET['required']=='rate'){
			$item= $this->add('xepan\commerce\Model_Item')->load($_GET['item_id']);
			extract($item->getPrice(json_decode($_GET['custome_fields'],true),$_GET['qty'],null));

			echo $this->api->currency['symbole']." ".$sale_price;
			exit;	
		}else{
			$item= $this->add('xepan\commerce\Model_Item')->load($_GET['item_id']);		
			extract($item->getAmount(json_decode($_GET['custome_fields'],true),$_GET['qty'],null));
			echo $this->api->currency['symbole']." ".$original_amount.','.$this->api->currency['symbole']." ".$sale_amount;
			exit;
		}
	}
}