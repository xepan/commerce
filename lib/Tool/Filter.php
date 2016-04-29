<?php

namespace xepan\commerce;

class Tool_Filter extends \xepan\cms\View_Tool{
	public $options = [];
	public $header_view;
	function init(){
		parent::init();

		$model_filter = $this->add('xepan\commerce\Model_Filter');

		if(!$model_filter->count()->getOne()){
			$this->add('View_Error')->set('no filter found');
			return;
		}

		//Filter Form
		$form = $this->add('Form');
		//price slider
		$form->addField('Slider','price');
		$q = $model_filter->dsql();

		/**
		get all unique value
		Filterable specification has many Association
		Association has many values
		*/
		//join with association
		$asso_join = $model_filter->Join('customfield_association.customfield_generic_id','id');
		
		//association join with values
		$value_join = $asso_join->join('customfield_value.customfield_association_id','id');
		$value_join->addField('value_name','name');

		//group by with value name
		$cf_name_group_element = $q->expr('[0]',[$model_filter->getElement('id')]);
		//group by with specification name
		$value_group_element = $q->expr('[0]',[$model_filter->getElement('value_name')]);
		$model_filter->_dsql()->group($value_group_element);

		$model_filter->addCondition('value_name','<>',"");
		$model_filter->setOrder('name');

		// if($this->app->recall('filter',false)){
			$previous_selected_filter = json_decode($this->app->recall('filter'),true)?:[];
		// }
		
		$unique_specification_array = [];
		$count = 1;
	
		foreach ($model_filter as $specification) {

			if(!isset($unique_specification_array[$specification['name']])){
				$form->add('View')->set($specification['name']);
				$unique_specification_array[$specification['name']] = [];
			}
			$field = $form->addField('checkbox',$count,$specification['value_name']);
			
			if(count($previous_selected_filter)){
				// echo "<pre>";
					// print_r($previous_selected_filter[$specification['id']]['values']);
				if(isset($previous_selected_filter[$specification['id']]))
					if(in_array($specification['value_name'],$previous_selected_filter[$specification['id']]))
						$field->set(1);
			}
			$count++;
		}

		$form->on('click','input',$form->js()->submit());

		//specification_id_1:value1,value2|specification_id_2:value_1,value_2
		if($form->isSubmitted()){
			$selected_options = [];
							
			$str = "";
			$specification_array=[];
			$count = 1;
			foreach ($model_filter as $specification) {
				//if filter checked or not
				if($form[$count]){
					if(!isset($specification_array[$specification['id']]))
						$specification_array[$specification['id']] = [];

					$specification_array[$specification['id']][] = $specification['value_name'];
				}

				$count++;
			}

			$this->app->memorize('filter',json_encode($specification_array,true));
			$form->app->redirect();
		}

	}
}