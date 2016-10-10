<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Set extends \xepan\base\Model_Table{
 	public $acl =false;
 	public $table = "quantity_set";
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();


		$this->hasOne('xepan\commerce\Item','item_id');

		$this->addField('name');//->sortable(true); // To give special name to a quantity Set .. leave empty to have qty value here too
		$this->addField('qty')->type('number')->mandatory(true);//->sortable(true);
		$this->addField('old_price')->type('money')->mandatory(true)->caption('Unit Old Price');//->sortable(true);
		$this->addField('price')->type('money')->mandatory(true)->caption('Unit Price');//->sortable(true);
		$this->addField('is_default')->type('boolean')->defaultValue(false);
		
		$this->hasMany('xepan\commerce\Item\Quantity\Condition','quantity_set_id');

		$this->addExpression('custom_fields_conditioned')->set(function($m,$q){
			$temp =$m->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$q->getField('id'));
			return $temp->_dsql()->group('quantity_set_id')->del('fields')->field('count(*)');
		});//->sortable(true);

		$this->addExpression('conditions')->set(function($m,$q){
			$x = $m->add('xepan\commerce\Model_Item_Quantity_Condition',['table_alias'=>'qtycondition_str']);
			return $x->addCondition('quantity_set_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR "<br/>")',[$x->getElement('customfield_value')]));
		})->allowHTML(true);


		$this->addExpression('type')->set("'QuantitySet'");

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){

		$condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$this->id);
		
		foreach ($condition as $value) {
			$value->delete();
		}
	}

	function deleteQtySetCondition(){
		if(!$this->loaded())
			throw new \Exception("model must loaded", 1);

		$this->add('xepan\commerce\Model_Item_Quantity_Condition')
			->addCondition('quantity_set_id',$this->id)
			->deleteAll();
			
	}

	function beforeSave(){
		if(!$this['name'])
			$this['name'] = $this['qty'];
	}

	function deleteQtySetAndCondition($item_model){
		if( !($item_model instanceof xepan\commere\Model_Item or $item_model->loaded()))
			throw new \Exception("item model required at time of qty set and condition deletion");
		
		$sql = "
		            DELETE 
						quantity_set, _qcondition
						FROM
						`quantity_set`
						LEFT JOIN `quantity_condition` AS `_qcondition` ON `_qcondition`.`quantity_set_id` = `quantity_set`.`id`
						WHERE
							`quantity_set`.`item_id` in (".$item_model->id.")
		        ";

		$this->app->db->dsql()->expr($sql)->execute();

	}

	function insertQtysetAndCondition($item_model,$csv_data){

		if(!$item_model->loaded())
			throw new \Exception("item model must be loaded to duplicate", 1);
		
		if(!count($csv_data))
			throw new \Exception("no record found in your csv");
		
		$cf_data_array = $this->add('xepan\commerce\Model_Item_CustomField')->getRows();
		$cf_mapping_array = [];

		// get all saved cf mapping array
		foreach ($cf_data_array as $cf) {
			if(isset($cf_mapping_array[$cf['name']]))
				continue;
			$cf_mapping_array[trim($cf['name'])] = $cf['id'];
		}

		/**
			 insertig all qty set 
		*/
		$q_set_sql = "INSERT into quantity_set (item_id,name,qty,old_price,price,is_default) VALUES ";

		foreach ($csv_data as $row) {
			$q_name = $row['Name']?$row['Name']:$row['Qty'];
			$q_qty = $row['Qty'];
			$q_old_price = $row['OldPrice']?$row['OldPrice']:$row['Price'];
			$q_price = $row['Price'];
			$q_is_default = $row['IsDefault']?:0;

			$q_set_sql .= "('".$item_model['id']."','".$q_name."','".$q_qty."','".$q_old_price."','".$q_price."','".$q_is_default."'),";
		}

		$q_set_sql = trim($q_set_sql,',');
		// echo $q_set_sql .'<br/><br/><br/><br/>';
		$this->app->db->dsql()->expr($q_set_sql)->execute();
		
		// get new inserted qty
		$new_q_set_id = [];
		$new_qset_id_temp = $this->add('xepan\commerce\Model_Item_Quantity_Set')->setOrder('id')->addCondition('item_id',$item_model->id)->getRows();
		foreach ($new_qset_id_temp as $t) {
			$new_q_set_id[] = $t['id'];
		}


		$cf_asso_mapping_array = [];
		$cf_value_mapping_array = [];


		$q_set_val_sql = "INSERT into quantity_condition (quantity_set_id,customfield_value_id) VALUES ";
		//First check for Custom Field exit or not if not then create
		$count = 0;
		$query_updated = 0;
		foreach ($csv_data as $row) {
			unset($row['Price'],$row['Name'],$row['Qty'],$row['OldPrice'],$row['IsDefault']);

			foreach ($row as $field => $value) {

				// if custom field is new then add and insert into cf_mapping_array
				if(!isset($cf_mapping_array[trim($field)])){
					$cf = $this->add('xepan\commerce\Model_Item_CustomField')
								->addCondition('name',trim($field))
								->tryLoadAny();
					if(!$cf->loaded()){
						$cf->save();
						$cf_mapping_array[trim($cf['name'])] = $cf->id;
					} 
				}
				
				if(!$value) continue;

				$icassos_id = 0;
				if(!isset($cf_asso_mapping_array[$cf_mapping_array[trim($field)]][$item_model->id])){
					$icassos = $this->add('xepan\commerce\Model_Item_CustomField_Association')
											->addCondition('customfield_generic_id',$cf_mapping_array[trim($field)])
											->addCondition('item_id',$item_model->id)
											->tryLoadAny();
					if(!$icassos->loaded()){
						$icassos->addCondition('can_effect_stock',true)->save();
					}
					
					$icassos_id = $icassos->id;
					$cf_asso_mapping_array[$cf_mapping_array[trim($field)]][$item_model->id] = $icassos_id;
				}else
					$icassos_id = $cf_asso_mapping_array[$cf_mapping_array[trim($field)]][$item_model->id];


				$iassoscfval_id = 0;
				// $ncfv = check if its $value is added for current item, if not add xShop/Model_CustomFieldValue
				if(!isset($cf_value_mapping_array[$icassos_id][trim($value)])){
					$iassoscfval = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id',$icassos_id)
									->addCondition('name',trim($value))
									->addCondition('status',"Active")
									->tryLoadAny();

					if(!$iassoscfval->loaded()) $iassoscfval->save();
					$cf_value_mapping_array[$icassos_id][trim($value)] = $iassoscfval->id;
					$iassoscfval_id = $iassoscfval->id;

				}else
					$iassoscfval_id = $cf_value_mapping_array[$icassos_id][trim($value)];


				$q_set_val_sql .= "('".$new_q_set_id[$count]."','".$iassoscfval_id."'),";
				$query_updated++;
			}
			$count++;
		}

		if($query_updated){
			$q_set_val_sql = trim($q_set_val_sql,',');
			$this->app->db->dsql()->expr($q_set_val_sql)->execute();
		}

	}

} 
 
	

