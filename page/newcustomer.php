<?php
 
namespace xepan\commerce;

class page_newcustomer extends \xepan\base\Page {
	public $title='New Customers';
	public $breadcrumb=['Home'=>'index','Customers'=>'xepan_commerce_customer','New Customers'=>'#'];

	function init(){
		parent::init();

		$col = $this->add('Columns');
		$col1 =  $col->addColumn('4');
		$col2 = $col->addColumn('4');
		$col3 = $col->addColumn('4');

		// lead to customer
		$f = $col1->add('Form');
		$f->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'lead'=>'Convert Lead To Customer~c1-12',
				'FormButtons~&nbsp;'=>'c1~12'
			]);

		$lead_model = $this->add('xepan\base\Model_Contact');
		$lead_model->title_field = "search_name";
		$lead_model->addExpression('search_name',function($m,$q){
			return $q->expr('CONCAT_WS(" :: ",[name],[organization])',
						[
							'name'=>$m->getElement('name'),
							'organization'=>$m->getElement('organization'),
						]
					);
		});

		$lead_model->addCondition([['type','Contact'],['type',null],['type','Lead']]);

		$contact = $f->addField('xepan\base\Basic','lead')->validate('required');
		$contact->setModel($lead_model);

		$f->addSubmit('Convert to Customer')->addClass('btn btn-primary');
		if($f->isSubmitted()){
			try{
				$this->api->db->beginTransaction();
				
				$lead_id = $f['lead'];
				$c_m = $this->add('xepan\base\Model_Contact');
				$c_m->load($lead_id);
				
				if($c_m['type'] == "Customer")
					throw new \Exception("Selected lead already a customer - ".$lead_id);
																																												
				// insert into customer table entry where conatct_id = $form['lead']
				$this->app->db->dsql()->table('customer')
										->set('contact_id',$lead_id)
										->set('billing_country_id',$c_m['country_id'])
										->set('billing_state_id',$c_m['state_id'])
										->set('billing_name',$c_m['first_name']." ".$c_m['last_name'])
										->set('billing_address',$c_m['address'])
										->set('billing_city',$c_m['city'])
										->set('billing_pincode',$c_m['pin_code'])
										->set('shipping_country_id',$c_m['country_id'])
										->set('shipping_state_id',$c_m['state_id'])
										->set('shipping_name',$c_m['first_name']." ".$c_m['last_name'])
										->set('shipping_address',$c_m['address'])
										->set('shipping_city',$c_m['city'])
										->set('shipping_pincode',$c_m['pin_code'])
										->insert();

				$this->app->db->dsql()->table('contact')
										->set('remark',$c_m['narration'])
										->set('type','Customer')
										->where('id',$lead_id)
										->update();

				$this->add('xepan\commerce\Model_Customer')->load($lead_id)->save();

				$this->app->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}
			$f->js(null,$f->js()->reload())->univ()->successMessage('Lead Converted To Customer')->execute();
		}


		// affiliate to customer
		$form = $col2->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'affiliate'=>'Convert Affiliate To Customer~c1-12',
				'FormButtons~&nbsp;'=>'c2~12'
			]);

		$affilate = $form->addField('xepan\base\Basic','affiliate');
		$affilate->setModel('xepan\hr\Affiliate');
		$form->addSubmit('Convert To Customer')->addClass('btn btn-primary');
		if($form->isSubmitted()){

			try{
				$this->api->db->beginTransaction();
					
				$affilate_m = $this->add('xepan\hr\Model_Affiliate');
				$affilate_m->load($form['affiliate']);

				// insert into customer table entry where conatct_id = $form['affiliate']
				$this->app->db->dsql()->table('customer')
										->set('contact_id',$form['affiliate'])
										->insert();

				$this->app->db->dsql()->table('contact')
										->set('remark',$affilate_m['narration'])
										->set('type','Customer')
										->where('id',$form['affiliate'])
										->update();

				// remove afiliate table entry where conatct_id = $form['affiliate']
				$this->app->db->dsql()->table('affiliate')
										->where('contact_id',$form['affiliate'])
										->delete();

				$this->add('xepan\commerce\Model_Customer')->load($form['affiliate'])->save();

				$this->api->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}
			$form->js(null,$form->js()->reload())->univ()->successMessage('Affiliate Converted To Customer')->execute();
		}


		$new_btn = $col3->add('Button')->set('Create a New customer')->addClass('btn btn-primary');
		if($new_btn->isClicked()){
			$this->app->redirect($this->app->url('xepan_commerce_customerdetail',['action'=>'add']));
		}
	}
}



























// <?php
//  namespace xepan\commerce;
//  class page_customerprofile extends \Page{
//  	public $title='Customer';

// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/customerprofile'];
// 	}
// }