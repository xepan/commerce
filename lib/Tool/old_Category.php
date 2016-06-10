<?php

namespace xepan\commerce;

class Tool_Category extends \xepan\cms\View_Tool{
	public $options = [
				'show_name'=>false,
				'show_price' =>true,
				'grid-column' =>12,
				'show-category-description-only'=>false,
				'show-category-column'=>false,
				'category_show_list' =>1,
				'url_page' =>'index'
			];

	function init(){
		parent::init();

		$categories = $this->add('xepan\commerce\Model_Category');
		$categories->setOrder('display_sequence','asc');

		$this->add('xepan\cms\Controller_Tool_Optionhelper',['model'=>$categories]);

		if( ($this->options['show_name']) or ($this->options['show-category-description-only'])){
			
			if( ($_GET['xsnb_category_id'] and is_numeric($_GET['xsnb_category_id']))){
				$categories->tryLoad($_GET['xsnb_category_id']);
				if(!$categories->loaded()){
					$this->add('View_Error')->set("category not found");
					return;
				}
			}

		}


		//Only Category Description
		if($this->options['show-category-description-only'] == "true"){
			$cat_m = $categories->load($_GET['xsnb_category_id']);
			//Category id replace because acustomer need category detail then go to the next page with passing category id
			$content = str_replace("{{category_id}}", $_GET['xsnb_category_id'], $cat_m['description']);
			$content = str_replace("{{product_page_name}}",$this->options['url_page'] , $content);
			$this->add('View')->setHTML($content);			
			return;
		}

		if($this->options['show_name']){
			//Count Only Website Display Item
			$cat_item_model = $this->add('xepan\commerce\Model_CategoryItemAssociation');
			$cat_item_j = $cat_item_model->leftJoin('item.document_id','item_id');
			$cat_item_j->addField('is_saleable');
			$cat_item_j->addField('website_display');

			$item_doc_j = $cat_item_j->join('document','document_id');
			$item_doc_j->addField('status');
			
			$cat_item_model->addCondition('status','Published');
			$cat_item_model->addCondition('is_saleable',1);
			$cat_item_model->addCondition('website_display',1);			
			$cat_item_model->addCondition('category_id',$categories->id);

			$single_view = $this->add('View',null,null,["view/tool/category"]);
			$single_view->setModel($categories);
			$single_view->template->trySet('item_count',$cat_item_model->count()->getOne());
			return;
		}	

		$this->options['grid-column'];
		
		$width = 12;
		if($this->options['show-category-column']){	
			$width = 12 / $this->options['show-category-column'];
		}
		$this->col = 'col-md-'.$width.' col-sm-'.$width.' col-xl-'.$width;
		// Define no of sub-category show in parent category
		if($this->options['category_show_list']){
			$this->options['category_show_list'];
		}
				
		if(!$this->options['url_page']){
				$this->add('View_Error')->set('Please Specify Category URL Page Name (page name like.. about,contactus etc..)');
			return;
		}else{
			
			$categories->addCondition('status','Active');
			//todo OR Condition Using _DSQL 
	        $categories->addCondition(
	        	$categories->_dsql()->orExpr()
	            	->where('parent_category_id', null)
	            	->where('parent_category_id', 0)
	            	);
	        // $categories->addCondition('parent_id',Null);
	        $categories->tryLoadAny();
	        if(!$categories->loaded()){
	        	$this->add('View_Error')->setHTML('No Root Category Found');
	        	return;
	        }

			$output ="<div class='body epan-sortable-component epan-component  ui-sortable ui-selected'>";
			$output ="<ul class='sky-mega-menu sky-mega-menu-anim-slide sky-mega-menu-response-to-stack'>";
			foreach ($categories as $junk_category) {
				$output .= $this->getCategory($categories);
			}
			$output.="</ul></div>";
			$this->setHTML($output);
		}
		
		//loading custom CSS file
		// $category_css = 'epans/'.$this->api->current_website['name'].'/xshopcategory.css';
		// $this->api->template->appendHTML('js_include','<link id="xshop-category-customcss-link" type="text/css" href="'.$category_css.'" rel="stylesheet" />'."\n");
	}

	function getCategory($category){
		$url = $category['custom_link']?$category['custom_link']:$this->options['url_page'];

		if($category->ref('SubCategories')->count()->getOne() > 0){
			$sub_category = $category->ref('SubCategories')
							->addCondition('status','Active')
							->setOrder('name','asc')
							->setOrder('display_sequence','asc');

			$output = "<li aria-haspopup='true' class='xshop-category'>";
			$output .="<a href='".$this->api->url($url,array('xsnb_category_id'=>$category->id))."'>";
			$output .= $category['name'];
			$output .="</a>" ;
			$output .= "<div class='grid-container3'>";
			$output .= "<ul>";
			foreach ($sub_category as $junk_category) {
				$output .= $this->getCategory($sub_category);
			}
			$output .= "</ul>";
			$output .= "</div>";
			$output .= "</li>";

		}else{
			// throw new \Exception($category['id'], 1);
			$output = "<li class='text-center ".$this->col."'><a href='".$this->api->url($this->options['url_page'],array('xsnb_category_id'=>$category->id))."'><div class='sky-menu-thumbnail-name'>".$category['name']."</div></a></li>";
		}

		return $output;
	}

	function addToCondition_show_description($value){
		$this->model->load($value);
	}

}