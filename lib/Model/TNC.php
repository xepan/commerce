<?php

 namespace xepan\commerce;

 class Model_TNC extends \xepan\hr\Model_Document{
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		$document_j = $this->join('tnc.document_id');
		$document_j->addField('name')->sortable(true);
		$document_j->addField('content')->type('text')->display(['form'=>'xepan\base\RichText'])->defaultValue(null);

		$document_j->addField('is_default_for_quotation')->type('boolean');
		$document_j->addField('is_default_for_sale_order')->type('boolean');
		$document_j->addField('is_default_for_sale_invoice')->type('boolean');

		$document_j->hasMany('xepan/commerce/QSP_Master','tnc_id');

		$this->addCondition('type','TNC');

		$this->is([
				'name|required',
				'content|required'
			]);

		$this->addHook('beforeSave',[$this,'updateSearchString']);
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['name'];
		$search_string .=" ". $this['content'];
		$search_string .=" ". $this['type'];

		$this['search_string'] = $search_string;
	}
}
 
    