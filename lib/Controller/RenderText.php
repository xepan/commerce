<?php

namespace xepan\commerce;

class Controller_RenderText extends \AbstractController {
	public $options = array();
	public $phpimage;
	public $base_font_path;

	function init(){
		session_write_close();
		parent::init();
		$options = $this->options;

		$font_path = $this->getFontPath();
		$options['desired_width'] = round($options['desired_width']);
		// $text = $this->wrap($options['font_size'],$options['rotation_angle'],$font_path,$options['text'],$options['desired_width']);
		// $width_height = $this->getTextBoxWidthHeight($options['text'],$font_path);
		$options['halign'] = ($options['alignment_center']==true)?'center':($options['alignment_right'] == 'right'?'right':'left');

		/*
			Calculating height of the of the box 
		*/
		$im = imagecreatetruecolor( $options['desired_width'],10);
		imagesavealpha($im,true);
		$backgroundColor = imagecolorallocatealpha($im, 255, 255, 255,127);
		imagefill($im, 0, 0, $backgroundColor);
		$this->phpimage = $im;
		
		$box = new \GDText\Box($this->phpimage);
		$box->setFontFace($font_path);
		$rgb_color_array = $this->hex2rgb($options['text_color']);
		$box->setFontColor(new \GDText\Color($rgb_color_array[0],$rgb_color_array[1],$rgb_color_array[2]));
		$box->setFontSize($options['font_size']);
		$box->setBox(0, 0, $options['desired_width'], 0);
		$box->setTextAlign($options['halign'], 'top');

		if($options['underline'])
			$box->setUnderline();
		// $h = $box->draw($options['text']);
		$this->new_height = $h = $box->getBoxHeight($options['text']);

		
		//CREATING DEFAULT IMAGES
		$im = imagecreatetruecolor( $options['desired_width'],$h);
		imagesavealpha($im,true);
		$backgroundColor = imagecolorallocatealpha($im, 255, 255, 255,127);
		imagefill($im, 0, 0, $backgroundColor);
		$this->phpimage = $im;
		
		$box = new \GDText\Box($this->phpimage);
		$box->setFontFace($font_path);
		$rgb_color_array = $this->hex2rgb($options['text_color']);
		$box->setFontColor(new \GDText\Color($rgb_color_array[0],$rgb_color_array[1],$rgb_color_array[2]));
		// $box->setTextShadow(new \GDText\Color(0, 0, 0, 50), 2, 2);
		$box->setFontSize($options['font_size']);
		// $box->enableDebug();
		$box->setBox(0, 0, $options['desired_width'], $h);
		$box->setTextAlign($options['halign'], 'top');
		if($options['underline'])
			$box->setUnderline();
		
		$box->draw($options['text']);

		if($options['rotation_angle']){
		    $this->phpimage = imagerotate($this->phpimage, $options['rotation_angle'], imageColorAllocateAlpha($im, 255, 255, 255, 127));
		    imagealphablending($this->phpimage, false);
		    imagesavealpha($this->phpimage, true);
		}

	}

	function setFontPath($path){
		$this->base_font_path = $path;
	}


	function init_old(){
		parent::init();
		$options = $this->options;
		// print_r($options);
		// exit;
		if($options['bold'] and !$options['italic']){
			if(file_exists(getcwd().'/epan-components/xShop/templates/fonts/'.$options['font'].'-Bold.ttf'))
				$options['font'] = $options['font'].'-Bold';
			// else
				// $draw->setFontWeight(700);
		}

		if($options['italic'] and !$options['bold']){
			if(file_exists(getcwd().'/epan-components/xShop/templates/fonts/'.$options['font'].'-Italic.ttf'))
				$options['font'] = $options['font'].'-Italic';
			else
				$options['font'] = $options['font'].'-Regular';
		}

		if($options['italic'] and $options['bold']){
			if(file_exists(getcwd().'/epan-components/xShop/templates/fonts/'.$options['font'].'-BoldItalic.ttf'))
				$options['font'] = $options['font'].'-BoldItalic';
			else
				$options['font'] = $options['font'].'-Regular';
		}
		if(!$options['bold'] and !$options['italic'])
			$options['font'] = $options['font'] .'-Regular';

		$font_path = getcwd().'/epan-components/xShop/templates/fonts/'.$options['font'].'.ttf';
		// echo $font_path;
		$p = new \PHPImage($options['desired_width'],10);
		$p->setFont($font_path);
		$p->setFontSize($options['font_size']);
	    $p->textBox($options['text'], array('width' => $options['desired_width'], 'x' => 0, 'y' => 0));
	    $size = $p->getTextBoxSize($options['font_size'], 0, $font_path, $p->last_text);

		$new_width = abs($size[0]) + abs($size[2]); // distance from left to right
		$new_height = abs($size[1]) + abs($size[5]); // distance from top to bottom

	    $p1 = new \PHPImage($options['desired_width'] , $new_height); 
	    $p1->setFont($font_path);
		$p1->setFontSize($options['font_size']);
	    $p1->setTextColor($p1->hex2rgb($options['text_color']));
	    // $p1->setAlignHorizontal('right');
	    $p1->textBox($options['text'], array('width' => $new_width, 'x' => 0, 'y' => 0));

		if($this->options['rotation_angle']){
			$p1->xRotate($this->options['rotation_angle']);
			// $p1->rotate($this->options['rotation_angle']);
		}
	    $this->phpimage = $p1;
	    $this->new_height = $new_height;

	}

