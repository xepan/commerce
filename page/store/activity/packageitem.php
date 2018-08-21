<?php


namespace xepan\commerce;

class page_store_activity_packageitem extends \xepan\base\Page{
	// public $title="Dispatch Order Item";

	function init(){
		parent::init();

		// $department_id = $this->api->stickyGET('department_id')?:0;
		
		$form = $this->add('Form');
		
		// $warehouse_model = $this->add('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field = $form->addField('xepan\commerce\Warehouse','warehouse')->Validate('required');
		// $warehouse_field->setModel($warehouse_model);
		// $warehouse_field->setEmptyText('Please Select');

		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Store_Item')->addCondition('is_package',true);
		$item_field->other_field->validate('required');
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');

		$form->addField('line','no_of_package')->addClass('xepan-push-large');
		$form->addField('text','extra_info');
		$form->addField('text','narration');

		$create_package_btn = $form->addSubmit('Create Package')->addClass('btn btn-success');
		$open_package_btn = $form->addSubmit('Open Package')->addClass('btn btn-primary');

		if($form->isSubmitted()){

			//  todo check stock is available in warehouse in or not

			$result = ['status'=>'success','message'=>''];

			if($form->isClicked($create_package_btn)){

				try{
					$this->app->db->beginTransaction();
					$p_item = $this->add('xepan\commerce\Model_Item')->load($form['item']);

					// PackageCreated
					$pkg_asso = $this->add('xepan\commerce\Model_PackageItemAssociation');
					$pkg_asso->addCondition('package_item_id',$p_item->id);
					if(!$pkg_asso->count()->getOne()){
						throw new \Exception("no one sub product/item found");
					}

					$cf_key = $p_item->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));

					$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
					$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'PackageCreated',null,$form['warehouse'],$form['narration']);
					$transaction->addItem(null,$form['item'],$form['no_of_package'],null,$cf_key,'Received');
					
					// ConsumedInPackage
					$con_tra = $warehouse->newTransaction(null,null,$form['warehouse'],'ConsumedInPackage',null,$form['warehouse'],$form['narration']);
					foreach ($pkg_asso as $c_m) {
						$p_item = $this->add('xepan\commerce\Model_Item')->load($c_m['item_id']);
						$cf_key = $p_item->convertCustomFieldToKey(json_decode($c_m['extra_info']?:'{}',true));
						$con_tra->addItem(null,$c_m['item_id'],($form['no_of_package'] * $c_m['qty']),null,$cf_key,'Received');
					}

					$this->app->db->commit();
					$result = ['status'=>'success','message'=>'Package Created'];
				}catch(\Exception $e){
					$this->app->db->rollback();
					$result = ['status'=>'failed','message'=>'Package Not Created'];
				}

			}

			if($form->isClicked($open_package_btn)){
				try{
					$this->app->db->beginTransaction();

					$p_item = $this->add('xepan\commerce\Model_Item')->load($form['item']);
					// 'PackageOpened',
					$pkg_asso = $this->add('xepan\commerce\Model_PackageItemAssociation');
					$pkg_asso->addCondition('package_item_id',$p_item->id);
					if(!$pkg_asso->count()->getOne()){
						throw new \Exception("no one sub product/item found");
					}

					$cf_key = $p_item->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));

					$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
					$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'PackageOpened',null,$form['warehouse'],$form['narration']);
					$transaction->addItem(null,$form['item'],$form['no_of_package'],null,$cf_key,'Received');
					
					// ReleaseFromPackage
					$con_tra = $warehouse->newTransaction(null,null,$form['warehouse'],'ReleaseFromPackage',null,$form['warehouse'],$form['narration']);
					foreach ($pkg_asso as $c_m) {
						$p_item = $this->add('xepan\commerce\Model_Item')->load($c_m['item_id']);
						$cf_key = $p_item->convertCustomFieldToKey(json_decode($c_m['extra_info']?:'{}',true));
						$con_tra->addItem(null,$c_m['item_id'],($form['no_of_package'] * $c_m['qty']),null,$cf_key,'Received');
					}
					$this->app->db->commit();
					$result = ['status'=>'success','message'=>'Package Opened'];
				}catch(\Exception $e){
					$this->app->db->rollback();
					$result = ['status'=>'failed','message'=>'Package Not Opened'];
				}
			}

				
			if($result['status'] == "success"){
				$form->js(null,$form->js()->reload())->univ()->successMessage($result['message'])->execute();
			}else{
				$form->js(null,$form->js()->reload())->univ()->errorMessage($result['message'])->execute();
			}
		}

	}
}