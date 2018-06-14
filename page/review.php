<?php

namespace xepan\commerce;

class page_review extends \xepan\base\Page{
    public $title = "review";

    function init(){
        parent::init();


        $model = $this->add('xepan\commerce\Model_Review');
        $crud = $this->add('xepan\hr\CRUD');
        if($crud->isEditing()){
        	$form = $crud->form;
        	$form->add('xepan\base\Controller_FLC')
        		->showLables(true)
        		->addContentSpot()
        		->layout([
        				'customer_id'=>'Details~c1~6',
        				'created_at'=>'c2~6',
        				'related_type'=>'c3~6',
        				'related_document_id'=>'c4~6',
        				'review'=>'Review & Rating ~c5~12',
        				'rating'=>'c6~12',
        				'status'=>'c7~4',
        				'approved_by'=>'c8~4',
        				'approved_at'=>'c9~4',
        				// 'FormButtons~&nbsp;'=>'c10~12'
        		]);

        }


        $crud->setModel($model);
        $crud->grid->addFormatter('customer_profile_image','image');
        $crud->grid->removeAttachment();
    }
}
