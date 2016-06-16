<?php

namespace xepan\commerce;

class page_itemdetail_upload_execute extends \Page {
	
	function page_index(){
		$item_id = $this->api->stickyGET('item_id');
		
		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1,'item_id'=>$item_id))."' enctype='multipart/form-data'>
			<input type='file' name='csv_qty_set_file'/>
			<label> Remove old qty sets <input type='checkbox' name='remove_old'/></label>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_qty_set_file']){
			if ( $_FILES["csv_qty_set_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_qty_set_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_qty_set_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_qty_set_file']['tmp_name'],true,',');
				$data = $importer->get();
				$item = $this->add('xepan\commerce\Model_Item')->load($item_id);

				if($_POST['remove_old']){
					// Remove all Quantity sets if said so
					$qs = $this->add('xepan\commerce\Model_Item_Quantity_Set');
					$qs->deleteQtySetAndCondition($item); // via sql
				}

				$qs = $this->add('xepan\commerce\Model_Item_Quantity_Set');
				// foreach ($data as $row) { // field like Qty ... Cst 1, Cst 2 ... Price
				$qs->insertQtysetAndCondition($item,$data);
				
				$this->add('View_Info')->set(count($data).' Recored Imported');
				$this->js(true)->univ()->closeDialog();
			}
		}
	}

}