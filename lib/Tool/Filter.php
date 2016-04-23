<?php

namespace xepan\commerce;

class Tool_Filter extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$category_id = $this->app->stickyGET('xsnb_category_id');

		$model_filter = $this->add('xepan\commerce\Model_Filter');
		
		if(isset($category_id))
				$model_filter->addCondition('category_id',$category_id);

		if(!$model_filter->count()->getOne()){
			$this->add('View_Error')->set('no filter found');
			return;
		}

		$form = $this->add('Form');
		//price slider
		$form->addField('Slider','price');

		//get all specification with it's unique values
		$specification = [];
		foreach ($model_filter as $filter) {
			//check specification exist or not
			$spec_id = $filter['specification_id'];
			$spec_name = $filter['name'];

			if(!isset($specification[$spec_name]))
				$specification[$spec_name] = array(
												'specification_id'=>$spec_id,
												'values'=>[]
											);
			// check specification value exit or not
			$values = explode(',', $filter['unique_value']);
			foreach ($values as $value) {
				if(!isset($specification[$spec_name]['values'][$value]))
					$specification[$spec_name]['values'][$value]=[];
			}
		}


		$previous_selected_filter = $_GET['filter']?:[];
		if($previous_selected_filter){
			$previous_selected_filter = explode(",", $previous_selected_filter);
			$array = [];
			foreach ($previous_selected_filter as $junk) {
				$temp = explode(":", $junk);
				$array[$temp[0]] = explode("-", $temp[1]);
			}
			$previous_selected_filter = $array;
		}
		//Val;ue Array
			// (
			//     [specification_id] => 21
			//     [values] => Array
			//         (
			//             [red] => Array
			//                 (
			//                 )

			//             [green] => Array
			//                 (
			//                 )

			//             [blue] => Array
			//                 (
			//                 )
			//         )
			// )
		$count = 1;
		foreach ($specification as $spec_name => $values) {
			$form->add('View')->set($spec_name);
			
			foreach ($values['values'] as $value_name => $value_data) {
				$field = $form->addField('checkbox',$count,$value_name);				
				if(isset($previous_selected_filter[$values['specification_id']])){
					$temp = $previous_selected_filter[$values['specification_id']];
					if(in_array($value_name,$temp))
						$field->set(1);
				}
				$count++;
			}
		}

		$form->on('click','input',$form->js()->submit());

		if($form->isSubmitted()){
			$selected_options = [];
			
			$count=1;
			$str = "";
			foreach ($specification as $spec_name => $values) {
				$selected_checkbox = "";
				foreach ($values['values'] as $value_name => $value_data) {
					if($form[$count])
						$selected_checkbox .= $value_name."-";
					$count++;
				}

				if($selected_checkbox)
					$str.= $values['specification_id'].":".$selected_checkbox.",";
					
			}

			$form->app->redirect(['filter'=>$str]);
		}

	}
}