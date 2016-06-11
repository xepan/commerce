<?php
namespace xepan\commerce;

class Tool_Category extends \xepan\cms\View_Tool{
	public $options = [
		'url_page' =>'index',
		"custom_template"=>''
	];

	function init(){
		parent::init();
		
		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				$this->add('View_Warning')->set('template not found');
				return;	
			}	
		}else
			$this->options['custom_template'] = "categorylister";

		$lister = $this->add('xepan\commerce\View_CategoryLister',['options'=>$this->options]);
	}
}