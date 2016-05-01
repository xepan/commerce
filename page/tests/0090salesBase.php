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


class page_tests_0090salesBase extends \xepan\base\Page_Tester {
	
	public $title='Sales base Importer ie Tax etc';

	public $proper_responses=[
		'test_importTaxes'=>'7',
        'test_itemTaxAssosImport'=>436,
        'test_paymentGatewayImport'=>29
	];

	function init(){
        set_time_limit(0);
        $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function prepare_importTaxes(){
        $old_m = $this->pdb->dsql()->table('xshop_taxs')
                    ->get();
        $new_m = $this->add('xepan\commerce\Model_Taxation');
        $file_data=[];
        foreach ($old_m as $om) {
            $new_m['name'] = $om['name'];
            $new_m['percentage'] = $om['value'];
            $new_m->save();

            $file_data[$om['id']] = ['new_id'=>$new_m->id,'tax_percentage'=>$om['value']];
            $new_m->unload();
        }

        file_put_contents(__DIR__.'/tax_mapping.json', json_encode($file_data));
    }

    function test_importTaxes(){
        return $this->add('xepan\commerce\Model_Taxation')->count()->getOne();
    }

    function prepare_itemTaxAssosImport(){
        $old_m = $this->pdb->dsql()->table('xshop_itemtaxasso')
                    ->get();

        $item_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('item');
        $tax_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('tax');

        $new_m = $this->add('xepan\commerce\Model_Item_Taxation_Association');
        foreach ($old_m as $om) {
            $new_m['item_id'] = $item_mapping[$om['item_id']]['new_id'];
            $new_m['taxation_id'] = $tax_mapping[$om['tax_id']]['new_id'];
            $new_m->save();

            $new_m->unload();
        }
    }


    function test_itemTaxAssosImport(){
        return $this->add('xepan\commerce\Model_Item_Taxation_Association')->count()->getOne();
    }


    function prepare_paymentGatewayInport(){
        $old_m = $this->pdb->dsql()->table('xshop_payment_gateways')
                    ->get();
        $new_m = $this->add('xepan\commerce\Model_PaymentGateway');
        $file_data=[];
        foreach ($old_m as $om) {
            $new_m['name'] = $om['name'];
            $new_m['default_parameters'] = $om['default_parameters'];
            $new_m['parameters'] = $om['parameters'];
            $new_m['processing'] = $om['processing'];
            $new_m['gateway_image_id'] = $om['gateway_image_id'];
            $new_m->save();

            $file_data[$om['id']] = ['new_id'=>$new_m->id];
            $new_m->unload();
        }

        file_put_contents(__DIR__.'/paymentgateway_mapping.json', json_encode($file_data));
    }

    function test_paymentGatewayImport(){
    	return $this->add('xepan\commerce\Model_PaymentGateway')->count()->getOne();
    }

}

