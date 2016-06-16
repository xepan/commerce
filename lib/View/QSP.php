<?php
namespace xepan\commerce;
class View_QSP extends \View{

	public $qsp_model;
	public $qsp_view_field = ['x'];
	public $qsp_form_field = ['y'];
	public $document_label="Document";
	public $document_item;
	public $document = null;

	public $master_template = 'view/qsp/master';
	public $detail_template = 'view/qsp/details';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		// $this->add('View_Info')->set('QSP=');

		$this->document = $document = $this->add('xepan\hr\View_Document',
			['action'=>$action],
			null,
			[$this->master_template]
			);

		$contact_m = $this->qsp_model->getElement('contact_id')->getModel();
		$contact_m->addExpression('name_with_organization')->set('CONCAT(first_name," ",last_name," :: [",organization,"]")');
		$contact_m->title_field = 'name_with_organization';

		$document->setIdField('document_id');
		$document->setModel($this->qsp_model,$this->qsp_view_field,$this->qsp_form_field);

		if($this->qsp_model['contact_id']){
			$contact = $this->add('xepan\base\Model_Contact')->load($this->qsp_model['contact_id']);
			$document->template->trySetHTML('contacts',$contact['contacts_str']);
			$document->template->trySetHTML('emails',$contact['emails_str']);
		}		

		
		$document->form->getElement('discount_amount')->js('change')->_load('xepan-QSIP')->univ()->calculateQSIP();

		if($this->qsp_model->loaded()){

			$this->document_item = $qsp_details = $document
														->addMany(
																'Items',
																['no_records_message'=>'No item detail found'],
																'item_info',
																[$this->detail_template],
																'xepan\commerce\Grid_QSP',
																'xepan\commerce\CRUD_QSP'
															);

			// if(isset($this->document_item->form) && $this->document_item->form instanceof \Form){
				
			// 	$this->document_item->form->setLayout('view\qsp\detail_form');
			// }	

			$detail_model = $this->qsp_model->ref('Details');
			$qsp_details->setModel($detail_model);

			//comman vat and it's amount
			if($action!='add'){
				if( $this->document_item instanceof \Grid or ($this->document_item instanceof \CRUD && !$this->document_item->isEditing()) or $action=="pdf"){
					$lister = $document->add('Lister',null,'common_vat',[$this->master_template,'common_vat'])->setSource($this->qsp_model->getCommnTaxAndAmount());
					$document->template->trySetHTML('common_vat',$lister->getHtml());
				}
			}
			
			if($detail_model->count()->getOne()){
				$item_m = $this->add('xepan\commerce\Model_Item');
				$detail_j = $item_m->join('qsp_detail.item_id');
				$detail_j->addField('detail_id','id');
				$item_m->addCondition('detail_id','in',$detail_model->fieldQuery('id'));

				// $item_tnc_l = $document->add('CompleteLister',null,'terms_and_conditions',[$this->master_template,'terms_and_conditions']);
				// $item_tnc_l->setModel($item_m);
			}


			$qs = $this->add('xepan\commerce\View_QSPDetailJS');
			if(isset($qsp_details->form)){
				$form = $qsp_details->form;

				$form->setLayout('view\form\qspdetail');
				$tax_field = $form->getElement('taxation_id');
				$tax_percentage = $form->getElement('tax_percentage');
				$item_field=$form->getElement('item_id');
				$sale_price=$form->getElement('sale_amount');
				$original_price=$form->getElement('original_amount');
				
				if($item_id=$_GET['item_id']){
					$sale_price->set(
						$this->add('xepan\commerce\Model_Item')
						->load($item_id)
						->get('sale_price')
					);
					$original_price->set(
						$this->add('xepan\commerce\Model_Item')
						->load($item_id)
						->get('original_price')
					);
					return;
				}

				// $item_field->other_field->js('change',$form->js()->atk4_form(
				// 	'reloadField','sale_amount',
				// 	[
				// 	$this->app->url(),
				// 	'item_id'=>$item_field->js()->val()
				// 	]
				// ));
				// $item_field->other_field->js('change',$form->js()->atk4_form(
				// 	'reloadField','original_amount',
				// 	[
				// 	$this->app->url(),
				// 	'item_id'=>$item_field->js()->val()
				// 	]
				// ));

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

				//load only saleable and published item
				$item_model = $form->getElement('item_id')->getModel();
				$item_model->addCondition('is_saleable',true);
				$item_model->addCondition('status',"Published");
			}
		}
	}
}