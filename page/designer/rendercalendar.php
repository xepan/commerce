<?php

namespace xepan\commerce;

class page_designer_rendercalendar extends \Page {
	function init(){
		parent::init();
		$options=array();
				
		$zoom = $options['zoom'] = $_GET['zoom'];
		
		$now = new \DateTime('now');
   		$current_month = $now->format('m');
   		$current_year = $now->format('Y');

		// $options['font_size'] = $_GET['font_size'] * ($zoom / 1.328352013);
		$options['font'] = $_GET['font'];
		$options['month'] = $_GET['month'];
		
		$options['header_font_size'] = $_GET['header_font_size'] * ($zoom / 1.328352013);
		$options['header_font_color'] = $_GET['header_font_color'];
		$options['header_bold'] = $_GET['header_bold'];
		$options['header_show'] = $_GET['header_show'];
		$options['header_font_family'] = $_GET['calendar_font_family'];

		$options['header_align'] = $_GET['header_align'];
		if(!in_array($_GET['header_align'], array("left",'right','center')))
			$options['header_align'] = "left";
		
		$options['day_date_font_size'] = $_GET['day_date_font_size'] * ($zoom / 1.328352013);
		$options['day_date_font_color'] = $_GET['day_date_font_color'];
		$options['day_name_bold'] = $_GET['day_name_bold'];
		$options['day_name_cell_height'] = $_GET['day_name_cell_height'];
		
		$options['day_name_font_size'] = $_GET['day_name_font_size'] * ($zoom / 1.328352013);
		$options['day_name_font_color'] = $_GET['day_name_font_color'];
		
		$options['event_font_size'] = $_GET['event_font_size'] * ($zoom / 1.328352013);
		$options['event_font_color'] = $_GET['event_font_color'];

		$options['calendar_cell_heigth'] = $_GET['calendar_cell_heigth'];

		if($_GET['day_name_bg_color'] != "#")
			$options['day_name_bg_color'] = $_GET['day_name_bg_color'];
		else
			$options['day_name_bg_color'] = "";
			
		if($_GET['calendar_cell_bg_color'] != "#")
			$options['calendar_cell_bg_color'] = $_GET['calendar_cell_bg_color'];
		else
			$options['calendar_cell_bg_color'] = "";

		if($_GET['header_bg_color'] != "#")
			$options['header_bg_color'] = $_GET['header_bg_color'];
		else
			$options['header_bg_color'] = "";

		$options['zindex'] = $_GET['zindex'];
		$options['width'] = $_GET['width'] * $zoom;
		$options['height'] = $_GET['height'] * $zoom;
		
		$options['starting_date'] = $now;
		$options['starting_month']= $current_month;
		$options['starting_year'] = $current_year;

		if($_GET['starting_month']){
			$options['starting_month'] = $_GET['starting_month'];
		}

		if($_GET['starting_year']){
			$options['starting_year'] = $_GET['starting_year'];
		}

		
		$options['resizable']= $_GET['resizable'];
		$options['movable']= $_GET['movable'];
		$options['colorable']= $_GET['colorable'];
		$options['x'] = $_GET['x'];
		$options['y'] = $_GET['y'];
		$options['events'] = $_GET['events'];
		$options['alignment'] = $_GET['alignment'];
		$options['valignment'] = $_GET['valignment'];
		$options['border'] = $_GET['border'];

		//calculate the year and month on basis of month and starting-Year for which Calendar will be draw
		//ex:  Starting month= "Nov 2015" and month is "8" then calendar will draw 8th month from Nov 2015 that is "July 2016"
		if(!$_GET['month'])
			$options['month'] = $options['starting_month'];

		if($_GET['global_starting_year']){//globally set for calendar sequence
			$options['starting_year'] = $_GET['global_starting_year'];
		}
		if($_GET['global_starting_month']){//globally set for calendar sequence
			$options['starting_month'] = $_GET['global_starting_month'];
		}

		$options['year'] = $options['starting_year'];


   		if($options['month'] < $options['starting_month']){
   			$options['year'] = $options['starting_year'] + 1;
   		}


   		// throw new \Exception("month= ".$options['month'] . " year=".$options['year']."Startin_year=".$options['starting_year']."Starting month".$options['starting_month']);
		   		
		$cont = $this->add('xepan\commerce\Controller_RenderCalendar',array('options'=>$options));
		$cont->show('png',3,true,false);

	}
}