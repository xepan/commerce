<?php

namespace xepan\commerce;

class Controller_getRate extends \AbstractController {
	
	public $rate_field='rate';
	public $qty_field='qty';
	public $custom_fields_field='custom_fields';
	public $item_field='item_id';
	public $amount_field='amount';

	function init(){
		parent::init();


		if($this->owner instanceof \CRUD){
			if(!$this->owner->isEditing('add') and !$this->owner->isEditing('edit')) return;
			$form = $this->owner->form;
		}

		if($this->owner instanceof \Form)
			$form = $this->owner;


		$rate_field = $form->getElement($this->rate_field);
        $qty_field = $form->getElement($this->qty_field);
        $item_field = $form->getElement($this->item_field);
        $custom_fields_field = $form->getElement($this->custom_fields_field);
        $amount_field = $form->getElement($this->amount_field);

        $rate_field->js('blur',"\$('#$amount_field->name').val(\$('#$rate_field->name').val() * \$('#$qty_field->name').val())");

        $get_rate_js_chain = $this->owner->js()->univ()->ajaxec(
                array( // URL with JS parameters
                $this->api->url(null,array('xget_rate'=>1)),
                    'item_id'=>$item_field->js()->val(),
                    'qty'=>$qty_field->js()->val(),
                    'custom_fields'=>$custom_fields_field->js()->val()
                )
            );

        $item_field->other_field->on('change', $get_rate_js_chain);
        $qty_field->on('change', $get_rate_js_chain);
        $custom_fields_field->on('change', $get_rate_js_chain);

        if($this->api->stickyGET('xget_rate')){            
            $item = $this->add('xShop/Model_Item')->load($_GET['item_id']);
            $rate = $item->getPriceBack($custom_field_values_array = json_decode($_GET['custom_fields'],true), $qty=$_GET['qty'], $rate_chart='retailer');
            $out_work = array();
            $out_work[] = $rate_field->js()->val($rate['sale_price']);
            $out_work[] = $amount_field->js()->val($rate['sale_price'] * $_GET['qty']);
            echo implode(";", $out_work);
            exit;
        }

	}
}