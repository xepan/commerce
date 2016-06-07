<?php

namespace xepan\commerce;

class View_EasySetupWizard extends \View{
	function init(){
		parent::init();

		if($_GET[$this->name.'_set_tax']){
			$this->js(true)->univ()->frameURL("Taxation System",$this->app->url('xepan_commerce_tax'));
		}

		$isDone = false;

		$action = $this->js()->reload([$this->name.'_set_tax'=>1]);

			if($this->add('xepan\commerce\Model_TaxationRule')->count()->getOne() > 0){
				$isDone = true;
				$action = $this->js()->univ()->dialogOK("Already have Data",' You have already set taxation rules, visit page ? <a href="'. $this->app->url('xepan_commerce_tax')->getURL().'"> click here to go </a>');
			}

		$tax_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Specify The Tax')
			->setMessage('Specify the tax to particular item/product, and add taxes according to norms of organization')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		

		if($_GET[$this->name.'_set_item']){
			$this->js(true)->univ()->frameURL("Products",$this->app->url('xepan_commerce_item'));
		}

		$isDone = false;
		
		$action = $this->js()->reload([$this->name.'_set_item'=>1]);

			if($this->add('xepan\commerce\Model_Item')->count()->getOne() >0){
				$isDone = true;
				$action = $this->js()->univ()->dialogOK("Already have Data",' You have already added item, visit page ? <a href="'. $this->app->url('xepan_commerce_item')->getURL().'"> click here to go </a>');
			}

		$product_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Products/Item')
			->setMessage('Please add any product/item i.e. according organization')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
	}
}