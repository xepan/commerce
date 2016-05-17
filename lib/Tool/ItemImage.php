<?php
namespace xepan\commerce;

class Tool_ItemImage extends \xepan\cms\View_Tool{
	public $option = [];


	function init(){
		parent::init();
		$item_id = $this->app->stickyGET('commerce_item_id');

		$this->addClass('xepan-commerce-item-image');
		// $this->js('reload')->reload(['commerce_item_id'=>$item_id]);

		$item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id?:-1);
		if(!$item->loaded()){
			$this->add('View')->set('Item must be given to load Item Image');
			return;
		}


		$image = $this->add('xepan\commerce\Model_Item_image')->addCondition('item_id',$item->id);

		if($_GET['custom_field']){
			$department_wise_custom_field_array = json_decode($_GET['custom_field'],true);

			foreach ($department_wise_custom_field_array as $department) {
				foreach ($department as $cf_id => $values) {
					if(!is_numeric($cf_id))
						continue;
					$customfield_value_id_array[] = $values['custom_field_value_id'];
				}
			}
			$image->addCondition('customfield_value_id',$customfield_value_id_array);
			// $image->debug();
		}
		$image->tryLoadAny();

		$lister = $this->add('CompleteLister',null,null,['view/tool/itemimage']);
		$lister->setModel($image);

		$first_image = $this->add('xepan\commerce\Model_Item_Image')
						->addCondition('item_id',$item_id);

		if($_GET['custom_field'] and isset($customfield_value_id_array))
			$first_image->addCondition('customfield_value_id',$customfield_value_id_array);

		$firstimage_url = $first_image->setLimit(1)->fieldQuery('file');
		$lister->template->set('firstimage',$firstimage_url);
				
	}

	function render(){

		$this->js(true)->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery-elevatezoom.js')
					   ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery.fancybox.js');
		parent::render();

	}
}	