	function show_old($type='png',$quality=3, $base64_encode=true, $return_data=false){
		$this->phpimage->setOutput('png',3);
		return $this->phpimage->show($base64_encode,$return_data);
	}

	function show($type="png",$quality=3,$base64_encode=true, $return_data=false){
		ob_start();
		imagepng($this->phpimage, null,9,PNG_ALL_FILTERS);
		$imageData = ob_get_contents();
		ob_clean();
		$this->cleanup();
		if($base64_encode)
			$imageData = base64_encode($imageData);
		
		if($return_data)
			return $imageData;

		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		if($type="png")
			header("Content-type: image/png");
		// imagepng($this->phpimage, null, 9, PNG_ALL_FILTERS);
		
		echo $imageData;
		die();
	}

	public function cleanup(){
		imagedestroy($this->phpimage);
	}

	public function hex2rgb($hex) {
       $hex = str_replace("#", "", $hex);

       if(strlen($hex) == 3) {
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
       } else {
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
       }
       $rgb = array($r, $g, $b);
       // return implode(",", $rgb); // returns the rgb values separated by commas
       return $rgb; // returns an array with the rgb values
    }

    function getFontPath(){

    	if(!$this->base_font_path){
    		$this->base_font_path = getcwd();//->url()->absolute()->getBaseURL();
			$this->base_font_path .= "/vendor/xepan/commerce/templates/fonts/";
    		// throw new \Exception($this->base_font_path);
    	}

    	
    	$options = $this->options;
    
    	//GET Font Path
		if($options['bold'] and !$options['italic']){
			if(file_exists($this->base_font_path.$options['font'].'-Bold.ttf'))
				$options['font'] = $options['font'].'-Bold';
			// else
				// $draw->setFontWeight(700);
		}

		if($options['italic'] and !$options['bold']){
			if(file_exists($this->base_font_path.$options['font'].'-Italic.ttf'))
				$options['font'] = $options['font'].'-Italic';
			else
				$options['font'] = $options['font'].'-Regular';
		}

		if($options['italic'] and $options['bold']){
			if(file_exists($this->base_font_path.$options['font'].'-BoldItalic.ttf'))
				$options['font'] = $options['font'].'-BoldItalic';
			else
				$options['font'] = $options['font'].'-Regular';
		}
		if(!$options['bold'] and !$options['italic'])
			$options['font'] = $options['font'] .'-Regular';

		$font_path = $this->base_font_path.$options['font'].'.ttf';

		return $font_path;
    }

    function getTextBoxWidthHeight($text,$font_path=null){
    	$options = $this->options;
    	if(!$font_path){
    		$font_path = $this->getFontPath();
    	}

    	$rect = imageftbbox($options['font_size'], $options['rotation_angle'],$font_path, $text);
	    $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
	    $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
	    $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
	    $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));

		// $width  = $bbox[2] - $bbox[6];
		// $height = $bbox[3] - $bbox[7];
	    $width = $maxX - $minX;
	    $height = $maxY - $minY;
		return array(
				"left"   => abs($minX) - 1,
			    "top"    => abs($minY) - 1,
				'width'  => $width,
				'Width'	 => $width,
				'height' => $height,
				'Height' => $height,
				"box"    => $rect
				);

    }

    function wrap($fontSize, $angle=0, $fontFace, $string, $width){
	    $ret = "";
	    $arr = explode(' ', $string);
	    foreach ( $arr as $word ){
	        $teststring = $ret.' '.$word;
	        $testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
	        $testbox = $this->getTextBoxWidthHeight($teststring,$fontFace);
	        if ( $testbox['width'] > $width ){
	            $ret.=($ret==""?"":"\n").$word;
	        } else {
	            $ret.=($ret==""?"":' ').$word;
	        }
	    }
	    return $ret;
	}

}