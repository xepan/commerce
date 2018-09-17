<?php

namespace xepan\commerce;

class Model_QSP_Master extends \xepan\hr\Model_Document{

	public $number_field = 'document_no';
	public $title_field = 'document_no';

	function init(){
		parent::init();

		$qsp_master_j = $this->join('qsp_master.document_id');
		$qsp_master_j->hasOne('xepan/base/Contact','contact_id')->display(array('form' => 'xepan\base\Basic'))->sortable(true);
		$qsp_master_j->hasOne('xepan/accounts/Currency','currency_id')->defaultValue(@$this->app->epan->default_currency->id);
		$qsp_master_j->hasOne('xepan/accounts/Ledger','nominal_id');
		$qsp_master_j->hasOne('xepan/commerce/TNC','tnc_id')->defaultValue(null);
		$qsp_master_j->hasOne('xepan/commerce/PaymentGateway','paymentgateway_id')->defaultValue(null);
		$qsp_master_j->hasOne('xepan\production\OutsourceParty','outsource_party_id');
		$qsp_master_j->hasOne('xepan\base\Country','billing_country_id')->display(array('form' => 'xepan\commerce\DropDown'));
		$qsp_master_j->hasOne('xepan\base\State','billing_state_id')->display(array('form' => 'xepan\commerce\DropDown'));
		$qsp_master_j->hasOne('xepan\base\Country','shipping_country_id')->display(array('form' => 'xepan\commerce\DropDown'));
		$qsp_master_j->hasOne('xepan\base\State','shipping_state_id')->display(array('form' => 'xepan\commerce\DropDown'));
		
		//Related QSP Master
		$qsp_master_j->hasOne('xepan\commerce\RelatedQspMaster','related_qsp_master_id')->defaultValue(0);
		
		$qsp_master_j->addField('document_no')->sortable(true);
		$this->addExpression('document_no_number')->set('CAST(document_no AS decimal)')->sortable(true);

		$qsp_master_j->addField('billing_name');
		$qsp_master_j->addField('billing_address');
		$qsp_master_j->addField('billing_city');
		$qsp_master_j->addField('billing_pincode');
		
		$qsp_master_j->addField('shipping_name');
		$qsp_master_j->addField('shipping_address');
		$qsp_master_j->addField('shipping_city');
		$qsp_master_j->addField('shipping_pincode');

		$qsp_master_j->addField('is_shipping_inclusive_tax')->type('boolean')->defaultValue(false);
		$qsp_master_j->addField('is_express_shipping')->type('boolean');
		
		$qsp_master_j->addField('from')->hint('Offline,Online etc')->defaultValue('Offline');
		
		//Total Amount: calculate sum all item field amount_excluding_tax
		$this->addExpression('total_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $details->sum('amount_excluding_tax');
		})->type('money');

