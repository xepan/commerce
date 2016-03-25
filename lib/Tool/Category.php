<?php

namespace xepan\commerce;

class Tool_Category extends \xepan\cms\View_Tool{
	public $options = [
				'show_name'=>true,
				'layout'=>'vertical',
				'show_description' =>true,
				'show_price' =>true,
				'grid-column' =>12,
				'show-category-column' =>3,
				'category_show_list' =>true,
				'url_page' =>null,
				'category-add-pin' =>true,
				'category-pin-label' =>'xepan-base',
			];

	function init(){
		parent::init();

		$categories = $this->add('xepan\commerce\Model_Category');
		
		if($this->options['show_description']){
			$cat_m = $this->add('xepan\commerce\Model_Category')->load($_GET['xsnb_category_id']);

			// $content = str_replace("{{category_id}}", $_GET['xsnb_category_id'], $cat_m['description']);
			// $content = str_replace("{{product_page_name}}",$this->html_attributes['xshop_category_url_page'] , $content);
			// $this->add('View')->setHTML($content);
			// return;
		}
		if($this->options['show_name']){
			$cat_model=$this->add('xepan\commerce\Model_Category');
			$cat_name = $cat_model->get('name');

			//Count Only Website Display Item
			$cat_item_model = $cat_model->refSQL('xepan\commerce\CategoryItemAssociation');
			$cat_item_j = $cat_item_model->join('item','item_id');
			$cat_item_j->addField('is_publish');
			$cat_item_j->addField('is_saleable');
			$cat_item_j->addField('website_display');
			$cat_item_model->addCondition('is_publish',true);
			$cat_item_model->addCondition('is_saleable',true);
			$cat_item_model->addCondition('website_display',true);
			// $cat_item_model->addCondition('is_associate',true);
			
			// $item_count= $cat_item_model->count()->getOne();
			
			// $v=$this->add('View')->addClass('xshop-category-name-count');
			// $v->add('H2')->set($cat_name)->addClass('xshop-category-name');
			// $v->add('View')->set($item_count)->addClass('xshop-category-item-count');
			
			// return;
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

	    	// throw new \Exception("Error Processing Request", 1);
		
		if($this->options['url_page']){
				$this->add('View_Error')->set('Please Specify Category URL Page Name (epan page name like.. about,contactus etc..)');
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
	        	
	        	$this->add('View_Error')->setHTML('No Category Found in Selected Application');
	        	return;
	        }

			$output ="<div class='body epan-sortable-component epan-component  ui-sortable ui-selected'>";
			$output ="<ul class='sky-mega-menu sky-mega-menu-anim-slide sky-mega-menu-response-to-stack'>";
					foreach ($categories as $junk_category) {
					$output .= $this->getText($categories,$this->options['url_page']);
					}
			$output.="</ul></div>";
			$this->setHTML($output);
		}
		
		//loading custom CSS file	
		// $category_css = 'epans/'.$this->api->current_website['name'].'/xshopcategory.css';
		// $this->api->template->appendHTML('js_include','<link id="xshop-category-customcss-link" type="text/css" href="'.$category_css.'" rel="stylesheet" />'."\n");
	}

	function getText($category,$page_name){
		$item=$this->add('xepan\commerce\Model_Item');
		$cat_item_j=$item->join('category_item_association.item_id');
		$cat_item_j->addField('category_id');
		$item->addCondition('category_id',$category->id);
		// $item->setOrder('sale_price','asc');

		$item->tryLoadAny();

		if($category->ref('SubCategories')->count()->getOne() > 0){
			$sub_category = $category->ref('SubCategories')->addCondition('status','Active');
			$output = "<li aria-haspopup='true' class='xshop-category'>";
			$output .="<a href='".$this->api->url(null,array('subpage'=>$page_name,'xsnb_category_id'=>$category->id))."'>";
			$output .= $category['name'];
			$output .="</a>" ;
			$output .= "<div class='grid-container3'>";
			$output .= "<ul>";
			foreach ($sub_category as $junk_category) {
				$output .= $this->getText($sub_category,$page_name);
			}
			$output .= "</ul>";
			$output .= "</div>";
			$output .= "</li>";

			if($this->options['category-add-pin']){
				$output .= '<li class="xshop-pin-category pull-right"><a class="icon-pin" onclick="swapfixed(event);">'.$this->options["category-pin-label"].'</a>';
				$output .= "</li>";
			}
		}else{
			// throw new \Exception($category['id'], 1);
			if($this->options['layout']){
				$output = "<li class='text-center ".$this->col."'><a href='".$this->api->url(null,array('subpage'=>$page_name,'xsnb_category_id'=>$category->id))."'><img src='$category[image_url]' /><div class='sky-menu-thumbnail-name'>".$category['name']."</div></a></li>";
			}else{
				$output = "<li><a href='".$this->api->url(null,array('subpage'=>$page_name,'xsnb_category_id'=>$category->id))."'>".$category['name'];
 				if($this->html_attributes['show_price'])
					$output.= " " . $item['sale_price'];
				$output.="</a></li>";
			}

		}

		return $output;
	}


	// function getCategory($category){
	// 	if($category->ref('SubCategories')->count()->getOne() > 0){
	// 		$sub_category = $category->ref('SubCategories');
	// 		$lister = $this->add('CompleteLister',null,null,['view/tool/category']);
	// 		$lister->setModel($category);
	// 		// $output.=$category['name'];
	// 		foreach ($sub_category as $junk_category) {
	// 			$this->getCategory($sub_category);
	// 		}

	// 	}/*else{
	// 		$lister = $this->add('CompleteLister',null,null,['view/tool/category']);
	// 		$lister->setModel($category);
	// 	}*/
	// }
	

	function addToCondition_show_description($value){
		$this->model->load($value);
	}

}