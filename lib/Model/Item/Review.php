<?php

namespace xepan\commerce;

class Model_Item_Review extends \xepan\commerce\Model_Review{
	function init(){
		parent::init();

		$this->addCondition('related_type','xepan\commerce\Model_Item');

		$this->getElement('related_document_id')->display(['form'=>'DropDown'])
			->setModel($this->add('xepan\commerce\Model_Item'))->caption('Item/Product');
	}
}