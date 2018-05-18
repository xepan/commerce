<?php

namespace  xepan\commerce;

	class Tool_MyWishlist extends \xepan\cms\View_Tool{

		public $customer_id = null;
		public $show_status = 'Due';
		public $paginator = 10;
		public $detail_page = null;

			function init(){
			parent::init();

		$this->add("xepan\commerce\View_Wishlist",['customer_id']);
			}
		}
	
