<?php

namespace xepan\commerce;

class Controller_Auth extends \AbstractController {
	public $redirect_subpage=null;
	public $substitute_view=null;

	function init(){
		parent::init();

		
	}

	function checkCredential(){
		if(!($this->owner instanceof \View)){
			throw $this->exception('Must be added to a View');
		}

		if($this->redirect_subpage == null and $this->substitute_view==null){
			$this->owner->add('View_Error')->set('One of redirect_subpage or substitute_view must be defined to use this controller');
			// throw $this->exception('');
		}

		if(!$this->api->auth->isLoggedIn()){
			if($this->substitute_view){
				$this->owner->add($this->substitute_view);
				return false;
			}
			if($this->redirect_subpage){
				$this->api->redirect($this->api->url(null,array('subpage'=>$this->redirect_subpage)));			
				exit;
			}
		}
		return true;
	}
}