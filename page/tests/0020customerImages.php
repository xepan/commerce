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
        '--'=>'--'
	];

	function init(){
        set_time_limit(0);
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

    function prepare_libraryImageCategory(){
    	$old_m = $this->pdb->dsql()->table('xshop_image_library_category')
                        ->get();
        $this->proper_responses['test_libraryImageCategory'] = count($old_m);

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
        
        $this->proper_responses['test_designerImagesImport'] = count($old_m);
        
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
