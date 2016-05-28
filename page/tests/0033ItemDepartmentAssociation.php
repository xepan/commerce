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

class page_tests_0033ItemDepartmentAssociation extends \xepan\base\Page_Tester {
	
	public $title='Item Department Association Import';
	
	public $proper_responses=[
        'test_checkEmpty_Item_Department_Association'=>['count'=>0],
        'test_Import_Item_Department_Association'=>['count'=>-1],
               
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


    function test_checkEmpty_Item_Department_Association(){
        return ['count'=>$this->app->db->dsql()->table('item_department_association')->del('fields')->field('count(*)')->getOne()];
    }

    function prepare_Import_Item_Department_Association(){
        //setting count of old cf
        $this->proper_responses['test_Import_Item_Department_Association']['count'] = count($this->pdb->dsql()->table('xshop_item_department_asso')->having('is_active',1)->group(['item_id','department_id','is_active'])->del('fields')->field('count(*)')->get());
        
        $item_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('item');
        $department_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('department');

        $file_data = [];
        $old_dept_asso = $this->pdb->dsql()->table('xshop_item_department_asso')->having('is_active',1)->group(['item_id','department_id','is_active'])->get();

        foreach ($old_dept_asso as $old_asso) {

            $new_dept_asso = $this->add('xepan\commerce\Model_Item_Department_Association');
            $new_dept_asso
                ->addCondition('item_id',$item_mapping[$old_asso['item_id']]['new_id']?:100000)
                ->addCondition('department_id',$department_mapping[$old_asso['department_id']]['new_id']?:100000)
                ;
            $new_dept_asso->tryLoadAny();

            if(!$new_dept_asso->loaded()){
                $new_dept_asso['can_redefine_qty'] = $old_asso['can_redefine_qty'];
                $new_dept_asso['can_redefine_item'] = $old_asso['can_redefine_item'];
                $new_dept_asso->save();
            }

            $file_data[$old_asso['id']] = ['new_id'=>$new_dept_asso->id];
            $new_dept_asso->unload();
        }

        file_put_contents(__DIR__.'/item_department_association_mapping.json', json_encode($file_data));
    }

    function test_Import_Item_Department_Association(){
        $cf_count = $this->app->db->dsql()->table('item_department_association')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$cf_count];
    }

}
