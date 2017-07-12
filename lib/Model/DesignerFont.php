<?php

 namespace xepan\commerce;

 class Model_DesignerFont extends \xepan\base\Model_Table{
	public $table = 'designer_font';
	
	public $acl_type = "DesignerFont";
	public $status = ['All'];
	public $actions = [
					'All'=>['edit','delete']
					];
	public $acl = false;
	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id)->system(true);
		$this->addField('name')->caption('Font Family');
		
		$this->add('xepan\filestore\Field_Image','regular_file_id');
		$this->add('xepan\filestore\Field_Image','bold_file_id');
		$this->add('xepan\filestore\Field_Image','italic_file_id');
		$this->add('xepan\filestore\Field_Image','bold_italic_file_id');
	}
}
 
    