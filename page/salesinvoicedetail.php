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

 class page_salesinvoicedetail extends \xepan\base\Page {
	public $title='Sales Invoice Detail';

	public $breadcrumb=['Home'=>'index','Invoices'=>'xepan_commerce_salesinvoice','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$sale_inv_dtl = $this->add('xepan\commerce\Model_SalesInvoice')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$view_field = 	[
							'contact_id',
							'document_no',
							'type',

							'billing_landmark',
							'billing_address',
							'billing_city',
							'billing_state',
							'billing_country',
							'billing_pincode',
							'billing_contact',
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
							'tnc_text',
							'narration',
							'exchange_rate',
							'currency',
							'nominal_id'
							//'priority_id',
							// 'payment_gateway_id',
							// 'transaction_reference',
							// 'transaction_response_data',
						];
		$form_field	=	[
							'contact_id',
							'document_no',
							'created_at',
							'due_date',
							
							'billing_landmark',
							'billing_address',
							'billing_city',
							'billing_state',
							'billing_country',
							'billing_pincode',
							'billing_contact',
							'billing_email',
							'shipping_landmark',
							'shipping_address',
							'shipping_city',
							'shipping_state',
							'shipping_country',
							'shipping_pincode',
							'shipping_tel',
							'shipping_email',

							'discount_amount',
							'narration',
							'exchange_rate',
							'currency_id',
							// 'priority_id',
							// 'payment_gateway_id',
							// 'transaction_reference',
							// 'transaction_response_data',
							'tnc_id',
							'nominal_id'
						];
		
		$dv = $this->add('xepan\commerce\View_QSPAddressJS')->set('');

		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$sale_inv_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);
		
		if($action !='view'){
			$contact_field = $view->document->form->getElement('contact_id');
			$contact_field->model->addCondition('type','Customer');

			$contact_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));
		}

		if($action=='edit' && !$view->document_item->isEditing()){
			$view->app->addHook('post-submit',function($f)use($sale_inv_dtl){				
				$sale_inv_dtl->updateTransaction();
			});
			$lister = $view->document->add('Lister',null,'common_vat',['view/qsp/master','common_vat'])->setSource($sale_inv_dtl->getCommnTaxAndAmount());
			$view->document->effective_template->setHTML('common_vat',$lister->getHtml());
			$m=$view->document_item->model;
			
			$m->addHook('afterSave',function($m){
					$m->saleInvoice()->updateTransaction();
				});
		}

		// $b = $view->add('Button')->set('Print');
		// $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// // set document information
		// $pdf->SetCreator(PDF_CREATOR);
		// $pdf->SetAuthor('Nicola Asuni');
		// $pdf->SetTitle('TCPDF Example 006');
		// $pdf->SetSubject('TCPDF Tutorial');
		// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// // set default monospaced font
		// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


		// // set font
		// $pdf->SetFont('dejavusans', '', 10);

		// // add a page
		// $pdf->AddPage();

		// // create some HTML content
		// $html = '<h1>HTML Example</h1>'
		// ;

		// // output the HTML content
		// $pdf->writeHTML($html, true, false, true, false, '');


		// // output some RTL HTML content
		// $html = '<div style="text-align:center">The words &#8220;<span dir="rtl">&#1502;&#1494;&#1500; [mazel] &#1496;&#1493;&#1489; [tov]</span>&#8221; mean &#8220;Congratulations!&#8221;</div>';
		// $pdf->writeHTML($html, true, false, true, false, '');

		// // test some inline CSS
		// $html = $view->getHTML();

		// $pdf->writeHTML($html, true, false, true, false, 'I');

		// // reset pointer to the last page
		// $pdf->lastPage();

		// //Close and output PDF document
		// $pdf->Output('example_006.pdf', 'I');
		
	}
}
