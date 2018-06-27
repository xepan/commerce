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

class page_customerdetail extends \xepan\base\Page {
	public $title='Customer Details';
	public $breadcrumb=['Home'=>'index','Customers'=>'xepan_commerce_customer','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';

		$customer= $this->add('xepan\commerce\Model_Customer')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		$tag_model = $this->add('xepan\base\Model_Contact_Tag');
		$all_tag = array_column($tag_model->config_data, 'name');
		$all_tag = array_combine($all_tag, $all_tag);
		
		if($action=="add"){	
			$base_validator = $this->add('xepan\base\Controller_Validator');

			$form = $this->add('Form',['validator'=>$base_validator],'contact_view_full_width');		
			$form->setLayout(['page\customer\customer-detail-full-width']);
			$form->setModel($customer,['first_name','last_name','address','city','country_id','state_id','pin_code','organization','post','website','remark','pan_no','currency_id','gstin','customer_type']);
			$form->addField('line','email_1')->validate('email');
			$form->addField('line','email_2');
			$form->addField('line','email_3');
			$form->addField('line','email_4');
			
			$form->addField('line','contact_no_1');
			$form->addField('line','contact_no_2');
			$form->addField('line','contact_no_3');
			$form->addField('line','contact_no_4');
			$form->addField('Checkbox','want_to_add_next_customer')->set(true);
			$tags_field = $form->addField('DropDown','tag');
			$tags_field->addClass('multiselect-full-width');
			$tags_field->setAttr(['multiple'=>'multiple']);
			$tags_field->setValueList($all_tag);
			$tags_field->setEmptyText("Please Select");

			$country_field =  $form->getElement('country_id');
			$state_field = $form->getElement('state_id');
			$state_field->dependsOn($country_field);

			// if($cntry_id = $this->app->stickyGET('country_id')){			
			// 	$state_field->getModel()->addCondition('country_id',$cntry_id);
			// }

			// $country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));
			
			$user_field = $form->addField('line','user_id')->validate('email');
			$password_field = $form->addField('password','password');
			
			$customer->addOtherInfoToForm($form);

			$form->addSubmit('Add')->addClass('btn btn-primary');

			if($form->isSubmitted()){				
				try{
					$this->api->db->beginTransaction();
					$form->save();
					$new_customer_model = $form->getModel();

					$new_customer_model ['billing_address'] = $form['address'];
					$new_customer_model ['billing_country_id'] = $form['country_id'];
					$new_customer_model ['billing_state_id'] = $form['state_id'];
					$new_customer_model ['billing_city'] = $form['city'];
					$new_customer_model ['billing_pincode'] = $form['pin_code'];

					$new_customer_model ['shipping_address'] = $form['address'];
					$new_customer_model ['shipping_country_id'] = $form['country_id'];
					$new_customer_model ['shipping_state_id'] = $form['state_id'];
					$new_customer_model ['shipping_city'] = $form['city'];
					$new_customer_model ['shipping_pincode'] = $form['pin_code'];
					
					// tag
					$new_customer_model['tag'] = $form['tag'];

					$new_customer_model->save();

					if($form['user_id'] && $form['password']){
						$user = $this->add('xepan\base\Model_User');
						$user->addCondition('scope','WebsiteUser');
						$user->addCondition('username',$form['user_id']);
						$user->tryLoadAny();

						if($user->loaded())
							$form->displayError('user_id','username already exist');
						// $user=$this->add('xepan\base\Model_User');
						$this->add('BasicAuth')
						->usePasswordEncryption('md5')
						->addEncryptionHook($user);
						
						$user['username'] = $form['user_id'];
						$user['password'] = $form['password'];
						$user->save();
						
						$new_customer_model['user_id'] = $user->id;
						$new_customer_model->save();
					}

					if($form['email_1']){

						$new_customer_model->checkEmail($form['email_1'],$new_customer_model,'email_1');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_customer_model->id;
						$email['head'] = "Official";
						$email['value'] = $form['email_1'];
						$email->save();
					}

					if($form['email_2']){
						$new_customer_model->checkEmail($form['email_2'],$new_customer_model,'email_2');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_customer_model->id;
						$email['head'] = "Official";
						$email['value'] = $form['email_2'];
						$email->save();
					}

					if($form['email_3']){
						$new_customer_model->checkEmail($form['email_3'],$new_customer_model,'email_3');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_customer_model->id;
						$email['head'] = "Personal";
						$email['value'] = $form['email_3'];
						$email->save();
					}

					if($form['email_4']){
						$new_customer_model->checkEmail($form['email_4'],$new_customer_model,'email_4');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_customer_model->id;
						$email['head'] = "Personal";
						$email['value'] = $form['email_4'];
						$email->save();
					}

					// Contact Form
					if($form['contact_no_1']){
						$new_customer_model->checkPhone($form['contact_no_1'],$new_customer_model,'contact_no_1');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_customer_model->id;
						$phone['head'] = "Official";
						$phone['value'] = $form['contact_no_1'];
						$phone->save();
					}

					if($form['contact_no_2']){
						$new_customer_model->checkPhone($form['contact_no_2'],$new_customer_model,'contact_no_2');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_customer_model->id;
						$phone['head'] = "Official";
						$phone['value'] = $form['contact_no_2'];
						$phone->save();
					}

					if($form['contact_no_3']){
						$new_customer_model->checkPhone($form['contact_no_3'],$new_customer_model,'contact_no_3');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_customer_model->id;
						$phone['head'] = "Personal";
						$phone['value'] = $form['contact_no_3'];						
						$phone->save();
					}
					if($form['contact_no_4']){
						$new_customer_model->checkPhone($form['contact_no_4'],$new_customer_model,'contact_no_4');
						
						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_customer_model->id;
						$phone['head'] = "Personal";
						$phone['value'] = $form['contact_no_4'];	
						$phone->save();
					}

					// add contact other info
					$contact_other_info_config_m = $this->add('xepan\base\Model_Config_ContactOtherInfo');
					$contact_other_info_config_m->addCondition('for','Customer');

					foreach($contact_other_info_config_m->config_data as $of) {
						if($of['for'] != "Customer" ) continue;

						if(!$of['name']) continue;
						$field_name = $this->app->normalizeName($of['name']);

						$existing = $this->add('xepan\base\Model_Contact_Other')
							->addCondition('contact_id',$new_customer_model->id)
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

		        if($form['want_to_add_next_customer']){
		        	$form->js(null,$form->js()->reload())->univ()->successMessage('Customer Created Successfully')->execute();
		        }

				$form->js(null,$form->js()->univ()->successMessage('Customer Created Successfully'))->univ()->redirect($this->app->url(null,['action'=>"edit",'contact_id'=>$new_customer_model->id]))->execute();	
			}
			
			$this->template->del('details');
		}else{
			$contact_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\commerce\Model_Customer','view_document_class'=>'xepan\hr\View_Document'],'contact_view');
			$contact_view->setModel($customer);
		}

		if($customer->loaded()){
			$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
			$d->setIdField('contact_id');
			
			$d->setModel($customer,['shipping_address','shipping_city','shipping_pincode',
				'billing_address','billing_city','billing_pincode','pan_no','organization','currency','user','remark','is_designer','gstin','customer_type','tag'],
				['shipping_address','shipping_city','shipping_state_id','shipping_country_id','shipping_pincode','same_as_billing_address',
				'billing_address','billing_city','billing_state','billing_state_id','billing_country','billing_country_id','billing_pincode','pan_no','organization','currency_id','user_id','remark','is_designer','gstin','customer_type','tag']);

			// tag 
			$field_tag = $d->form->getElement('tag');
			$field_tag->addClass('multiselect-full-width');
			$field_tag->setAttr(['multiple'=>'multiple']);
			$field_tag->setValueList($all_tag);
			$field_tag->set(explode(",",$customer['tag']));

			$b_country = $d->form->getElement('billing_country_id');
			$b_state = $d->form->getElement('billing_state_id');
			
			if($this->app->stickyGET('billing_country_id'))
				$b_state->getModel()->addCondition('country_id',$_GET['billing_country_id'])->setOrder('name','asc');
				$b_country->js('change',$b_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$b_state->name]),'billing_country_id'=>$b_country->js()->val()]));
			
