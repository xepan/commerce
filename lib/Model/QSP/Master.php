<?php

namespace xepan\commerce;

class Model_QSP_Master extends \xepan\hr\Model_Document{

	public $number_field = 'document_no';

	function init(){
		parent::init();

		$qsp_master_j = $this->join('qsp_master.document_id');
		$qsp_master_j->hasOne('xepan/base/Contact','contact_id')->sortable(true);
		$qsp_master_j->hasOne('xepan/accounts/Currency','currency_id');
		$qsp_master_j->hasOne('xepan/accounts/Group','nominal_id');
		$qsp_master_j->hasOne('xepan/commerce/TNC','tnc_id')->defaultValue(null);
		$qsp_master_j->hasOne('xepan/commerce/PaymentGateway','paymentgateway_id')->defaultValue(null);

		//Related QSP Master
		$qsp_master_j->hasOne('xepan\commerce\RelatedQspMaster','related_qsp_master_id')->defaultValue('Null');
		
		$qsp_master_j->addField('document_no')->sortable(true);
		$this->addExpression('document_no_number')->set('CAST(document_no AS decimal)')->sortable(true);

		$qsp_master_j->addField('billing_address');
		$qsp_master_j->addField('billing_city');
		$qsp_master_j->addField('billing_state');
		$qsp_master_j->addField('billing_country');
		$qsp_master_j->addField('billing_pincode');
		
		$qsp_master_j->addField('shipping_address');
		$qsp_master_j->addField('shipping_city');
		$qsp_master_j->addField('shipping_state');
		$qsp_master_j->addField('shipping_country');
		$qsp_master_j->addField('shipping_pincode');
		
		$qsp_master_j->addField('from')->hint('Offline,Online etc')->defaultValue('Offline');
		
		//Total Amount: calculate sum all item field amount_excluding_tax
		$this->addExpression('total_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $details->sum('amount_excluding_tax');
		})->type('money');

