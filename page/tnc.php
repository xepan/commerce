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
		$crud->add('xepan\base\Controller_MultiDelete');

		$g = $crud->grid;
		$g->addHook('formatRow',function($g){
			$g->current_row_html['content']= $g->model['content'];
		});		

		$this->app->stickyGET('tnc_id');
		$vp = $this->add('VirtualPage');
		$vp->set(function($p){			
			$tnc_m = $p->add('xepan\commerce\Model_TNC')->load($_GET['tnc_id']);
			$p->add('View')->setHTML($tnc_m['content']);
		});
		
		$g->on('click','.do-view-tnc-detail',function($js,$data)use($vp){
			return $js->univ()->frameURL("TERMS AND CONDITIONS",$this->api->url($vp->getURL(),['tnc_id'=>$data['id']]));
		});
	}
}  