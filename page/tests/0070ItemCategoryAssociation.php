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
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
        $result=[];
        $result['count'] = $this->app->db->dsql()->table('category_item_association')->del('fields')->field('count(*)')->getOne();
        return $result;
    }

    function prepare_Import_Association() {
        $this->proper_responses['test_Import_Association']['count'] = $this->pdb->dsql()->table('xshop_category_item')->where('is_associate',1)->del('fields')->field('count(*)')->getOne();
        
        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');
        $category_mapping = $this->add('xepan\commerce\page_tests_init')
                    ->getMapping('category');

        $new_asso = $this->add('xepan\commerce\Model_CategoryItemAssociation');

        $old_associations = $this->pdb->dsql()->table('xshop_category_item')
                            ->where('is_associate',1)
                            ->get();

        $file_data = [];
        foreach ($old_associations as $old_asso) {
            if(!$item_mapping[$old_asso['item_id']]['new_id'] or !$category_mapping[$old_asso['category_id']]['new_id'])
                continue;                
            $new_asso
            ->set('item_id',$item_mapping[$old_asso['item_id']]['new_id'])
            ->set('category_id',$category_mapping[$old_asso['category_id']]['new_id'])
            ->save();

            $file_data[$old_asso['id']] = ['new_id'=>$new_asso->id];
            $new_asso->unload();
        }
        
        file_put_contents(__DIR__.'/category_item_association_mapping.json', json_encode($file_data));
    }

    function test_Import_Association(){
        $count = $this->app->db->dsql()->table('category_item_association')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$count];
    }

}
