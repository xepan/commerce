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

class page_tests_0048ItemImages extends \xepan\base\Page_Tester {
	
	public $title='Item Images';
	
	public $proper_responses=[
    
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_Images'=>['count'=>-1],
        'test_Import_ImageExtensionCorrection' =>['count'=>-1]
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
    	$result['count'] = $this->app->db->dsql()->table('item_image')->del('fields')->field('count(*)')->getOne();
    	return $result;
    }

    function prepare_Import_Images(){

        // did you copy "xshop_item_images" to your new databse from old one !!!

        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');

        $this->proper_responses['test_Import_Images']['count'] = $this->pdb->dsql()->table('xshop_item_images')->del('fields')->field('count(*)')->getOne();
        
        $item_image_sql = "CASE item_id "; 
        foreach ($item_mapping as $old_id => $values) {
            $item_image_sql .= " WHEN $old_id THEN ". $values['new_id'];
        }
        $item_image_sql.=" END";

        $sql="
            INSERT INTO item_image (item_id,customfield_value_id,file_id,alt_text,title) SELECT $item_image_sql ,customefieldvalue_id, item_image_id, alt_text, title FROM xshop_item_images
        ";


        $this->app->db->dsql()->expr($sql)->execute();
    }

    function test_Import_Images(){
        $set_count = $this->app->db->dsql()->table('item_image')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$set_count];
    }

    function test_Import_ImageExtensionCorrection(){

        $file_model = $this->add('xepan\filestore\Model_File');
        $file_model->addCondition('filename',"NOT LIKE","%.%");
        $total_count = $file_model->count()->getOne();


        $directory = getcwd().'/../websites/www/upload/';
        $count = 1;
        $file_not_exit = 0;
        foreach ($file_model as $file) {
            $file_path = $directory.$file['filename'];
            if(file_exists($file_path)){
                rename($file_path, $file_path.".png");
                $rename_model = $this->add('xepan\filestore\Model_File')->load($file->id);
                
                $rename_model['original_filename'] = $rename_model['original_filename'].".png";
                $rename_model['filename'] = $rename_model['filename'].".png";
                $rename_model->save();
                $count ++;
            }else
                $file_not_exit ++;
        }

        $this->proper_responses['test_Import_ImageExtensionCorrection']['count'] = $total_count - $file_not_exit;

        return ['count'=>$count];
    }

}
