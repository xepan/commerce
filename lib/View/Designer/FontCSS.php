<?php

namespace xepan\commerce;

class View_Designer_FontCSS extends \View {
	function init(){
		parent::init();

		$this->setElement('style');
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
		}

		$this->setHtml($source);
		// return parent::setModel($model);
	}

}