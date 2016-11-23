<?php

namespace xepan\commerce;

class Widget_FavouriteItem extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->grid = $this->add('xepan\hr\Grid',null,null,['view\dashboard\favitem']);
	}

	function recursiveRender(){
		$detail = $this->add('xepan\commerce\Model_QSP_Detail');
		$detail->addExpression('doc_type')->set(function($m,$q){
			return $m->refSQL('qsp_master_id')->fieldQuery('type');
		});

		$detail->addExpression('from')->set(function($m,$q){
			return $m->refSQL('qsp_master_id')->fieldQuery('from');
		});

		$detail->addCondition('doc_type','SalesOrder');
		$detail->addCondition('from','Online');

		$detail->addExpression('count','count(*)');
		$detail->_dsql()->group('item_id');
		$detail->setOrder('count','desc');
		$detail->setLimit(5);
		
		$this->grid->setModel($detail,['item','count']);
		
		return parent::recursiveRender();
	}
}