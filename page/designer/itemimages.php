<?php

namespace xepan\commerce;

class page_designer_itemimages extends \Page {


  function page_index(){
      $folder_wrapper = $this->add('View',null,'category_lister');
      $image_wrapper = $this->add('View',null,'images_lister');

      $contact = $this->add('xepan\base\Model_Contact');
      $contact->loadLoggedIn("Customer");

      $category_id = $this->app->stickyGET('image_folder_id');
      if(!$category_id ){
        $category = $this->add('xepan\commerce\Model_Designer_Image_Category');
        $category->addCondition('contact_id',$contact->id);
        $category->addCondition('name','Default');
        $category->tryLoadAny();
        if(!$category->loaded())
          $category->save();
        $category_id = $category->id;
      }

      if(!$contact->loaded()){
        $c = $this->add('Columns',null,'tool_user_login')->addClass('row');
        $l = $c->addColumn(4)->addClass('col-md-3');
        $m = $c->addColumn(4)->addClass('col-md-6');
        $r = $c->addColumn(4)->addClass('col-md-3');
        $m->add('xepan\base\Tool_UserPanel',['reload_object'=>$this]);
        // $this->template->set('image_wrapper',"");
        $this->template->tryDel('my_image_wrapper');
        $this->js(true)->hide()->_selector('.xepan-designer-images-addimagebutton-spot button');
        // return;
      }else{
        /******** C A T E G O R Y ********/
        $cat_model = $folder_wrapper->add('xepan\commerce\Model_Designer_Image_Category')
                      ->addCondition('is_library',false)
                      ->addCondition('contact_id',$contact->id);
        $cat_crud = $folder_wrapper->add('xepan\base\CRUD',['entity_name'=>'Folder'],null,['view\designer\managecategory-grid']);
        $cat_crud->frame_options=['width'=>500];
        $cat_crud->setModel($cat_model);
        $cat_crud->grid->addQuickSearch(['name']);
        
        /*********** I M A G E ***********/
        $image_model = $image_wrapper->add('xepan\commerce\Model_Designer_Images');
        $image_model->addCondition('contact_id',$contact->id);
        if($category_id)
          $image_model->addCondition('designer_category_id',$category_id);

        $image_model->setOrder('id','desc');
        $image_crud = $image_wrapper->add('xepan\base\CRUD',['entity_name'=>'Image','allow_edit'=>false,'grid_options'=>['paginator_class'=>'Paginator']],null,['view/designer/designer-item-grid']);
        $image_crud->frame_options=['width'=>500];
        $image_crud->setModel($image_model,['image_id'],['image']);
        $image_crud->grid->addPaginator(50);
        // $image_crud->grid->js()->trigger('reload')->univ()->alert('vijkay');
        $image_crud->grid->js(true)->_load('designer-images-insert')->univ()->makeInsertBtn();
        
        // onclick on button call image crud button
        $this->js('click',$this->js()->find('.xepan-designer-images-addimagebutton button')->click())->_selector('.xepan-designer-images-addimagebutton-spot button');
        // filter image according to category
        $img_url = $this->app->url(null,['cut_object'=>$image_wrapper->name]);
        $cat_url = $this->app->url(null,['cut_object'=>$folder_wrapper->name]);

        // //Jquery For Filter the images
        $cat_crud->on('click','li',function($js,$data)use($img_url,$cat_crud,$image_crud){
          return [
              $cat_crud->js()->find('.list-group-item')->removeClass('image-category-active'),
              $image_crud->js()->reload(['image_folder_id'=>$data['id']],null,$img_url),
              $js->addClass('image-category-active'),
            ] ;
        });
      }


      /*Library Images section Start*/
      $cat_grid = $this->add('xepan\base\Grid',['entity_name'=>'Library Category'],'library_cat',['view\designer\managecategory-grid']);
      $cat_grid->frame_options = ['width'=>'500'];
      $lib_cat_model = $this->add('xepan\commerce\Model_Designer_Image_Category')->addCondition('is_library',true);
      $cat_grid->setModel($lib_cat_model,array('name'));

      
      //Setting up Model according to the Category id
      $lib_image_model = $this->add('xepan\commerce\Model_Designer_Images');
      $lib_image_model->addExpression('library_category')->set($lib_image_model->refSQL('designer_category_id')->fieldQuery('is_library'));
      $lib_image_model->addCondition('library_category',true);
      $lib_image_model->setOrder('id','desc');
      
      if($cat_id = $this->app->stickyGET('category_id')){
        $lib_image_model->addCondition('designer_category_id',$cat_id);        
      }

      // //Member Image Crud
      $image_grid = $this->add('xepan\base\Grid',['entity_name'=>'Images'],'library_img',['view/designer/designer-item-grid']);
      $image_grid->frame_options = ['width'=>'500'];
      $image_grid->setModel($lib_image_model);
      $image_grid->addPaginator(20);
      $image_grid->addQuickSearch(['image','description']);
      
      $lib_cat_url = $this->app->url(null,['cut_object'=>$cat_grid->name]);
      $lib_image_url=$this->app->url(null,['cut_object'=>$image_grid->name]);
      
      $cat_grid->on('click','li',function($js,$data)use($lib_image_url,$cat_grid,$image_grid){
        // return $js->univ()->alert($data['id']);
        return [
            $cat_grid->js()->find('.list-group-item')->removeClass('image-category-active'),
            $image_grid->js()->reload(['category_id'=>$data['id']],null,$lib_image_url),
            $js->addClass('image-category-active'),
          ] ;
      });
      /*Library Images section Closed*/

  }

  function page_upload(){
      $this->add('CRUD')->setModel('xepan\commerce\Model_Designer_Image_Category');
  }

  function page_previous_upload(){
      $this->add('View')->set('Previous Upload Images');
  }

  function page_image_library(){
  }

  function defaultTemplate(){
      return array("page/manageimage"); 
  }

}