<?php
 namespace xepan\commerce;
class View_QSP extends \View{

	public $qsp_model;
	public $qsp_view_field = ['x'];
	public $qsp_form_field = ['y'];
	public $document_label="Document";

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
			$qsp_details = $document->addMany('Items',
										null,
										'item_info',
										['view/qsp/details'],
										'xepan\commerce\Grid_QSP',	
										'xepan\commerce\CRUD_QSP'
									);
			$qsp_details->setModel($this->qsp_model->ref('Details'));
		}


	}

}