<?php

// ?width=&height=&item_id=&item_member_design_id=&page_name=&layout=
namespace xepan\commerce;

class page_designer_thumbnail extends \Page {
	
	public $print_ratio = 1;
	public $false_array=array('undefined','null','false',false);

	function init(){
		parent::init();


		$item_id = $_GET['xsnb_design_item_id']?:false;
		$item_member_design_id = !in_array($_GET['item_member_design_id'], $this->false_array) ? $_GET['item_member_design_id']:false;
		$xsnb_design_template = $_GET['xsnb_design_template']=='true'?true:false;


		// if member_item_id then must be member it self or any backend member;
	
		$member = $this->add('xepan\base\Model_Contact');
		$member_logged_in = $member->loadLoggedIn();

		if($item_member_design_id){
			$target = $this->item = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($item_member_design_id);
			if(!$target->loaded()){
				echo "could not load design";
				exit;
			} 
			$item =$target->ref('item_id');
		}

		if($item_id  and !isset($target)){
			$target = $this->item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			if(!$target->loaded()){
				echo "could not load item";
				exit;
			}
			$item = $target;
		}

		if($item_member_design_id and $target['member_id'] != $member->id AND !$this->api->auth->model->isBackEndUser()){
			echo "You are not allowed to take the design preview ";
			exit;
		}

		
		$design = $target['designs'];
		$design = json_decode($design,true);
		$cont = $this->add('xepan\commerce\Controller_DesignTemplate',array('item'=>$item,'design'=>$design,'page_name'=>$_GET['page_name']?:'Front Page','layout'=>$_GET['layout_name']?:'Main Layout'));
		$cont->show($type='png',$quality=1, $base64_encode=false, $return_data=false);
		exit;
	}

	
}