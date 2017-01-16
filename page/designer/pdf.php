<?php

namespace xepan\commerce;

class page_designer_pdf extends \Page {
	public $put_water_mark  = true;
	public $print_ratio = 1;
	public $false_array=array('undefined','null','false',false);

	function init(){
		parent::init();

		if($_GET['print_ratio'])
			$this->print_ratio = $_GET['print_ratio'];
		
		$item_id = $_GET['item_id']?:false;
		$item_member_design_id = !in_array($_GET['item_member_design_id'], $this->false_array) ? $_GET['item_member_design_id']:false;
		$xsnb_design_template = $_GET['xsnb_design_template']=='true'?true:false;

		if(!$this->api->auth->isLoggedIn()){
			echo "Something doesn't look right";
			exit;
		}

		$member = $this->add('xepan\base\Model_Contact');
		$member_logged_in = $member->loadLoggedIn("Customer");

		if(!$member_logged_in){
			echo "Must be called from a valid logged in xShop Member";
			exit;
		}

		if($item_member_design_id){
			$target = $this->item = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($item_member_design_id);
			if(!$target->loaded()){
				echo "could not load design";
				exit;
			} 
			$item =$target->ref('item_id');
		}

		if($item_id  and !isset($target)){
			$target = $this->item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			if(!$target->loaded()){
				echo "could not load item";
				exit;
			}
			$item = $target;
		}
		
		if( !($member->user()->isAdminUser() or $member->user()->isSuperUser()) ){
			if($xsnb_design_template and $target['designer_id'] != $member->id){
				echo "You are not allowed to take the template preview";
				exit;
			}

			if( !$xsnb_design_template and $target['contact_id'] != $member->id){
				echo "You are not allowed to take the design preview";
				exit;
			}
		}else{
			$this->put_water_mark = false;
		}

		// $design = json_decode($this->item['designs'],true);
		// $design = $design['design']; // trimming other array values like px_width etc
		// $design = json_encode($design);
		
		$design = $target['designs'];
		
		$design = json_decode($design,true);

		$this->px_width = $design['px_width'] * $this->print_ratio;

		$selected_layouts_for_print = $design['selected_layouts_for_print'];
		$design=$design['design'];
		// foreach ($design as $page_name => $layouts) {
		// 	foreach ($layouts as $layout_name => $content) {
		// 		echo $page_name .' ' . $layout_name . ' <br/>';
		// 	}
		// }
		// exit;

		
		$this->specification = $this->fetchDimensions($item);

		$pdf = new FPDF_xPdf($this->getOrientation($this->specification),$this->specification['unit'],array($this->specification['width'],$this->specification['height']));
		foreach ($design as $page_name => $layouts) {
			$pdf->AddPage();
			
			// $pdf->SetFont('Arial','B',16);
			// $pdf->Cell(40,10,$page_name);
			$i=1;
			// foreach ($layouts as $layout_name => $content) {
				// if($i++ > 1) continue; // Just mainlayout only for now
				// background
				$content = $layouts[$selected_layouts_for_print[$page_name]];
				$background_options = json_decode($content['background'],true);
				$this->addImage($background_options,$pdf);

				// components
				foreach ($content['components'] as $comp) {
					$options = json_decode($comp,true);
					if($options['type']=='Image'){
						$this->addImage($options,$pdf);
					}
					if($options['type'] == 'Text'){
						$this->addText($options,$pdf);
					}
					if($options['type'] == 'Calendar'){
						$this->addCalendar($options,$pdf);
					}
				}
				// $pdf->Cell(40,10,$layout_name);
			// }
			if($this->put_water_mark)
				$this->putWaterMark($pdf);
		}

		$pdf->Output();
	}

	function putWaterMark($pdf){
		$pdf->SetAlpha(0.4);
		$pdf->SetFont('Arial','B',30);
	    $pdf->SetTextColor(255,192,203);
	    $pdf->Rotate(35,5,30);
	    $pdf->Text(0,$this->specification['height'],$_GET['watermark_text']);
	    $pdf->Rotate(0);
	    // $pdf->RotatedText(35,190,'W a t e r m a r k   d e m o',45);
	}

	function addImage($options, $pdf){
		if($options['url']){

			$path1 = getcwd().'/'.$options['url'];
			// $path2 = getcwd().'/websites/'.$this->app->current_website_name."/".$_GET['url'];
			$path2 = dirname(getcwd()).'/'.$options['url'];
			$url = $path1;
			if(!file_exists($url)) {
				$url = $path2;
			}

			$options['url']= $url;
			
			$options['width'] = $options['width'] * $this->print_ratio;
			$options['height'] = $options['height'] * $this->print_ratio;
			$options['x'] = $options['x'] * $this->print_ratio;
			$options['y'] = $options['y'] * $this->print_ratio;

			$cont = $this->add('xepan\commerce\Controller_RenderImage',array('options'=>$options));
			$data = $cont->show('png',1,false,true);
			$pdf->MemImage($data, $this->pixcelToUnit($options['x']), $this->pixcelToUnit($options['y']), $this->pixcelToUnit($options['width']), $this->pixcelToUnit($options['height']));
		}
	}


