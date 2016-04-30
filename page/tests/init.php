<?php

namespace xepan\commerce;

class page_tests_init extends \AbstractController{
	public $title = "Commerce Test Init";

	function init(){
		parent::init();

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
			$this->app->auth->login($user);
			throw $e;
		}

	}
}