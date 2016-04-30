<?php

namespace xepan\commerce;

class page_test extends \Page{
	
	function init(){
		parent::init();

		$d = $this->app->db->dsql();
		$d->sql_templates['delete'] = "delete [table] from  [table] [join] [where]";
		$d->table('contact_info')->where('contact.id is null')->join('contact',null,'left')->debug()->delete();

		}
}