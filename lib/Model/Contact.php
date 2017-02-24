<?php

/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class Model_Contact extends \xepan\base\Model_Contact{
	
	function contacttypeconversion($contact_id,$type=null){
		
		if($this->loaded()) $this->unload();
		
		$this->addCondition('id',$contact_id);

		if($type)
			$this->addCondition('type','<>',$type);

		$this->tryLoadAny();
		if(!$this->loaded()) return false;
		
		try
		{
			$this->api->db->beginTransaction();
			
			// insert into customer table entry where conatct_id = $form['affiliate']
			$this->app->db->dsql()->table('customer')
									->set('contact_id',$this->id)
									->insert();						
			// update type of contact
			$this->app->db->dsql()->table('contact')
									->set('type',$type)
									->where('id',$this->id)
									->update();

			
		}catch(\Exception $e){
			$this->api->db->rollback();
			throw $e;
		}
		$this->api->db->commit();

		return true;
	}
}
 
    
