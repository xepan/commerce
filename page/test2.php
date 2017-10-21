<?php 
 namespace xepan\commerce;

 class page_test2 extends \xepan\base\Page{

	function init(){
		parent::init();

		$cat = $this->add('xepan\commerce\Model_Category');
		foreach ($cat as $c) {
			$c->save();
		}

		$cat = $this->add('xepan\blog\Model_BlogPost');
		foreach ($cat as $c) {
			$c->save();
		}
	}
} 