<?php
 
namespace xepan\commerce;

class page_primeimporter extends \Page {
	public $title='Category';

	function init(){
		parent::init();

		$dsql = $this->app->db->dsql();

		$members = $dsql->table('prime_members');

		foreach ($members as $member){
			$customer = $this->add('xepan\commerce\Model_Customer');

			// $customer['address'] = $member['address'];
			// $customer['address'] = $member['shipping_address'];
			// $customer['address'] = $member['landmark'];
			
			$customer['shipping_address'] = $customer['billing_address'] = $member['billing_address'];
			$customer['shipping_city'] = $customer['billing_city'] = $member['city'];
			$customer['shipping_state'] = $customer['billing_state'] = $member['state'];
			$customer['shipping_country'] = $customer['billing_country'] = $member['country'];
			$customer['shipping_pincode'] = $customer['billing_pincode'] = $member['pincode'];

			$status = "Active";
			if(!$member['is_active'])
				$status = "InActive";

			$customer['status'] = $status;

			$customer['tin_no'] = $member['tin_no'];
			$customer['pan_no'] = $member['pan_no'];
			$customer['created_at'] = $member['created_at'];
			$customer['organization'] = $member['organization_name'];
			$customer['website'] = $member['website'];
			$customer['image_id'] = $member['member_photo_id'];
			$customer->save();

			if($member['other_emails']){
				$emails = explode(",", $member['other_emails']);
				foreach ($emails as $email) {

					try{
						$customer->ref('Emails')
							->addCondition('type','Email')
							->addCondition('head','Official')
							->addCondition('value',$email)
							->tryLoadAny()->save()
							;
					}catch(\Exception $e){
						
					}

				}
			}

			if($member['mobile_number']){
				$contacts = explode(",", $member['mobile_number']);
				foreach ($contacts as $contact) {

					try{
						$customer->ref('Phones')
							->addCondition('type','Phone')
							->addCondition('head','Official')
							->addCondition('value',$contact)
							->tryLoadAny()->save()
							;
					}catch(\Exception $e){
						
					}

				}
			}
		}


	}
}