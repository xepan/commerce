<?php

namespace xepan\commerce;
class View_ItemType extends \View{

    public $title='Item Type';
    
    function init(){
        parent::init();

        // $this->add('View_Info')->set('Item');

    }
    
    function defaultTemplate(){
    	return ['view/tool/itemview'];
    }

}
