<?php
namespace xepan\commerce;
class page_itemtype extends \Page{
	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];
		if(!$item_id)
			return;
		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
				
		// $m = $this->setModel($item);
		
		$this->app->side_menu->addItem('Check Box Filter');
		$sal = $this->app->side_menu->addItem('Is_Saleable','');
		$this->app->side_menu->addItem('Is_Purchasable','');
		$this->app->side_menu->addItem('Is_Productionable','');
		$this->app->side_menu->addItem('Is_AllowUploadable','');
		$this->app->side_menu->addItem('Website_Display','');
	}

	// function setModel($item){
 //        parent::setModel($item)

	//         $this->sal->onClick(function()use($sal){
	//             return $this->js()->reload();
 //        });
	// }
} 