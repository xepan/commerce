<?php

 namespace xepan\commerce;

 class Model_TNC extends \xepan\hr\Model_Document{
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		$document_j = $this->join('tnc.document_id');
		$document_j->addField('name')->sortable(true);
		$document_j->addField('content')->type('text')->display(['form'=>'xepan\base\RichText'])->defaultValue(null);

		$document_j->hasMany('xepan/commerce/QSP_Master','tnc_id');

		$this->addCondition('type','TNC');

		$this->is([
				'name|required',
				'content|required'
			]);
	}
}
 
    