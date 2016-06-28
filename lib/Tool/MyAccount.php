<?php 

namespace xepan\commerce;

class Tool_MyAccount extends \xepan\cms\View_Tool{
    public $options = [
        'layout'=>'myaccount',
        'custom_template'=>''
    ];
	function init(){
		parent::init();

        $message = $this->validateRequiredOption();
        if($message){
            $this->add('View_Warning',null,'no_auth_message')->set($message);
            $this->template->tryDel('myaccount_container_wrapper');
            return;
        }

        //check authentication
        if(!$this->app->auth->model->loaded()){            
            if($this->options['xepan_commerce_login_page']){                
                $this->app->memorize('next_url',$_GET['page']);
                $this->app->redirect($this->app->url($this->options['xepan_commerce_login_page']));
            }else{
                $this->add('View_Warning',null,'no_auth_message')->set('Login First');
            }

            $this->template->tryDel('myaccount_container_wrapper');
            return;
        }
        
        $this->app->stickyGET('selectedmenu');
        $customer = $this->add('xepan\commerce\Model_Customer');
        $customer->loadLoggedIn();

        //check customer is loaded
        if(!$customer->loaded()){
            $this->add('View_Info',null,'no_auth_message')->set('customer account not found')->addClass('jumbotron well text-center row alert alert-info h3');
            $this->template->tryDel('myaccount_container_wrapper');            
            return;            
        }

        $this->setModel($customer);
        //adding avtar
        $this->add('xepan\base\Controller_Avatar',['options'=>['size'=>20,'border'=>['width'=>0]],'name_field'=>'name','default_value'=>'']);
    }

    function setModel($model){

        //action menu item
        $myaccount_btn = $this->add('View',null,'myaccount')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('My Account')->setAttr('data-type','myaccount');
        $order_btn = $this->add('View',null,'order')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('Order History')->setAttr('data-type','order');
        $mydesign_btn = $this->add('View',null,'mydesign')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('My Designs')->setAttr('data-type','mydesign');
        $mytemplate_btn = $this->add('View',null,'mytemplate')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('My Templates')->setAttr('data-type','mytemplate');
        $setting_btn = $this->add('View',null,'setting')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('Settings')->setAttr('data-type','setting');

        //Default selected Menu
        
        if( !($selected_menu = $this->app->stickyGET('selectedmenu')))
            $selected_menu = 'myaccount';
        
        ${$selected_menu."_btn"}->addClass('active');
        //My Account Info
        if( $selected_menu == "myaccount"){
            //remove extra tab spot
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('mytemplate_wrapper');

            //all email set at spot emails and lister template define at  email layout
            if(!$model->ref('Emails')->count()->getOne()){
                $this->template->tryDel('email_wrapper');
            }else{
                $email_lister = $this->add('CompleteLister',null,'emails',['view\\tool\\'.$this->options['layout'],'email_layout']);
                $email_lister->setModel($model->ref('Emails'));
            }

            if(!$model->ref('Phones')->count()->getOne()){
                $this->template->tryDel('Contact_wrapper');
            }else{
                $contact_lister = $this->add('CompleteLister',null,'contacts',['view\\tool\\'.$this->options['layout'],'contact_layout']);
                $contact_lister->setModel($model->ref('Phones'));
            }
                
            //Recent Order
            $recent_order = $this->add('xepan\commerce\Model_SalesOrder')->addCondition('contact_id',$model->id)->setOrder('id','desc')->setLimit(5);
            $this->add('xepan\base\Grid',null,'recentorder',['view/tool/myaccount-resent-order'])->setModel($recent_order,['document_no','created_at','total_amount','gross_amount','net_amount']);
            
        }elseif($selected_menu == "order"){
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('mytemplate_wrapper');
            
            $order = $this->add('xepan\commerce\Model_SalesOrder')
                        ->addCondition('contact_id',$model->id)
                        ->setOrder('id','desc');
            $this->add('xepan\base\Grid',null,'order_history',['view/tool/myaccount-resent-order'])->setModel($order,['document_no','created_at','total_amount','gross_amount','net_amount']);
        
        }elseif($selected_menu == "mydesign"){
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('mytemplate_wrapper');

            // my_designs
            $this->add('xepan/commerce/View_CustomerDesign',array('options'=>$this->options),'my_designs');
        
        }elseif($selected_menu == "setting"){
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('mytemplate_wrapper');

            $this->add('xepan\commerce\View_MyAccountSetting',array('options'=>$this->options),'settings');

        }elseif($selected_menu == "mytemplate"){
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('setting_wrapper');

            $this->add('xepan/commerce/View_CustomerTemplate',array('options'=>$this->options),'my_templates');
        }
        
        $this_url = $this->api->url(null,['cut_object'=>$this->name]);
       
        // //Js For Reloading the Right Column and passed the type valued
        $this->on('click','button.xepan-commerce-myaccount-action',function($js,$data)use($this_url){
            $js = [
                    $this->js()->reload(['selectedmenu'=>$data['type']],null,$this_url),
                    $js->addClass('active')
                ];
            return $js;
        });

        parent::setModel($model);
    }

	function defaultTemplate(){  
        $template_name =  $this->options['layout'];

        if($this->options['custom_template']){
            $path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/".$this->options['custom_template'].".html";
            if(file_exists($path)){   
                $template_name = $this->options['custom_template'];
            }
        }
		return["view/tool/".$template_name];
	}

    function validateRequiredOption(){
        if($this->options['custom_template']){
            $path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/".$this->options['custom_template'].".html";
            if(!file_exists($path)){
                return "custom template not found";
            }
        }

        return 0;
    }
}