			$s_country = $d->form->getElement('shipping_country_id');
			$s_state = $d->form->getElement('shipping_state_id');
			
			if($this->app->stickyGET('shipping_country_id'))
				$s_state->getModel()->addCondition('country_id',$_GET['shipping_country_id'])->setOrder('name','asc');
				$s_country->js('change',$s_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$s_state->name]),'shipping_country_id'=>$s_country->js()->val()]));
		
			$s_a = $d->form->getElement('shipping_address');
			$s_cr = $s_country;
			$s_s = $s_state;
			$s_c = $d->form->getElement('shipping_city');
			$s_p = $d->form->getElement('shipping_pincode');

			$b_a = $d->form->getElement('billing_address');
			$b_cr = $b_country;
			$b_s = $b_state;
			$b_c = $d->form->getElement('billing_city');
			$b_p = $d->form->getElement('billing_pincode');

			$js = array(	
				$s_a->js()->val($b_a->js()->val()),
				$s_cr->js()->val($b_cr->js()->val()),
				$s_s->js()->val($b_s->js()->val()),
				$s_c->js()->val($b_c->js()->val()),
				$s_p->js()->val($b_p->js()->val())
				);

			$same_as_billing_field = $d->form->getElement('same_as_billing_address');

			$same_as_billing_field->js(true)->univ()->bindConditionalShow([
				''=>['shipping_address'],
				'*'=>['']
			],'div#shipping_address');

			$same_as_billing_field->js('change',$this->js()->val($js));


			

