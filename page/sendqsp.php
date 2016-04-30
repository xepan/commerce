<?php

namespace xepan\commerce;

class page_sendqsp extends \Page{
	public $title="DOCUMENT SENDING";

	function init(){
		parent::init();

		$form = $this->add('Form');
		$form->setLayout('view/form/send-qsp');

		$form->addField('Line','cc');
		$form->addField('Line','bcc');
		$form->addField('xepan\base\RichText','body');
		$form->addField('Date','date');
		$form->addField('CheckBox','attachdoc');
		$form->addSubmit('Send');
		
	}
} 