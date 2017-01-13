<?php

namespace xepan\commerce;

class page_designer_save extends \Page {
	function page_index(){

		if(!$this->api->auth->isLoggedIn()){
			//not logged in save current design in session and return to login page
			//using virtual page for login panel
			$vp = $this->add('VirtualPage');
			$options = [
					'width'=>600
				];
			$vp->set(function($vp){
				$this->app->addHook('login_panel_user_loggedin',function($app,$user){
					$js = [
							$this->js()->_selector('.xshop-render-tool-save-btn')->click(),
							$this->js()->univ()->closeDialog()
							];
					$this->js(null,$js)->execute();
				});

				$vp->add('xepan\base\Tool_UserPanel',["_options"=>['layout'=>'login_view']]);
			});

			$this->js(null)->univ()->frameURL('Login Panel',$vp->getURL(),$options)->execute();
			echo "false:9";
			exit;
		}

		$designer = $this->add('xepan\base\Model_Contact');
		if(!$designer->loadLoggedIn("Customer")) {
			// Current logged in user, either user is logged out or does not have any member entry
			// So...
			echo "false :17";
			exit;
		}

		if($_POST['item_member_design_id']){
			$target = $this->item = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($_POST['item_member_design_id']);
		}

		if($_POST['item_id']  and !isset($target)){
			$target = $this->item = $this->add('xepan\commerce\Model_Item')->tryLoad($_POST['item_id']);
			if(!$target->loaded()){
				echo "false : 28";
				exit;
			}

		}

		if(!count(json_decode($_POST['xshop_item_design'],true))){
			echo "design not found : 30";
			exit;
		}


		$design = json_decode($_POST['xshop_item_design'],true);
		// foreach ($design as &$page) {
		// 	foreach ($page as &$layout) {
		// 		usort($layout['components'], function($a,$b){
		// 				$a_array = json_decode($a,true);
		// 				$b_array = json_decode($b,true);
		// 				$a_zindex = $a_array['zindex']?$a_array['zindex']:0;
		// 				$b_zindex = $b_array['zindex']?$b_array['zindex']:0;
		// 				return $a_zindex > $b_zindex;
		// 			});
		// 	}
		// }


		$save_data =array();
		$save_data['px_width']=$_POST['px_width'];
		$save_data['design']=$design;
		$save_data['selected_layouts_for_print']=json_decode($_POST['selected_layouts_for_print'],true);
		$save_data['calendar_starting_month'] = $_POST['calendar_starting_month'];
		$save_data['calendar_starting_year'] = $_POST['calendar_starting_year'];
		$save_data['calendar_event'] = json_decode($_POST['calendar_event'],true);
		$save_data['mode'] = $_POST['mode'];
		$save_data['ComponentsIncludedToBeShow'] = $_POST['ComponentsIncludedToBeShow'];
		$save_data['BackgroundImage_tool_label'] = $_POST['BackgroundImage_tool_label'];
		
		$save_data['show_pagelayout_bar'] = $_POST['show_pagelayout_bar'];
		$save_data['show_canvas'] = $_POST['show_canvas'];
		$save_data['show_layout_bar'] = $_POST['show_layout_bar'];
		$save_data['show_paginator'] = $_POST['show_paginator'];
		$save_data['show_tool_calendar_starting_month'] = $_POST['show_tool_calendar_starting_month'];
		$save_data = json_encode($save_data);
		
		// echo ("Designer Mode: ".$_POST['designer_mode']. ",Target Designer_id: ".$target['designer_id'] ." Designer Id: ".$designer->id);
		if(isset($target) and $_POST['designer_mode']=='true' and $target['designer_id'] == $designer->id){
			// am I the designer of item ?? .. checked in if condition above
			
			// check for required specifications like width / height

			// set designer_mode=true to desginer js
			$target['designs'] = $save_data;
			$target->save();

			if($_POST['image_array']){
				if(strlen($_POST['image_array']) != $_POST['checksum']){
					echo "false : image not saved properly, send checksum = ".$_POST['checksum']." received checksum".strlen($_POST['image_array']);
					exit;
				}
				$status = $target->updateImageFromDesign(json_decode($_POST['image_array'],true),$_POST['delete_all_image']);
				if($status != "success"){
					echo $status;
					exit;
				}
			}

			echo $target['id'];
			exit;
			
		}elseif(isset($target) and ($_POST['designer_mode']=='false' OR !isset($_POST['designer_mode'])) and /* Made by me only --> */ $target['contact_id'] == $designer->id ){
			// set target model to member_item_assos
			// set designer_mode=false to desginer js
			if($target->loaded()){
				$target['designs'] = $save_data;
				$target->save();
			}else{
				$target['item_id'] = $_POST['item_id'];
				$target['contact_id'] = $desginer['id'];
				$target['designs']	= $save_data;
				$target->save();
			}		
			echo $target['id'];
			exit;
		}elseif(($_POST['designer_mode']=='false' OR !isset($_POST['designer_mode'])) and /*Saving first time .. no saved id sent --> */ !$_POST['item_member_design_id']){
			$target = $this->add('xepan\commerce\Model_Item_Template_Design');
			$target['item_id'] = $_POST['item_id'];
			$target['contact_id'] = $designer->id;
			$target['designs']	= $save_data;
			$target->save();
			echo $target['id'];
			exit;
		}
		else{
			// NOTHING ??? .. Something wrong .. 
			// url not proper
			// Or target cold not be get
			// or trying to design template whose owner is not you (HAKINGGGGG)
			// or trying to edit a design not made by you (Hakinggg)
			// Put Common error for all
			// throw $this->exception('Something gone wrong... Please try again later');
			echo "false: 66";
			exit;
		}
	}
}		