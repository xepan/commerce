<?php

namespace xepan\commerce;

class page_store_uploadbarcodedata extends \Page {
	
	function page_index(){
		
		$item_id = $this->api->stickyGET('item_id');

		$form = $this->add('Form');
		$form->addField('line','barcode')->setFieldHint('Header Name is (name) Fixed  can not change ( Only Csv Files Upload ) ');
		$form->addSubmit('Generate Sample File');
		
		if($_GET['headers']){
			
			$output=array("name");
			foreach (explode(",", $_GET[$this->name]) as $cfs) {
				$output[] = trim($cfs);
			}
			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_barcode_file.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->reload(['headers'=>$form['barcode']])->execute();
			// $form->js()->univ()->location($this->api->url(null,array($this->name=>$form['custom_fields'])))->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('xepan_commerce_store_upload_execute',array('cut_page'=>1)))->setAttr('width','100%');
	}


}