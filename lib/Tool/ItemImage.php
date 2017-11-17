<?php
namespace xepan\commerce;

class Tool_ItemImage extends \xepan\cms\View_Tool{
	public $options = [
		'zoom-type'=>"window",
		'zoom-window-position'=>1,
		'zoom-effect'=>'true',
		'showtitle'=>0
	];
	public $lister;

	function init(){
		parent::init();

		$item_id = $this->app->stickyGET('commerce_item_id');
		$item_slug_url = $this->app->stickyGET('commerce_item_slug_url');

		$this->app->stickyGET('custom_field');
		$this->addClass('xepan-commerce-item-image');
		$this->js('reload')->reload();

		$item = $this->add('xepan\commerce\Model_Item');
		if($this->app->enable_sef && $item_slug_url)
			$item->tryLoadBy('slug_url',$item_slug_url);
		else
			$item->tryLoad($item_id?:-1);

		if(!$item->loaded()){
			$this->add('View')->set('No Record Found');
			return;
		}


		$image = $this->add('xepan\commerce\Model_Item_image')
			->addCondition('item_id',$item->id)
			->setOrder('sequence_no','asc');
		
		if(!$image->count()->getOne()){
			$this->add('View')->set('No Record Found')->addClass('no-record-found');
			return;
		}		

		if($_GET['custom_field']){

			$department_wise_custom_field_array = json_decode($_GET['custom_field'],true);

			foreach ($department_wise_custom_field_array as $department) {
				foreach ($department as $cf_id => $values) {
					if(!is_numeric($cf_id))
						continue;
					$customfield_value_id_array[] = $values['custom_field_value_id'];
				}
			}

			if(isset($customfield_value_id_array)){
				$temp_image = $this->add('xepan\commerce\Model_Item_image')
							->addCondition('item_id',$item->id);
				$temp_image->addCondition('customfield_value_id',$customfield_value_id_array);
				if($temp_image->count()->getOne()){
					$image->addCondition('customfield_value_id',$customfield_value_id_array);
					$image->tryLoadAny();
				}else
					$customfield_value_id_array = [];
			}

		}

		$template = 'view/tool/itemimage';
		
		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/".$this->options['custom_template'].".html";
			if(file_exists($path)){
				$template = 'view/tool/'.$this->options['custom_template'];
			}else{
				$this->add('View_Error')->set('Custom template not found.');
				return;
			}
		}
		
		$this->lister = $lister = $this->add('CompleteLister',null,null,[$template]);
		$this->lister->setModel($image);
		$this->lister->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$image]);

		$first_image = $this->add('xepan\commerce\Model_Item_Image')
						->addCondition('item_id',$item->id)
						->setOrder('sequence_no','asc');

		if(isset($customfield_value_id_array) and count($customfield_value_id_array))
			$first_image->addCondition('customfield_value_id',$customfield_value_id_array);

		$firstimage_url = $first_image->setLimit(1)->fieldQuery('file');
		$this->lister->template->set('firstimage',$firstimage_url);
				
	}

	function render(){
		
		if($this->lister){
			$this->lister->js(true)->_load($this->app->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery-elevatezoom.js')
						   ->_load($this->app->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery.fancybox.js')
							->_css("tool/jquery.fancybox-buttons")
							->_css("tool/jquery.fancybox");
											
			if($this->options['zoom-effect']=='true'){
				$option_array = array(
								'gallery'=>"gal1".$this->lister->name,
								'cursor'=> 'pointer',
							    'galleryActiveClass'=> 'active',
							    // 'imageCrossfade'=> true,
							    'constrainType'=>"height",
							    'containLensZoom'=> true,
							    'scrollZoom' => false,
							    'responsive'=>true,
								'easing' => true,
							    'lensShape' => "round",
								// 'lensSize'=> 2,
								'zoomWindowPosition'=>(int)$this->options['zoom-window-position'],
							    'zoomType'=>$this->options['zoom-type']
		   					);
				if(!trim($this->options['zoom-type']))
					unset($option_array['zoomType']);
				$this->js(true)->_selector('.xepan-commerce-item-image-to-zoom')->elevateZoom($option_array);
			}

			$this->js('click','var ez =$(".xepan-commerce-item-image-to-zoom").data("elevateZoom");ez.closeAll();$.fancybox(ez.getGalleryList({}));return false;')->_selector('.xepan-commerce-item-image-to-zoom');
		}

		parent::render();

	}

	function addToolCondition_row_showtitle($value,$l){
		if(!$value){
			$l->current_row_html['title_wrapper'] = "";
			return;
		}
		
	}

}	