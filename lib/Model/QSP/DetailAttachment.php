<?php

 namespace xepan\commerce;

 class Model_QSP_DetailAttachment extends \xepan\base\Model_Table{ 	
 	public $table="qsp_detail_attachment";

 	function init(){
 		parent::init();

 		$this->hasOne('xepan\base\Contact','contact_id');
 		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
 		$this->add('xepan\filestore\Field_File','file_id');
 	}
}
 
    