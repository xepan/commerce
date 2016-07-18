<?php


namespace xepan\commerce;

class View_Designer_FontUpload extends \View{
	function init() {
		parent::init();

			$target_dir =DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'xepan2'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'xepan'.DIRECTORY_SEPARATOR.'commerce'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR;
			
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$fontFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
				$font_format= explode('-', $target_file);
				
				if(! in_array($font_format[1], array('Regular.ttf','Bold.ttf','Italic.ttf','BoldItalic.ttf'))){
					$this->api->js(true,$this->js()->reload())->univ()->errorMessage('Wrong Font name ');
					$uploadOk=0;
					echo $font_format[1];
				}
				if (file_exists($target_file)) {
				    $this->api->js(true)->univ()->errorMessage('File Already Exist');
				    $uploadOk = 0;
				}
				if($fontFileType != "ttf") {
				    $this->api->js(true)->univ()->errorMessage('Only ttf file is upload');
				    $uploadOk = 0;
				}
				if ($uploadOk == 1) {
			    	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				    	$this->api->js(true,$this->js()->reload())->univ()->successMessage('Font File '.basename( $_FILES["fileToUpload"]["name"]." has been uploded"));
						
						/*TTF File To Convert & create TCPDF Font file*/
						$pdf = new \TCPDF('l', 'pt', '', true, 'UTF-8', false);
					    $fontname = \TCPDF_FONTS::addTTFfont($target_file, 'TrueTypeUnicode', '',32);
					    $pdf->AddFont($fontname, '', 14, '', false);

			    	} else {
					    $this->app->js(true)->univ()->errorMessage('Sorry, there was an error uploading your file');
			    	} 
				}
			}
	}
	function defaultTemplate() {
		return ['view/designer/fontupload'];
	}
}			