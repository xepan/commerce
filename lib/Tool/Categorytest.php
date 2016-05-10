<?php

namespace xepan\commerce;

class Tool_Categorytest extends \CompleteLister{
	function init(){
		parent::init();
		$this->setModel('xepan\commerce\Category')->addCondition('parent_category_id',null);
	}
	function formatRow(){
		$sub_cat=$this->add('xepan\commerce\Model_Category',['name'=>'mc'.$this->model->id]);
		$sub_cat->addCondition('parent_category_id',$this->model->id);
		if($sub_cat->count()->getOne() > 0){			
			$sub_c =$this->add('xepan\commerce\Tool_Categorytest',null,'child_category',['view/test','nested_template']);
			$sub_c->setModel($sub_cat);
			$this->current_row_html['child_category']= $sub_c->getHTML();
		}
		return parent::formatRow();
	}

	function defaultTemplate(){
		return ['view\test'];
	}
}