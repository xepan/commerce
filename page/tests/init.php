<?php

namespace xepan\commerce;

class page_tests_init extends \AbstractController{
	public $title = "Commerce Test Init";

	function resetDB(){
		
		if(isset($this->app->resetDbDone)) return;
		$this->app->resetDbDone = true;
		
		try{
			$user = clone $this->app->auth->model;
			$this->api->db->beginTransaction();
			$this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 0;')->execute();
			$this->app->db->dsql()->expr('SET unique_checks=0;')->execute();
            $this->app->db->dsql()->expr('SET autocommit=0;')->execute();
			foreach ($this->app->xepan_addons as $addon) {
				$this->app->xepan_app_initiators[$addon]->resetDB();	
			}
			$this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
			$this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
			$this->api->db->commit();
		}catch(\Exception_StopInit $e){

		}catch(\Exception $e){
			$this->api->db->rollback();
			echo "rolled back and using user ". $user->id . " again";
			$this->app->auth->login($user);
			throw $e;
		}
	}

	function getMapping($table_name){
		$data = file_get_contents(__DIR__.'/'.$table_name.'_mapping.json');
		return json_decode($data,true);
	}

	function parseCustomFieldsJSON($old_json){
		if(!$old_json) return $old_json;

		$department_mapping = $this->getMapping('department');
		$customfield_mapping = $this->getMapping('customfield');
		$customfield_value_mapping = $this->getMapping('customfield_association_value');

		$old_cf_array = json_decode($old_json,true);
		$new_cf_array = [];

		foreach ($old_cf_array as $old_department_id => $cf_array) {
			$new_department_id = $department_mapping[$old_department_id]['new_id'];
			$department_name = $department_mapping[$old_department_id]['name'];

			$new_cf_array[$new_department_id] = ['department_name'=>$department_name];

			foreach ($cf_array as $cf_id => $cf_value_id) {
				$new_cf_id = $customfield_mapping[$cf_id]['new_id'];
				$cf_name = $customfield_mapping[$cf_id]['name'];
				$display_type = $customfield_mapping[$cf_id]['display_type'];
						
				if($display_type == "Line"){
					$new_cf_value_id = $cf_value_id;
					$cf_value_name = $cf_value_id;
				}else{
					$new_cf_value_id = $customfield_value_mapping[$cf_value_id]['new_id'];
					$cf_value_name = $customfield_value_mapping[$cf_value_id]['name'];					
				}


				$new_cf_array[$new_department_id][$new_cf_id] = ['custom_field_name'=>$cf_name,"custom_field_value_id"=>$new_cf_value_id,"custom_field_value_name"=>$cf_value_name];
			}
		}

		$new_json = json_encode($new_cf_array);
		return $new_json;
	}
}