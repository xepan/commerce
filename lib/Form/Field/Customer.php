<?php

namespace xepan\commerce;

class Form_Field_Customer extends  \xepan\base\Form_Field_Contact {

	public $id_field=null;
	public $title_field=null;
	public $include_status='Active'; // all, no condition
	public $contact_class = 'xepan\commerce\Model_Customer';

}