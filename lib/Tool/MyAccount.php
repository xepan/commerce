<?php 

namespace xepan\commerce;

class Tool_MyAccount extends \xepan\cms\View_Tool{
    public $options = [
        'keep-login-on-password-change'=>true,
        'show_my_template'=>true,
        'show_duplicate_form'=>true,
        'show_empty_category'=>true,
        'layout'=>'myaccount',
        'custom_template'=>'',
        'customer-setting-layout'=>'myaccountsetting',
        'designer-page'=>'designs',
        'xepan_commerce_login_page'=>null,
        'designer-page'=>"designs",
        'customer-design-grid-layout'=>"customerdesign",
        'customer-template-grid-layout'=>"customertemplate",
        'customer-setting-layout'=>"myaccountsetting",
        'show_wishlist'=>true,
        'product-detail-page'=>null,
        'wish_list_status'=>'Due',
        'show_support_ticket'=>false
    ];
	function init(){
		parent::init();
        
        $this->options['login-page'] = $this->options['xepan_commerce_login_page'];
        
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
        $this->customer = $customer = $this->add('xepan\commerce\Model_Customer');
        $customer->loadLoggedIn("Customer");

        //check customer is loaded
        if(!$customer->loaded()){
            $this->add('View_Info',null,'no_auth_message')->set('customer account not found')->addClass('jumbotron well text-center row alert alert-info h3');
            $this->template->tryDel('myaccount_container_wrapper');            
            return;            
        }

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

        $this->js('click',$this->js()->univ()->dialogURL("CHANGE IMAGE",$this->api->url($vp->getURL())))->_selector('.myaccount-user-image');
        
        $print_url = $this->api->url('xepan_commerce_orderdetailprint');        
        $this->on('click','.xepan-customer-order-detail',function($js,$data)use($print_url){
            return $js->univ()->newWindow($print_url."&document_id=".$data['id']);
        });
    }

    function render(){
        $this->app->addStyleSheet('jquery-ui');
        return parent::render();
    }

