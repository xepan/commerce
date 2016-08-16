<?php

namespace xepan\commerce;

class Controller_RenderCalendar extends \AbstractController {
	public $options = array();
	public $phpimage;
	public $pdf;

	function init(){
		session_write_close();
		parent::init();

		$all_events = json_decode($this->options['events'],true);
		$current_month_events = [];
		if(count($all_events[$this->options['month']]))
			$current_month_events = $all_events[$this->options['month']];

		// converting calendar month according to calendar starting month
		$current_month = strtotime("+".((int)$this->options['month'] - 1)." months", strtotime($this->options['starting_year']."-".$this->options['starting_month']."-01"));
		$this->options['month'] = date("m",$current_month);
		$this->options['year'] = date("Y",$current_month);
		
		$calendar_html = $this->drawCalendar($this->options['month'],$this->options['year'],[],$current_month_events,$this->options);
		//Convert Html to PDF
		$this->convertHtmlToPdf($calendar_html);
		//Convert PDF Data to Image Data
		$this->convertPdfToImage($this->pdf);

	}

	function convertPdfToImage($pdfData){
		$imageData = new \Imagick();
	   	$imageData->readimageblob($pdfData);
	   	$imageData->setImageFormat('png');
		// $imageData->extentImage($this->options['width'],$this->options['height'],0,0);
	   	$this->phpimage = $imageData;
	}

	function convertHtmlToPdf($html){
		if(!$html)
			throw new \Exception("Html Not Given");

		$pagelayout = array($this->options['width'],$this->options['height']); //  or array($width,$height)
		$pdf = new \TCPDF('l', 'px', $pagelayout, true, 'UTF-8', false);
		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);
		// $pdf = new \TCPDF_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetAutoPageBreak(false);

