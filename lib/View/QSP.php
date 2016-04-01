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
			$m = $this->qsp_model->ref('Details');
			$m->addHook('beforeSave',function($m){
				$m->saleInvoice()->updateTransaction();
			});

			$qsp_details->setModel($m);

			$qs = $this->add('xepan\commerce\View_QSPDetailJS');
			$form=$qsp_details->form;
			$tax_field = $form->getElement('taxation_id');
			// $tax_percentage = $qsp_details->form->getElement('tax_percentage');
			$tax_field->js('change',$this->js()->reload(['tax_id'=>$tax_field->js()->val()]));..
			// $tax_field->js('change',$qsp_details->form->js()->atk4_form(
			// 				'reloadField','tax_percentage',
			// 				[
			// 					$this->app->url(),
			// 					'tax_id'=>$tax_field->js()->val()
			// 				]
			// 				));

			$tax_id=$this->api->stickyGET('tax_id');
			if($tax_id){
				$tax = $this->add('xepan\commerce\Model_Taxation');
				$tax->load($tax_id);
				$js=[];
				// throw new \Exception($tax['percentage'], 1);
						
				// tax pecentage
				$js[] = $this->js()->_selector('.tax_percentage')->find('input')->val($tax['percentage']);
				
				$this->js(true,$js);
			}

		}
	}

}