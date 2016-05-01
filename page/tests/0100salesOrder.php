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


class page_tests_0100salesOrder extends \xepan\base\Page_Tester {
	
	public $title='Sales Order Importer';

	public $proper_responses=[
		'test_importSalesOrder'=>'43'
	];

	function init(){
        set_time_limit(0);
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    private function getNewStatus($status){
        $mapping =[
        'draft'=>'Draft',
        'submitted'=>'Submitted',
        'approved'=>'Approved',
        'processing'=>'InProgress',
        'processed'=>'InProgress',
        'dispatched'=>'Dispatched',
        'complete'=>'Completed',
        'cancel'=>'Canceled',
        'return'=>'Dispatched',
        'redesign'=>'Redesign',
        'onlineunpaid'=>'OnlineUnpaid'];

        return $mapping[$status];
    }

    function prepare_importSalesOrder(){
        $old_m = $this->pdb->dsql()->table('xshop_orders')
                    ->get();

        $init_obj = $this->add('xepan\commerce\page_tests_init');
        $customer_mapping = $init_obj->getMapping('customer');
        $payg_mapping = $init_obj->getMapping('paymentgateway');
        $item_mapping = $init_obj->getMapping('item');
        $tax_mapping = $init_obj->getMapping('tax');

        $new_m = $this->add('xepan\commerce\Model_SalesOrder');
        $new_d_m = $this->add('xepan\commerce\Model_QSP_Detail');
        $file_data=[];
        foreach ($old_m as $om) {
            $new_m['contact_id'] = $customer_mapping[$om['member_id']]['new_id'];
            $new_m['document_no'] = $om['name'];
            $new_m['billing_address'] = $customer_mapping[$om['member_id']]['address'];
            $new_m['billing_city'] = $customer_mapping[$om['member_id']]['city'];
            $new_m['billing_state'] = $customer_mapping[$om['member_id']]['state'];
            $new_m['billing_country'] = $customer_mapping[$om['member_id']]['country'];
            $new_m['billing_pincode'] = $customer_mapping[$om['member_id']]['pincode'];
            $new_m['shipping_address'] = $customer_mapping[$om['member_id']]['address'];
            $new_m['shipping_city'] = $customer_mapping[$om['member_id']]['city'];
            $new_m['shipping_state'] = $customer_mapping[$om['member_id']]['state'];
            $new_m['shipping_country'] = $customer_mapping[$om['member_id']]['country'];
            $new_m['shipping_pincode'] = $customer_mapping[$om['member_id']]['pincode'];
            $new_m['currency_id'] = $this->app->epan->default_currency->id;
            $new_m['discount_amount'] = 0;
            $new_m['search_string'] = $om['search_string'];
            $new_m['narration'] = '';
            $new_m['from'] = $om['order_from'];
            $new_m['priority_id'] = $om[''];
            $new_m['due_date'] = $om[''];
            $new_m['exchange_rate'] = $om[''];
            $new_m['tnc_id'] = $om[''];
            $new_m['tnc_text'] = $om[''];
            $new_m['paymentgateway_id'] = $payg_mapping[$om['paymentgateway_id']]['new_id'];
            $new_m['status'] = $this->getNewStatus($om['status']);
            $new_m['created_at'] = $om['created_at'];
            $new_m['updated_at'] = $om['updated_at'];
            $new_m->save();

            // Order Items
            $old_m_2 = $this->pdb->dsql()->table('xshop_orderdetails')
                            ->where('order_id',$om['id'])
                            ->get();
            foreach ($old_m_2 as $od) {
                $new_d_m['qsp_master_id']=$new_m->id;
                $new_d_m['item_id'] = $item_mapping[$od['item_id']]['new_id'];
                $new_d_m['taxation_id'] = $tax_mapping[$od['tax_id']]['new_id'];
                $new_d_m['price'] = $od['rate'];
                $new_d_m['quantity'] = $od['qty'];
                $new_d_m['tax_percentage'] = $tax_mapping[$od['tax_id']]['tax_mapping'];
                $new_d_m['shipping_charge'] = $od['shipping_charge'];
                $new_d_m['narration'] = $od['narration'];
                $new_d_m['extra_info'] = $init_obj->parseCustomFieldsJSON($od['custom_fields']);
                $new_d_m->saveAndUnload();
            }

            $file_data[$om['id']] = ['new_id'=>$new_m->id];
            $new_m->unload();
        }

        file_put_contents(__DIR__.'/tax_mapping.json', json_encode($file_data));
    }

    function test_importSalesOrder(){
    	return "TODO";
    }

}
