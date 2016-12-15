<?php
 
namespace xepan\commerce;

class page_newcustomer extends \xepan\base\Page {
	public $title='New Customers';
	public $breadcrumb=['Home'=>'index','Customers'=>'xepan_commerce_customer','New Customers'=>'#'];

	function init(){
		parent::init();

		$form = $this->add('Form');
		$form->setLayout('view/customer/affilatetocustomer');
		$affilate = $form->addField('Dropdown','affiliate');
		$affilate->setModel('xepan\hr\Affiliate');

		$form->addSubmit('Convert To Customer')->addClass('btn btn-primary');
		$new_btn = $form->addButton('Create A New Customer')->addClass('btn btn-primary');
	
		if($new_btn->isClicked()){
			$this->app->redirect($this->app->url('xepan_commerce_customerdetail',['action'=>'add']));
		}
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

			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}
			$this->api->db->commit();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Affiliate Converted To Customer')->execute();
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