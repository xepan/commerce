<?php

namespace xepan\commerce;

/**
* 
*/
class page_quickpos extends \xepan\base\Page{
	public $title = "Qucik POS";
	function init(){
		parent::init();

		$customer =  $this->add('xepan\commerce\Model_Customer');
		$item = $this->add('xepan\commerce\Model_Item');

		$form = $this->add('Form');
		$template = $this->add('GiTemplate');
        $template->loadTemplate('view/quickpos');
        $template->trySetHTML('customer','{$customer}');
        $template->trySetHTML('sales_person','{$sales_person}');

		for ($m=1; $m < 6; $m++) { 
			$item_template = $this->add('GiTemplate');
            $item_template->loadTemplate('view/quickpositemrows');
            $item_template->trySetHTML('item','{$item_x_'.$m.'}');
            $item_template->trySetHTML('qty','{$qty_x_'.$m.'}');
            $item_template->trySetHTML('price','{$price_x_'.$m.'}');
            $item_template->trySetHTML('amount','{$amount_x_'.$m.'}');

			$template->appendHTML('items',$item_template->render());
		}

		$template->loadTemplateFromString($template->render());
        $form->setLayout($template);

		$cus_field = $form->addField('xepan\base\DropDown','customer')->setEmptytext('Please Select Customer');
		$cus_field->setModel($customer);

		for ($m=1; $m < 6; $m++) { 
			$item_field = $form->addField('xepan\base\Basic','item_x_'.$m);
			$item_field->setModel($item);
			$qty_field = $form->addField('line','qty_x_'.$m,'Quantity');
			$price_field = $form->addField('line','price_x_'.$m,'Price');
			$amount_field = $form->layout->add('View',null,'amount_x_'.$m);

			if($qty = $this->app->stickyGET('qty')){
				$qty_price = $_GET['price'] * $qty;
				$amount_field->set($qty_price);
			}
			if($price = $this->app->stickyGET('price')){
				$amount_field->set( $_GET['qty'] * $price );
			}
			$qty_field->js('change',
					$amount_field->js()->reload(null,null,
						[
							$this->app->url(null,['cut_object'=>$amount_field->name]),
							'qty'=>$qty_field->js()->val(),
							'price'=>$price_field->js()->val()
						]
					)
			);
			$price_field->js('change',
					$amount_field->js()->reload(null,null,
						[
							$this->app->url(null,['cut_object'=>$amount_field->name]),
							'qty'=>$qty_field->js()->val(),
							'price'=>$price_field->js()->val()
						]
					)
			);
		}

		$rsp_field = $form->addField('xepan\base\DropDown','sales_person')->addClass('xepan-push');
		$rsp_field->setModel('xepan\hr\Employee');
		$rsp_field->setAttr(['multiple'=>'multiple']);

		$form->addSubmit('Quick POS')->addClass('btn btn-success btn-lg');

		if($form->isSubmitted()){
			$invoice = $this->add('xepan\commerce\Model_SalesInvoice');	
			$invoice->generateInvoiceFromPOS('Due',$form['customer']);
			// throw new \Exception($invoice->id, 1);
			
			
			for ($m=1; $m < 6; $m++) {
				if($form['item_x_'.$m]){
						//todo check all invoice created or not
					$invoice->addItem(
						$form['item_x_'.$m],
						$form['qty_x_'.$m],
						$form['price_x_'.$m],
						$form['amount_x_'.$m],
						$form['price_x_'.$m],
						0,
						0,
						0,
						0,
						null,
						"{}",
						null,
						0
						);
				}
			}

			foreach (explode(',', $form['sales_person']) as $name => $id) {
				// $to_emp->load($id);
				// $to_raw[] = ['name'=>$to_emp['name'],'id'=>$id];
				$qsp_sale_p = $this->add('xepan\commerce\Model_QspSalesPerson');
				$qsp_sale_p['contact_id'] = $id;
				$qsp_sale_p['qsp_master_id'] = $invoice->id;
				$qsp_sale_p->save();
			}

			$form->js()->univ()->redirect($this->app->url(null))->execute();
		}
	}
}