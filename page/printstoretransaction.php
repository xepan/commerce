<?php

namespace xepan\commerce;

class page_printstoretransaction extends \Page{

	function init(){
		parent::init();

		if(!$transaction_id = $_GET['transaction_id'])
			throw $this->exception('Transaction Id not found in Query String');
		
		$action = "dump";
		if($_GET['action'])
			$action = $_GET['action'];

		$transaction = $this->add('xepan\commerce\Model_Store_TransactionAbstract');
		$transaction->load($transaction_id);

		$config_key = "";
		switch ($transaction['type']) {
			case 'Issue':
				$config_key = "STORE_ISSUE_LAYOUT";
				break;
		}

		$config_model = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
						'master'=>'xepan\base\RichText',
						'detail'=>'xepan\base\RichText',
						],
				'config_key'=>$config_key,
				'application'=>'commerce'
			]);
		$config_model->tryLoadAny();

		$master_html = $config_model['master'];
		$detail_html = $config_model['detail'];

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('xEpan ERP');
		$pdf->SetTitle($transaction['type']." ".$transaction['id']);
		$pdf->SetSubject($transaction['type']." ".$transaction['id']);
		$pdf->SetKeywords($transaction['type']." ".$transaction['id']);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		//remove header or footer hr lines
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		// add a page
		$pdf->AddPage();

		$master_layout = $this->add('GiTemplate');
		$master_layout->loadTemplateFromString($master_html);

		$detail_layout = $this->add('GiTemplate');
		$detail_layout->loadTemplateFromString($detail_html);
		
		$view = $this->add('View',null,null,$master_layout);
		$view->setModel($transaction);

		$row_model = $this->add('xepan\commerce\Model_Store_TransactionRow');
		$row_model->addCondition('store_transaction_id',$transaction->id);
		$lister = $view->add('CompleteLister',null,'item_info',$detail_layout);
		$lister->setModel($row_model);
		
		$html = $view->getHTML();
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
} 