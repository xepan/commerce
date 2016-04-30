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
		}
	}
}