<?php 

namespace xepan\commerce;

class Tool_MyAccount extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();

		$customer = $this->add('xepan\commerce\Model_Customer');
		$customer->load($_GET['id']);
		
		//Adding Two view Left and Right
		$left = $this->add('View',null,'left');
		$right = $this->add('View',null,'right');

		//Left Menu bar Buttons 
		$left->add('View')->setElement('button')->addClass('list-group-item atk-swatch-yellow')->set('My Account')->setAttr('data-type','myaccount')->setStyle('padding','10px !important');
        $left->add('View')->setElement('button')->addClass('list-group-item ')->set('Order History')->setAttr('data-type','order')->setStyle('padding','10px !important');
        $left->add('View')->setElement('button')->addClass('list-group-item ')->set('My Designs')->setAttr('data-type','mydesign')->setStyle('padding','10px !important');
        $left->add('View')->setElement('button')->addClass('list-group-item ')->set('Settings')->setAttr('data-type','setting')->setStyle('padding','10px !important');

        //Default selected Menu
        $selected_menu = 'myaccount';
        
        //My Account Info
        if($this->api->stickyGET('selectedmenu'))
            $selected_menu = $this->api->stickyGET('selectedmenu');

        if( $selected_menu == "myaccount"){
            $right->add('H2','heading')->set('Account Information')->setStyle(array('border-bottom'=>'2px solid #f2f2f2','padding-bottom'=>'10px'));
            $right->add('H2')->set($customer['name']);
            $right->add('view')->setElement('p')->set(" ".$customer['Email'])->addClass('icon-mail');
            $right->add('view')->setElement('p')->set(" ".($customer['Phones']?:'Not Added') )->addClass('icon-phone');

            $c = $right->add('Columns');
            $col_1 = $c->addColumn(4);
            $col_2 = $c->addColumn(4);
            $col_3 = $c->addColumn(4);

            //Permanent Address
            $col_1->add('H4')->set('Permanent Address')->addClass('bg-primary xepan-push-large');
            $col_1->add('View')->setElement('p')->set(($member['address']?:"Not Added"))->addClass('xepan-push-large');
           
            //Recent Order
            $right->add('H2')->set('Recent Order');
            $right->add('Grid')->setModel('xepan\commerce\SalesOrder',['document_no','created_at','total_amount','gross_amount','net_amount']);
        }
            
        elseif($selected_menu == "order"){
        // $this->template->trySet('heading','Order History');
        $right->add('H2','heading')->set('Order History')->setStyle(array('border-bottom'=>'2px solid #f2f2f2','padding-bottom'=>'10px'));
        // $right->add('xShop/View_MemberOrder',['ipp'=>10,'gridFields'=>['name','created_date','total_amount','gross_amount','tax','net_amount']]);
		}
        
        elseif($selected_menu == "mydesign"){
            $right->add('H2','heading')->set('My Designs')->setStyle(array('border-bottom'=>'2px solid #f2f2f2','padding-bottom'=>'10px'));
            // $right->add('xShop/View_MemberDesign',array('designer_page'=>$this->html_attributes['xsnb-desinger-page']));
		}

        elseif($selected_menu == "setting"){
            $right->add('H2','heading')->set('Settings')->setStyle(array('border-bottom'=>'2px solid #f2f2f2','padding-bottom'=>'10px'));
            $right->add('xShop/View_MemberAccountInfo');
        }

        // $right->add('View')->set($_GET['type1']);

        // $right_url = $this->api->url(null,['cut_object'=>$right->name]);
       

        //Js For Reloading the Right Column and passed the type valued
        $left->on('click','button',
            [
                $left->js()->find('.atk-swatch-yellow')->removeClass('atk-swatch-yellow'),
                $right->js()->reload(['selectedmenu'=>$this->js()->_selectorThis()->attr('data-type'),'designer_page'=>$this->html_attributes['xsnb-desinger-page']]),
                $this->js()->_selectorThis()->addClass('atk-swatch-yellow'),
            ]
            );



            $tab = $right->add('Tabs'/*,null,null,['view/tabs_vertical']*/)->addClass('nav-stacked');
            // Account Information
            // $s = $tab->addTabUrl('xShop/page/owner_member_accountinfo','Settings');
            // MEMBER ORDER tab
            // $tab->addTabUrl('xShop/page/owner_member_order','Order');
            // MEMBER DESIGNS
            // $tab->addTabUrl($this->api->url('xShop/page/owner_member_design',array('designer_page'=>$this->html_attributes['xsnb-desinger-page'])),'Designs');

	}

	function defaultTemplate(){
		return['view\tool\myaccount'];
	}
}


