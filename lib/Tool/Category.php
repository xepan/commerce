<?php
namespace xepan\commerce;

class Tool_Category extends \xepan\cms\View_Tool{
	public $options = [
		'url_page' =>'index',
		'custom_template'=>'',
		'show_name'=>true,
		'show_price'=>false,
		'show_image'=>false,
		'show_item_count'=>false,
		'include_sub_category'=>true,
		'show_only_parent'=>false,
		'show_only_sub_category'=>false,
		'show_only_sub_category_of_ids'=>0,
		'display_layout'=>'list',//list, horizontalmenu, verticalmenu
		'submenu_background_color'=>'76260f',
		'submenu_hover_background_color'=>'000',
		'main_menu_color'=>'21242b',
		'main_menu_hover_color'=>'76260f'
	];

	function init(){
		parent::init();
		
		if($this->owner instanceof \AbstractController){
			$this->add('View_Info')->set('Please select this options by double clicking on it');
			return;
		} 

		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				$this->add('View_Warning')->set('template not found');
				return;	
			}	
		}else{

			if($this->options['display_layout'] == "horizontalmenu" OR $this->options['display_layout'] == "verticalmenu"){
				$this->options['custom_template'] = "categorylistermenu";
			}else
				$this->options['custom_template'] = "categorylister";
		}
		
		$this->lister = $this->add('xepan\commerce\View_CategoryLister',['options'=>$this->options]);
		
		$this->lister->template->trySet('submenu_background_color',"#".$this->options['submenu_background_color']);
		$this->lister->template->trySet('submenu_hover_background_color',"#".$this->options['submenu_hover_background_color']);
		$this->lister->template->trySet('main_menu_hover_color',"#".$this->options['main_menu_hover_color']);
		$this->lister->template->trySet('main_menu_color',"#".$this->options['main_menu_color']);
		
		if($this->options['display_layout'] == "verticalmenu")
			$this->lister->addClass('vertical-align');
	}

	function getTemplate(){
		return $this->lister->template;
	}
}