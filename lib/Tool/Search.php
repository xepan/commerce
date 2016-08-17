<?php
namespace xepan\commerce;

class Tool_Search extends \xepan\cms\View_Tool{
	public $options = [
					'form_layout'=>'view/tool/form/search',
					'xepan_commerce_search_result_page'=>"index",
					'search-field-label'=>"Type Your Search String",
					'search-form-btn'=>true,
					'search-form-btn-label'=>'Search',
					'search-button-position'=>"after",
					'search-form-btn-icon'=>"glyphicon glyphicon-search"
					];

	function init(){
		parent::init();

		$this->addClass('xepan-commerce-search-tool');

		$search_result_subpage=$this->options['xepan_commerce_search_result_page'];
		if(!$search_result_subpage){
			$search_result_subpage="product";
		}


	   	$label = "";
		if($this->options['search-field-label'] != "")
			$label = $this->options['search-field-label'];

	   	
		$form = $this->add('Form',null,null,['form/empty']);
		$search_field = $form->addField('line','search',$label)->validate('required');//->setAttr('PlaceHolder',$this->options['search-input-placeholder']);
	   	
	   	if($this->options['search-form-btn']){
	   		if($this->options['search-button-position'] === "before")
	  	 		$submit_button = $search_field->beforeField()->add('Button');
	  	 	else
	  	 		$submit_button = $search_field->afterField()->add('Button');
	  	 	$submit_button->setIcon('fa '.$this->options['search-form-btn-icon']);
	  	 	$submit_button->set($this->options['search-form-btn-label']);
	  	 	$submit_button->js('click',$form->js()->submit());
	 	  	// $form->addSubmit($this->options['search-form-btn-label'] !=""?$this->options['search-form-btn-label']:"Search");
	   	}

		if($form->isSubmitted()){
			$form->api->redirect(
						$this->api->url(
									null,
									array('page'=>$search_result_subpage,'search'=>$form['search'])));
		}
	}
}