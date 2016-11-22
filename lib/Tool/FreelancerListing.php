<?php

namespace xepan\commerce;

class Tool_FreelancerListing extends \xepan\cms\View_Tool{
	public $options = [
						'show_design_count'=>true,
						'freelance_result_page'=>''
					];

	function init(){
		parent::init();
		if($this->options['freelance_result_page']==''){
			$this->add('View_Error')->set('Please Specify Designer Design page Url First');
			return ;
		}

		$contact = $this->add('xepan\base\Model_Contact');
		$item_j = $contact->join('item.designer_id');
		$item_j->addField('designer_id');
		$contact->addCondition('status','Active');
		// $contact->dsql()->group('designer_id');
		$c =  $this->add('CompleteLister',null,null,['view\tool\freelancerlisting']);
		$c->setModel($contact);
		$c->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$contact]);
	}

	function addToolCondition_row_show_design_count($value, $l){
		if(!$value){
			$l->current_row_html['design_count'] = "";
			return;
		}
		$item = $this->add('xepan\commerce\Model_Item');
		$item->addCondition('designer_id',$l->model->id);
		$l->current_row['design_count'] = $item->count()->getOne();
	}
	function addToolCondition_row_freelance_result_page($value,$l){
		$design_page_url = $this->api->url($this->options['freelance_result_page'],['designer_id'=>$l->model->id]);
		$l->current_row_html['url'] = $design_page_url;
	}
}