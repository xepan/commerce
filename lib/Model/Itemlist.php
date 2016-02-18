// <?php

// namespace xepan\commerce;
// class Model_Itemlist extends \Model_Table{
// 	public $table='item';
	
	
// 	function init(){
// 		parent::init();
		
// 		 // $this->hasOne('xepan\base\epan','epan_id');
// 		// $this->addCondition('epan_id',$this->api->current_website->id);

// 		// Basic Field
// 		$this->addField('name')->mandatory(true);
// 		//$this->addField('sku')->PlaceHolder('Insert Unique Referance Code')->caption('Code')->hint('Place your unique Item code ')->mandatory(true);
// 		$this->addField('is_publish')->type('boolean')->defaultValue(true);
// 		$this->addField('is_party_publish')->type('boolean')->defaultValue(false);

// 		// $this->addField('original_price')->type('money')->mandatory(true);
// 		// $this->addField('sale_price')->type('money')->mandatory(true);
// 		// $this->addField('short_description')->type('text');
		
// 		// $this->addField('rank_weight')->defaultValue(0)->hint('Higher Rank Weight Item Display First')->mandatory(true);
// 		// $this->addField('expiry_date')->type('date');
// 		// $this->addField('description')->type('text');

// 		// $this->add('dynamic_model/Controller_AutoCreator');
		
		
// 	}
// }