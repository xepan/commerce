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


class page_tests_0200salesInvoice extends \xepan\base\Page_Tester {
	
	public $title='Sales Invoice Importer';

	public $proper_responses=[
        'test_testEmptyRows'=>['master'=>2766,'detail'=>3858],
		'test_importSalesInvoices'=>['master'=>(2766+2994),'deyail'=>(3858+4452)]
	];

	function init(){
        set_time_limit(0);
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_testEmptyRows(){
        return [
            'master'=>$this->api->db->dsql()->table('qsp_master')->del('fields')->field('count(*)')->getOne(),
            'detail'=>$this->api->db->dsql()->table('qsp_detail')->del('fields')->field('count(*)')->getOne()
        ];
    }

    private function getNewStatus($status){
        // ['Draft','Submitted','Redesign','Due','Paid','Canceled']
        // ['draft','submitted','approved','canceled','completed','processed']
        $mapping =[
        'draft'=>'Draft',
        'submitted'=>'Submitted',
        'approved'=>'Due',
        'completed'=>'Paid',
        'canceled'=>'Canceled',
        ];

        return $mapping[$status];
    }

    function prepare_importSalesInvoices(){
        $old_m = $this->pdb->dsql()->table('xshop_invoices')
                    ->where('type','salesInvoice')
                    ->get();

        $init_obj = $this->add('xepan\commerce\page_tests_init');
        $customer_mapping = $init_obj->getMapping('customer');
        $payg_mapping = $init_obj->getMapping('paymentgateway');
        $item_mapping = $init_obj->getMapping('item');
        $tax_mapping = $init_obj->getMapping('tax');
        $tnc_mapping = $init_obj->getMapping('tnc');
        $salesorder_mapping = $init_obj->getMapping('salesorder');

        $new_m = $this->add('xepan\commerce\Model_SalesInvoice');
        $new_d_m = $this->add('xepan\commerce\Model_QSP_Detail');
        $new_d_m->removeHook('afterInsert');

        $file_data=[];
        foreach ($old_m as $om) {
            $new_m['contact_id'] = $customer_mapping[$om['member_id']]['new_id']?:100000;
            $new_m['document_no'] = $om['name'];
            $new_m['billing_address'] = $customer_mapping[$om['member_id']]['address']?:'__';
            $new_m['billing_city'] = $customer_mapping[$om['member_id']]['city']?:'__';
            $new_m['billing_state'] = $customer_mapping[$om['member_id']]['state']?:'__';
            $new_m['billing_country'] = $customer_mapping[$om['member_id']]['country']?:'__';
            $new_m['billing_pincode'] = $customer_mapping[$om['member_id']]['pincode']?:'__';
            $new_m['shipping_address'] = $customer_mapping[$om['member_id']]['address']?:'__';
            $new_m['shipping_city'] = $customer_mapping[$om['member_id']]['city']?:'__';
            $new_m['shipping_state'] = $customer_mapping[$om['member_id']]['state']?:'__';
            $new_m['shipping_country'] = $customer_mapping[$om['member_id']]['country']?:'__';
            $new_m['shipping_pincode'] = $customer_mapping[$om['member_id']]['pincode']?:'__';
            $new_m['currency_id'] = $this->app->epan->default_currency->id;
            $new_m['discount_amount'] = $om['discount'];
            $new_m['search_string'] = $om['search_string'];
            $new_m['narration'] = '';
            $new_m['from'] = $om['order_from'];
            $new_m['priority_id'] = '';
            $new_m['due_date'] = date('Y-m-d',strtotime('+1 month',strtotime($om['created_at'])));
            $new_m['exchange_rate'] = '1';
            $new_m['tnc_id'] = $tnc_mapping[$om['termsandcondition_id']]['new_id']?:0;
            $new_m['tnc_text'] = $tnc_mapping[$om['termsandcondition_id']]['content'];
            $new_m['paymentgateway_id'] = $payg_mapping[$om['paymentgateway_id']]['new_id'];
            $new_m['status'] = $this->getNewStatus($om['status']);
            $new_m['created_at'] = $om['created_at'];
            $new_m['updated_at'] = $om['updated_at'];
            $new_m['related_qsp_master_id'] = $salesorder_mapping[$om['sales_order_id']]['new_id'];
            $new_m->save();

            // Order Items
            $old_m_2 = $this->pdb->dsql()->table('xshop_invoice_item')
                            ->where('invoice_id',$om['id'])
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

        file_put_contents(__DIR__.'/salesinvoice_mapping.json', json_encode($file_data));
    }

    function test_importSalesInvoices(){
        return [
            'master'=>$this->api->db->dsql()->table('qsp_master')->del('fields')->field('count(*)')->getOne(),
            'detail'=>$this->api->db->dsql()->table('qsp_detail')->del('fields')->field('count(*)')->getOne()
        ];
    }

}
