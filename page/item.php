 <?php
namespace xepan\commerce;
class page_item extends \Page{

	public $title='Items';

	function init(){
		parent::init();
		$crud=$this->add('CRUD');
		$crud->setModel('xepan\commerce\Model_Item');

		$g=$this->add('Grid')->setModel('xepan\commerce\Model_Item');
	}

} 