/**

		Orders

*/

			$ord = $this->add('xepan\commerce\Model_SalesOrder')
			->addCondition('contact_id',$customer->id);
			$crud_ord = $this->add('xepan\hr\CRUD',
							['action_page'=>'xepan_commerce_salesorderdetail'],
							'orders',
							['view/customer/order/grid']
						);

			$crud_ord->setModel($ord)->setOrder('created_at','desc');
			$crud_ord->grid->addQuickSearch(['orders']);
			$crud_ord->add('xepan\base\Controller_MultiDelete');

			if(!$crud_ord->isEditing()){
				$crud_ord->grid->js('click')->_selector('.do-view-customer-order')->univ()->frameURL('Salesorder Detail',[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesorder-id]')->data('id')]);
			}

/**

		Invoices

*/
			$inv = $this->add('xepan\commerce\Model_SalesInvoice')
			->addCondition('contact_id',$customer->id);
			$crud_inv = $this->add('xepan\hr\CRUD',
							['action_page'=>'xepan_commerce_salesinvoicedetail'],
							'invoices',
							['view/customer/invoice/grid']
						);
			$crud_inv->setModel($inv)->setOrder('created_at','desc');
			$crud_inv->grid->addQuickSearch(['invoices']);		
			$crud_inv->add('xepan\base\Controller_MultiDelete');		
			
			if(!$crud_inv->isEditing()){
				$crud_inv->grid->js('click')->_selector('.do-view-customer-invoice')->univ()->frameURL('Salesinvoice Detail',[$this->api->url('xepan_commerce_salesinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesinvoice-id]')->data('id')]);
			}
		}
/*
	Activity

*/
		if($customer->loaded()){
			$activity_view = $this->add('xepan\base\Grid',null,'activity',['view/activity/activity-grid']);

			$activity=$this->add('xepan\base\Model_Activity');
			$activity->addCondition('related_contact_id',$_GET['contact_id']);
			$activity->tryLoadAny();
			$activity_view->setModel($activity);
		}

		/*	Category Item Association */

		$crud_cat_asso = $this->add('xepan\base\Grid',
									null,
									'freelancer_cat_asso'
									// ['view/item/associate/category']
								);

		$model_active_category = $this->add('xepan\commerce\Model_FreelancerCategory')->addCondition('status','Active');

		$form = $this->add('Form',null,'cat_asso_form');
		$ass_cat_field = $form->addField('hidden','ass_cat')->set(json_encode($customer->getAssociatedCategories()));
		$form->addSubmit('Update');

		$crud_cat_asso->addQuickSearch(['name']);
		$crud_cat_asso->setModel($model_active_category,array('name'));
		$crud_cat_asso->addSelectable($ass_cat_field);

		if($form->isSubmitted()){
			$this->add('xepan\commerce\Model_FreelancerCatAndCustomerAssociation')
					->addCondition('customer_id',$customer->id)
					->deleteAll();

			$selected_categories = array();
			$selected_categories = json_decode($form['ass_cat'],true);
			foreach ($selected_categories as $cat_id) {
				$model_asso = $this->add('xepan\commerce\Model_FreelancerCatAndCustomerAssociation');
				$model_asso->addCondition('freelancer_category_id',$cat_id);
				$model_asso->addCondition('customer_id',$customer->id);
				$model_asso->tryLoadAny();
				$model_asso->saveAndUnload();
			}
			$form->js(null,$this->js()->univ()->successMessage('Category Associated'))->reload()->execute();
		}


	}

	function defaultTemplate(){
		return ['page/customer/detail'];
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
