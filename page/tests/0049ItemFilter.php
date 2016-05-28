<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class page_tests_0049ItemFilter extends \xepan\base\Page_Tester {
	
	public $title='Item Filter';
	
	public $proper_responses=[
        'test_Import_Filters'=>['count'=>775]
        
    ];

    public $last_cf_asso_value_id=99999999;
    public $count = 0;

    function init(){
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect($this->app->getConfig('dsn2'));        

        $last_cf_value_model = $this->add('xepan\commerce\Model_Item_CustomField_Value')->setOrder('id','desc')->setLimit(1)->tryLoadAny();
        $this->last_cf_asso_value_id = $last_cf_value_model->id;

        //first remove all filterable specification value
        $filter_spec = $this->add('xepan\commerce\Model_Item_CustomField_Generic')->addCondition('is_filterable',true);
        foreach ($filter_spec as $spec) {
            $all_pre_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('customfield_generic_id',$spec->id);            
            foreach ($all_pre_asso as $asso) {
                $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$asso->id)->deleteAll();
            }
        }

        try{
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=0;')->execute();
            $this->app->db->dsql()->expr('SET autocommit=0;')->execute();

            $this->api->db->beginTransaction();
                parent::init();
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
            $this->api->db->commit();
        }catch(\Exception_StopInit $e){

        }catch(\Exception $e){
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
            $this->api->db->rollback();
            throw $e;
        }
    }

    function prepare_Import_Filters(){

        $specification_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('specification');
        $item_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('item');

        $old_filters = $this->pdb->dsql()->table('xshop_filters')->where('item_id','<>',null)->get();

        foreach ($old_filters as $filter) {

            $new_spec_id = $specification_mapping[$filter['specification_id']]['new_id'];
            $new_item_id = $item_mapping[$filter['item_id']]['new_id'];
            if(!isset($new_item_id) or !(isset($new_spec_id)))
                continue;

            //update specification
            $new_cf_generic = $this->add('xepan\commerce\Model_Item_CustomField_Generic');
            $new_cf_generic->tryLoad($new_spec_id);

            $old_specification = $this->pdb->dsql()->table('xshop_specifications')->where('id',$filter['specification_id'])->get();
            if(!$new_cf_generic->loaded()){
                $new_cf_generic['name'] = $old_specification['name'];
                $new_cf_generic['type'] = "DropDown";
            }

            $new_cf_generic['is_filterable'] = true;
            $new_cf_generic->save();

            // create new association
            $new_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association');
            $new_asso['item_id'] = $new_item_id;
            $new_asso['customfield_generic_id'] = $new_spec_id;
            $new_asso->save();

            // insert comma separated multiple values into separate value
            $old_values = explode(",",$filter['unique_values']);
            foreach ($old_values as $value) {
                
                $new_value = $this->add('xepan\commerce\Model_Item_CustomField_Value');
                $new_value
                    ->set('customfield_association_id',$new_asso->id)
                    ->set('name',trim($value))
                    ->save()
                    ;
                $this->count++;
            }

        }
    }

    function test_Import_Filters(){
        return ['count'=>$this->count];
    }
}
