<?php

	namespace xepan\commerce;

	class Model_Wishlist extends \xepan\base\Model_Table{
		public $table = "wishlist";
		public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'InActive'=>['view','edit','delete','activate']
				];

				function init(){
			parent::init();
					$this->addField('item_id');
					$this->addField('status')->enum($this->status)->defaultValue('Active');

					$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);

					$this->addField('is_active')->type('boolean');
					/*$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		*/
					$this->add('dynamic_model\Controller_Autocreator');

					$this->hasMany('xepan\commerce\wishlist','item_id');
		$this->is(
			[
				'id|to_trim|required',
				'status|to_trim|required',
			]
		);



		}
function deactivate(){
		$this['status'] = "InActive";
		$this->save();
	}

	function activate(){
		$this['status'] = "Active";
		$this->save();
	}

}