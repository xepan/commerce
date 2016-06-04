<?php

namespace xepan\commerce;

class page_designer_itemimages extends \Page {


  function page_index(){
      $vp = $this->add('virtualPage');
      $form = $this->add('Form');
      $form->js('reload')->reload();

      $vp->set(function($p)use($form){
        $contact_id = $this->api->stickyGET('contact_id');

        $category = $p->add('xepan\commerce\Model_Designer_Image_Category');
        $category->addCondition('contact_id',$contact_id);

        $crud = $p->add('xepan\base\CRUD');
        $crud->setModel($category,['name']);
        $crud->js('reload',$form->js()->trigger('reload'));
      });

      $contact = $this->add('xepan\base\Model_Contact');
      $contact->loadLoggedIn();
        
      $category_id = $this->app->stickyGET('category_id');

      if(!$category_id ){
        $category = $this->add('xepan\commerce\Model_Designer_Image_Category');
        $category->addCondition('contact_id',$contact->id);
        $category->addCondition('name','default');
        $category->tryLoadAny();
        if(!$category->loaded())
          $category->save();

        $category_id = $category->id;
      }

      /******** C A T E G O R Y ********/
      if(!$contact->loaded()){
        return $this->add('View_Error')->set('You need to login first');
      }
      
      $cat_model = $this->add('xepan\commerce\Model_Designer_Image_Category')
                    ->addCondition('is_library',false)
                    ->addCondition('contact_id',$contact->id);
       
      $form->setLayout('view/designer/category-grid');
      $category_field = $form->addField('xepan/commerce/DropDown','category')->setEmptyText("All");
      $category_field->setModel($cat_model);
      $category_field->set($category_id);
      $filter_button = $form->addSubmit('Filter Images');
      $management_button = $form->addSubmit('Category Management');
      
      /*********** I M A G E ***********/
      $image_model = $this->add('xepan\commerce\Model_Designer_Images');
      
      if($category_id)
        $image_model->addCondition('designer_category_id',$category_id);
      
      $image_model->addCondition('contact_id',$contact->id);
      $image_model->setOrder('id','desc');


      $image_crud = $this->add('xepan\base\CRUD',['entity_name'=>'Image','allow_edit'=>false,'grid_options'=>['paginator_class'=>'Paginator']],null,['view/designer/designer-item-grid']);
      $image_crud->frame_options=['width'=>500];
      $image_crud->setModel($image_model);
      $image_crud->grid->addPaginator(20);
      
      if($form->isSubmitted()){
        if($form->isClicked($filter_button)){
            return $image_crud->js()->reload(['category_id'=>$form['category']])->execute();
        }

        if($form->isClicked($management_button)){
          return $form->js(true,$this->js()->univ()->frameURL("MANAGE CATEGORIES",$this->api->url($vp->getURL(),['contact_id'=>$contact->id])))->execute();
        }
      }
  }

  function page_upload(){
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