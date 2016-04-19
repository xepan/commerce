<?php



namespace xepan\commerce;

class page_tests_init extends \AbstractController{

	function init(){
		parent::init();

		$this->app->xepan_app_initiators['xepan\commerce']->resetDB();

		$cust = $this->add('xepan\commerce\Model_Customer')
				->set('first_name',"Customer1")
				->set('last_name',"Sirname")
        		->save();



        $supl = $this->add('xepan\commerce\Model_Supplier')
				->set('first_name',"Supplier1")
				->set('last_name',"Sirname")
        		->save();

        $tax = $this->add('xepan\commerce\Model_Taxation')
        		->set('name','VatTax')
        		->save();

		$item = $this->add('xepan\commerce\Model_Item')
    			->set('name','Item1')
    			->set('sku','48848')
        		->save();

        $cat= $this->add('xepan\commerce\Model_CategoryItemAssociation')->getAssociatedCategories($item)
        		->set('name','ItemCategories')
        		->set('meta_description','Testing Description')
        		->save();
	}
}