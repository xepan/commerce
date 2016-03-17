<?php
namespace xepan\commerce;
class page_qspstatus extends \Page{
	function init(){
		parent::init();

		// $doc_id = $_GET['commerce_document_id'];
		// if(!$doc_id)
		// 	return;
		// $doc = $this->add('xepan\commerce\Model_QSP_Master')->load($doc_id);
		
		$this->app->side_menu->addItem('Check Box Filter');
		$this->app->side_menu->addItem('Draft','');
		$this->app->side_menu->addItem('Submitted','');
		$this->app->side_menu->addItem('Rejected','');
		$this->app->side_menu->addItem('Approved','');
		$this->app->side_menu->addItem('Redesign','');
	}

} 