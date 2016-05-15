<?php
namespace xepan\commerce;

class Tool_Search extends \xepan\cms\View_Tool{
	public $options = [
					'form_layout'=>'view/tool/form/search',
					'form_layout'=>'view/tool/form/search'
					];

	function init(){
		parent::init();

		$search_result_subpage=$this->options['xepan_commerce_search_result_page'];
		if(!$search_result_subpage){
			$search_result_subpage="product";
		}

		$form = $this->add('Form',null,null,['form/empty']);
		$form_field = $form->addField('line','search');

		if($form->isSubmitted()){
			$form->api->redirect(
						$this->api->url(
									null,
									array('page'=>$search_result_subpage,'search'=>$form['search'])));
		}
	}
}