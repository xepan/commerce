<?php

/*
default_value: self.options.default_value,
crop:self.options.crop,
crop_x: self.options.crop_x,
crop_y: self.options.crop_y,
crop_height: self.options.crop_height,
crop_width: self.options.crop_width,
replace_image: self.options.replace_image,
rotation_angle:self.options.rotation_angle,
url:self.options.url,
zoom: self.designer_tool.zoom,
width:self.options.width,
height:self.options.height,
max_width: self.designer_tool.safe_zone.width(),
max_height: self.designer_tool.safe_zone.height(),
auto_fit: is_new_image===true;
*/

namespace xepan\commerce;
class page_designer_renderimage extends \Page {
	
	function init(){
		session_write_close();
		
		parent::init();
		$options=array();

		$path1 = getcwd().'/'.$_GET['url'];
		// $path2 = getcwd().'/websites/'.$this->app->current_website_name."/".$_GET['url'];
		$path2 = dirname(getcwd()).'/'.$_GET['url'];

		$options['url'] = $path1;
		if(!file_exists($options['url'])){
			$options['url'] = $path2;
			if(!file_exists($options['url'])){
				throw $this->exception('Image not found')
							->addMoreInfo('url',$_GET['url'])
							->addMoreInfo('path 1',$path1)
							->addMoreInfo('path 2',$path2)
							;
						
				return;
			}
		}
		$zoom = $options['zoom'] = $_GET['zoom'];
		$options['width'] = $_GET['width'] * $zoom ;
		$options['height'] = $_GET['height'] * $zoom;
		$options['max_width'] = $_GET['max_width'];
		$options['max_height'] = $_GET['max_height'];
		$options['x'] = $_GET['x'];
		$options['y'] = $_GET['y'];

		$options['crop'] = $_GET['crop'] =='true';
		$options['crop_x'] = $_GET['crop_x'];
		$options['crop_y'] = $_GET['crop_y'];

		$options['crop_width'] = $_GET["crop_width"];
		$options['crop_height'] = $_GET["crop_height"];

		$options['rotation_angle'] = $_GET['rotation_angle'];
		
		$options['mask'] = $_GET['mask'];
		$options['mask_added'] = $_GET['mask_added']=='true';
		$options['apply_mask'] = $_GET['apply_mask']=='true';
		$options['is_mask_image'] = $_GET['is_mask_image'];		

		$options['mask']['x'] = $zoom * $options['mask']['x'];
		$options['mask']['y'] = $zoom * $options['mask']['y'];
		$options['mask']['width'] = $zoom * $options['mask']['width'];
		$options['mask']['height'] = $zoom * $options['mask']['height'];
		
		$cont = $this->add('xepan\commerce\Controller_RenderImage',array('options'=>$options));
		$cont->show('png',3,true,false); // exiting as well
		return;
	}
}