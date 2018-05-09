<?php
	namespace xepan\commerce;

	class Tool_Review extends \xepan\cms\View_Tool{
		
		function init(){
			parent::init();
			if($this->owner instanceof \AbstractController) return;

		}

	}


