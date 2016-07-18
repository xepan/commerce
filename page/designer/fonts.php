<?php

namespace xepan\commerce;

class page_designer_fonts extends \Page {
	function page_index(){

        $location = $this->api->addLocation(array(
            'ttf'=>array('/vendor/xepan/commerce/templates/fonts')
        ))->setParent($this->api->pathfinder->base_location);

        $p=$this->api->pathfinder->searchDir('ttf','.');
        sort($p);
        
        $font_array=array();

        foreach ($p as  $junk) {
            if(strpos($junk,'.ttf') === false) continue;
            
            $font_name = explode("-", $junk);
            $font_name = $font_name[0];
            if(!in_array($font_name, $font_array)){
                $font_array[]=$font_name;
            }
        }
        // print_r($p);
        $m= $this->add('Model');
        $m->setSource('Array',$font_array);
        $opts="";
        
        foreach ($m as $junk) {
            $opts .= "<option value='".str_replace(".ttf", "", $m['name'])."'>".str_replace(".ttf", "", $m['name'])."</option>";
        }
        echo $opts;
        exit;
        // $options = '<option>1</option>';
        // echo $options;
        // exit;
	}

	function page_testfonts(){
    //load background
    $im = new Imagick();
    $im->newimage(800, 10000, 'lightgray');
    $y = 10;
    $i = 1;
    foreach($im->queryFonts() as $font){
        $insert_image = new Imagick();
        $insert_image->newImage(600, 30, 'whitesmoke');
        $insert_image->setImageFormat("png");
        $draw = new ImagickDraw();
        $draw->setFont($font);
        $draw->setFontSize(25);
        $draw->setFillColor(new ImagickPixel('black'));
        $draw->setgravity(imagick::GRAVITY_NORTH);
        $insert_image->annotateImage($draw, 0, 0, 0, $i . '.' .  $font );
        $im->compositeImage( $insert_image,  $insert_image->getImageCompose(),100, $y);
        $y += 30;
        $i++;
    }
    $im->setImageFormat('jpg');
    header('Content-Type: image/jpg');
    echo $im;

}
}