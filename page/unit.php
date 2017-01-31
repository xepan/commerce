<?php 
 namespace xepan\commerce;
 class page_unit extends \xepan\commerce\page_configurationsidebar{

	public $title='Unit Configuration';

	function init(){
		parent::init();

		$tab = $this->add('Tabs');
		$tab_ug = $tab->addTab('Unit Group');
		$tab_u = $tab->addTab('Unit');
		$tab_uc = $tab->addTab('Unit Conversion');

		$model_group = $tab_ug->add('xepan\commerce\Model_UnitGroup');
		$crud_g = $tab_ug->add('xepan\hr\CRUD',null,null,
							['view/configuration/unit/group-grid']);
		$crud_g->setModel($model_group);

		$model_unit = $tab_ug->add('xepan\commerce\Model_Unit');
		$model_unit->acl= 'xepan\commerce\Model_UnitGroup';
		$crud_u = $tab_u->add('xepan\hr\CRUD',null,null,
							['view/configuration/unit/grid']);
		$crud_u->setModel($model_unit);
		
		$model_unit_conversion = $tab_uc->add('xepan\commerce\Model_UnitConversion');
		$model_unit_conversion->acl= 'xepan\commerce\Model_UnitGroup';
		$crud_uc = $tab_uc->add('xepan\hr\CRUD',null,null,
							['view/configuration/unit/conversion-grid']);
		$crud_uc->setModel($model_unit_conversion);

		// if($crud_uc->isEditing()){
		// 	$form = $crud_uc->form;
		// 	$field_one_of_id = $form->getElement('one_of_id');
		// 	$field_to_become_id = $form->getElement('to_become_id');

		// 	$field_one_of_id->js('change',$field_to_become_id->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$field_to_become_id->name]),'one_of_unit'=>$field_one_of_id->js()->val()]));
		// }
	}
} 