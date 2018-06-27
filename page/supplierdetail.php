<?php
 
/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class page_supplierdetail extends \xepan\base\Page {
	public $title='Supplier Details';
	public $breadcrumb=['Home'=>'index','Suppliers'=>'xepan_commerce_supplier','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$supplier= $this->add('xepan\commerce\Model_Supplier')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		if($action=="add"){
			$base_validator = $this->add('xepan\base\Controller_Validator');

			$form = $this->add('Form',['validator'=>$base_validator],'contact_view_full_width');		
			$form->setLayout(['page\customer\supplier-detail-full-width']);
			$form->setModel($supplier,['first_name','last_name','address','city','country_id','state_id','pin_code','organization','post','website','remark','pan_no','currency_id']);
			$form->addField('line','email_1')->validate('email');
			$form->addField('line','email_2');
			$form->addField('line','email_3');
			$form->addField('line','email_4');
			
			$form->addField('line','contact_no_1');
			$form->addField('line','contact_no_2');
			$form->addField('line','contact_no_3');
			$form->addField('line','contact_no_4');
			$form->addField('Checkbox','want_to_add_next_supplier')->set(true);

			$country_field =  $form->getElement('country_id');
			$state_field = $form->getElement('state_id');
			$state_field->dependsOn($country_field);
			// if($cntry_id = $this->app->stickyGET('country_id')){			
			// 	$state_field->getModel()->addCondition('country_id',$cntry_id);
			// }

			// $country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));
			$supplier->addOtherInfoToForm($form);
			$form->addSubmit('Add')->addClass('btn btn-primary');

			if($form->isSubmitted()){				
				try{
					$this->api->db->beginTransaction();

					if(!$form['currency_id'])
						$form->error('currency_id','must not be empty');
					
					$form->save();
					$new_supplier_model = $form->getModel();

					if($form['email_1']){
						$new_supplier_model->checkEmail($form['email_1'],$new_supplier_model,'email_1');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_supplier_model->id;
						$email['head'] = "Official";
						$email['value'] = $form['email_1'];
						$email->save();
					}

					if($form['email_2']){
						$new_supplier_model->checkEmail($form['email_2'],$new_supplier_model,'email_2');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_supplier_model->id;
						$email['head'] = "Official";
						$email['value'] = $form['email_2'];
						$email->save();
					}

					if($form['email_3']){
						$new_supplier_model->checkEmail($form['email_3'],$new_supplier_model,'email_3');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_supplier_model->id;
						$email['head'] = "Personal";
						$email['value'] = $form['email_3'];
						$email->save();
					}

					if($form['email_4']){
						$new_supplier_model->checkEmail($form['email_4'],$new_supplier_model,'email_4');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_supplier_model->id;
						$email['head'] = "Personal";
						$email['value'] = $form['email_4'];
						$email->save();
					}

					// Contact Form
					if($form['contact_no_1']){
						$new_supplier_model->checkPhone($form['contact_no_1'],$new_supplier_model,'contact_no_1');
						
						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_supplier_model->id;
						$phone['head'] = "Official";
						$phone['value'] = $form['contact_no_1'];
						$phone->save();
					}

					if($form['contact_no_2']){
						$new_supplier_model->checkPhone($form['contact_no_2'],$new_supplier_model,'contact_no_2');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_supplier_model->id;
						$phone['head'] = "Official";
						$phone['value'] = $form['contact_no_2'];
						$phone->save();
					}

					if($form['contact_no_3']){
						$new_supplier_model->checkPhone($form['contact_no_3'],$new_supplier_model,'contact_no_3');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_supplier_model->id;
						$phone['head'] = "Personal";
						$phone['value'] = $form['contact_no_3'];
						$phone->save();
					}
					if($form['contact_no_4']){
						$new_supplier_model->checkPhone($form['contact_no_4'],$new_supplier_model,'contact_no_4');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_supplier_model->id;
						$phone['head'] = "Personal";
						$phone['value'] = $form['contact_no_4'];
						$phone->save();
					}

					// add contact other info
					$contact_other_info_config_m = $this->add('xepan\base\Model_Config_ContactOtherInfo');
					$contact_other_info_config_m->addCondition('for','Supplier');

					foreach($contact_other_info_config_m->config_data as $of) {
						if($of['for'] != "Supplier" ) continue;

						if(!$of['name']) continue;
						$field_name = $this->app->normalizeName($of['name']);

						$existing = $this->add('xepan\base\Model_Contact_Other')
							->addCondition('contact_id',$new_supplier_model->id)
							->addCondition('head',$of['name'])
							->tryLoadAny();
						$existing['value'] = $form[$field_name];
						$existing->save();
					}

					$this->api->db->commit();
				}catch(\Exception_StopInit $e){

		        }catch(\Exception $e){
		            $this->api->db->rollback();
		            throw $e;
		        }	

		        if($form['want_to_add_next_supplier']){
		        	$form->js(null,$form->js()->reload())->univ()->successMessage('Supplier Created Successfully')->execute();
		        }

				$form->js(null,$form->js()->univ()->successMessage('Supplier Created Successfully'))->univ()->redirect($this->app->url(null,['action'=>"edit",'contact_id'=>$new_supplier_model->id]))->execute();	
			}
			
			$this->template->del('details');
		}else{
			$contact_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\commerce\Model_Supplier','view_document_class'=>'xepan\hr\View_Document'],'contact_view');
			$contact_view->setModel($supplier);
		}		

		if($supplier->loaded()){
			$d = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'contact_id'],'basic_info',['page/supplier/detail','basic_info']);
			$d->setModel($supplier,['address','pan_no','organization','city','currency','pin_code','remark'],['address','pan_no','organization','city','state_id','country_id','currency_id','pin_code','remark']);

			$country = $d->form->getElement('country_id');
			$state = $d->form->getElement('state_id');
			
			if($this->app->stickyGET('country_id'))
				$state->getModel()->addCondition('country_id',$_GET['country_id'])->setOrder('name','asc');
				$country->js('change',$state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state->name]),'country_id'=>$country->js()->val()]));
			

	/**

			Orders

	*/

				$ord = $this->add('xepan\commerce\Model_PurchaseOrder')
				->addCondition('contact_id',$supplier->id);
				$crud_ord = $this->add('xepan\hr\CRUD',
									['action_page'=>'xepan_commerce_purchaseorderdetail'],
									'orders',
									['view/supplier/order/grid']);
				$crud_ord->setModel($ord);
				// $crud_ord->grid->addQuickSearch(['orders']);
				$crud_ord->add('xepan\base\Controller_MultiDelete');

				if(!$crud_ord->isEditing()){
					$crud_ord->grid->js('click')->_selector('.do-view-supplier-order')->univ()->frameURL('Purchaseorder Detail',[$this->api->url('xepan_commerce_purchaseorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-purchaseorder-id]')->data('id')]);
				}
	/**

			Invoices

	*/
				$inv = $this->add('xepan\commerce\Model_PurchaseInvoice')
				->addCondition('contact_id',$supplier->id);
				$crud_inv = $this->add('xepan\hr\CRUD',
										['action_page'=>'xepan_commerce_purchaseinvoicedetail'],
										'invoices',
										['view/supplier/invoice/grid']);
				$crud_inv->setModel($inv);
				// $crud_inv->grid->addQuickSearch(['invoices']);
				$crud_inv->add('xepan\base\Controller_MultiDelete');
				
				if(!$crud_inv->isEditing()){
					$crud_inv->grid->js('click')->_selector('.do-view-supplier-invoice')->univ()->frameURL('Purchaseinvoice Detail',[$this->api->url('xepan_commerce_purchaseinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-purchaseinvoice-id]')->data('id')]);
				}
	/*
		Activity

	*/
				$activity_view = $this->add('xepan\base\Grid',null,'activity',['view/activity/activity-grid']);

				$activity=$this->add('xepan\base\Model_Activity');
				$activity->addCondition('related_contact_id',$_GET['contact_id']);
				$activity->tryLoadAny();
				$activity_view->setModel($activity);
		}


	}

	function defaultTemplate(){
		return ['page/supplier/detail'];
	}

	function checkPhoneNo($phone_id,$phone_value,$contact_id,$form){

		 $contact = $this->add('xepan\base\Model_Contact');
        
        if($contact_id)
	        $contact->load($contact_id);

		$contactconfig_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'contact_no_duplcation_allowed'=>'DropDown'
							],
					'config_key'=>'contact_no_duplication_allowed_settings',
					'application'=>'base'
			]);
		$contactconfig_m->tryLoadAny();	

		if($contactconfig_m['contact_no_duplcation_allowed'] != 'duplication_allowed'){
	        $contactphone_m = $this->add('xepan\base\Model_Contact_Phone');
	        $contactphone_m->addCondition('id','<>',$phone_id);
	        $contactphone_m->addCondition('value',$phone_value);
			
			if($contactconfig_m['contact_no_duplcation_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$contactphone_m->addCondition('contact_type',$contact['contact_type']);
		        $contactphone_m->tryLoadAny();
		 	}

	        $contactphone_m->tryLoadAny();
	        
	        if($contactphone_m->loaded())
	        	for ($i=1; $i <=4 ; $i++){ 
	        		if($phone_value == $form['contact_no_'.$i])
			        	$form->displayError('contact_no_'.$i,'Contact No. Already Used');
	        	}
		}	
    }

    function checkEmail($email_id,$email_value,$contact_id,$form){

    	$contact = $this->add('xepan\base\Model_Contact');
        
        if($contact_id)
	        $contact->load($contact_id);

		$emailconfig_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'email_duplication_allowed'=>'DropDown'
							],
					'config_key'=>'Email_Duplication_Allowed_Settings',
					'application'=>'base'
			]);
		$emailconfig_m->tryLoadAny();

		if($emailconfig_m['email_duplication_allowed'] != 'duplication_allowed'){
	        $email_m = $this->add('xepan\base\Model_Contact_Email');
	        $email_m->addCondition('id','<>',$email_id);
	        $email_m->addCondition('value',$email_value);
			
			if($emailconfig_m['email_duplication_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$email_m->addCondition('contact_type',$contact['contact_type']);
			}
	        
	        $email_m->tryLoadAny();
	        
	        if($email_m->loaded())
	        	for ($i=1; $i <=4 ; $i++){ 
	        		if($email_value == $form['email_'.$i])
			        	$form->displayError('email_'.$i,'Email Already Used');
	        	}
		}	
    }
}