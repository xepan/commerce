<?php

namespace xepan\commerce;

class page_store_upload_execute extends \Page {
	
	function page_index(){
		// $item_id = $this->api->stickyGET('item_id');
		
		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1))."' enctype='multipart/form-data'>
			<input type='file' name='csv_barcode_file'/>
			<label> Remove All borcode  <input type='checkbox' name='remove_old'/></label>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_barcode_file']){
			if ( $_FILES["csv_barcode_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_barcode_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_barcode_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_barcode_file']['tmp_name'],true,',');
				$data = $importer->get();
				
				// var_dump($data);
				// exit;
				foreach ($data as  $row) {
					if($row['name']){
						$barcode = $this->add('xepan\commerce\Model_BarCode');
						$barcode->addCondition('name',$row['name']);
						$barcode->tryLoadAny();
						if(!$barcode->loaded()){
							$barcode->save();
						}
					}
				}
				
				$this->add('View_Info')->set(count($data).' Recored Imported');
				$this->js(true)->univ()->closeDialog();
			}
		}
	}

}