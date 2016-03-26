<?php

namespace xepan\commerce;

class Model_PaymentGateway extends \xepan\base\Model_Table {
	public $table ="payment_gateway";

	function init(){
		parent::init();

		$this->addField('name')->mandatory(true);
		$this->addField('default_parameters')->type('text');
		$this->addField('parameters')->type('text');

		$this->addField('processing')->enum(array('OnSite','OffSite'));
		$this->addField('is_active')->type('boolean')->defaultValue(true);
		
		$this->add('filestore/Field_File','gateway_image_id');
		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}