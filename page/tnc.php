<?php 
 namespace xepan\commerce;
 class page_tnc extends \Page{

	public $title='Terms & Condition';

	function init(){
		parent::init();

		$tnc=$this->add('xepan\commerce\Model_TNC');
		$crud=$this->add('xepan\hr\CRUD',null,
						null,
						['view/tnc/grid']
					);

		
		$crud->setModel($tnc);
		$crud->grid->addQuickSearch(['name']);

		$crud->add('xepan\base\Controller_Avatar');
	}

}  