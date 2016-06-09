<?php  

/**
* description: ATK Page
* 
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/ 

 namespace xepan\commerce;

 class page_itemtemplate extends \xepan\base\Page {
	public $title='Item Template';
	public $breadcrumb=['Home'=>'index','Items'=>'xepan_commerce_item','Templates'=>'#'];

	function init(){
		parent::init();
		
		$vp = $this->add('VirtualPage');
		$vp->set(function($p){
			if(!($template_id = $this->api->stickyGET('template_id'))) {
				$p->add('View_Error')->set('No Template ID found');
				return;
			}

			$template_item_m = $this->add('xepan\commerce\Model_Item')->load($template_id);
			$result = $template_item_m->page_duplicate($p,$acl=false);
			if($result){
				if($result instanceof \jQuery_Chain){
					$js=[];
					$js[]=$p->js()->univ()->closeDialog();
					$p->js(null,$js)->execute();
				}
			}
		});

		$vp_url = $vp->getURL();

		$item = $this->add('xepan\commerce\Model_Item');
		$item->addCondition('is_template',true);
		// $item->addCondition('is_designable',true);
		
		$lister = $this->add('CompleteLister',null,null,['page/item/template']);
		$lister->setModel($item);
		$lister->add('QuickSearch',null,'quick_search')->useWith($lister)->useFields(['name']);

		$lister->on('click','.duplicate-btn',function($js,$data)use($vp,$vp_url){
			return $js->univ()->frameURL('Duplicate',$this->app->url($vp_url,['template_id'=>$data['id']]));
		});

		$lister->on('click','.btn-empty',function($js,$data){
			return $js->univ()->redirect($this->app->url('xepan_commerce_itemdetail',['action'=>'add']));
		});

		$lister->on('click','.btn-template',function($js,$data){
			return $js->univ()->redirect($this->app->url('xepan_commerce_itemdetail',['action'=>'add','new_template'=>true]));
		});		
	}
}


