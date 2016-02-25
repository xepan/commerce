<?php

 namespace xepan\commerce;

 class Model_CategoryItemAssociation extends \SQL_Model{
	public $table = 'category_item_asso';
	
	function init(){
		parent::init();

		$this->hasOne('xepan/commerce/Item','item_document_id');
		$this->hasOne('xepan/commerce/Category','category_document_id');
		
		$this->addField('is_active')->type('boolean')->defaultValue(true);

	}
}
 
    