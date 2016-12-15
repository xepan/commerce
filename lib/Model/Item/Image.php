<?php

/**
* description: Model Documet Attachment
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;


class Model_Item_Image extends \xepan\base\Model_Table{
	
	public $table='item_image';
	public $acl = false;

	function init(){
		parent::init();
		
		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');

		$this->add('xepan\filestore\Field_File','file_id');

		$this->addExpression('thumb_url')->set(function($m,$q){
			return $q->expr('[0]',[$m->getElement('file')]);
			// return $m->refSQL('file_id')->fieldQuery('thumb_url');
		});
		$this->addField('alt_text');
		$this->addField('title');
		$this->addField('sequence_no')->type('int')->hint('ascending order');
		$this->addField('auto_generated')->type('boolean')->defaultValue(false)->system(true);
		// $this->addExpression('customfield_type')->set(function($m,$q){
		// 	return $m->refSQL('customfield_value_id')->fieldQuery('customfield_type');
		// });

		$this->setOrder('sequence_no','asc');
	}
}
