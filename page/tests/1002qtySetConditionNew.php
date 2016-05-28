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

class page_tests_1002qtySetConditionNew extends \xepan\base\Page_Tester {
	
	public $title='Qty Set Condition Importer';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_QtySetConditions'=>['count'=>-1]
        
    ];


    function init(){
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect($this->app->getConfig('dsn2'));
        
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

    function test_checkEmptyRows(){
    	$result=[];
    	$result['count'] = $this->app->db->dsql()->table('quantity_condition')->del('fields')->field('count(*)')->getOne();
    	return $result;
    }

    function prepare_Import_QtySetConditions(){

        // did you imported "xshop_item_quantity_set_conditions" in new database

        $qtyset_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('qtyset');
        $cf_value_mapping = $this->add('xepan\commerce\page_tests_init')
                    ->getMapping('customfield_association_value');

        $this->proper_responses['test_Import_QtySetConditions']['count'] = $this->pdb->dsql()->table('xshop_item_quantity_set_conditions')->del('fields')->field('count(*)')->getOne();

        $qty_set_sql = "CASE quantityset_id "; 
        foreach ($qtyset_mapping as $old_id => $values) {
            $qty_set_sql .= " WHEN $old_id THEN ". $values['new_id'];
        }
        $qty_set_sql.=" END";

        $cf_value_sql = "CASE custom_field_value_id "; 
        foreach ($cf_value_mapping as $old_id => $values) {
            $cf_value_sql .= " WHEN $old_id THEN ". $values['new_id'];
        }
        $cf_value_sql.=" END";


        $sql="
            INSERT INTO quantity_condition (quantity_set_id,customfield_value_id) SELECT $qty_set_sql ,$cf_value_sql FROM xshop_item_quantity_set_conditions
        ";

        $this->app->db->dsql()->expr($sql)->execute();
    }

    function test_Import_QtySetConditions(){
        $set_count = $this->app->db->dsql()->table('quantity_condition')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$set_count];
    }

}