	function addText($options, $pdf){
		if($options['text']){
			$options['desired_width'] = $options['width'] * $this->print_ratio;
			$options['x'] = $options['x'] * $this->print_ratio;
			$options['y'] = $options['y'] * $this->print_ratio;
			$options['font_size'] = $options['font_size'] * ($this->print_ratio);
			// $options['font_size'] = $options['font_size'] * ($this->print_ratio / 1.328352013);
			$options['text_color'] = $options['color_formatted'];

			// echo "<pre>";
			// print_r($options);
			// echo "</pre>";
			// exit;

			$cont = $this->add('xepan\commerce\Controller_RenderText',array('options'=>$options));
			$options['height'] = $cont->new_height /  $this->print_ratio;

			$data = $cont->show('png',1,false,true);
			// $pdf->MemImage($data, 0, 0, 100, 20);
			$pdf->MemImage($data, $this->pixcelToUnit($options['x']), $this->pixcelToUnit($options['y']), $this->pixcelToUnit($options['desired_width']), $this->pixcelToUnit($options['height'] * $this->print_ratio));
		}
	}

	function addCalendar($options,$pdf){

		//BG Color 
		if($options['header_bg_color'] == "#")
			$options['header_bg_color'] = "";

		if($options['day_name_bg_color'] == "#")
			$options['day_name_bg_color'] = "";

		if($options['calendar_cell_bg_color'] == "#")
			$options['calendar_cell_bg_color'] = "";


		//Position, Dimention and Zindex Calculation
		$options['width'] = $options['width'] * $this->print_ratio;
		$options['height'] = $options['height'] * $this->print_ratio;
		$options['x'] = $options['x'] * $this->print_ratio;
		$options['y'] = $options['y'] * $this->print_ratio;
		$options['zindex'] = $options['zindex'];

		//Font Size Calculation
		$options['header_font_size'] = $options['header_font_size'] * ($this->print_ratio / 1.328352013);
		$options['day_name_font_size'] = $options['day_name_font_size'] * ($this->print_ratio / 1.328352013);
		$options['day_date_font_size'] = $options['day_date_font_size'] * ($this->print_ratio / 1.328352013);
		$options['event_font_size'] = $options['event_font_size'] * ($this->print_ratio / 1.328352013);
		$options['calendar_cell_heigth'] = 40 * $this->print_ratio;
		//Date Calculaion
		$now = new \DateTime('now');
   		$current_month = $now->format('m');
   		$current_year = $now->format('Y');
   		$options['starting_date'] = $now;
		$options['starting_month']= $current_month;
		$options['starting_year'] = $current_year;

		if($options['starting_month']){
			$options['starting_month'] = $options['starting_month'];
		}

		if($options['starting_year']){
			$options['starting_year'] = $options['starting_year'];
		}

		if(!$options['month'])
			$options['month'] = $options['starting_month'];

		if($options['global_starting_year']){//globally set for calendar sequence
			$options['starting_year'] = $options['global_starting_year'];
		}
		if($options['global_starting_month']){//globally set for calendar sequence
			$options['starting_month'] = $options['global_starting_month'];
		}

		$options['year'] = $options['starting_year'];


   		if($options['month'] < $options['starting_month']){
   			$options['year'] = $options['starting_year'] + 1;
   		}
   		//--------------------------End of date Calculation--------------------
		$cont = $this->add('xepan\commerce\Controller_RenderCalendar',array('options'=>$options));
		$data = $cont->show('png',1,false,true);
		// $pdf->MemImage($data, 0, 0, 100, 20);
		$pdf->MemImage($data, $this->pixcelToUnit($options['x']), $this->pixcelToUnit($options['y']), $this->pixcelToUnit($options['width']), $this->pixcelToUnit($options['height']));
	}

	function fetchDimensions($item){
		$this->specification=array();
		preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $item->specification('width'),$temp);
		$this->specification['width']= $temp[1][0];
		preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $item->specification('height'),$temp);
		$this->specification['height']= $temp[1][0];
		$this->specification['unit']=$temp[2][0];

		preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $item->specification('trim'),$temp);
		$this->specification['trim']= $temp[1][0];

		return $this->specification;
	}

	function getOrientation($specification){
		if($specification['width'] > $specification['height'])
			return 'l';
		else
			return 'p';
	}

	function pixcelToUnit($pixels){
		return $this->specification['width']/$this->px_width * $pixels;
	}
}