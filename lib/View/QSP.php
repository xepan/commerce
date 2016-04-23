<?php
namespace xepan\commerce;
class View_QSP extends \View{

	public $qsp_model;
	public $qsp_view_field = ['x'];
	public $qsp_form_field = ['y'];
	public $document_label="Document";
	public $document_item;
	public $document = null;

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		// $this->add('View_Info')->set('QSP=');

		$this->document = $document = $this->add('xepan\base\View_Document',
			['action'=>$action],
			null,
			['view/qsp/master']
			);
		$document->setIdField('document_id');
		$document->setModel($this->qsp_model,$this->qsp_view_field,$this->qsp_form_field);

		$document->form->getElement('discount_amount')->js('change')->_load('xepan-QSIP')->univ()->calculateQSIP();
		

		if($this->qsp_model->loaded()){
			$this->document_item=$qsp_details = $document->addMany('Items',
				null,
				'item_info',
				['view/qsp/details'],
				'xepan\commerce\Grid_QSP',	
				'xepan\commerce\CRUD_QSP'
				);
			$m = $this->qsp_model->ref('Details');
			$qsp_details->setModel($m);
			
			$qs = $this->add('xepan\commerce\View_QSPDetailJS');
			if(isset($qsp_details->form)){
				$form = $qsp_details->form;
				$tax_field = $form->getElement('taxation_id');
				$tax_percentage = $form->getElement('tax_percentage');

				if($id=$_GET['tax_id']){
					$tax_percentage->set(
						$this->add('xepan\commerce\Model_Taxation')
						->load($id)
						->get('percentage')
						);
					return;
				}


				$tax_field->js('change',$form->js()->atk4_form(
					'reloadField','tax_percentage',
					[
					$this->app->url(),
					'tax_id'=>$tax_field->js()->val()
					]
					));
			}

			if($_GET['generatePdf']){
				$this->generatePDF();
				return;
			}
		}
		
		$this->on('click','.xepan-print-qsp',function($js,$data)
		{
			return $js->univ()->location($this->api->url(null,['generatePdf'=>1]));
		});
		
	}

	function generatePDF(){


		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Rakesh Sinha');
		$pdf->SetTitle('PDF Example');
		$pdf->SetSubject('PDF Subject');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set font
		$pdf->SetFont('dejavusans', '', 10);
		// add a page
		$pdf->AddPage();

		// create some HTML content
		$html = "<style>".file_get_contents('templates/css/bootstrap/bootstrap.min.css');
		// $html .= file_get_contents('templates/css/compiled/theme_styles.css');
		// $html .= file_get_contents('templates/css/xepan.css');
		$html .="</style>";

		$html .= $this->document->getHtml();
		// $html = '<table border="1"><tr><td style="width:30%;">Rakeh</td><td style="width:70%;">Rakesh 33</td></tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, false, false, true, false, '');
		// set default form properties
		$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
		// reset pointer to the last page
		$pdf->lastPage();
		//Close and output PDF document
		$pdf->Output(null, 'I');
	}
}