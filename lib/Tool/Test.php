<?php

namespace xepan\commerce;

class Tool_Test extends \View{
    public $options = [];
	function init(){
		parent::init();

		$selected_menu = $this->app->stickyGET('selectedmenu')?:'myaccount';

		$customer = $this->add('xepan\commerce\Model_Customer');
		$customer->tryLoadAny();
		
		$btn_myacc = $this->add('Button',null,'myaccount')->set('My Account');
		$btn_ordr = $this->add('Button',null,'orderhistory')->set('Order History');
		$btn_mydsgn = $this->add('Button',null,'mydesign')->set('My Design');
		$btn_stng = $this->add('Button',null,'settings')->set('Settings');
		
		$btn_myacc->onClick(function($btn){
			return $btn->js()->univ()->redirect($this->app->url('/',['selectedmenu'=>'myaccount']));
		});

		$btn_ordr->onClick(function($btn){
			return $btn->js()->univ()->redirect($this->app->url('/',['selectedmenu'=>'orderhistory']));
		});

		$btn_mydsgn->onClick(function($btn){
			return $btn->js()->univ()->redirect($this->app->url('/',['selectedmenu'=>'mydesign']));
		});

		$btn_stng->onClick(function($btn){
			return $btn->js()->univ()->redirect($this->app->url('/',['selectedmenu'=>'settings']));
		});

		if($selected_menu == 'myaccount'){	
			$myaccount = $this->add('xepan\commerce\Tool_Accountviews_Myaccount',null,'sideview');
			$myaccount->template->trySet('name',$customer['name']);
			
			$email = $customer->ref('Emails')->fieldQuery('value')->getOne();
			$myaccount->template->trySet('email',$email);
			
			$contact = $customer->ref('Phones')->fieldQuery('value')->getOne();
			$myaccount->template->trySet('contact',$contact);

			$myaccount->template->trySet('address',$customer['address']);

			$order_grid = $myaccount->add('xepan\hr\Grid',null,'grid',['view\tool\accountmain_grid']);
			$order_grid->setModel('xepan\commerce\SalesOrder',['document_no','created_at','total_amount','gross_amount','net_amount'])->setLimit(5)->setOrder('created_at','desc');
		}

		if($selected_menu == 'orderhistory'){	
			$orderhistory = $this->add('xepan\hr\Grid',null,'sideview',['view\tool\accountmain_grid']);

			$orderhistory->setModel('xepan\commerce\SalesOrder',['document_no','created_at','total_amount','gross_amount','net_amount'])->setOrder('created_at','desc');
		}
		
		if($selected_menu == 'mydesign'){	
			$mydesign = $this->add('xepan\commerce\Tool_Accountviews_Mydesign',null,'sideview');
		}
		
		if($selected_menu == 'settings'){	
			$settings = $this->add('xepan\commerce\Tool_Accountviews_Settings',null,'sideview');
		}

			

	}

	function defaultTemplate(){
		return['view\tool\accountmain'];
	}
}		