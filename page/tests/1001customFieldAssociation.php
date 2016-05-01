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
        $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['count'] = $this->app->db->dsql()->table('quantity_set')->del('fields')->field('count(*)')->getOne();
    	return $result;
    }

    function prepare_Import_QtySets(){

        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');

        $this->proper_responses['test_Import_QtySets']['count'] = $this->pdb->dsql()->table('xshop_item_quantity_sets')->del('fields')->field('count(*)')->getOne();
        
        $new_qty_set = $this->add('xepan\commerce\Model_Item_Quantity_Set');

        $old_qty_sets = $this->pdb->dsql()->table('xshop_item_quantity_sets')
                            ->get()
                            ;
        $file_data = [];
        foreach ($old_qty_sets as $old_qty_set) {
            $new_qty_set
            ->set('item_id',$item_mapping[$old_qty_set['item_id']]['new_id'])
            ->set('name',$old_qty_set['name'])
            ->set('qty',$old_qty_set['qty'])
            ->set('old_price',$old_qty_set['old_price'])
            ->set('price',$old_qty_set['price'])
            ->set('is_default',$old_qty_set['is_default'])
            ->set('shipping_charge',$old_qty_set['shipping_charge'])
            ->save();

            $file_data[$old_qty_set['id']] = ['new_id'=>$new_qty_set->id];
            $new_qty_set->unload();
        }
        
        file_put_contents(__DIR__.'/item_qty_set_mapping.json', json_encode($file_data));
    }

    function test_Import_QtySets(){
        $set_count = $this->app->db->dsql()->table('quantity_set')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$set_count];
    }

}
