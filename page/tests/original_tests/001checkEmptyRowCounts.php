<?php 

namespace xepan\commerce;

class page_tests_001checkEmptyRowCounts extends \xepan\base\Page_Tester{
	public $title = "Row Count Test";
	public $proper_responses=[
    	'-'=>'-'
    ];

	function init(){
		$this->add('xepan\commerce\page_tests_init');
		parent::init();
	}

	function prepare_rowCount(){
		
		$this->proper_responses['test_rowCount']=[
			'item'=>0,
			'category_item_association'=>0,
			'customfield_association'=>0,
			'customfield_generic'=>0,
			'customfield_value'=>0,
			'item_department_association'=>0,
			'item_department_consumption'=>0,
			'item_image'=>0,
			'item_template_design'=>0,
			'quantity_condition'=>0,
			'quantity_set'=>0,
			'taxation'=>0,
			'taxation_association'=>0
		];
	}	

	function test_rowCount(){
		$result = [];

		foreach ($this->proper_responses['test_rowCount'] as $table=>$requiredcount ) {
			$result[$table]=$this->app->db->dsql()->table($table)->del('fields')->field('count(*)')->getOne();
		}

		return $result;
	}
}