		//Total Item amount Sum
		$this->addExpression('gross_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $q->expr("round([0],2)", [$details->sum('total_amount')]);
		})->type('money');
		
		$qsp_master_j->addField('discount_amount')->defaultValue(0);

		$this->addExpression('net_amount')->set(function($m,$q){
			return $q->expr('round( ([0] - [1]), 2 )',[$m->getElement('gross_amount'), $m->getElement('discount_amount')]);
		})->type('money');

		$qsp_master_j->addField('due_date')->type('datetime')->defaultValue(null);
		$qsp_master_j->addField('priority_id');
		$qsp_master_j->addField('narration')->type('text');

		$qsp_master_j->addField('exchange_rate')->defaultValue(1);		
		$qsp_master_j->addField('tnc_text')->type('text')->defaultValue('');		
		$this->addExpression('net_amount_self_currency')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('net_amount'), $m->getElement('exchange_rate')]);
		})->type('money');


		$this->addExpression('round_amount')->set(function($m,$q){
			return "'0'";
		})->type('money');

		$qsp_master_j->addField('transaction_reference');
		$qsp_master_j->addField('transaction_response_data');

		$this->getElement('status')->defaultValue('Draft');

		$qsp_master_j->hasMany('xepan\commerce\QSP_Detail','qsp_master_id',null,'Details');
		$qsp_master_j->hasMany('xepan\commerce\QSP_Master','related_qsp_master_id',null,'RelatedQSP');
		
		//Currency Icon 
		$this->addExpression('invoice_currency_icon',function($m,$q){
			return $m->refSQL('currency_id')->fieldQuery('icon');
		});

		$this->addHook('beforeDelete',[$this,'deleteDetails']);

		$this->addHook('beforeSave',[$this,'updateTnCTextifChanged']);
		$this->addHook('beforeSave',[$this,'updateSearchString']);

		$this->is([
			'contact_id|required',
			'billing_address|required',
			'billing_city|required',
			'billing_state|required',
			'billing_country|required',
			'billing_pincode|required',
			'document_no|required|number|unique_in_epan_for_type',
			'due_date|date_after|created_at',
			'currency_id|required',
			'exchange_rate|number|gt|0'
			]);
	}

	function newNumber(){
		return $this->_dsql()->del('fields')->field('max(CAST('.$this->number_field.' AS decimal))')->where('type',$this['type'])->getOne() + 1 ;
	}

	function updateTnCTextifChanged(){
		if($this->isDirty('tnc_id')){
			$this['tnc_text'] = $this->ref('tnc_id')->get('content');
		}
	}

	function deleteDetails(){

		$deatils = $this->ref('Details');
		
		foreach ($deatils as $deatil) {
			$deatil->delete();
		}
	}

	function generatePDF($action ='return'){

		if(!in_array($action, ['return','dump']))
			throw $this->exception('Please provide action as result or dump');

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('xEpan ERP');
		$pdf->SetTitle($this['type']. ' '. $this['document_no']);
		$pdf->SetSubject($this['type']. ' '. $this['document_no']);
		$pdf->SetKeywords($this['type']. ' '. $this['document_no']);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		// add a page
		$pdf->AddPage();

		if($org = $this->ref('contact_id')->get('organization')) {
			$this['contact'] = $org;
			$this['contact_id'] = '';
		}


		$view = $this->app->add('xepan\commerce\View_QSP',['qsp_model'=>$this, 'master_template'=>'view/print-templates/master-'.strtolower($this['type']),'detail_template'=>'view/print-templates/print-detail','action'=>'pdf']);
		// $view = $this->owner->add('xepan\commerce\View_QSP',['qsp_model'=>$this]);
		
		$html = $view->getHTML();

		// output the HTML content
		$pdf->writeHTML($html, false, false, true, false, '');
		// set default form properties
		$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
		// reset pointer to the last page
		$pdf->lastPage();
		//Close and output PDF document
		switch ($action) {
			case 'return':
				return $pdf->Output(null, 'S');
				break;
			case 'dump':
				return $pdf->Output(null, 'I');
				exit;
			break;
		}
	}

	function print_QSP(){
		$this->api->redirect($this->api->url('xepan_commerce_printqsp',['document_id'=>$this->id]));
	}

	function send_QSP($f,$original_obj){

		$form=$f->add('Form',null,null,['form/empty']);
		$form->setLayout('view/form/send-qsp');
		$from_email = $form->addField('dropdown','from_email')->validate('required')->setEmptyText('Please Select from Email');
		$from_email->setModel('xepan\hr\Post_Email_MyEmails');
		$form->addField('line','to')->set(str_replace("<br/>", ",",$this->ref('contact_id')->get('emails_str')));
		$form->addField('line','cc');
		$form->addField('line','bcc');
		$form->addField('line','subject')->validate('required');
		$form->addField('xepan\base\RichText','body');
		$email_setting=$this->add('xepan\communication\Model_Communication_EmailSetting');
		if($_GET['from_email'])
			$email_setting->tryLoad($_GET['from_email']);
		$view=$form->layout->add('View',null,'signature')->setHTML($email_setting['signature']);
		$from_email->js('change',$view->js()->reload(['from_email'=>$from_email->js()->val()]));

		foreach ($original_obj->ref('Attachments') as $attach) {
			$form->addField('CheckBox','attachdoc'.$attach->id,"File : ".$attach['file']);
		}

		$form->addSubmit('Send')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$email_setting = $this->add('xepan\communication\Model_Communication_EmailSetting');
				$email_setting->tryLoad($form['from_email']?:-1);

			$qsp = $f->add('xepan\communication\Model_Communication_Abstract_Email');					
			$qsp->getElement('status')->defaultValue('Draft');



			$qsp->setfrom($email_setting['from_email'],$email_setting['from_name']);
			$qsp->addCondition('communication_type','Email');
			
			$to_emails=explode(',', trim($form['to']));
			foreach ($to_emails as $to_mail) {
				$qsp->addTo($to_mail);
			}
			if($form['cc']){
				$cc_emails=explode(',', trim($form['cc']));
				foreach ($cc_emails as $cc_mail) {
						$qsp->addCc($cc_mail);
				}
			}
			if($form['bcc']){
				$bcc_emails=explode(',', trim($form['bcc']));
				foreach ($bcc_emails as $bcc_mail) {
						$qsp->addBcc($bcc_mail);
				}
			}
			$qsp->setSubject($form['subject']);
			$qsp->setBody($form['body']);
			$qsp->save();

			// Attach Invoice
			$file =	$this->add('filestore/Model_File',array('policy_add_new_type'=>true,'import_mode'=>'string','import_source'=>$original_obj->generatePDF('return')));
			$file['filestore_volume_id'] = $file->getAvailableVolumeID();
			$file['original_filename'] =  strtolower($original_obj['type']).'_'.$this['document_no_number'].'_'.$this->id.'.pdf';
			$file->save();
			$qsp->addAttachment($file->id);
			
			// Attach Other attachments
			$other_attachments = $this->add('xepan\base\Model_Document_Attachment');
			$other_attachments->addCondition('document_id',$original_obj->id);

			foreach ($other_attachments as $attach) {
				if($form['attachdoc'.$attach->id]){
					$file =	$this->add('filestore/Model_File',array('policy_add_new_type'=>true,'import_mode'=>'copy','import_source'=>$_SERVER["DOCUMENT_ROOT"].$attach['file']));
					$file['filestore_volume_id'] = $file->getAvailableVolumeID();
					$file['original_filename'] = $attach['original_filename'];
					$file->save();
					$qsp->addAttachment($file->id);
				}
			}

			$qsp->findContact('to');

			$qsp->send($email_setting);
		}

	}
    //Return qspItem sModel
	function items(){
		return $this->ref('Details');
	}

	function details(){
		return $this->ref('Details');
	}

	function customer(){
		return $this->ref('contact_id');
	}


	function currency(){
		return $this->add('xepan\accounts\Model_Currency')->load($this['currency_id']);		
	}


	//return tax id and it's total amount
	function getCommnTaxAndAmount(){
		
		if(!$this->loaded())
			throw new \Exception("model must loaded", 1);
		
		$comman_tax_array = [];
		foreach ($this->details() as $invoice_item) {
			if(!$invoice_item['taxation_id'])
				continue;

			$comman_tax_array[$invoice_item['taxation']] += $invoice_item['tax_amount'];
		}

		return $comman_tax_array;
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['created_at'];
		$search_string .=" ". $this['updated_at'];
		$search_string .=" ". $this['document_no'];
		$search_string .=" ". $this['from'];
		$search_string .=" ". $this['billing_address'];
		$search_string .=" ". $this['billing_city'];
		$search_string .=" ". $this['billing_state'];
		$search_string .=" ". $this['billing_state'];
		$search_string .=" ". $this['billing_pincode'];
		$search_string .=" ". $this['shipping_address'];
		$search_string .=" ". $this['shipping_city'];
		$search_string .=" ". $this['shipping_state'];
		$search_string .=" ". $this['shipping_state'];
		$search_string .=" ". $this['shipping_pincode'];
		$search_string .=" ". $this['total_amount'];
		$search_string .=" ". $this['net_amount'];
		$search_string .=" ". $this['gross_amount'];
		$search_string .=" ". $this['discount_amount'];
		$search_string .=" ". $this['type'];
		
		if($this->loaded()){
			$qsp_detail = $this->ref('Details');
			foreach ($qsp_detail as $all_qsp_detail) {
				$search_string .=" ". $all_qsp_detail['price'];
				$search_string .=" ". $all_qsp_detail['quantity'];
				$search_string .=" ". $all_qsp_detail['amount_excluding_tax'];
				$search_string .=" ". $all_qsp_detail['tax_percentage'];
				$search_string .=" ". $all_qsp_detail['shipping_charge'];
				$search_string .=" ". $all_qsp_detail['narration'];
				$search_string .=" ". $all_qsp_detail['extra_info'];
			}			
		}
		$this['search_string'] = $search_string;
	}
} 