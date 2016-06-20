<?php 
 namespace xepan\commerce;
 class page_tnc extends \xepan\commerce\page_configurationsidebar{

	public $title='Terms & Condition';

	function init(){
		parent::init();

		$tnc=$this->add('xepan\commerce\Model_TNC');
		$crud=$this->add('xepan\hr\CRUD',null,
						null,
						['view/tnc/grid']
					);

		if($crud->isEditing()){
			$crud->form->setLayout('view\form\tnc');
		}

		$crud->setModel($tnc);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);

		$crud->add('xepan\base\Controller_Avatar');

		$g = $crud->grid;
		$g->addHook('formatRow',function($g){
			$g->current_row_html['content']= $g->model['content'];
		});		

		$g->js('click')->_selector('.do-view-tnc-detail')->univ()->frameURL('Terms And Condition',[$this->api->url('xepan_commerce_tncdetail'),'tnc_id'=>$this->js()->_selectorThis()->closest('[data-quotation-id]')->data('id')]);
	}

}  