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

class page_tests_0060category extends \xepan\base\Page_Tester {
	
	public $title='Category';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_Category'=>['count'=>-1]
        
    ];


    function init(){
        
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
        $result=[];
        $result['count'] = $this->app->db->dsql()->table('category')->del('fields')->field('count(*)')->getOne();
        return $result;
    }

    function prepare_Import_Category() {
        $this->proper_responses['test_Import_Category']['count'] = $this->pdb->dsql()->table('xshop_categories')->del('fields')->field('count(*)')->getOne();
        
        $new_category = $this->add('xepan\commerce\Model_Category');

        $old_categories = $this->pdb->dsql()->table('xshop_categories')->get();

        $file_data = [];
        foreach ($old_categories as $old_cat) {
            $new_category
            ->set('name',$old_cat['name'])
            ->set('display_sequence',$old_cat['order_no'])
            ->set('alt_text',$old_cat['alt_text'])
            ->set('description',$old_cat['description'])
            ->set('meta_title',$old_cat['meta_title'])
            ->set('meta_description',$old_cat['meta_description'])
            ->set('meta_keywords',$old_cat['meta_keywords'])
            ->set('cat_image_id',$old_cat['image_url_id']?:0)
            ->set('status',$old_cat['is_active']?"Active":"DeActive")
            ->save();

            $file_data[$old_cat['id']] = ['new_id'=>$new_category->id];
            $new_category->unload();
        }
        
        file_put_contents(__DIR__.'/category_mapping.json', json_encode($file_data));
    }

    function test_Import_Category(){
        $count = $this->app->db->dsql()->table('category')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$count];
    }

}