    function setModel($model){

        //action menu item
        $myaccount_btn = $this->add('View',null,'myaccount')->setElement('a')->setAttr('data-type','myaccount')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'myaccount']))->set('My Account');
        $order_btn = $this->add('View',null,'order')->setElement('a')->setAttr('data-type','order')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'order']))->set('Order History');
        $mydesign_btn = $this->add('View',null,'mydesign')->setElement('a')->setAttr('data-type','mydesign')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'mydesign']))->set('My Design');
        $mytemplate_btn = $this->add('View',null,'mytemplate');
        if($this->options['show_my_template']){
            $mytemplate_btn->setElement('a')->setAttr('data-type','mytemplate')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'mytemplate']))->set('My Template');
        }else{
            $this->template->tryDel('mytemplate_bar');
        }
        $setting_btn = $this->add('View',null,'setting')->setElement('a')->setAttr('data-type','setting')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'setting']))->set('Settings');
        
        $wishlist_btn = $this->add('View',null,'wishlist');
        $supportticket_btn = $this->add('View',null,'supportticket');

        if($this->options['show_wishlist']){
                    $wishlist_btn->setElement('a')->setAttr('data-type','Your Wishlist')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'wishlist']))->set('Your Wishlist');
                }else{
                    $this->template->tryDel('wishlist_bar');
                }

        if($this->options['show_support_ticket']){
            $supportticket_btn = $this->add('View',null,'supportticket');
            $supportticket_btn->setElement('a')->setAttr('data-type','Support Ticket')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->setAttr('href',$this->app->url(null,['selectedmenu'=>'supportticket']))->set('Support Ticket');
        }else{
            $this->template->tryDel('support_ticket_wrapper');
        }
        // $mydesign_btn = $this->add('View',null,'mydesign')->setElement('a')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('My Designs')->setAttr('data-type','mydesign');
        // $mytemplate_btn = $this->add('View',null,'mytemplate')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('My Templates')->setAttr('data-type','mytemplate');
        // $setting_btn = $this->add('View',null,'setting')->setElement('button')->addClass('xepan-commerce-myaccount-action btn btn-block btn-primary')->set('Settings')->setAttr('data-type','setting');

        $this->js(true)->_selector(".xepan-commerce-myaccount-action[data-type='".$_GET['selectedmenu']."']")->closest('div')->addClass('active');
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
            $this->template->tryDel('wishlist_wrapper');
            $this->template->trydel('support_ticket_wrapper');
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
            $this->template->tryDel('wishlist_wrapper');
            $this->template->trydel('support_ticket_wrapper');

            $order = $this->add('xepan\commerce\Model_SalesOrder')
                        ->addCondition('contact_id',$model->id)
                        ->setOrder('id','desc');

            $order->addExpression('is_invoice_paid')->set(function($m,$q){
                $invoice_m = $this->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'related_invoice_paid'])
                            ->addCondition('related_qsp_master_id',$q->getField('id'))
                            ->addCondition('status','Due');
                return $q->expr('IFNULL([0],0)',[$invoice_m->count()]);
            })->type('boolean');

            $order->addExpression('invoice_status')->set(function($m,$q){
                $invoice_m = $this->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'related_invoice'])
                            ->addCondition('related_qsp_master_id',$q->getField('id'));
                return $invoice_m->fieldQuery('status');
            });

            $order_grid = $this->add('xepan\base\Grid',null,'order_history',['view/tool/myaccount-resent-order']);
            
            $pay_now = $order_grid->addColumn('pay_now');

            $order_grid->addHook('formatRow',function($g){
                $link = "paid";
                if($g->model['invoice_status']=='Due'){
                    $payment_step_url = $this->app->url('checkout',array('step'=>"Payment",'order_id'=>$g->model->id));
                    $link = '<a class="btn btn-primary" target="_blank" href="'.$payment_step_url.'">Pay Now</a>';
                    $g->current_row_html['pay_now'] =  $link;
                }else{
                    $g->current_row_html['pay_now']=$g->model['invoice_status'];
                }
                
                // if($_GET['pay_now']){
                //     $sale_order = $this->add('xepan\commerce\Model_SalesOrder')->load($_GET['pay_now']);
                //     $this->app->memorize('checkout_order',$sale_order);

                //     $this->app->redirect($payment_step_url);
                // }
            });

            $order_grid->setModel($order,['document_no','created_at','total_amount','gross_amount','net_amount','status','is_invoice_paid','invoice_status']);
            $order_grid->addQuickSearch(['document_no']);
            

        }elseif($selected_menu == "mydesign"){
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('mytemplate_wrapper');
            $this->template->tryDel('wishlist_wrapper');
            $this->template->tryDel('support_ticket_wrapper');

            // my_designs
            $this->add('xepan/commerce/View_CustomerDesign',array('options'=>$this->options),'my_designs');
        
        }elseif($selected_menu == "setting"){
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('mytemplate_wrapper');
            $this->template->tryDel('wishlist_wrapper');
            $this->template->tryDel('support_ticket_wrapper');

            $this->add('xepan\commerce\View_MyAccountSetting',array('options'=>$this->options),'settings');

        }elseif($selected_menu == "mytemplate"){
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('wishlist_wrapper');
            $this->template->tryDel('support_ticket_wrapper');


            $this->add('xepan/commerce/View_CustomerTemplate',array('options'=>$this->options),'my_templates');
        }elseif($selected_menu == "wishlist"){
            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('mytemplate_wrapper');
            $this->template->tryDel('support_ticket_wrapper');

            $this->add("xepan\commerce\View_Wishlist",['customer_id'=>$this->customer->id,'detail_page'=>$this->options['product-detail-page'],'show_status'=>$this->options['wish_list_status']]);

        }elseif($selected_menu == "supportticket"){

            $this->template->tryDel('mydesign_wrapper');
            $this->template->tryDel('order_wrapper');
            $this->template->tryDel('myaccount_wrapper');
            $this->template->tryDel('setting_wrapper');
            $this->template->tryDel('mytemplate_wrapper');
            $this->template->tryDel('wishlist_wrapper');

            // if application is installed 
            $installed_app_model = $this->add('xepan\base\Model_Epan_InstalledApplication');
            $installed_app_model->addCondition('application_namespace','xepan\crm')
                        ->addCondition('is_active',true)
                        ->addCondition('is_valid',true);
            $installed_app_model->tryLoadAny();
            if($installed_app_model->loaded())
                $this->add("xepan\crm\Tool_SupportTicket",null,'view_support_ticket');
            else
                $this->add("View",null,'view_support_ticket')->set('CRM Application is not installed')->addClass('alert alert-warning');

        }
        
        $this_url = $this->api->url(null,['cut_object'=>$this->name]);
       
        //Js For Reloading the Right Column and passed the type value      

        // return $this->js('click')->_selector('.button.xepan-commerce-myaccount-action')->univ()
        //             ->location($this->app->url(null,['selectedmenu'=>$data['type']]));    
        
        $this->template->trySet('member_address',empty($model['address'])?"Update Your information":$model['address']);
        $this->template->trySet('member_billing_address',empty($model['billing_address'])?"Update Your information":$model['billing_address']);
        $this->template->trySet('member_shipping_address',empty($model['shipping_address'])?"Update Your information":$model['shipping_address']);
        
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


