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
				if($_FILES['csv_qty_set_file']['type'] != 'text/csv'){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_qty_set_file']['tmp_name'],true,',');
				$data = $importer->get();
				$item = $this->add('xepan\commerce\Model_Item')->load($item_id);

				if($_POST['remove_old']){
					// Remove all Quantity sets if said so
					// TODO
					$qs = $this->add('xepan\commerce\Model_Item_Quantity_Set');
					$qs->addCondition('item_id',$item->id);

					foreach ($qs as $junk) {
						$qs->deleteQtySetCondition();
						$qs->delete();
					}
				}

				foreach ($data as $row) { // field like Qty ... Cst 1, Cst 2 ... Price
					// take qty and price and enter a condition set row 
					// $nqs = (unset qty, price and continue) xShop/QuantitySet
					$qs = $this->add('xepan\commerce\Model_Item_Quantity_Set');
					$qs['item_id'] = $item->id;
					$qs['qty'] = $row['Qty'];
					$qs['price'] = $row['Price'];
					$qs->save();
					unset($row['Qty']);
					unset($row['Price']);

					// Take rest of fields for making conditions in this set 
					foreach ($row as $field => $value) {
						// $ncf = Create/Load new Custom field If not exists $field xShop/Model_CustomFields
						$cf = $this->add('xepan\commerce\Model_Item_CustomField')
							->addCondition('name',$field)
							->tryLoadAny();
						if(!$cf->loaded()) $cf->save();

						$icassos = $this->add('xepan\commerce\Model_Item_CustomField_Association')
										->addCondition('customfield_generic_id',$cf->id)
										->addCondition('item_id',$item->id)
										->addCondition('can_effect_stock',true)
										->tryLoadAny();

						if(!$icassos->loaded()) $icassos->save();
						
						if(!$value) continue;

						// $ncfv = check if its $value is added for current item, if not add xShop/Model_CustomFieldValue
						$iassoscfval = $this->add('xepan\commerce\Model_Item_CustomField_Value')
										->addCondition('customfield_association_id',$icassos->id)
										->addCondition('name',trim($value))
										->addCondition('status',"Active")
										->tryLoadAny();

						if(!$iassoscfval->loaded()) $iassoscfval->save();
						// add condition if not exists xShop/Model_QuantitySetCondition


						$qscond = $this->add('xepan/commerce/Model_Item_Quantity_Condition')
										->addCondition('quantity_set_id',$qs->id)
										->addCondition('customfield_value_id',$iassoscfval->id)
										->tryLoadAny();

						if(!$qscond->loaded())
							$qscond->save();
					}
				}
				
				$this->add('View_Info')->set(count($data).' Recored Imported');
				$this->js(true)->univ()->closeDialog();
			}
		}
	}

}