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

class page_tests_1005finetune extends \xepan\base\Page_Tester {
	
	public $title='Fine Tune';
	
	public $proper_responses=[
       
        'test_replace_subpage_to_page'=>1,
        'test_relative_url'=>1,
        'test_image_url'=>1
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

    function prepare_replace_subpage_to_page(){
        $sql="
            UPDATE category SET description=REPLACE(description,'subpage','page')
        ";

        $this->app->db->dsql()->expr($sql)->execute();
    }

    function test_replace_subpage_to_page(){
        return 1;
    }

    function prepare_relative_url(){
        $sql="
            UPDATE category set description = REPLACE(description,'http://printonclick.in','')
        ";
        $this->app->db->dsql()->expr($sql)->execute();

        $sql='
            UPDATE category set description = REPLACE(description,\'="/?page\',\'="?page\')
        ';
        $this->app->db->dsql()->expr($sql)->execute();

        $sql='
            UPDATE category set description = REPLACE(description,\'="../?page\',\'="?page\')
        ';
        $this->app->db->dsql()->expr($sql)->execute();
    }

    function test_relative_url(){
        return 1;
    }

    function prepare_image_url(){
        $sql ="
            UPDATE category set description = REPLACE(description,'epans/web/','websites/www/www/image/')
        ";
        $this->app->db->dsql()->expr($sql)->execute();
    }

    function test_image_url(){
        return 1;
    }

}