		if($this->options['header_font_family'])
			$pdf->SetFont($this->options['header_font_family']);
		// add a page
		$pdf->AddPage();
		$pdf->WriteHTML($html, true, false, true, false);
		$this->pdf = $pdf->Output(null,'S');
		//for test
		// $this->pdf = $pdf->Output(null);
		// echo $this->pdf;
		// exit;
	}

	function drawCalendar($month,$year,$resultA,$events=[],$styles=[]){
		$calendar = "";
		// style="text-align:left;"
		$header_font_size = 30;
		$day_date_font_size = 16;
		$day_name_font_size = 20;
		$event_font_size = 13;

		//Calculate Vertical Alignment as cellPadding
		//cell_paddding = cell_height / 2 - font_size / 2;

		$month_name_array = ['01'=>'January','1'=>'January','02'=>'February','2'=>'February','03'=>'March','3'=>'March','04'=>'April','4'=>'April','05'=>'May','5'=>'May','06'=>'June','6'=>'June','07'=>'July','7'=>'July','08'=>'August','8'=>'August','09'=>'September','9'=>'September','10'=>'October','11'=>'November','12'=>'December'];
		$month_name = $month_name_array[$month];
		if(is_array($styles)){
			$header_font_size = isset($styles['header_font_size'])?$styles['header_font_size']:32;
			$day_date_font_size = isset($styles['day_date_font_size'])?$styles['day_date_font_size']:16;
			$day_name_font_size = isset($styles['day_name_font_size'])?$styles['day_name_font_size']:20;
			$event_font_size = isset($styles['event_font_size'])?$styles['event_font_size']:13;
		}
		
		$cell_padding = 0;
		if($styles['valignment'] == 'middle')
			$cell_padding = (($styles['calendar_cell_heigth'] / 2) - ($day_date_font_size / 2));
		if($styles['valignment'] == 'bottom')
			$cell_padding = ( $styles['calendar_cell_heigth'] - $day_date_font_size);

  		/* draw table */
  		// echo "cell-padidng = ".$cell_padding."<br/>font-size=".$day_date_font_size." <br/>cell Height=".$styles['calendar_cell_heigth'];
  		// exit;  			
  		if($styles['header_show'] == "true"){
  			$calendar = '<div style="text-align:'.$styles['header_align'].';background-color:'.$styles['header_bg_color'].'; font-size:'.$header_font_size.'px;color:'.$styles['header_font_color'].';">'.$month_name.' - '.$year.'</div>';

	  		//Header Bold Options
	  		if($styles['header_bold'] == "true")
	  			$calendar = '<div style="text-align:'.$styles['header_align'].';background-color:'.$styles['header_bg_color'].'; font-face:K010; font-family:K010; font-size:'.$header_font_size.'px;color:'.$styles['header_font_color'].';"><b>'.$month_name.' - '.$year.'</b></div>';
  		}

  		$calendar .= '<table cellspacing="0" class="calendar" width="100%" align="center" border-collapse: collapse; border="'.$styles['border'].'" style="background-color:'.$styles['day_name_bg_color'].';">';
 		/* table headings */
  		$headings = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
  		if($styles['day_name_bold'] == "true")
  			$calendar.= '<tr style="font-size:'.$day_name_font_size.'px;color:'.$styles['day_name_font_color'].';" class="calendar-row"><td class="calendar-day-head"><b>'.implode('</b></td><td class="calendar-day-head"><b>',$headings).'</b></td></tr>';
  		else
  			$calendar.= '<tr style="font-size:'.$day_name_font_size.'px;color:'.$styles['day_name_font_color'].';" class="calendar-row"><td class="calendar-day-head" style="height:'.$styles['day_name_cell_height'].'px;">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
  		$calendar.="</table>";

  		$calendar .= '<table cellspacing="0" class="calendar" width="100%" align="center" border="'.$styles['border'].'" style="table-layout:fixed;padding-top:'.$cell_padding.'px; background-color:'.$styles['calendar_cell_bg_color'].'">';
  		/* days and weeks vars now ... */
  		$running_day = date('w',mktime(0,0,0,$month,1,$year));
  		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
  		$days_in_this_week = 1;
  		$day_counter = 0;

		 /* row for week one */
		$calendar.= '<tr class="calendar-row" style="text-align:'.$styles['alignment'].';">';
  		/* print "blank" days until the first of the current week */
  		for($x = 0; $x < $running_day; $x++){
    		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
    		$days_in_this_week++;
  		}

  		$run = 1;
		/* keep going with days.... */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++){

		    $calendar.= '<td class="calendar-day" style="overflow:hidden; height:'.$styles['calendar_cell_heigth'].'px; max-height:'.$styles['calendar_cell_heigth'].'px;font-size:'.$day_date_font_size.'px;color:'.$styles['day_date_font_color'].'">';
		    
		    /* add in the day number */

		    $date=date('Y-m-d',mktime(0,0,0,$month,$list_day,$year));
		    
		    $event_date_format = date('d-F-Y',strtotime($date));
		    if($message = $events[$event_date_format]){
		    	$calendar .= '<div style="height:'.$styles['calendar_cell_heigth'].'px; overflow:hidden; text-align:center; font-size:'.$event_font_size.'px;color:'.$styles['event_font_color'].'">'.$message."</div>";
		    }else
		    	$calendar .= '<div class="day-number" style="height:'.$styles['calendar_cell_heigth'].'px;">'.$list_day.'</div>';
		    

		    $tdHTML='';        
		    if(isset($resultA[$date])) $tdHTML=$resultA[$date];

		    $calendar.=$tdHTML;      

		    $calendar.= '</td>';

		    if($running_day == 6){
		      $calendar.= '</tr>';
		      if(($day_counter+1) != $days_in_month){
		        $calendar.= '<tr class="calendar-row" style="text-align:'.$styles['alignment'].';">';
		      }else{
		      	$run = 0;
		      }

		      $running_day = -1;
		      $days_in_this_week = 0;
		    }
		    $days_in_this_week++; $running_day++; $day_counter++;
		 	
		}

	  	/* finish the rest of the days in the week */
	  	if($run){
		  	if($days_in_this_week < 8){	
		    	for($x = 1; $x <= (8 - $days_in_this_week); $x++){
		    		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		  		}
		  	}
		  	/* final row */
		  	$calendar.= '</tr>';
		}
	  	/* end the table */
	  	$calendar.= '</table>';
	  	/* all done, return result */
	  	return $calendar;
	}

	function show($type="png",$quality=3,$base64_encode=true, $return_data=false){
		// $this->cleanup();

		// ob_start();
		// imagepng($this->phpimage, null,9,PNG_ALL_FILTERS);
		// $imageData = ob_get_contents();
		// ob_clean();

		$imageData = $this->phpimage;

		if($base64_encode){			
			$imageData = base64_encode($imageData);
		}
		
		if($return_data)
			return $imageData;

		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		if($type=="png")
			header("Content-type: image/png");
		// imagepng($this->phpimage, null, 9, PNG_ALL_FILTERS);
		
		echo $imageData;
		die();
	}

	public function cleanup(){
		imagedestroy($this->phpimage);
	}
}