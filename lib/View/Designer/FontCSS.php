<?php

namespace xepan\commerce;

class View_Designer_FontCSS extends \View {

	public $font_list=[];

	function init(){
		parent::init();

		$this->js(true)
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fontfaceobserver.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fontfaceobserver.standalone.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/xfontobserver.js')
			;

		$this->setElement('style');

		$font_family_config = $this->add('xepan\base\Model_ConfigJsonModel',
				    [
				      'fields'=>[
				            'font_family'=>'text',
				            ],
				        'config_key'=>'COMMERCE_DESIGNER_TOOL_FONT_FAMILY',
				        'application'=>'commerce'
				    ]);
		$font_family_config->tryLoadany();
		$font_family_config_array = explode("," ,$font_family_config['font_family']);
		$font_family = [];
		foreach ($font_family_config_array as $key => $value) {
			$font_family[] = $value.":bold,bolditalic,italic,regular";
		}

		// Default Fonts
		if(!count($font_family))
			$font_family_config_array = $font_family = ['Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Cabin','Cambay','Cambo','Candal','Petit Formal Script', 'Petrona', 'Philosopher','Piedra', 'Ubuntu'];

		//  === web font loader old code with one call ======

		// // RE DEFINED ALSO AT page_designer_exportpdf
		// $this->js(true)
		// 		->_library('WebFont')->load(['google'=>['families'=>$font_family]]);

		// === Web font new call in chunks ========
		// RE DEFINED ALSO AT page_designer_exportpdf
		$chunk_fonts_array = array_chunk($font_family, 30);
		
		foreach ($chunk_fonts_array as $font_array) {
			$this->js(true)
					->_library('WebFont')->load(['google'=>['families'=>$font_array]]);
		}
		
		// custom Fonts
		$designer_font = $this->add('xepan\commerce\Model_DesignerFont');
		$custom_fonts = $designer_font->getRows();
		$custom_font_array = [];
		foreach ($custom_fonts as $row) {
			$custom_font_array[] = $row['name'];
		}

		$this->font_list = array_merge($font_family_config_array,$custom_font_array);
		
		$this->js(true)->univ()->isLoaded($custom_font_array);
				
		$this->setModel($designer_font);
	}


	function getFontList(){
		return $this->font_list;
	}

	function setModel($model){
		$regular_template = "@font-face { font-family: {\$name}; src: url('{\$regular_file}');}";
		$bold_template = "@font-face { font-family: {\$name}; src: url('{\$bold_file}');font-weight:bold;}";
		$italic_template = "@font-face { font-family: {\$name}; src: url('{\$italic_file}');font-style:italic;} @font-face { font-family: {\$name}; src: url('{\$italic_file}');font-style:oblique;}";		
		$bold_italic_template = "@font-face { font-family: {\$name}; src: url('{\$bold_italic_file}');font-weight:bold;font-style:italic;} @font-face { font-family: {\$name}; src: url('{\$bold_italic_file}');font-weight:bold;font-style:oblique;}";
		
		$source = "";
		foreach ($model as $font) {
			if($font['regular_file_id']){
				$source .= $this->add('GiTemplate')->loadTemplateFromString($regular_template)
													->set('name',$font['name'])
													->set('regular_file',$font['regular_file'])
													->render();
			}

			if($font['bold_file_id']){
				$source .= $this->add('GiTemplate')->loadTemplateFromString($bold_template)
												->set('name',$font['name'])
												->set('bold_file',$font['bold_file'])
												->render();
			}

			if($font['italic_file_id']){
				$source .= $this->add('GiTemplate')->loadTemplateFromString($italic_template)
												->set('name',$font['name'])
												->set('italic_file',$font['italic_file'])
												->render();
			}

			if($font['bold_italic_file_id']){
				$source .= $this->add('GiTemplate')->loadTemplateFromString($bold_italic_template)
												->set('name',$font['name'])
												->set('bold_italic_file',$font['bold_italic_file'])
												->render();
			}
			
			$this->owner->add('View')->setHtml('<div style="display:none;font-family:'.$font['name'].';">&nbsp;'.$font['name'].'</div>');
		}

		$this->setHtml($source);
		// return parent::setModel($model);
	}

}