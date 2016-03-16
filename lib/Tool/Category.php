<?php

namespace xepan\commerce;

class Tool_Category extends \xepan\cms\View_Tool{
	public $options = [
				'show_name'=>true,
				'layout'=>'vertical',
				'show_description' =>true,
				'show_price' =>true
			];

	function init(){
		parent::init();

		$parent_categories = $this->add('xepan\commerce\Model_Category')->addCondition('parent_category_id',Null);

		// $output = "";
		// foreach ($parent_categories as $pc) {			
		// 	$output .= $this->getCategory($pc);
		// }

		// $this->add('View')->setHtml($output);
	}


	function getCategory($category){
		if($category->ref('SubCategories')->count()->getOne() > 0){
			$sub_category = $category->ref('SubCategories');
			$lister = $this->add('CompleteLister',null,null,['view/tool/category']);
			$lister->setModel($category);
			// $output.=$category['name'];
			foreach ($sub_category as $junk_category) {
				$this->getCategory($sub_category);
			}

		}else{
			$lister = $this->add('CompleteLister',null,null,['view/tool/category']);
			$lister->setModel($category);
		}
	}

	function addToCondition_show_description($value){
		$this->model->load($value);
	}

}