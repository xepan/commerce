<?php

namespace xepan\commerce;

class page_test extends \Page{
	
	function init(){
		parent::init();

		$this->add('xepan\commerce\View_QSP')->generatePDF();

		// create new PDF document
		// $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// // set document information
		// $pdf->SetCreator(PDF_CREATOR);
		// $pdf->SetAuthor('Rakesh Sinha');
		// $pdf->SetTitle('PDF Example');
		// $pdf->SetSubject('PDF Subject');
		// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// // set default monospaced font
		// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// // set font
		// $pdf->SetFont('dejavusans', '', 10);

		// // add a page
		// $pdf->AddPage();

		// // writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)


		// // create some HTML content
		// // $html = file_get_contents('../text.txt');

		// $html = "<style>".file_get_contents("../bootstrap.min.css")."</style>";
		// $html .= file_get_contents('../text.txt');
		// // output the HTML content
		// $pdf->writeHTML($html, true, false, true, false, '');
		// // set default form properties
		// $pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
		// // reset pointer to the last page
		// $pdf->lastPage();
		// //Close and output PDF document
		// $pdf->Output(null, 'I');

	}
}