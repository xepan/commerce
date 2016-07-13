<?php 
 namespace xepan\commerce;
 class page_designerlibraryimages extends \xepan\commerce\page_configurationsidebar{

	public $title='Designer Library Images';

	function init(){
		parent::init();

		$designer_cat=$this->add('xepan\commerce\Model_Designer_Image_Category');
		$designer_cat->addCondition('is_library',true);
		$cat_crud=$this->add('xepan\base\CRUD',['entity_name'=>'Library Category'],
						'designer_category',
						['view/designer/backend-designer-category-grid']
					);

		$cat_crud->setModel($designer_cat,['name']);
		
		$designer_image=$this->add('xepan\commerce\Model_Designer_Images');
		$category_id=$this->app->stickyGET('category_id');
		$view=$this->add('View',null,'category_name');
		$view->set('No Category');
		
		if($category_id){
			$designer_image->addCondition('designer_category_id',$category_id);
			$view_cat=$this->add('xepan\commerce\Model_Designer_Image_Category')->addCondition('id',$category_id);
			$view_cat->tryloadAny();
			$view->set($view_cat['name']?:'No Category');
		}

		
		$image_crud=$this->add('xepan\base\CRUD',['entity_name'=>'Images'],'designer_category_images',['view/designer/backend-designer-images-grid']);
		$image_crud->setModel($designer_image);

		$image_url=$this->app->url(null,['cut_object'=>$image_crud->name]);
		$view_url=$this->app->url(null,['cut_object'=>$view->name]);
		
		$cat_crud->on('click','a.category-image-filter',function($js,$data)use($cat_crud,$image_crud,$image_url,$view,$view_url){
	        return [
	        	$view->js()->reload(['category_id'=>$data['id']],null,$view_url),
	            $image_crud->js()->reload(['category_id'=>$data['id']],null,$image_url),
	          ] ;

	      });
		// $this->on('click','a.do-remove-category',function($js,$data,$view,$view_url,$image_url,$image_crud){
		// 	return $js->univ()->alert('hello');
		// 	return [
	 //        	$view->js()->reload(['category_id'=>""],null,$view_url),
	 //            $image_crud->js()->reload(['category_id'=>""],null,$image_url),
	 //          ] ;
		// });
	}

	function defaultTemplate(){
		return ['view/designer/designer-cat-item'];
	}

}  