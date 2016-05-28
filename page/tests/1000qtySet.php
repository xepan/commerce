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

class page_tests_1000qtySet extends \xepan\base\Page_Tester {
	
	public $title='Qty Set Importer';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_QtySets'=>['count'=>-1]
        
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
    	$result['count'] = $this->app->db->dsql()->table('quantity_set')->del('fields')->field('count(*)')->getOne();
    	return $result;
    }

    function prepare_Import_QtySets(){

        $this->proper_responses['test_Import_QtySets']['count'] = $this->pdb->dsql()->table('xshop_item_quantity_sets')->join('xshop_items','item_id')->where('xshop_items.application_id','<>',null)->del('fields')->field('count(*)')->getOne();

        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');
        
        $new_qty_set = $this->add('xepan\commerce\Model_Item_Quantity_Set');

        $old_qty_sets = $this->pdb->dsql()->table('xshop_item_quantity_sets')
                            ->get()
                            ;
        $file_data = [];
        foreach ($old_qty_sets as $old_qty_set) {
            if(!$item_mapping[$old_qty_set['item_id']]['new_id'])
                continue;
            
            $new_qty_set
            ->set('item_id',$item_mapping[$old_qty_set['item_id']]['new_id'])
            ->set('name',$old_qty_set['name'])
            ->set('qty',$old_qty_set['qty'])
            ->set('old_price',$old_qty_set['old_price'])
            ->set('price',$old_qty_set['price'])
            ->set('is_default',$old_qty_set['is_default'])
            // ->set('shipping_charge',$old_qty_set['shipping_charge'])
            ->save();

            $file_data[$old_qty_set['id']] = ['new_id'=>$new_qty_set->id];
            $new_qty_set->unload();
        }
        
        file_put_contents(__DIR__.'/qtyset_mapping.json', json_encode($file_data));
    }

    function test_Import_QtySets(){
        $set_count = $this->app->db->dsql()->table('quantity_set')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$set_count];
    }

}
