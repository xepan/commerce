<?php

namespace xepan\commerce;

class Form_Field_Item extends \xepan\base\Form_Field_Basic {

	public $custom_field_page ;
	public $show_custom_fields=true;
	public $custom_field_element = 'extra_info';
	public $custom_field_btn_class = 'extra-info';
	public $selected_item_id;
	public $existing_json;
	public $new_jobcard_json;
	public $is_mandatory = true;

	function init(){
		parent::init();

		if($this->show_custom_fields){
			$this->custom_field_page();
		}

		$self = $this;

		if($this->is_mandatory)
			$validator = $this->add('xepan\base\Controller_Validator');
	}

	function recursiveRender(){
		if($this->show_custom_fields){
			$this->owner->getElement($this->custom_field_element)->js(true)->closest('.atk-form-row')->hide();
			$this->manageCustomFields();
		}

		// RESET Custom Fields if Item is changed
        $this->other_field->on('change',$this->owner->getElement($this->custom_field_element)->js()->val(''));

		parent::recursiveRender();
	}

	function manageCustomFields(){
		$this->js('click',$this->js()->univ()->frameURL
			(
				'Custom Field Values',
				array(
					$this->api->url(
						$this->custom_field_page->getURL(),
						array(
							'custom_field_name'=>$this->owner->getElement($this->custom_field_element)->name,
							)
						),
					"selected_item_id"=>$this->js()->val(),
					'current_json'=>$this->owner->getElement($this->custom_field_element)->js()->val()
					)
				)
			)->_selector(".".$this->custom_field_btn_class);
	}

	function custom_field_page(){
		$self = $this;
		
		$this->custom_field_page = $this->add('VirtualPage');
			
		$this->custom_field_page->set(function($p)use($self){

			$p->api->stickyGET('custom_field_name');
			$p->api->stickyGET('current_json');
			
			$item_id = $p->api->stickyGET('selected_item_id');
			
			$p->item = $item = $p->add('xepan\commerce\Model_Item')->tryLoad($item_id);

			if(!$item->loaded()) {
				$p->add('View_Error')->set('Item not selceted');
				return;
			}
			
			//Make PredefinedPhase Array
			$p->preDefinedPhase = array();
			foreach ($item->getAssociatedDepartment() as $key => $value) {
				$p->preDefinedPhase[$value] = array();
			}

			$p->existing_values = $_GET['current_json']?json_decode($_GET['current_json'],true):$p->preDefinedPhase;

			// Form
			$p->form = $form = $p->add('Form');
			$save_button_view = $p->form->add('View')->addClass('xepan-padding');
			$save_button = $save_button_view->add('Button')->set('update')->addClass('btn btn-primary');
			$save_button->js('click',$p->form->js()->submit());
			//None Department Association Custom
			$none_dept_cf = $item->noneDepartmentAssociateCustomFields();

			$view = $p->form->add('view',null,null,['view/item/associate/field']);
			$view->template->trySet('heading',"Other\No Department");
			foreach ($none_dept_cf as $cf_asso) {
				$field = $self->addCustomField($cf_asso,$p,$view);
				$existing_cf_array = $p->existing_values[0][$cf_asso['customfield_generic_id']];
				if( isset($existing_cf_array['custom_field_value_id'])){
					$field->set($existing_cf_array['custom_field_value_id']);
				}
			}		

			//Department Associated CustomFields
			$phases = $p->add('xepan\hr\Model_Department')->setOrder('production_level','asc');
			foreach ($phases as $phase) {
				$field_type = 'Checkbox';

				$custom_fields_asso = $item->ref('xepan\commerce\Item_CustomField_Association')->addCondition('department_id',$phase->id);
				// if item has custome fields for phase & set if editing
				$view = $form->add('View',null,null,['view/item/associate/field']);
				$heading = $view->add('View',null,'heading')->addClass('xepan-customfield-department-name');
				$phase_field = $heading->addField($field_type,'phase_'.$phase->id,$phase['name']);

				if( is_array($p->existing_values[$phase->id]) ){
					$phase_field->set(true);
				}
				
				// $custom_fields_array=array();
				foreach ($custom_fields_asso as $cfassos) {
					$field = $self->addCustomField($custom_fields_asso,$p,$view);
					$existing_cf_array = $p->existing_values[$phase->id][$cfassos['customfield_generic_id']];
					if( isset($existing_cf_array['custom_field_value_id'])){
						$field->set($existing_cf_array['custom_field_value_id']);
					}
					// $custom_fields_array[] = 'custom_field_'.$custom_fields_asso->id;
				}
			}


			$form->addSubmit('Update')->addClass('btn btn-primary');
			$custom_fields_asso_values=array();
			
			if($form->isSubmitted()){

				// check No Department Association custom Fields
				$none_dept_cf = $item->noneDepartmentAssociateCustomFields();
				$none_dept_cf->addExpression('display_type')->set(function($m,$q){
					return $m->refSQL('customfield_generic_id')->fieldQuery('display_type');
				});
				$none_department_cf = $none_dept_cf;

				$custom_fields_asso_values[0]=array();
				foreach ($none_department_cf as $cf_asso) {
					if(!$form['custom_field_'.$cf_asso->id])
						$form->displayError('custom_field_'.$cf_asso->id,'Please define custom fields for selected phase');

					$custom_fields_asso_values[0]['department_name'] = "Other\ No Department";
					$custom_fields_asso_values[0][$cf_asso['customfield_generic_id']] = array();
					$custom_fields_asso_values[0][$cf_asso['customfield_generic_id']]['custom_field_name'] =  $cf_asso['customfield_generic'];
					$custom_fields_asso_values[0][$cf_asso['customfield_generic_id']]['custom_field_value_id'] = $form['custom_field_'.$cf_asso->id];

					$value = $form['custom_field_'.$cf_asso->id]; // line type and color type
					if($cf_asso['display_type'] == 'DropDown'){
						$cf_value_model = $this->add('xepan\commerce\Model_Item_CustomField_Value')->load($value);
						$value  = $cf_value_model['name'];
					}

					$custom_fields_asso_values [0][$cf_asso['customfield_generic_id']]['custom_field_value_name'] = $value;
				}

				//Check For the Custom Field Value Not Proper
				foreach ($phases as $phase) {

					if( $form['phase_'.$phase->id] ){
						
						$custom_fields_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')
												->addCondition('department_id',$phase->id)
												->addCondition('item_id',$item->id);

						$custom_fields_asso->addExpression('display_type')->set(function($m,$q){
							return $m->refSQL('customfield_generic_id')->fieldQuery('display_type');
						});

						$custom_fields_asso_values[$phase->id] = array();
						$custom_fields_asso_values[$phase->id]['department_name'] = $phase['name'];

						foreach ($custom_fields_asso as $cfassos) {

							if(!$form['custom_field_'.$custom_fields_asso->id]){
								$form->displayError('custom_field_'.$custom_fields_asso->id,'Please define custom fields for selected phase');
							}

							$custom_fields_asso_values [$phase->id][$cfassos['customfield_generic_id']] = array();
							
							$custom_fields_asso_values [$phase->id][$cfassos['customfield_generic_id']]['custom_field_name'] =  $cfassos['customfield_generic'];
							$custom_fields_asso_values [$phase->id][$cfassos['customfield_generic_id']]['custom_field_value_id'] = $form['custom_field_'.$custom_fields_asso->id];

							$value = $form['custom_field_'.$custom_fields_asso->id]; // line type and color type
							if($cfassos['display_type'] == 'DropDown'){
								$cf_value_model = $this->add('xepan\commerce\Model_Item_CustomField_Value')->load($value);
								$value  = $cf_value_model['name'];
							}

							$custom_fields_asso_values [$phase->id][$cfassos['customfield_generic_id']]['custom_field_value_name'] = $value;
						}
					}
				}

				$selected_phases = array_keys($custom_fields_asso_values);
				//Check For the One Department at One Leve
				$level_touched=array();
				foreach ($selected_phases as $ph) {
					if($ph == 0)
						continue;
					if(in_array(($prd_level = $p->add('xepan\hr\Model_Department')->load($ph)->get('production_level')),$level_touched)){
						$form->displayError('phase_'.$ph,' Cannot Select More phases/Departments at a level');
					}
					$level_touched[] = $prd_level;
				}

				$json = json_encode($custom_fields_asso_values);
				$getamount=$item->getPrice($custom_fields_asso_values,2);
				// var_dump($getamount);
				$js_array=[
							$this->js()->_selector('input[data-shortname="price"]')->val($getamount['sale_price'])->trigger('change'),
							$form->js()->univ()->closeDialog()							
						];

				$form->js(null,$js_array)->_selector('#'.$_GET['custom_field_name'])->val($json)->trigger('change')->execute();
			}
		});

	}


