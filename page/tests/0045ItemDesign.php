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

class page_tests_0045ItemDesign extends \xepan\base\Page_Tester {
	
	public $title='Item Template Design';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_Template_Design'=>['count'=>-1]
        
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
        $result['count'] = $this->app->db->dsql()->table('item_template_design')->del('fields')->field('count(*)')->getOne();
        return $result;
    }

    function prepare_Import_Template_Design() {
        $this->proper_responses['test_Import_Template_Design']['count'] = $this->pdb->dsql()->table('xshop_item_member_designs')->del('fields')->field('count(*)')->getOne();
        
        $item_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('item');
        $customer_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('customer');

        $new_design = $this->add('xepan\commerce\Model_Item_Template_Design');

        $old_designs = $this->pdb->dsql()->table('xshop_item_member_designs')->get();

        $file_data = [];
        foreach ($old_designs as $old_design) {
            $new_design
            ->set('item_id',$item_mapping[$old_design['item_id']]['new_id'])
            ->set('contact_id',$customer_mapping[$old_design['member_id']]['new_id'])
            ->set('last_modified',$old_design['last_modified'])
            ->set('name',$old_design['name'])
            ->set('is_ordered',$old_design['is_ordered'])
            ->set('designs',$old_design['designs'])
            ->save();

            $file_data[$old_design['id']] = ['new_id'=>$new_design->id];
            $new_design->unload();
        }
        
        file_put_contents(__DIR__.'/category_mapping.json', json_encode($file_data));
    }

    function test_Import_Template_Design(){
        $count = $this->app->db->dsql()->table('item_template_design')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$count];
    }

}
