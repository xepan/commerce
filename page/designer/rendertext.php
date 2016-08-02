<?php

namespace xepan\commerce;

class page_designer_rendertext extends \Page {
	function init(){
		parent::init();

		$options=array();

		$zoom = $options['zoom'] = $_GET['zoom'];
		$options['font_size'] = $_GET['font_size'] * ($zoom);
		// $options['font_size'] = $_GET['font_size'] * ($zoom / 1.328352013);
		$options['font'] = $_GET['font'];
		$options['text'] = $_GET['text'];
		$options['text_color'] = $_GET['color'];
		$options['desired_width'] = $_GET['width'] * $zoom;

		$options['bold'] = $_GET['bold']=='true'?true:false;
		$options['italic'] = $_GET['italic']=='true'?true:false;
		$options['underline'] = $_GET['underline']=='true'?true:false;
		$options['alignment_justify'] = $_GET['alignment_justify']=='true'?true:false;
		$options['alignment_center'] = $_GET['alignment_center']=='true'?true:false;
		$options['alignment_left'] = $_GET['alignment_left']=='true'?true:false;
		$options['alignment_right'] = $_GET['alignment_right']=='true'?true:false;
		$options['rotation_angle'] = $_GET['rotation_angle'];
		$options['stokethrough'] = $_GET['stokethrough']=='true'?true:false;

		$base_font_path = getcwd();//->url()->absolute()->getBaseURL();
		$base_font_path .= "/vendor/xepan/commerce/templates/fonts/";
		$cont = $this->add('xepan\commerce\Controller_RenderText',array('options'=>$options,'base_font_path'=>$base_font_path));
		// var_dump($options);
		$cont->show('png',3,true,false); // exiting as well

	}
}