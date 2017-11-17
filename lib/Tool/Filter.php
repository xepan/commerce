<?php

namespace xepan\commerce;

class Tool_Filter extends \xepan\cms\View_Tool{
	public $options = [
			"show_price_filter"=>true,
			"min_price"=>0,
			"max_price"=>10,
			"left_label" => "min",
			"right_label" => "max",
			"custom_template"=>''
	];
	public $header_view;
	
	function init(){
		parent::init();

		$form_layout = 'view/tool/filter/formsection';

		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/filter".$this->options['custom_template'].".html";
			if(file_exists($path)){
				$form_layout = 'view/tool/filter/'.$this->options['custom_template'];
			}else{
				$this->add('View_Error')->set('Custom template not found.');
				return; 
			}
		}

		$category_id = $this->app->stickyGET('xsnb_category_id');

		$previous_selected_filter = json_decode($this->app->recall('filter'),true)?:[];
		
		$model_filter = $this->add('xepan\commerce\Model_Filter');		
		if(!$model_filter->count()->getOne()){
			$this->add('View_Error')->set('no filter found');
			return;
		}

		//Filter Form
		$form = $this->add('Form',null,null,['form/empty']);

		//price slider
		if($this->options['show_price_filter']){
			$this->heading = $form->add('View',null,null,[$form_layout]);

			$price = $this->heading->addField('xepan\commerce\RangeSlider','price');
			$price->min = $this->options['min_price']?:0;
			$price->max = $this->options['max_price']?:10;
			$price->step = $this->options['step']?:1;
			$price->left = $this->options['left_label']?:'min';
			$price->right = $this->options['right_label']?:'max';

			if($price_range = $this->app->recall('price_range')){
				$range_array = explode(",", $price_range);
				$price->selected_min = $range_array[0];
				$price->selected_max = $range_array[1];
				$price->set($price_range);
				// $this->app->forget('price_range');
			}
			$this->heading->template->trySet('name','Price Range '.$price->selected_min." - ".$price->selected_max);
		}

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
		$value_join->addField('value_id','id');

		// join with category id and show only custom field that are associate with category
		$cat_asso_join = $asso_join->Join('category_item_association.item_id','item_id');
		$cat_asso_join->addField('item_asso_category_id','category_id');

		//group by with value name
		$cf_name_group_element = $q->expr('[0]',[$model_filter->getElement('id')]);
		//group by with specification name
		$value_group_element = $q->expr('[0]',[$model_filter->getElement('value_name')]);
		$model_filter->_dsql()->group($value_group_element);

		if($category_id)
			$model_filter->addCondition('item_asso_category_id',$category_id);

		$model_filter->addCondition('value_name','<>',"");
		$model_filter->setOrder('value_name','asc');
		$model_filter->setOrder('name','asc');


		$unique_specification_array = [];
		$count = 1;
	
		foreach ($model_filter as $specification) {

			if(!isset($unique_specification_array[$specification['name']])){
				$this->heading = $form->add('View',null,null,['view/tool/filter/formsection']);
				$this->heading->template->trySet('name',$specification['name']);
				// $this->heading->add('H2')->set($specification['name']);
				$unique_specification_array[$specification['name']] = [];
			}
			$field = $this->heading->addField('checkbox',$specification['value_id'],$specification['value_name']);
			
			if(count($previous_selected_filter)){

				// echo "<pre>";
					// print_r($previous_selected_filter[$specification['id']]['values']);
				if(isset($previous_selected_filter[$specification['id']]))
					if(in_array($specification['value_name'],$previous_selected_filter[$specification['id']]))
						$field->set(1);
			}
			// $count++;
		}
		// $form->on('click','input',$form->js()->submit());
		$form->on('change','input',$form->js()->submit());

		//specification_id_1:value1,value2|specification_id_2:value_1,value_2
		if($form->isSubmitted()){			
			$selected_options = [];				
			$str = "";
			$specification_array=[];
			// $count = 1;

			foreach ($model_filter as $specification) {
				//if filter checked or not
				if($form[$specification['value_id']]){
						
					if(!isset($specification_array[$specification['id']]))
						$specification_array[$specification['id']] = [];

					$specification_array[$specification['id']][] = $specification['value_name'];
					// echo '<pre>';
					// var_dump($specification_array);
					// exit;
					
				}
				// $count++;
			}

			$this->app->memorize('filter',json_encode($specification_array,true));
			$this->app->memorize('price_range',$form['price']);

			$form->app->redirect($this->app->url());
		}

	}

	function render(){
		$this->js(true)->_css("jquery-ui");
		parent::render();
	}
}