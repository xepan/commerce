<?php
namespace xepan\commerce;

class Model_Store_Delivered extends \xepan\commerce\Model_Store_Transaction{
	public $status = ['Shipped','Delivered','Return'];
	public $actions=[
				'Shipped'=>['view','edit','delete','delivered'],
				'Delivered'=>['view','edit','delete','return'],
				'Return'=>['view','edit','delete']
			];
	function init(){
		parent::init();
		
		$this->addCondition('document_type','Deliver');
	}

	function printChallan(){
		$this->api->redirect($this->api->url('xepan_commerce_store_printchallan',['transaction_id'=>$this->id]));
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

		$view = $this->add('View')->set("Challan View TODO");
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
}