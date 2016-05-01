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
			foreach ($this->app->xepan_addons as $addon) {
				$this->app->xepan_app_initiators[$addon]->resetDB();	
			}
			$this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();        
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
		$new_json = $old_json;

		return $new_json;
	}
}