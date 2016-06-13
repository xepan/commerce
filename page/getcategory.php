<?php

namespace xepan\commerce;

class page_getcategory extends \Page{
	function init(){
		parent::init();

		$c = $this->add('xepan\commerce\Model_Category')
				->addCondition('status','Active');

		$rows = $c->getRows(['id','name']);
		$option = "";
		foreach ($rows as $row) {
			$option .= "<option value='".$row['id']."'>".$row['name']."</option>";
		}

		echo $option;
		exit;
	}
}