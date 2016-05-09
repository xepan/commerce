<?php
 
namespace xepan\commerce;

class page_categoryitem extends \xepan\base\Page {

	public $title='Category Item';

	function init(){
		parent::init();
		
		$cat = $this->add('xepan\commerce\Tool_CategoryItem');

		function CategoryItem($cat){
			for($i=1;$i>=$category_id;$i++){
				$element==$cat[$i];
				if(gettype($element)=="array"){
					CategoryItem($element);
				}else{
					echo $element."<br>";
					}
			}
		}
	}
	
}
