<?php

namespace xepan\commerce;

class page_review extends \xepan\base\Page{
    public $title = "review";

    function init(){
        parent::init();


        $model = $this->add('xepan\commerce\Model_Item_Review');

        $crud = $this->add('xepan\hr\CRUD');
        if($crud->isEditing()){
        	$form = $crud->form;
        	$form->add('xepan\base\Controller_FLC')
        		->showLables(true)
        		->addContentSpot()
        		->layout([
        				'customer_id'=>'Details~c1~4',
        				'created_at'=>'c2~4',
        				'related_document_id~Item/Product'=>'c4~4',
        				'name~Title'=>'Review & Rating ~c5~12',
        				'review'=>'c6~12',
                        'rating'=>'c7~3',
        				'status'=>'c8~3',
        				'approved_by_id'=>'c9~3',
        				'approved_at'=>'c10~3',
        				// 'FormButtons~&nbsp;'=>'c11~3'
        		]);

        }
        $model->setOrder('id','desc');
        $model->add('xepan\base\Controller_TopBarStatusFilter');

        $crud->setModel($model,null,['customer_profile_image','customer','created_at','name','review','rating','related_type','related_document_id','related_document_name','approved_by','approved_at','status']);
        $crud->grid->addQuickSearch(['customer','name','review']);
        $crud->grid->addPaginator(25);
        $crud->grid->addFormatter('customer_profile_image','image');
        $crud->grid->removeAttachment();
        $crud->grid->removeColumn('created_by');
        $crud->grid->removeColumn('status');
        $crud->add('xepan\base\Controller_Avatar');

    }
}
