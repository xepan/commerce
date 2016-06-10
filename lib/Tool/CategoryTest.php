<?php
namespace xepan\commerce;
class Tool_Categorytest extends \CompleteLister{
	public $options = [
			'url_page' =>'index'
		];

	function init(){
		parent::init();		
		$categories = $this->setModel('xepan\commerce\Category')->addCondition('parent_category_id',null);
		$categories->addCondition('status','Active');
	}
	function formatRow(){
		$sub_cat=$this->add('xepan\commerce\Model_Category',['name'=>'model_child_'.$this->model->id]);
		$sub_cat->addCondition('parent_category_id',$this->model->id);

		if($sub_cat->count()->getOne() > 0){								
			$sub_c =$this->add('xepan\commerce\Tool_Categorytest',null,'nested_category',['view\tool\categorytest','category_list']);
			$sub_c->setModel($sub_cat);
			$this->current_row_html['nested_category']= $sub_c->getHTML();
		}
		return parent::formatRow();
	}

	function defaultTemplate(){
		return ['view\tool\categorytest'];
	}
}