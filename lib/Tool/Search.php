<?php
namespace xepan\commerce;

class Tool_Search extends \xepan\cms\View_Tool{
	public $options = [
					'form_layout'=>'view/tool/form/search',
					'form_layout'=>'view/tool/form/search',
					'xepan_commerce_search_result_page'=>"index",
					'search-field-label'=>"Type Your Search String",
					'search-form-btn'=>'0',
					'search-form-btn-label'=>'Search'
					];

	function init(){
		parent::init();

		$search_result_subpage=$this->options['xepan_commerce_search_result_page'];
		if(!$search_result_subpage){
			$search_result_subpage="product";
		}


	   	$label = "";
		if($this->options['search-field-label'] != "")
			$label = $this->options['search-field-label'];

	   	
		$form = $this->add('Form',null,null,['form/empty']);
		$form_field = $form->addField('line','search',$label);//->setAttr('PlaceHolder',$this->options['search-input-placeholder']);
	   	
	   	if($this->options['search-form-btn']){
	 	  		$form->addSubmit($this->options['search-form-btn-label'] !=""?$this->options['search-form-btn-label']:"Search");
	   	}

		if($form->isSubmitted()){
			$form->api->redirect(
						$this->api->url(
									null,
									array('page'=>$search_result_subpage,'search'=>$form['search'])));
		}
	}
}