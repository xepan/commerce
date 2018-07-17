<?php

namespace xepan\commerce;

class page_correction_saleinvoicetransactionupdate extends \xepan\base\Page {
	public $title='Sale Invoice Transaction Update';


	function init(){
		parent::init();

		ini_set("memory_limit", "-1");
		set_time_limit(0);

		$this->invid = $this->app->stickyGET("invid")?:0;

		$b = $this->add('Button')->set('Update All Invoice Transaction');
		$b->add('VirtualPage')
		->bindEvent('Updating Invoice Transaction','click')
		->set(function($page){

			$page->add('View_Console')->set(function($c){
				$c->out('Collecting Invoice Data');
				$si_model = $this->add('xepan\commerce\Model_SalesInvoice');
				$si_model->setOrder('id','asc');
				$si_model->addCondition('status',['Due','Paid']);

				if($siid = $this->app->recall('siid',0)){
					$si_model->addCondition('id','>=',$siid);
				}

				if($this->invid)
					$si_model->addCondition('id',$this->invid);

				$c->out('Total Invoice:'.$si_model->count()->getOne());
				$c->out('Please Wait ...<i class=" fa fa-cog fa-spin"></i>');
				$count = 1;
				$str = "";
				foreach ($si_model as $model) {
					try{
						$model->save();
						$str .= $model['document_no'].",";

						if( $count%10 == 0 || $this->invid){
							$c->out('Invoice Updated: '.$count." ids are: ".$str);
							$c->out('Please Wait ... <i class=" fa fa-spinner fa-spin"></i>');
							$str = "";
						}

						$count++;

						if(!$this->invid)
							$this->app->memorize('siid',$model->id);
					}catch(\Exception $e){
						throw $e;
					}
				}

				$c->out('Complete');
			});

		});

	}
}
