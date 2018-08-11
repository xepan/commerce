<?php
namespace xepan\commerce;

class Model_Store_Delivered extends \xepan\commerce\Model_Store_TransactionAbstract{
	public $status = ['Shipped','Delivered','Return'];
	public $actions=[
				'Shipped'=>['view','edit','delete','delivered'],
				'Delivered'=>['view','edit','delete'],
				'Return'=>['view','edit','delete']
			];
	public $s_no = 1;
			
	function init(){
		parent::init();
		
		$this->addCondition('type','Store_Delivered');

	}

	function printDocument($print_document,$return_array=false,$challan_transaction=[]){
		$js = [];
		

		if($print_document=='print_challan' or $print_document=='print_all'){
			foreach ($challan_transaction as $challan_transaction_id) {
				$js[] = $this->app->js()->univ()->newWindow($this->api->url('xepan_commerce_store_printchallan',['transaction_id'=>$challan_transaction_id]),'Print Challan',null);
			}
		}

		if($print_document=='print_invoice' or $print_document=='print_all')
			$js[] = $this->app->js()->univ()->newWindow($this->api->url('xepan_commerce_store_printinvoice',['transaction_id'=>$this->id]),'Print Invoice',null);
		
		if($return_array)
			return $js;

		$this->app->js(null,$js)->univ()->execute();
	}

	function generatePDF($action ='return'){

		if(!in_array($action, ['return','dump']))
			throw $this->exception('Please provide action as result or dump');

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('xEpan ERP');
		$pdf->SetTitle($this['type']. ' '. $this['id']);
		$pdf->SetSubject($this['type']. ' '. $this['id']);
		$pdf->SetKeywords($this['type']. ' '. $this['id']);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		// add a page
		$pdf->AddPage();
		
		$challan_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'CHALLAN_LAYOUT',
					'application'=>'commerce'
			]);
		$challan_m->tryLoadAny();
		
		$chalan_config = $challan_m['master'];
		// $chalan_config = $this->app->epan->config->getConfig('CHALLANLAYOUT');
		$chalan_layout = $this->add('GiTemplate');
		$chalan_layout->loadTemplateFromString($chalan_config);	
	

		// $detail_config = $this->app->epan->config->getConfig('CHALLANDETAILLAYOUT');
		$detail_config = $challan_m['detail'];
		$detail_layout = $this->add('GiTemplate');
		$detail_layout->loadTemplateFromString($detail_config);	

		$company_m = $this->add('xepan\base\Model_Config_CompanyInfo');
		
		$company_m->tryLoadAny();
		$address = $company_m['company_address']." (Pincode : ".$company_m['company_pin_code'].")";

		$new = $this->add('xepan\commerce\Model_Store_Delivered');
		$new->load($this->id);
		$view = $this->app->add('View',null,null,$chalan_layout);
		$view->setModel($new);
		$tr_row = $this->add('xepan\commerce\Model_Store_TransactionRow');
		$tr_row->addExpression('from_warehousename')->set($tr_row->refSQL('store_transaction_id')->fieldQuery('from_warehouse'));
		$tr_row->addCondition('store_transaction_id',$new->id);
		$details_view = $view->add('CompleteLister',null,'item_info',$detail_layout);
		$details_view->setModel($tr_row);

		$details_view->addHook('formatRow',function($m){
			$m->current_row_html['s_no']=$this->s_no;
			$this->s_no ++;
			$m->current_row_html['from_warehouse_name'] = $m['from_warehousename'];
		});			

		$tr_row->tryLoadAny();
		$view->template->trySetHTML('related_sale_order',$tr_row['related_sale_order']);
		$view->template->trySetHTML('date',$this->app->today);
		$view->template->trySetHTML('company_name',$company_m['company_name']);
		$view->template->trySetHTML('company_contact',$company_m['mobile_no']);
		$view->template->trySetHTML('company_address',$address);
		
		


		
		// if($bar_code = $this->getBarCode()){
		// 	$barcodeobj = new \TCPDFBarcode($bar_code, 'C128');
		// 	// $barcode_html = $barcodeobj->getBarcodePNG(2, 30, 'black');
		// 	$barcode_html = $barcodeobj->getBarcodePngData(1, 20, array(0,128,0));
		// 	$info_layout->trySetHtml('dispatch_barcode','<img src="data:image/png;base64, '.base64_encode($barcode_html).'"/>');
		// }

		// $view = $this->add('View')->set("Challan View TODO");
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

	function send($send_document,$from_email,$emails,$subject,$message,$challan_transaction=[]){
		if(!$from_email){ return; }
		if(!$emails){
			return ;
		}

		$email_setting = $this->add('xepan\communication\Model_Communication_EmailSetting');
		$email_setting->tryLoad($from_email?:-1);

		// throw new \Exception($email_setting['name'], 1);
		
		
		$email = $this->add('xepan\communication\Model_Communication_Abstract_Email');					
		$email->getElement('status')->defaultValue('Draft');
		$email->setfrom($email_setting['from_email'],$email_setting['from_name']);
		$email->addCondition('direction','Out');
		$email->setSubject($subject);
		$email->setBody($message);
		$to_emails=$emails;
		foreach (explode(',',$to_emails) as $toemails) {
			$email->addTo($toemails);
		}
		$email->save();

		// Attach Invoice
		if($send_document=='send_invoice' or $send_document=='all'){
			$invoice=$this->saleOrder()->invoice();
			$file =	$this->add('xepan\filestore\Model_File',array('policy_add_new_type'=>true,'import_mode'=>'string','import_source'=>$invoice->generatePDF('return')));
			$file['filestore_volume_id'] = $file->getAvailableVolumeID();
			$file['original_filename'] =  strtolower($invoice['type']).'_'.$invoice['document_no_number'].'_'.$invoice->id.'.pdf';
			$file->save();
			$email->addAttachment($file->id);
		}
		// Attach Challan attachments
		
		if($send_document=='send_challan' or $send_document=='all'){
			foreach ($challan_transaction as $challan_transaction_id) {
				$store_deliver_model = $this->add('xepan\commerce\Model_Store_Delivered')->load($challan_transaction_id);
				$file =	$this->add('xepan\filestore\Model_File',array('policy_add_new_type'=>true,'import_mode'=>'string','import_source'=>$store_deliver_model->generatePDF('return')));
				$file['filestore_volume_id'] = $file->getAvailableVolumeID();
				$file['original_filename'] =  strtolower($this['type']).'_'.$this->id.'.pdf';
				$file->save();
				$email->addAttachment($file->id);
			}
		}
		$email->findContact('to');
		$email->send($email_setting);
	}

	// function getBarCode(){
	// 	$m = $this->add('xepan\commerce\Model_BarCode');
	// 	$m->addCondition('related_document_id',$this->id);
	// 	$m->addCondition('related_document_type',$this['type']);
	// 	$m->tryLoadAny();
	// 	if($m->loaded()){
	// 		return $m['name'];
	// 	}
	// 	return false;
	// }

	function delivered(){
		$this['status'] = 'Delivered';
		$this->app->employee
			->addActivity("Sales Order No : '".$this['related_document_no']."' has been successfully Delivered to customer ".$this['to_contact_name'], $this->id/* Related Document ID*/, $this['related_contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this['related_document_id']."")
			->notifyWhoCan('edit,delete','Delivered',$this);

		// todo check if all item are delivered then order status set to complete
		$this->save();
	}
}