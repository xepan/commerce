<?php
 namespace xepan\commerce;
class View_QSP extends \View{

	public $qsp_model;
	public $document_label="Document";

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		// $this->add('View_Info')->set('QSP=');

		$document = $this->add('xepan\base\View_Document',
							['action'=>$action],
							null,
							['view/qsp/master']
						);
		$document->setIdField('document_id');
		$document->setModel($this->qsp_model,
							[
								'contact_id',
								'document_no',
								'billing_landmark',
								'billing_address',
								'billing_city',
								'billing_state',
								'billing_country',
								'billing_pincode',
								'billing_tel',
								'billing_email',
								'shipping_landmark',
								'shipping_address',
								'shipping_city',
								'shipping_state',
								'shipping_country',
								'shipping_pincode',
								'shipping_tel',
								'shipping_email',

								'gross_amount',
								'discount_amount',
								'net_amount',
								'delivery_date',
								'priority_id',
								'narration',
								'exchange_rate',
								'payment_gateway_id',
								'transaction_reference',
								'transaction_response_data',
							],
							[
								'contact_id',
								'document_no',
								'billing_landmark',
								'billing_address',
								'billing_city',
								'billing_state',
								'billing_country',
								'billing_pincode',
								'billing_tel',
								'billing_email',
								'shipping_landmark',
								'shipping_address',
								'shipping_city',
								'shipping_state',
								'shipping_country',
								'shipping_pincode',
								'shipping_tel',
								'shipping_email',

								'gross_amount',
								'discount_amount',
								'net_amount',
								'delivery_date',
								'priority_id',
								'narration',
								'exchange_rate',
								'payment_gateway_id',
								'transaction_reference',
								'transaction_response_data',
							]			
						);

		// $document->form->getElement('discount_amount')->js('change')->_load('xepan-QSIP')->univ()->calculateQSIP();

		if($this->qsp_model->loaded()){
			$qsp_details = $document->addMany('Items',
										null,
										'item_info',
										['view/qsp/details']
									);
			$qsp_details->setModel($this->qsp_model->ref('Details'));
		}

	}

}