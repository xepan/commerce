<?php


namespace xepan\commerce;

class View_QSPAddressJS extends \View {
	function render(){

		if($_GET['changed_contact_id']){
			$contact = $this->add('xepan\base\Model_Contact');
			$contact->load($_GET['changed_contact_id']);
			$js=[];

			if(in_array($contact['type'],['Customer','Supplier'])){
				$contact_m=$this->add('xepan\commerce\Model_'.$contact['type']);
				$contact_m->load($_GET['changed_contact_id']);
			}else{
				$contact_m = $contact;
			}
			
			$due_invoice = $this->add('xepan\commerce\Model_SalesInvoice')
							->addCondition('contact_id',$contact_m->id)	
							->addCondition('status','Due');	
			// echo "string" . $due_invoice->sum('net_amount');				
				// billing address
				$js[] = $this->js()->_selector('.billing_address')->find('input')->val($contact_m['billing_address']?:$contact_m['address']);
				$js[] = $this->js()->_selector('.billing_country_id')->find('select')->val($contact_m['billing_country_id']?:$contact_m['country_id']);
				$js[] = $this->js()->_selector('.billing_state_id')->find('select')->val($contact_m['billing_state_id']?:$contact_m['state_id']);
				$js[] = $this->js()->_selector('.billing_city')->find('input')->val($contact_m['billing_city']?:$contact_m['city']);
				$js[] = $this->js()->_selector('.billing_pincode')->find('input')->val($contact_m['billing_pincode']?:$contact_m['pin_code']);
				
				// shipping address
				$js[] = $this->js()->_selector('.shipping_address')->find('input')->val($contact_m['shipping_address']?:$contact_m['address']);
				$js[] = $this->js()->_selector('.shipping_country_id')->find('select')->val($contact_m['shipping_country_id']?:$contact_m['country_id']);
				$js[] = $this->js()->_selector('.shipping_state_id')->find('select')->val($contact_m['shipping_state_id']?:$contact_m['state_id']);
				$js[] = $this->js()->_selector('.shipping_city')->find('input')->val($contact_m['shipping_city']?:$contact_m['city']);
				$js[] = $this->js()->_selector('.shipping_pincode')->find('input')->val($contact_m['shipping_pincode']?:$contact_m['pin_code']);

				$js[] = $this->js()->_selector('.currency')->find('select')->select2("val",$contact_m['currency_id']?:$contact_m['currency_id']);
				if($due_invoice->sum('net_amount') > '0')
						$js[] = $this->js()->_selector('span.contact-due-amount')->html("Due Amount :-".$due_invoice->sum('net_amount'));
				else	
						$js[] = $this->js()->_selector('span.contact-due-amount')->html(" ");
			$this->js(true,$js);
		}

		parent::render();
	}
}