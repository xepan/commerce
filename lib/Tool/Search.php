<?php
namespace xepan\commerce;

class Tool_Search extends \xepan\cms\View_Tool{
	public $options = ['form_layout'=>'view/tool/form/search'];
	function init(){
		parent::init();

		$form = $this->add('Form',null,null,['form/empty']);
		$form->setLayout($this->options['form_layout']);
		$form_field = $form->addField('line','search');

		$btn = $form->layout->add('Button',null,'search_button')
					->set(
							array(
									$this->options['form-btn-label']?:"search",
									'icon'=>'search'
								)
						)->js('click',$form->js()->submit());

	}
}