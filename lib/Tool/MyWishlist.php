<?php

namespace  xepan\commerce;
class Tool_MyWishlist extends \xepan\cms\View_Tool{
	public $options = [
		'product-detail-page'=>null,
        'wish_list_status'=>'Due',
        'paginator' => 10
		];

	function init(){
	parent::init();

    	    $this->app->stickyGET('selectedmenu');
            $this->customer = $customer = $this->add('xepan\commerce\Model_Customer');
            $customer->loadLoggedIn("Customer");

    			/*if(!$customer->loaded()){
    			   $this->add('View_Info',null,'no_auth_message')->set('customer account not found')->addClass('jumbotron well text-center row alert alert-info h3');
    			    $this->template->tryDel('myaccount_container_wrapper');            
                return;            
            }*/

            $this->setModel($customer);
            $self_url = $this->app->url();
            $vp = $this->add('VirtualPage');
            $vp->set(function($p)use($customer,$self_url){                       
                $f = $p->add('Form',null,null,['form\empty']);
                $f->setModel($customer,['image_id','image']);
                
                if($f->isSubmitted()){
                    $f->save();
                    return $f->app->redirect($self_url);
                }
            });
    		$customer = $this->add("xepan\commerce\View_Wishlist",
                ['customer_id'=>$this->customer->id,
                'detail_page'=>$this->options['product-detail-page'],
                'show_status'=>$this->options['wish_list_status'],'paginator' =>$this->options['paginator']
            ]);
	   }
}
	
