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

class page_tests_0070ItemCategoryAssociation extends \xepan\base\Page_Tester {
	
	public $title='Item Category Association';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_Association'=>['count'=>-1]
        
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
        $result['count'] = $this->app->db->dsql()->table('category_item_association')->del('fields')->field('count(*)')->getOne();
        return $result;
    }

    function prepare_Import_Association() {
        
        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');
        $category_mapping = $this->add('xepan\commerce\page_tests_init')
                    ->getMapping('category');

        $new_asso = $this->add('xepan\commerce\Model_CategoryItemAssociation');

        $old_associations = $this->pdb->dsql()->table('xshop_category_item')
                            ->where('is_associate',1)
                            ->get();

        $this->proper_responses['test_Import_Association']['count'] = count($old_associations);

        $file_data = [];
        $items_not_found=0;
        foreach ($old_associations as $old_asso) {
            if(!$item_mapping[$old_asso['item_id']]['new_id'] or !$category_mapping[$old_asso['category_id']]['new_id']){
                $items_not_found++;
                continue;                
            }
            $new_asso
            ->set('item_id',$item_mapping[$old_asso['item_id']]['new_id'])
            ->set('category_id',$category_mapping[$old_asso['category_id']]['new_id'])
            ->save();

            $file_data[$old_asso['id']] = ['new_id'=>$new_asso->id];
            $new_asso->unload();
        }

        $this->proper_responses['test_Import_Association']['count'] -= $items_not_found;
        
        file_put_contents(__DIR__.'/category_item_association_mapping.json', json_encode($file_data));
    }

    function test_Import_Association(){
        $count = $this->app->db->dsql()->table('category_item_association')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$count];
    }

}
