<?php

	namespace xepan\commerce;

	class Model_Edit Wishlist extends \xepan\base\Model_Table{
		public $table = "wishlist";

		function init(){
			parent::init();
			/*$cols = $this->add('Columns')->addClass('well');
        $col1 = $cols->addColumn(4)->addClass('bg bg-danger');
        $col2 = $cols->addColumn(4)->addClass('bg bg-success');
        $col3 = $cols->addColumn(4);*/
			$this->addField('name');
			$this->addField('privicy','privicy')->setValueList(['Privat'=>'Privat','Public'=>'Public']);//->setEmptyText('Please Select');
			/*$this->addField('gender');\
			$this->addField('contact_number')->type('int');*/
			$this->add('dynamic_model\Controller_Autocreator');
		}

	}