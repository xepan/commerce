<?php

namespace xepan\commerce;

class page_test1 extends \xepan\base\Page{
    public $title = "Test Page";

    function init(){
        parent::init();


        $model = $this->add('xepan\commerce\Model_Test1');
        $grid = $this->add('Grid');
        $grid->setModel($model);





   /*     $cols = $this->add('Columns')->addClass('well');
        $col1 = $cols->addColumn(4)->addClass('bg bg-danger');
        $col2 = $cols->addColumn(4)->addClass('bg bg-success');
        $col3 = $cols->addColumn(4);

        $col1->add('View')->setHtml('<b>Col 1</b>');
        $col1->add('View')->setHtml('<b>Col 1 sds </b>');

        $col2->add('View')->setHtml('<b>Col 2</b>');
        $col3->add('View')->setHtml('<b>Col 3</b>');
        // $this->js(true)->univ()->successMessage('hello');

        $rand_view = $col2->add('View')->set(rand(1000,9999))->addClass('alert alert-danger');

        $btn = $col2->add('Button');
        $btn->setLabel('Rakesh');
        $btn->js('click',$rand_view->js()->reload())->univ()->alert('clicked');

        $form = $col3->add('Form');
        $form->add('xepan\base\Controller_FLC')
            ->showLables(true)
            ->addContentSpot()
            ->makePanelsCoppalsible(true)
            ->layout([
                'full_name~Full Name Lable'=>'Your Detail|success~c1~6',
                'dob~DOB'=>'c2~6',
                'Gender~Gender'=>'Detail~c1~6',
                'radio~radio'=>'c2~6',

            ]);

        $name_field = $form->addField('Line','full_name');
        $name_field->validate('required');
        $name_field->setFieldHint('Enter your full name');
        $name_field->setAttr('PlaceHolder','place holder');

        $form->addField('DatePicker','dob');
        $form->addField('DropDown','Gender')->setValueList(['Male'=>'Male','Female'=>'Female'])->setEmptyText('Please Select');
        $form->addField('xepan\base\Basic','g');
        $form->addField('radio','radio')->setValueList(['Male'=>'Male','Female'=>'Female']);//->setEmptyText('Please Select');;
        $form->addField('checkbox','is_active');
        $form->addField('text','message');
        $form->addField('xepan\base\RichText','rich');

        $form->addSubmit('Submit')->addClass('btn btn-primary');
        if($form->isSubmitted()){
            // throw new \Exception($form['full_name']);
            // 

            $form->js(null,$form->js()->reload())->univ()->successMessage('Done')->execute();
        }
    */



    }
}