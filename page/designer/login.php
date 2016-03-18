<?php

class page_xShop_page_designer_login extends Page{
	function init(){
		parent::init();
		$self=$this;
		$this->api->auth->addHook('login',function($auth)use($self){
			$self->js(null,'$(".xshop-render-tool-save-btn ").click();')->univ()->closeDialog()->execute();
		});
		$this->add('baseElements/View_Tools_UserPanel',
											array(
												'html_attributes'=>
															array(
																'show_register_new_user'=>true
															)
														)
											);
	}
}