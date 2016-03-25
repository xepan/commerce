<?php

namespace xepan\commerce;

class View_Item_AddToCart extends \View{
	public $item_model;
	public $item_member_design_model;
	public $name;
	public $show_custom_fields=false;
	public $show_qty_selection=false;
	public $show_price=false;
	public $options = array();
	public $qty_set = array();
	public $show_cart_btn = 1;
	public $file_upload_id;

	function init(){
		parent::init();
		

		$this->options = $this->item_model->getBasicCartOptions();
		$this->options['item_member_design_id'] = $this->item_member_design_model['id']?:'0';
		$this->options['show_qty'] = $this->show_qty_selection ?'1':'0';
		$this->options['show_price'] = $this->show_price;
		$this->options['show_custom_fields'] = $this->show_custom_fields;
		$this->options['is_designable'] = $this->item_model['is_designable'];
		$this->options['show_cart_btn'] = $this->show_cart_btn;
		$this->options['is_uploadable'] = $this->item_model['allow_uploadedable'];
		$this->options['file_upload_id'] = $this->file_upload_id;

		// echo"<pre>";
		// print_r($this->options);
		// // print_r($qty_set_array);
		// echo"</pre>";
		// exit;
	}

	function render(){
		// $this->api->jui->addStaticStyleSheet('addtocart');
		$this->js(true)->_load('item/addtocart')->xepan_xshop_addtocart($this->options);
		parent::render();
	}
}

/*
options:{
		item_id: undefined,
		item_member_design_id: undefined,

		show_qty: false,
		qty_from_set_only: false,
		qty_set: {
			Values:{
				value:{
					name:'Default',
					qty:1,
					old_price:100,
					price:90,
					conditions:{
							custom_fields_condition_id:'custom_field_value_id'
					.......//QyantitySetCondition_id :Custom Fields Calue Id ................
						}
				}
			}
		},

		show_custom_fields: false,
		custom_fields:{
			size : {
				type: 'DropDown',
				values:[
					{value:9},
					{value:10},
					{
						value: 11,
						filters:{
							color: 'red' // This is filter
						}
					},
				]
			},
			color: {
				type: 'Color',
				values:[
					{value:'red'},
					{
						value:'green',
						filters :{
							size: [9,11] // not available in 9 and 11 sizes
						}
					}
				]
			}
		},
	},
*/