<?php

namespace xepan\commerce;

class Model_Item extends \xepan\base\Model_Document{

	function init(){
		parent::init();

		$item_j = $this->join('item.document_id');

		// Basic Field
		$item_j->addField('name')->mandatory(true);
		$item_j->addField('is_publish')->type('boolean')->defaultValue(true);
		$item_j->addField('is_party_publish')->type('boolean')->defaultValue(false);
		
		$item_j->addField('is_active')->type('boolean')->defaultValue(true);

	}
}
