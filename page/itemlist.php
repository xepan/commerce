<?php

namespace xepan\commerce;

class page_itemlist extends \Page {
	public $title='Item';

	function init(){
		parent::init();

		
		$itemlist=$this->add('xepan\commerce\Model_Itemlist');

		$crud=$this->add('xepan\base\CRUD',['grid_class'=>'xepan\base\Grid','grid_options'=>['defaultTemplate'=>['grid/itemlist']]]);
		$crud->setModel($itemlist);
		$crud->grid->addQuickSearch(['name']);
	}
}




 



























// <?php
//  namespace xepan\commerce;
//  class page_itemlist extends \Page{

//  	public $title='Items';


//  	function init(){
//  		parent::init();
		
//  	}

//  	function defaultTemplate(){

//  		return['page/itemlist'];

//  	}
//  } 