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


class page_tests_0020customerImages extends \xepan\base\Page_Tester {
	public $title='Customer Images';

	public $proper_responses=[
		'test_libraryImageCategory'=>'43',
		'test_designerImagesImport'=>'664'
	];

	function init(){
        set_time_limit(0);
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function prepare_libraryImageCategory(){
    	$old_m = $this->pdb->dsql()->table('xshop_image_library_category')
                        ->get();

        $customer_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('customer');

        $new_m = $this->add('xepan\commerce\Model_Designer_Image_Category');
		$file_data=[];
        foreach ($old_m as $om) {
        	$new_m['contact_id'] = $customer_mapping[$om['member_id']]['new_id'];
        	$new_m['name'] = $om['name'];
        	$new_m->save();

        	$file_data[$om['id']] = ['new_id'=>$new_m->id];
        	$new_m->unload();
        }

        file_put_contents(__DIR__.'/image_library_catagory_mapping.json', json_encode($file_data));
    }

    function test_libraryImageCategory(){
        return $this->add('xepan\commerce\Model_Designer_Image_Category')->count()->getOne();

    }
  

    function prepare_designerImagesImport(){
        $old_m = $this->pdb->dsql()->table('xshop_member_images')
			        ->get();
        
        $image_lib_cat_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('image_library_catagory');

		$new_m = $this->add('xepan\commerce\Model_Designer_Images');

		foreach ($old_m as $om) {
			$new_m['image_id'] = $om['image_id'];
			$new_m['designer_category_id'] = $image_lib_cat_mapping[$om['category_id']]['new_id'];
			$new_m->saveAndUnload();
		}

    }

    function test_designerImagesImport(){
    	return $this->api->db->dsql()->table('designer_images')->del('fields')->field('count(*)')->getOne();
    }


}
