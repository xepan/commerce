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

class page_tests_0035Specification extends \xepan\base\Page_Tester {
	
	public $title='Specification Import';
	
	public $proper_responses=[

        'test_checkEmpty_Specification'=>['count'=>0],
        'test_Import_Specification'=>['count'=>-1],
               
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


    function test_checkEmpty_Specification(){
        return ['count'=>$this->app->db->dsql()->table('customfield_generic')->where('type','Specification')->del('fields')->field('count(*)')->getOne()];
    }

    function prepare_Import_Specification(){
        //setting count of old cf
        $this->proper_responses['test_Import_Specification']['count'] = $this->pdb->dsql()->table('xshop_specifications')->del('fields')->field('count(*)')->getOne();

        $file_data = [];
        $old_specs = $this->pdb->dsql()->table('xshop_specifications')->get();

        foreach ($old_specs as $old_spec) {

            $new_spec = $this->add('xepan\commerce\Model_Item_Specification');
            $new_spec
                ->addCondition('name',$old_spec['name'])
                ->tryLoadAny();

            if(!$new_spec->loaded()){
                $new_spec['is_filterable'] = $old_spec['is_filterable']?:0;
                $new_spec['sequence_order'] = $old_spec['order'];
                $new_spec->save();
            }

            $file_data[$old_spec['id']] = ['new_id'=>$new_spec->id];

            $new_spec->unload();
        }

        file_put_contents(__DIR__.'/specification_mapping.json', json_encode($file_data));
    }

    function test_Import_Specification(){
        $cf_count = $this->app->db->dsql()->table('customfield_generic')->where('type','Specification')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$cf_count];
    }

}
