<?php

namespace xepan\commerce;

class page_itemdetail_upload extends \Page {
	
	function page_index(){
		
		$item_id = $this->api->stickyGET('item_id');

		$form = $this->add('Form');
		$form->addField('line','custom_fields')->setFieldHint('Enter comma separated custom fields, Qty,Price,Name,OldPrice,Price,IsDefault');
		$form->addSubmit('Generate Sample File');
		
		if($_GET['headers']){
			
			$output=array("Qty");
			foreach (explode(",", $_GET[$this->name]) as $cfs) {
				$output[] = trim($cfs);
			}
			$output[] = "Price";

			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_qty_set_file.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->reload(['headers'=>$form['custom_fields']])->execute();
			// $form->js()->univ()->location($this->api->url(null,array($this->name=>$form['custom_fields'])))->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('./execute',array('cut_page'=>1,'item_id'=>$item_id)))->setAttr('width','100%');
	}


}