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
		$this->add('filestore\Field_File','file_id');
	}
}
