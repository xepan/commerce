<?php 
 namespace xepan\commerce;
 class page_qspconfig extends \xepan\commerce\page_configurationsidebar{

	public $title='QSP Config';

	function init(){
		parent::init();

		$qsp_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'discount_per_item'=>'checkbox',
							'discount_on_taxed_amount'=>'checkbox',
							'tax_on_discounted_amount'=>'checkbox'
							],
					'config_key'=>'COMMERCE_QSP_TAX_AND_DISCOUNT_CONFIG',
					'application'=>'commerce'
			]);
		
		$qsp_config->add('xepan\hr\Controller_ACL');
		$qsp_config->tryLoadAny();

		$apply = "tax_on_discounted_amount";
		if($qsp_config['discount_on_taxed_amount']){
			$apply = "discount_on_taxed_amount";
		}

		$form = $this->add('Form');
		$form->addField('checkbox','discount_per_item')->set($qsp_config['discount_per_item']);
		$form->addField('Radio','apply')->setValueList(
									[
										'discount_on_taxed_amount'=>'Discount On Taxed Amount',
										'tax_on_discounted_amount'=>'Tax On Discounted Amount'
									])->validate('required')->set($apply);

		$form->addSubmit('Save')->addClass('btn btn-primary');
		if($form->isSubmitted()){			

			$qsp_config['discount_per_item'] = $form['discount_per_item'];
			
			if($form['apply'] == "discount_on_taxed_amount"){
				$qsp_config['tax_on_discounted_amount'] = 0;
				$qsp_config['discount_on_taxed_amount'] = 1;
			}else{
				$qsp_config['tax_on_discounted_amount'] = 1;
				$qsp_config['discount_on_taxed_amount'] = 0;
			}
			$qsp_config->save();

			$msg = "Discount on QSP";
			if($qsp_config['discount_per_item']){
				$msg = "Discount Per Item Wise";
			}

			$qsp_config->app->employee
            ->addActivity("Qsp Config set to ".$msg." and apply ".$form['apply'])
			->notifyWhoCan(' ',' ',$qsp_config);
			$form->js(null,$form->js()->reload())->univ()->successMessage('QSP Updated')->execute();
		}
	}
} 