	function addCustomField($custom_fields_asso,$page,$view_layout,$mandatory=false){
		$field=null;
		$cf = $this->add('xepan\commerce\Model_Item_CustomField_Generic')->load($custom_fields_asso['customfield_generic_id']);
		switch($cf['display_type']){
			case "Line":
				$field = $view_layout->addField('line','custom_field_'.$custom_fields_asso->id , $custom_fields_asso['name']);
			break;
			case "DropDown":
				$field = $drp = $view_layout->addField('DropDown','custom_field_'.$custom_fields_asso->id , $custom_fields_asso['name']);
				$values = $page->add('xepan\commerce\Model_Item_CustomField_Value');
				$values->addCondition('customfield_association_id',$custom_fields_asso->id);
				$values_array=array();
				foreach ($values as $value) {
					$values_array[$value['id']]=$value['name'];
				}
				$drp->setValueList($values_array);
				$drp->setEmptyText('Please Select Value');
			break;
			case "Color":
			break;
		}

		if($field and $mandatory)
			$field->validate('required');

		return $field;
	}

	function validate(){
		if(!$this->is_mandatory)
			return;

		if(!$this->get()) $this->displayFieldError('Please specify Item');
				
		$item = $this->add('xepan/commerce/Model_Item')->load($this->get());
		$cf_filled =  trim($this->owner->get($this->custom_field_element));
		
		if($cf_filled == ''){
			$phases_ids = $item->getAssociatedDepartment();
			$cust_field_array = array();
		}else{
			$cust_field_array = json_decode($cf_filled,true);
			$phases_ids = array_keys($cust_field_array);
		}

		foreach($phases_ids as $phase_id) {

			if($phase_id==0){
				$associate_model = $item->associateCustomField();
				$associate_model->addCondition('department_id',0);
				$custom_fields_assos_ids = [];
				foreach ($associate_model as $temp) {
					$custom_fields_assos_ids[] = $temp['customfield_generic_id'];
				}
			}else
				$custom_fields_assos_ids = $item->getAssociatedCustomFields($phase_id);

			foreach ($custom_fields_assos_ids as $cf_id) {
				if(!isset($cust_field_array[$phase_id][$cf_id]) or $cust_field_array[$phase_id][$cf_id] == ''){
					$this->displayFieldError('This Item requires custom fields to be filled');
				}
			}
		}
		
	}
}