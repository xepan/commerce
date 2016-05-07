<?php

namespace xepan\commerce;

class page_designer_itemimages extends \Page {

  function page_index(){
    // parent::init();  
       // $this->add('View')->set('Member Images');
       // $tabs = $this->add('Tabs');
       // $tabs->addTabUrl('./upload','Your Library');
       // // $tabs->addTabUrl('./previous_upload','Previuos Upload');
       // $tabs->addTabUrl('./image_library','Image Library');

       //Check Member Auth
      $contact = $this->add('xepan\base\Model_Contact');
      $contact->loadLoggedIn();

      //Creating Column
      $col = $this->add('Columns');
      $cat_col = $col->addColumn(6)->addStyle(['overflow-y'=>'auto','height'=>'400px'])->addClass('xepan-image-library-category');
      $image_col = $col->addColumn(6);

      //Category Crud and It's Model
      $cat_crud = $cat_col->add('xepan\base\CRUD',['entity_name'=>'Category'],null,['view/designer/category-grid']);
      // $cat_crud->frame_options = ['width'=>'500'];
      $cat_crud->addStyle(['width'=>'350px']);

      $cat_model = $this->add('xepan\commerce\Model_Designer_Image_Category')
                    ->addCondition('is_library',false)
                    ->addCondition('contact_id',$contact->id);
      $cat_crud->setModel($cat_model,array('name'));

      //Member Images
      //Setting up Model according to the Category id
      $image_model = $image_col->add('xepan\commerce\Model_Designer_Images');
      // $image_model->addCondition('member_id',$member->id);
      $image_model->setOrder('id','desc');
      if($cat_id = $this->api->stickyGET('cat_id')){
        $image_model->addCondition('designer_category_id',$cat_id);        
      }

      //Member Image Crud
      $crud = $image_col->add('xepan\base\CRUD',[
                                      'entity_name'=>'Image',
                                      'allow_edit'=>false,
                                      ],
                                      null,
                                      ['view/designer/designer-item-grid']
                                      );
      
      $crud->frame_options = ['width'=>'500'];
      $item_images_lister = $crud->setModel($image_model);

      $img_url = $this->api->url(null,['cut_object'=>$image_col->name]);
      $cat_url = $this->api->url(null,['cut_object'=>$cat_col->name]);

      //Jquery For Filter the images
      $cat_col->on('click','tr',function($js,$data)use($image_col,$img_url,$cat_col){
        return [
            $cat_col->js()->children('td')->find('.alert-success')->removeClass(' alert-success'),
            $image_col->js()->reload(['cat_id'=>$data['id']],null,$img_url),
            $js->children('td:first-child ')->addClass('alert alert-success'),
          ] ;
      });

      //All Category Filter 
      $all_cat_btn = $crud->grid->addButton('All Category');
      $self = $this;
      $all_cat_btn->on('click',function($js,$data)use($cat_col,$cat_url,$image_col,$img_url,$self){
        $self->api->stickyForget('cat_id');
        return [
            $image_col->js()->reload(['cat_id'=>0],null,$img_url),
            $cat_col->js()->find('.alert-success')->removeClass('alert-success')
          ]; 
      });

  }

  function page_upload(){

      

      //Creating Column

      $this->add('CRUD')->setModel('xepan\commerce\Model_Designer_Image_Category');

  }

  function page_previous_upload(){
      $this->add('View')->set('Previous Upload Images');
  }

  function page_image_library(){

      //Creating Column
      $col = $this->add('Columns');
      $cat_col = $col->addColumn(4)->addStyle(['overflow-y'=>'auto','height'=>'400px'])->addClass('xepan-image-library-category');

      $image_col = $col->addColumn(8);

      //Category Crud and It's Model
      $cat_crud = $cat_col->add('xepan\base\CRUD');
      $cat_crud->frame_options = ['width'=>'500'];
      $cat_model = $this->add('xShop/Model_ImageLibraryCategory')->addCondition('is_library',true);
      $cat_crud->setModel($cat_model,array('name'));

      if(!$cat_crud->isEditing()){
        $g = $cat_crud->grid;
        $g->addMethod('format_width',function($g,$f){
          $g->current_row_html[$f] = '<div style="max-width:140px;white-space:normal !important;overflow:hidden;">'.$g->current_row[$f]."</div>";
        });
        $g->addFormatter('name','width');
        // $cat_crud->grid->addQuickSearch(['name']);
      }
      
      //Setting up Model according to the Category id
      $image_model = $image_col->add('xShop/Model_MemberImages');
      $image_model->addCondition('member_id',Null);
      $image_model->setOrder('id','desc');
      if($cat_id = $this->api->stickyGET('cat_id')){
        $image_model->addCondition('category_id',$cat_id);        
      }

      // //Member Image Crud
      $crud = $image_col->add('xepan\base\CRUD');
      $crud->frame_options = ['width'=>'500'];
      $item_images_lister = $crud->setModel($image_model,array('category_id','image_id','image'),array('image_id','image'));
      $crud->grid->addQuickSearch(array('image_id','image'));
      $crud->grid->addPaginator(12);

      $img_url = $this->api->url(null,['cut_object'=>$image_col->name]);
      $cat_url = $this->api->url(null,['cut_object'=>$cat_col->name]);

      // //Jquery For Filter the images
      $cat_col->on('click','tr',function($js,$data)use($image_col,$img_url,$cat_col){
        return [
            $cat_col->js()->children('td')->find('.atk-swatch-green')->removeClass('atk-swatch-green'),
            $image_col->js()->reload(['cat_id'=>$data['id']],null,$img_url),
            $js->children('td:first-child ')->addClass('atk-swatch-green'),
          ] ;
      });

      // //All Category Filter 
      $all_cat_btn = $crud->grid->addButton('All Category');
      $self = $this;

      $all_cat_btn->on('click',function($js,$data)use($cat_col,$cat_url,$image_col,$img_url,$self){
        $self->api->stickyForget('cat_id');
        return [
            $image_col->js()->reload(['cat_id'=>0],null,$img_url),
            $cat_col->js()->find('.atk-swatch-green')->removeClass('atk-swatch-green')
          ]; 
      });   
  }


}