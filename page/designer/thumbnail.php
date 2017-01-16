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
		$member_logged_in = $member->loadLoggedIn("Customer");

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

		if($item_member_design_id and $target['contact_id'] != $member->id AND !$this->api->auth->model->isAdminUser()){
			echo "You are not allowed to take the design preview ".$member['name'];
			exit;
		}

		session_write_close();
		$design = $target['designs'];
		$design = json_decode($design,true);

		if($_GET['page_name']){
			$page_name = $_GET['page_name'];
		}else
			$page_name = 'Front Page';

		if($_GET['layout_name']){
			$layout_name = $_GET['layout_name'];
		}elseif(isset($design['selected_layouts_for_print']) and isset($design['selected_layouts_for_print'][$page_name])){
			$layout_name = $design['selected_layouts_for_print'][$page_name];
		}else
			$layout_name = "Main Layout";

		$cont = $this->add('xepan\commerce\Controller_DesignTemplate',array('item'=>$item,'design'=>$design,'page_name'=>$page_name,'layout'=>$layout_name));
		$cont->show($type='png',$quality=1, $base64_encode=false, $return_data=false,$_GET['thumbnail_width'],$_GET['thumbnail_height']);
		exit;
	}

	
}