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
        'test_paymentGatewayImport'=>29,
        'test_termsAndConditionsImport'=>2
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


    function prepare_paymentGatewayImport(){
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


    function prepare_termsAndConditionsImport(){
        $old_m = $this->pdb->dsql()->table('xshop_termsandcondition')
                    ->get();
        $new_m = $this->add('xepan\commerce\Model_TNC');
        $file_data=[];
        foreach ($old_m as $om) {
            $new_m['name'] = $om['name'];
            $new_m['content'] = $om['content']?:'-';
            $new_m->save();

            $file_data[$om['id']] = ['new_id'=>$new_m->id,'content'=>$om['content']];
            $new_m->unload();
        }

        file_put_contents(__DIR__.'/tnc_mapping.json', json_encode($file_data));
    }

    function test_termsAndConditionsImport(){
    	return $this->add('xepan\commerce\Model_TNC')->count()->getOne();
    }

}