		//Total Item amount Sum including tax
		$this->addExpression('gross_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $q->expr("round([0],2)", [$details->sum('total_amount')]);
		})->type('money');
			
		// shipping charge sum
		$this->addExpression('total_shipping')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $q->expr("round([0],2)", [$details->sum('shipping_charge')]);
		})->type('money');

		$qsp_master_j->addField('discount_amount')->defaultValue(0)->type('money');

		$this->addExpression('tax_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $q->expr("[0]", [$details->sum('tax_amount')]);
		})->type('money');

		$this->addExpression('net_amount')->set(function($m,$q){
			return $q->expr('round( ([0] - [1] - IFNULL([2],0)), 2 )',[$m->getElement('gross_amount'), $m->getElement('discount_amount'),$m->getElement('round_amount')]);
		})->type('money');

		$qsp_master_j->addField('due_date')->type('datetime')->defaultValue(null);
		$qsp_master_j->addField('priority_id');
		$qsp_master_j->addField('narration')->type('text');

		$qsp_master_j->addField('exchange_rate')->defaultValue(1);		
		$qsp_master_j->addField('tnc_text')->type('text')->defaultValue('');
		$qsp_master_j->addField('round_amount')->defaultValue('0.00');
		
		$qsp_master_j->addField('cancel_reason');
		$qsp_master_j->addField('cancel_narration')->type('text');

		$this->addExpression('net_amount_self_currency')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('net_amount'), $m->getElement('exchange_rate')]);
		})->type('money');

		$this->addExpression('qsp_sent_date')->set(function($m,$q){
			$activity = $m->add('xepan\base\Model_Activity')
						->addCondition('related_document_id',$this->getElement('id'))
						->addCondition('activity','like','% successfully sent to %')
						->setLimit(1)
						;
			return $q->expr('IFNULL([0],0)',[$activity->fieldQuery('created_at')]);
		});


		$qsp_master_j->addField('transaction_reference');
		$qsp_master_j->addField('transaction_response_data');

		$qsp_master_j->addField('serial')->system(true);

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
		$this->addHook('beforeSave',[$this,'updateRoundAmount']);

		$this->is([
			'contact_id|required',
			'billing_address|required',
			'billing_country_id|required',
			'billing_state_id|required',
			'billing_city|required',
			'billing_pincode|required',
			// 'due_date|date_after_without_time|created_at',
			// 'document_no|required|number|unique_in_epan_for_type',
			// 'tnc_id|required',
			'currency_id|required',
			'exchange_rate|number|gt|0'
			]);
	}

	function updateRoundAmount(){
		$round_standard_name = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'round_amount_standard'=>'DropDown'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$round_standard_name->tryLoadAny();
		$round_standard = $round_standard_name['round_amount_standard'];

		$gross_amount = $this['gross_amount'];
		if($this['discount_amount'])
			$gross_amount = $this['gross_amount'] - $this['discount_amount'];
		
		$rounded_gross_amount = $gross_amount;

		switch ($round_standard) {
			case 'Standard':
				$rounded_gross_amount = round($gross_amount);
				break;
			case 'Up':
				$rounded_gross_amount = ceil($gross_amount);
				break;
			case 'Down':
				$rounded_gross_amount = floor($gross_amount);
				break;
		}
		
		$this['round_amount'] = $gross_amount - $rounded_gross_amount;

		// echo "round amount = ".$this['round_amount']." = gross = ".$gross_amount." = rounded ".$rounded_gross_amount."<br/>";
	}


	function populateSerialNo(){

		if(!$this->loaded()){
			if($serial = $this->getSerialNo())
				$this['serial'] = $serial;
		}
	}

	function getSerialNo(){
		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		$serial = "";
		if($this['type'] == "SalesOrder"){
			$serial = $qsp_config['sale_order_serial'];
		}

		if($this['type'] == "SalesInvoice"){
			$serial = $qsp_config['sale_invoice_serial'];
		}

		if($this['type'] == "Quotation"){
			$serial = $qsp_config['quotation_serial'];
		}

		return $serial;
	}

	function checkQSPNumberExist($document_no,$serial=null){

		$qsp_master = $this->add('xepan\commerce\Model_QSP_Master');
		$qsp_master->addCondition('type',$this['type']);

		if($this->loaded())
			$qsp_master->addCondition('id','<>',$this->id);

		$qsp_master->addCondition('document_no',$document_no);
		$qsp_master->addCondition('document_no','<>','-');
		if($serial)
			$qsp_master->addCondition('serial',$serial);
		$qsp_master->tryLoadAny();
		return $qsp_master->loaded();
	}

	function newNumber($serial_number=null){
		
		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		$serial = "";
		if($this['type'] == "SalesOrder"){
			$serial = $qsp_config['sale_order_serial'];
		}

		if($this['type'] == "SalesInvoice"){
			$serial = $qsp_config['sale_invoice_serial'];
		}

		if($this['type'] == "Quotation"){
			$serial = $qsp_config['quotation_serial'];
		}

		if(trim($serial_number)){
			$serial = $serial_number;
		}

		$qsp_model = $this->add('xepan\commerce\Model_QSP_Master');
		if($serial){
			return $qsp_model->_dsql()->del('fields')->field('max(CAST('.$this->number_field.' AS decimal))')->where('type',$this['type'])->where('serial',$serial)->getOne() + 1 ;
		}else{
			return $qsp_model->_dsql()->del('fields')->field('max(CAST('.$this->number_field.' AS decimal))')->where('type',$this['type'])->getOne() + 1 ;
		}

	}

	function updateTnCTextifChanged(){
		// throw new \Exception($this['tnc_id'], 1);
		if($this['tnc_id']){
			$tnc_m = $this->add('xepan\commerce\Model_TNC');
			$tnc_m->load($this['tnc_id']);
			$this['tnc_text'] = '';
			$this['tnc_text'] = $tnc_m['content'];
		}
		if($this->loaded()){
			$details = $this->ref('Details');
			$item_array = [];
			foreach ($details as $detail_obj) {
				if (in_array($detail_obj['item_id'], $item_array))
					continue;

				if(!$detail_obj['item_id']) continue;

				$item_array [] = $detail_obj['item_id'];
				$item = $this->add('xepan\commerce\Model_item')->load($detail_obj['item_id']);
				$this['tnc_text'] .= $item['terms_and_conditions'];
			}
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
		//remove header or footer hr lines
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		// add a page
		$pdf->AddPage();

		if($org = $this->ref('contact_id')->get('organization')) {
			$this['contact'] = $org;
			$this['contact_id'] = '';
		}

		// getting layouts from config

		$layout_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>strtoupper($this['type']).'_LAYOUT',
					'application'=>'commerce'
			]);
		$layout_m->tryLoadAny();

		$info_config = $layout_m['master'];
		$info_layout = $this->add('GiTemplate');
		$info_layout->loadTemplateFromString($info_config);	
	

		$detail_config = $layout_m['detail'];
		$detail_layout = $this->add('GiTemplate');
		$detail_layout->loadTemplateFromString($detail_config);	

		$new = $this->add('xepan\commerce\Model_QSP_Master');
		$new->addHook('afterLoad',function($m){
			$m['round_amount'] = abs($m['round_amount']);
		});
		$new->load($this->id);
		$view = $this->app->add('xepan\commerce\View_QSP',['qsp_model'=>$new, 'master_template'=>$info_layout,'detail_template'=>$detail_layout,'action'=>'pdf']);
		// $view = $this->owner->add('xepan\commerce\View_QSP',['qsp_model'=>$this]);
		if($bar_code = $this->getBarCode()){
			$barcodeobj = new \TCPDFBarcode($bar_code, 'C128');
			// $barcode_html = $barcodeobj->getBarcodePNG(2, 30, 'black');
			$barcode_html = $barcodeobj->getBarcodePngData(1, 20, array(0,128,0));
			$info_layout->trySetHtml('dispatch_barcode','<img src="data:image/png;base64, '.base64_encode($barcode_html).'"/>');
		}
		
		$html = $view->getHTML();
		// echo "string".$html;

		// echo htmlspecialchars($html);
		// exit;

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
		// $this->api->redirect($this->api->url('xepan_commerce_printqsp',['document_id'=>$this->id]));
		$js=$this->app->js()->univ()->newWindow($this->app->url('xepan_commerce_printqsp',['document_id'=>$this->id]),'Print'.$this['type']);
		$this->app->js(null,$js)->univ()->execute();
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
			$other_attachments=[];
			
			foreach ($original_obj->ref('Attachments') as $attach) {
				if($form['attachdoc'.$attach->id])
					$other_attachments[]=$attach->id;
			}
			// var_dump($other_attachments);
			$this->send($form['from_email'],$form['to'],$form['cc'],$form['bcc'],$form['subject'],$form['body'],$other_attachments);
			$this->app->page_action_result = $form->js(null,$form->js()->closest('.dialog')->dialog('close'))->univ()->successMessage('Email Send SuccessFully');
			
			// Activity Message
			$qsp_mdl_for_msg = $this->load($this->id);
			$this->app->employee
				->addActivity("'".$qsp_mdl_for_msg['type']."' No. '".$qsp_mdl_for_msg['document_no']."' successfully sent to '".$qsp_mdl_for_msg['contact']."' ", $qsp_mdl_for_msg->id/* Related Document ID*/, $qsp_mdl_for_msg['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_".strtolower($qsp_mdl_for_msg['type'])."detail&document_id=".$qsp_mdl_for_msg->id."")
				->notifyWhoCan('send',' ',$qsp_mdl_for_msg);
		}

	}

	// send invoice & other Document to custom by default
	function send($from_email=null,$to_emails=null,$cc_emails=null,$bcc_emails=null,$subject=null,$body=null,$other_attachments=[]){
		$email_setting = $this->add('xepan\communication\Model_Communication_EmailSetting');
		$email_setting->tryLoad($from_email?:-1);

		$communication = $this->add('xepan\communication\Model_Communication_Abstract_Email');					
		$communication->getElement('status')->defaultValue('Draft');
		$communication['direction']='Out';


		$communication->setfrom($email_setting['from_email'],$email_setting['from_name']);
		$communication->addCondition('communication_type','Email');
		
		$to_emails=explode(',', trim($to_emails));
		foreach ($to_emails as $to_mail) {
			$communication->addTo($to_mail);
		}
		if($cc_emails){
			$cc_emails=explode(',', trim($cc_emails));
			foreach ($cc_emails as $cc_mail) {
					$communication->addCc($cc_mail);
			}
		}
		if($bcc_emails){
			$bcc_emails=explode(',', trim($bcc_emails));
			foreach ($bcc_emails as $bcc_mail) {
					$communication->addBcc($bcc_mail);
			}
		}
		$communication->setSubject($subject);
		$communication->setBody($body);
		$communication->save();

		// Attach Invoice
		$file =	$this->add('xepan/filestore/Model_File',array('policy_add_new_type'=>true,'import_mode'=>'string','import_source'=>$this->generatePDF('return')));
		$file['filestore_volume_id'] = $file->getAvailableVolumeID();
		$file['original_filename'] =  strtolower($this['type']).'_'.$this['document_no_number'].'_'.$this->id.'.pdf';
		$file->save();
		$communication->addAttachment($file->id);
		// Attach Other attachments
		if(count($other_attachments)){
			$attachments_m = $this->add('xepan\base\Model_Document_Attachment');
			$attachments_m->addCondition('id',$other_attachments);
			foreach ($attachments_m as $attach) {
					$file =	$this->add('xepan/filestore/Model_File',array('policy_add_new_type'=>true,'import_mode'=>'copy','import_source'=>$_SERVER["DOCUMENT_ROOT"].$attach['file']));
					$file['filestore_volume_id'] = $file->getAvailableVolumeID();
					$file['original_filename'] = $attach['original_filename'];
					$file->save();
					$communication->addAttachment($file->id);
			}
		}

		$communication->findContact('to');

		$communication->send($email_setting);
	}

	function getBarCode(){
		$m = $this->add('xepan\commerce\Model_BarCode');
		$m->addCondition('related_document_id',$this->id);
		$m->addCondition('related_document_type',$this['type']);
		$m->tryLoadAny();
		if($m->loaded()){
			return $m['name'];
		}
		return false;
	}

    //Return qspItem sModel
	function items(){
		return $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
		// return $this->ref('Details');
	}

	function details(){
		return $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
		// return $this->ref('Details');
	}

	function customer(){
		return $this->ref('contact_id');
	}


	function currency(){
		return $this->add('xepan\accounts\Model_Currency')->load($this['currency_id']);		
	}


	//return tax id and it's total amount
	function getCommnTaxAndAmount($gst = true){
		
		if(!$this->loaded())
			throw new \Exception("model must loaded", 1);
		
		if(!$gst){
			foreach ($this->details() as $invoice_item) {
				if(!$invoice_item['taxation_id'])
					continue;

				$comman_tax_array[$invoice_item['taxation']]['taxation_sum'] += $invoice_item['tax_amount'];
				$comman_tax_array[$invoice_item['taxation']]['net_amount_sum'] += $invoice_item['amount_excluding_tax'];
			}

			return $comman_tax_array;
		}

		// return tax data in gst format
		$all_tax = $this->add('xepan\commerce\Model_Taxation')->getRows();
		$taxation = [];
		foreach ($all_tax as $tax) {
			$taxation[$tax['id']] = [
									'name'=>$tax['name'],
									'percentage'=>$tax['percentage'],
									'sub_tax'=>$tax['sub_tax'],
									'show_in_qsp'=>$tax['show_in_qsp']
								];
		}

		$detail_model = $this->add('xepan\commerce\Model_QSP_Detail')
						->addCondition('qsp_master_id',$this->id)
						;

		$gst_tax_array = [];

		foreach ($detail_model as $oi) {
			if(!$oi['taxation_id'])
				continue;
			
			$hsn_sac_no = $oi['hsn_sac'];
			if(!isset($gst_tax_array[$oi['hsn_sac']])){
				$gst_tax_array[$hsn_sac_no] = [
													'hsn_sac'=>$hsn_sac_no,
													'total_taxation_sum'=>0,
													'net_amount'=>0,
												];

			}

			$gst_tax_array[$hsn_sac_no]['net_amount'] += $oi['amount_excluding_tax'];
			// if has subtax
			$tax_id = $oi['taxation_id'];
			$sub_tax = $taxation[$oi['taxation_id']]['sub_tax'];
			if($sub_tax){
				$temp = explode(',', $sub_tax);
				foreach ($temp as $key => $sub_tax_str) {
					$sub_tax_array = explode("-", $sub_tax_str);
					$sub_tax_id = $sub_tax_array[0];

					$tax = $taxation[$sub_tax_id];
					if(!isset($gst_tax_array[$hsn_sac_no][$sub_tax_id])){
						$gst_tax_array[$hsn_sac_no][$sub_tax_id] = [];
						$gst_tax_array[$hsn_sac_no][$sub_tax_id]['tax_name'] = $tax['name'];
						$gst_tax_array[$hsn_sac_no][$sub_tax_id]['taxation_sum'] = 0;
						$gst_tax_array[$hsn_sac_no][$sub_tax_id]['tax_rate'] = $tax['percentage'];
					}

					$tax_percentage = $tax['percentage'];
					$discount_amount = $oi['discount'];

					$amount = $oi['amount_excluding_tax'];
					$tax_amount = ($amount * $tax_percentage)/100;

					$gst_tax_array[$hsn_sac_no]['total_taxation_sum'] += $tax_amount;
					$gst_tax_array[$hsn_sac_no][$sub_tax_id]['taxation_sum'] += $tax_amount;
				}

			}else{
				$tax = $taxation[$tax_id];
				if(!isset($gst_tax_array[$hsn_sac_no][$tax_id])){
					$gst_tax_array[$hsn_sac_no][$tax_id] = [];
					$gst_tax_array[$hsn_sac_no][$tax_id]['tax_name'] = $tax['name'];
					$gst_tax_array[$hsn_sac_no][$tax_id]['taxation_sum'] = 0;
					$gst_tax_array[$hsn_sac_no][$tax_id]['tax_rate'] = $tax['percentage'];
				}

				$tax_percentage = $tax['percentage'];
				$discount_amount = $oi['discount'];

				$amount = $oi['amount_excluding_tax'];
				$tax_amount = ($amount * $tax_percentage)/100;
				
				$gst_tax_array[$hsn_sac_no]['total_taxation_sum'] += $tax_amount;
				$gst_tax_array[$hsn_sac_no][$tax_id]['taxation_sum'] += $tax_amount;

			}
		}

		// echo "<pre>";
		// print_r($gst_tax_array);
		// echo "</pre>";
		// die();
		return $gst_tax_array;
		
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['created_at'];
		$search_string .=" ". $this['updated_at'];
		$search_string .=" ". $this['document_no'];
		$search_string .=" ". $this['from'];
		$search_string .=" ". $this['billing_address'];
		$search_string .=" ". $this['billing_city'];
		$search_string .=" ". $this['billing_pincode'];
		$search_string .=" ". $this['shipping_address'];
		$search_string .=" ". $this['shipping_city'];
		$search_string .=" ". $this['shipping_pincode'];
		$search_string .=" ". $this['total_amount'];
		$search_string .=" ". $this['net_amount'];
		$search_string .=" ". $this['gross_amount'];
		$search_string .=" ". $this['discount_amount'];
		$search_string .=" ". $this['type'];
		
		if($this->loaded()){
			$qsp_detail = $this->ref('Details');
			foreach ($qsp_detail as $all_qsp_detail) {
				$search_string .=" ". $all_qsp_detail['item'];
				$search_string .=" ". $all_qsp_detail['price'];
				$search_string .=" ". $all_qsp_detail['amount_excluding_tax'];
				$search_string .=" ". $all_qsp_detail['tax_percentage'];
				$search_string .=" ". $all_qsp_detail['shipping_charge'];
				$search_string .=" ". $all_qsp_detail['narration'];
				$search_string .=" ". $all_qsp_detail['extra_info'];
			}			
		}
		$this['search_string'] = $search_string;
	}
	
	function page_communication($p){
		$contact = $this->add('xepan\base\Model_Contact');
		$contact->loadBy('id',$this['contact_id']);
		$contact->page_communication($p);
	}


	function page_duplicate($page){
		$form = $page->add('Form');
		$form->addField('xepan\base\Basic','contact')->setModel('xepan\base\Contact');
		$form->addSubmit('Duplicate');

		if($form->isSubmitted()){
			$item = $this->add('xepan\commerce\Model_'.$this['type']);

			try{
				$this->api->db->beginTransaction();

				$new_quotation = $this->duplicate($form['contact']);

				$this->app->employee
				->addActivity($this['type'] . " : '".$this['name']."' Duplicated as New" .$this['type']. ": '".$form['contact']."'", $this->id/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_itemdetail&document_id=".$this->id."")
				->notifyWhoCan('unpublish,duplicate','Published');
				$this->api->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
	            throw $e;
			}
			$url=null;
			switch ($this['type']) {
				case 'SalesOrder':
					$url = 'xepan_commerce_salesorderdetail';
					break;
				case 'Quotation':
					$url = 'xepan_commerce_quotationdetail';
					break;
				case 'SalesInvoice':
					$url = 'xepan_commerce_salesinvoicedetail';
					break;
				case 'PurchaseOrder':
					$url = 'xepan_commerce_purchaseorderdetail';
					break;
				case 'PurchaseInvoice':
					$url = 'xepan_commerce_purchaseinvoicedetail';
					break;				
				
				default:
					# code...
					break;
			}
			return $this->api->js()->univ()->location($this->app->url($url,['document_id'=>$new_quotation->id, 'action'=>'edit']));
		}
	}

	function duplicate($contact_id){

		$address_field= 'address';
		$country_field = 'country_id';
		$state_field = 'state_id';
		$contact_model='xepan\base\Model_Contact';

		switch ($this['type']) {
			case 'SalesOrder':
			case 'SalesInvoice':
				$address_field='billing_address';
				$country_field='billing_country_id';
				$state_field='billing_state_id';
				$contact_model='xepan\commerce\Model_Customer';
				break;
		}

		$new_quotation = $this->add('xepan\commerce\Model_'.$this['type']);
	
		$contact = $this->add($contact_model);
		if($contact_id)
			$contact->load($contact_id);

		$fields=$this->getActualFields();
		$fields = array_diff($fields,array('id','contact_id'));

		foreach ($fields as $fld) {
			$new_quotation[$fld] = $this[$fld];
		}

		$new_quotation['contact_id'] = $contact->id;
		$new_quotation['currency_id'] = $this['currency_id'];
		$new_quotation['nominal_id'] = $this['nominal_id'];
		$new_quotation['tnc_id'] = $this['tnc_id'];
		$new_quotation['paymentgateway_id'] = $this['paymentgateway_id'];
		$new_quotation['outsource_party_id'] = $this['outsource_party_id'];
		$new_quotation['billing_country_id'] = $contact[$country_field];
		$new_quotation['billing_state_id'] = $contact[$state_field];
		$new_quotation['shipping_country_id'] = $this['shipping_country_id'];
		$new_quotation['shipping_state_id'] = $this['shipping_state_id'];
		$new_quotation['related_qsp_master_id'] = $this['related_qsp_master_id'];
		$new_quotation['document_no'] = $this->newNumber();
		$new_quotation['status'] = "Draft";
		$new_quotation['billing_name'] = $this['billing_name'];
		$new_quotation['billing_address'] = $contact[$address_field];
		$new_quotation['billing_city'] = $this['billing_city'];
		$new_quotation['billing_pincode'] = $this['billing_pincode'];
		$new_quotation['shipping_name'] = $this['shipping_name'];
		$new_quotation['shipping_address'] = $this['shipping_address'];
		$new_quotation['shipping_city'] = $this['shipping_city'];
		$new_quotation['shipping_pincode'] = $this['shipping_pincode'];
		$new_quotation['priority_id'] = $this['priority_id'];
		$new_quotation['narration'] = $this['narration'];
		$new_quotation['exchange_rate'] = $this['exchange_rate'];
		$new_quotation['tnc_text'] = $this['tnc_text'];
		$new_quotation['round_amount'] = $this['round_amount'];
		$new_quotation['transaction_reference'] = $this['transaction_reference'];
		$new_quotation['transaction_response_data'] = $this['transaction_response_data'];

		$new_quotation->save();

		$detail = $this->add('xepan\commerce\Model_QSP_Detail')
				->addCondition('qsp_master_id',$this->id);

		
		foreach ($detail as $oi) {
			$item = $oi['item_id'];		
				//todo check all invoice created or not
			if(!($item instanceof \xepan\commerce\Model_Item) and is_numeric($item)){
				$item = $this->add('xepan\commerce\Model_Item')->load($item);
			}


			$in_item = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$new_quotation->id);
				$in_item['item_id'] = $oi['item_id'];

			$in_item['qsp_master_id'] = $new_quotation->id;
			$in_item['quantity'] = $oi['quantity'];
			$in_item['price'] = $oi['price'];
			$in_item['shipping_charge'] = $oi['shipping_charge'];
			$in_item['shipping_duration'] = $oi['shipping_duration'];
			$in_item['sale_amount'] = $oi['sale_amount'];
			$in_item['original_amount'] = $oi['original_amount'];
			$in_item['shipping_duration'] = $oi['shipping_duration'];
			$in_item['express_shipping_charge'] = $oi['express_shipping_charge'];
			$in_item['express_shipping_duration'] = $oi['express_shipping_duration'];
			$in_item['narration'] = $oi['narration'];
			$in_item['extra_info'] = $oi['extra_info'];
			$in_item['taxation_id'] = $oi['taxation_id'];
			$in_item['tax_percentage'] = $oi['tax_percentage'];
			$in_item['qty_unit_id'] = $oi['qty_unit_id'];

			$in_item->save();
		}	

		return $new_quotation;
	}

	function getDetailIds(){
		if(!$this->loaded()) throw new \Exception("model must loaded");
		
		$detail_model = $this->add('xepan\commerce\Model_QSP_Detail')
						->addCondition('qsp_master_id',$this->id)
						;
		$detail_model = $detail_model->_dsql()->del('fields')->field('id')->getAll();

		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($detail_model)),false);
	}

	/*
	Mater Detail Array Format
	$master_detail = [
		'contact_id' => $form['supplier'],
		'currency_id' => $this->app->epan->default_currency->get('id'),
		'nominal_id' => 0,
		'billing_country_id'=> $model['country_id'],
		'billing_state_id'=> $model['state_id'],
		'billing_name'=> $model['effective_name'],
		'billing_address'=> $model['address'],
		'billing_city'=> $model['city'],
		'billing_pincode'=> $model['pincode'],
		'shipping_country_id'=> $model['country_id'],
		'shipping_state_id'=> $model['state_id'],
		'shipping_name'=> $model['effective_name'],
		'shipping_address'=> $model['address'],
		'shipping_city'=> $model['city'],
		'shipping_pincode'=> $model['pincode'],
		'is_shipping_inclusive_tax'=> 0,
		'is_express_shipping'=> 0,
		'narration'=> null,
		'round_amount'=> 0,
		'discount_amount'=> 0,
		'exchange_rate' => $this->app->epan->default_currency['value'],
		'tnc_id'=>$tnc_id,
		'tnc_text'=> $tnc_text,
		'status' => "Submitted",
		'due_date'=>$this->app->nextDate($this->app->now)
	];
	*/

	function createQSP($master_data,$detail_data,$type){
		if(!$type ) throw new \Exception("type must define");
		if(!is_array($master_data) && count($master_data) < 0) throw new \Exception("must pass master data");
		if(!is_array($detail_data) && count($detail_data) < 0) throw new \Exception("must pass detail data");

		$old_new_ids_array = [];
		try{
			$this->app->hook('beforeQSPSave',[$master_data,$detail_data,$type]);

			$this->api->db->beginTransaction();
			$master_model = $this->createQSPMaster($master_data,$type);
			$old_new_ids_array = $this->addQSPDetail($detail_data,$master_model);
			if($type == "SalesInvoice"){
				$master_model->updateTransaction();
			}
			$this->api->db->commit();
			$this->app->hook('afterQSPSave',[$master_model,$old_new_ids_array]);
		}catch(\Exception $e){
			$this->api->db->rollback();
			throw new \Exception($e->getMessage());
		}

		// echo "<pre>"; 
		// echo "New Id = ".$master_model->id;
		// print_r($old_new_ids_array);
		// echo "</pre>";
		// die();
		return ['master_detail'=>$master_model->data,'row_details'=>$old_new_ids_array,'master_model'=>$master_model];
	}

	function createQSPMaster($master_data,$type){

		if(!is_array($master_data)) throw new \Exception("must pass array of master");
		$qsp_type = ['Quotation','SalesOrder','SalesInvoice','PurchaseOrder','PurchaseInvoice'];
		
		if(!in_array($type, $qsp_type)) throw new \Exception("type not defined");

		if($master_data['tnc_id'])
			$tnc_model = $this->add('xepan\commerce\Model_TNC')->load($master_data['tnc_id']);

		$master_model = $this->add('xepan\commerce\Model_'.$type);
		$qsp_no = $master_data['qsp_no'];

		if($master_data['document_id'] > 0){
			$master_model->addCondition('id',$master_data['document_id']);
			$master_model->tryLoadAny();
		}

		if(!$qsp_no){
			$qsp_no = $master_model->newNumber($master_data['serial']);
		}elseif($master_model->checkQSPNumberExist($qsp_no,$master_data['serial'])){
			$qsp_no = $master_model->newNumber($master_data['serial_number']);
		}
		
		$contact = $this->add('xepan\base\Model_Contact')->addCondition('id',$master_data['contact_id']);
		$contact->tryLoadAny();

		$master_model['document_no'] = $qsp_no;
		$master_model['serial'] = $master_data['serial'];
		$master_model['contact_id'] = $master_data['contact_id'];
		$master_model['currency_id'] = $master_data['currency_id'];
		$master_model['nominal_id'] = $master_data['nominal_id'];

		if(!$master_model['branch_id']){
			if($contact->loaded() AND $contact['branch_id'])
				$master_model['branch_id'] = $contact['branch_id'];
			else
				$master_model['branch_id'] = isset($master_data['branch_id'])?($master_data['branch_id']):(@$this->app->branch->id);
		}

		$master_model['billing_country_id'] = $master_data['billing_country_id'];
		$master_model['billing_state_id'] = $master_data['billing_state_id'];
		$master_model['billing_name'] = $master_data['billing_name']?:'not defined';
		$master_model['billing_address'] = $master_data['billing_address']?:'not defined';;
		$master_model['billing_city'] = $master_data['billing_city']?:'not defined';
		$master_model['billing_pincode'] = $master_data['billing_pincode']?:'not defined';

		$master_model['shipping_country_id'] = $master_data['shipping_country_id']?:$master_data['billing_country_id'];
		$master_model['shipping_state_id'] = $master_data['shipping_state_id']?:$master_data['billing_state_id'];
		$master_model['shipping_name'] = $master_data['shipping_name']?:'not defined';
		$master_model['shipping_address'] = $master_data['shipping_address']?:'not defined';
		$master_model['shipping_city'] = $master_data['shipping_city']?:'not defined';
		$master_model['shipping_pincode'] = $master_data['shipping_pincode']?:'not defined';

		$master_model['is_shipping_inclusive_tax'] = $master_data['is_shipping_inclusive_tax'];
		$master_model['is_express_shipping'] = $master_data['is_express_shipping'];

		$master_model['created_at'] = $master_data['created_date']?:$this->app->now;
		$master_model['due_date'] = $master_data['due_date'];
		$master_model['narration'] = $master_data['narration'];
		
		$master_model['round_amount'] = $master_data['round_amount'];
		$master_model['discount_amount'] = $master_data['discount_amount'];
		$master_model['exchange_rate'] = $master_data['exchange_rate'];
		$master_model['related_qsp_master_id'] = $master_data['related_qsp_master_id'];

		if($master_data['status'])
			$master_model['status'] = $master_data['status'];

		$master_model['tnc_id'] = $master_data['tnc_id'];
		if($master_data['tnc_id'])
			$master_model['tnc_text'] = $tnc_model['content'];
		
		if($master_data['created_by_id'])
			$master_model['created_by_id'] = $master_data['created_by_id'];

		return $master_model->save();
	}

	function addQSPDetail($detail_data,$master_model){
		if(!is_array($detail_data)) throw new \Exception("must pass array of details");

		$master_id = $master_model;
		if($master_model instanceof \xepan\commerce\Model_QSP_Master) {
			$master_id = $master_model->id;
		}

		$old_new_ids_array = ['new'=>[],'mapping'=>[]];

		$taxation_list = $this->add('xepan\commerce\Model_Taxation')->getRows();

		foreach($detail_data as $key => $row) {
			if(!isset($row['item_id'])) continue;

			$qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail');
			$qsp_detail->addCondition('qsp_master_id',$master_id);

			$old_detail_id = isset($row['id'])?$row['id']:0;
			if($row['qsp-detail-id']){
				$qsp_detail->addCondition('id',$row['qsp-detail-id']);
				$qsp_detail->tryLoadAny();
				$old_detail_id = $row['qsp-detail-id'];
			}

			$qsp_detail['item_id'] = $row['item_id'];
			$qsp_detail['price'] = $row['price'];
			$qsp_detail['quantity'] = $row['quantity'];
			
			$qsp_detail['taxation_id'] = $row['taxation_id'];
			$tax_percentage = 0;
			foreach ($taxation_list as $key => $tax) {
				if($tax['id'] == $row['taxation_id']){
					$tax_percentage = $tax['percentage'];
					break;
				}
			}
			$qsp_detail['tax_percentage'] = $tax_percentage;
			$qsp_detail['narration'] = $row['narration'];
			$qsp_detail['extra_info'] = $row['extra_info'];
			$qsp_detail['shipping_charge'] = $row['shipping_charge'];
			$qsp_detail['shipping_duration'] = $row['shipping_duration'];
			$qsp_detail['express_shipping_charge'] = $row['express_shipping_charge'];
			$qsp_detail['express_shipping_duration'] = $row['express_shipping_duration'];
			$qsp_detail['qty_unit_id'] = $row['qty_unit_id'];
			$qsp_detail['discount'] = $row['discount']?:0;
			$qsp_detail['treat_sale_price_as_amount'] = isset($row['treat_sale_price_as_amount'])?$row['treat_sale_price_as_amount']:0;
			$qsp_detail->save();

			// inserting new ids
			array_push($old_new_ids_array['new'], $qsp_detail->id);

			if($row['recurring_from_qsp_detail_id'] && $row['recurring_from_qsp_detail_id'] != $qsp_detail->id){
				$old_model = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('id',$row['recurring_from_qsp_detail_id']);
				$old_model->tryLoadAny();
				if($old_model->loaded()){
					$old_model['recurring_qsp_detail_id'] = $qsp_detail->id;
					$old_model->save();
					$old_new_ids_array['mapping'][$row['recurring_from_qsp_detail_id']] = $qsp_detail->id;
				}
			}
		}

		if($this->debug){
			echo "<pre>";
			print_r($old_new_ids_array); 
			echo "</pre>";
		}
		return $old_new_ids_array;
	}

	function getMissingNo($serial_no=null){
		$m = $this->add('xepan\commerce\Model_'.$this['type']);
		if(!$serial_no){
			$serial_no = $this->getSerialNo();
		}
		$m->addCondition('serial',$serial_no);
		if($m->app->branch->id)
			$m->addCondition('branch_id',$m->app->branch->id);

		$data = $m->getRows();
		$missing_numbers = $inv_no_exist = [];

		if(count($data)){
			$inv_no_exist = array_column($data,'document_no');
			sort($inv_no_exist);
			$range = range(1,end($inv_no_exist));
			$missing_numbers = array_diff($range,$inv_no_exist);
		}
		
		return $missing_numbers;
	}


	function page_cancel($page){
		$m = $this->add('xepan\commerce\Model_Config_QSPCancelReason');
		$m->addCondition('for',$this['type']);
		$m->tryLoadAny();
		$reason_array = explode(",", $m['name']);
		$reason_array = array_combine($reason_array,$reason_array);

		$f = $page->add('Form');
		$f->addField('DropDown','cancel_reason')
			->setValueList($reason_array)
			->setEmptyText('Please Select Cancel Reason')
			->validate('required');	
		$f->addField('Text','cancel_narration');
		$f->addButton('Submit');

		if($f->isSubmitted()){
			$this->cancel($f['cancel_reason'],$f['cancel_narration']);
			return $page->js()->univ()->closeDialog();
		}
	}
} 