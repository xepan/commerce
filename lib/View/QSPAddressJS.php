<?php


namespace xepan\commerce;

class View_QSPAddressJS extends \View {
	function render(){

		if($_GET['changed_contact_id']){
			$contact = $this->add('xepan\base\Model_Contact');
			$contact->load($_GET['changed_contact_id']);
			$js=[];

			// billing address
			$js[] = $this->js()->_selector('.billing_address')->find('input')->val($contact['billing_address']?:$contact['address']);
			$js[] = $this->js()->_selector('.billing_country_id')->find('select')->val($contact['billing_country_id']?:$contact['country_id']);
			$js[] = $this->js()->_selector('.billing_state_id')->find('select')->val($contact['billing_state_id']?:$contact['state_id']);
			$js[] = $this->js()->_selector('.billing_city')->find('input')->val($contact['billing_city']?:$contact['city']);
			$js[] = $this->js()->_selector('.billing_pincode')->find('input')->val($contact['billing_city']?:$contact['pin_code']);
			
			// shipping address
			$js[] = $this->js()->_selector('.shipping_address')->find('input')->val($contact['shipping_address']?:$contact['address']);
			$js[] = $this->js()->_selector('.shipping_country_id')->find('select')->val($contact['shipping_country_id']?:$contact['country_id']);
			$js[] = $this->js()->_selector('.shipping_state_id')->find('select')->val($contact['shipping_state_id']?:$contact['state_id']);
			$js[] = $this->js()->_selector('.shipping_city')->find('input')->val($contact['shipping_city']?:$contact['city']);
			$js[] = $this->js()->_selector('.shipping_pincode')->find('input')->val($contact['shipping_city']?:$contact['pin_code']);

			if(in_array($contact['type'],['Customer','Supplier'])){
				
				$tmp=$this->add('xepan\commerce\Model_'.$contact['type']);
				$tmp->load($_GET['changed_contact_id']);

				$js[] = $this->js()->_selector('.currency')->find('select')->select2("val",$tmp['currency_id']?:$tmp['currency_id']);
			}

			$this->js(true,$js);
		}

		parent::render();
	}
}