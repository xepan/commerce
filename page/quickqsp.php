<?php

namespace xepan\commerce;

class page_quickqsp extends \Page{
	public $title = "xEpan POS";
	public $document_type = "";
	function init(){
		parent::init();
		
		$this->document_type = $this->app->stickyGET('document_type');

		// nominal list
		$nominal_model = $this->add('xepan\accounts\Model_Ledger');
		$default_nominal_id = 0;
		if(in_array($this->document_type, ['SalesOrder','SalesInvoice'])){
			$nominal_model->addCondition('group','Sales');
			$default_nominal_id = $this->add('xepan\accounts\Model_Ledger')->load('Sales Account')->get('id');
			
		}elseif (in_array($this->document_type, ['PurchaseOrder','PurchaseInvoice'])) {
			$nominal_model->addCondition('group','Purchase');
			$default_nominal_id = $this->add('xepan\accounts\Model_Ledger')->load('Purchase Account')->get('id');
		}
		$nominal_list = $nominal_model->getRows();

		//load saved design and pass it to widge
		$qsp_data=[];
		$common_tax_and_amount = [];
		if($_GET['document_id']){
			$document = $this->add('xepan\commerce\Model_QSP_Master');
			$document->addCondition('id',$_GET['document_id']);
			$document->tryLoadAny();

			if($document->loaded()){
				$this->document_type = $document['type'];

				$master_data = $document->getRows();
				unset(
						$master_data[0]['sub_type'],
						$master_data[0]['search_string']
					);

				$qsp_data = $master_data[0];
				$qsp_data['created_at'] = date('Y-m-d',strtotime($qsp_data['created_at']));
				$qsp_data['due_date'] = $qsp_data['due_date']?(date('Y-m-d',strtotime($qsp_data['due_date']))):"";
				$qsp_data['nominal_id'] = $master_data['nominal_id']?:$default_nominal_id;
				// get all qsp_detail
				$qsp_details_model = $this->add('xepan\commerce\Model_QSP_Detail')
						->addCondition('qsp_master_id',$document->id);
				$qsp_details_model->addExpression('qsp-detail-id')->set(function($m,$q){
					return $q->expr('[0]',[$m->getElement('id')]);
				});

				$detail_data = $qsp_details_model->getRows();

				// add read_only_custom_field_values
				foreach ($detail_data as $key => &$qsp_item) {
					$item = $this->add('xepan\commerce\Model_Item')->load($qsp_item['item_id']);
					$item_read_only_cf = $item->getReadOnlyCustomField();

					// merge QSP_DETAIL into ITEM_READ_ONLY_CF
					$updated_cf = $this->updateReadOnlyDeptCF($item_read_only_cf,$qsp_item['extra_info']);
					$qsp_item['read_only_custom_field_values'] = $updated_cf;

					$export_design = "";
					$design = $this->add('xepan\commerce\Model_Item_Template_Design')
								->addCondition('item_id',$qsp_item['item_id'])
								->addCondition('id',$qsp_item['item_template_design_id'])
								->addCondition('contact_id',$qsp_item['customer_id'])
								;
					$design->tryLoadAny();
					if($design->loaded()){
						$url =  $this->api->url('xepan_commerce_designer_exportpdf',array('item_id'=>"not-defined",'item_member_design_id'=>$design->id,'xsnb_design_template'=>false,'print_ratio'=>10,'order_no'=>$this->model['qsp_master_id']));
						$export_design = '<a class="btn btn-primary" href="'.$url.'" target="_blank">Export Design</a>';
					}
					$qsp_item['export_design'] = $export_design;
					
					$attachements = $this->add("xepan\commerce\Model_QSP_DetailAttachment")
										 ->addCondition('qsp_detail_id',$qsp_item['id'])
										 ->count()->getOne();
					if($attachements){
						$qsp_item['export_attachments'] = '<a class="btn btn-primary order-export-attachments" data-id="'.$qsp_item['id'].'" >Export Attachements</a>';
					}

				}

				$qsp_data['details'] = $detail_data;

				$common_tax_and_amount = $document->getCommnTaxAndAmount();
			}
		}else{
			if(!$this->document_type) throw new \Exception("document type not define");
			// set data of guest customer or default value
			$qsp_data['document_no'] = $document = $this->add('xepan\commerce\Model_'.$this->document_type)->newNumber();
			$qsp_data['nominal_id'] = $default_nominal_id;
			$qsp_data['exchange_rate'] = 1;
		}


		$all_tax = $this->add('xepan\commerce\Model_Taxation')->getRows();
		$taxation = [];
		foreach ($all_tax as $tax) {
			$taxation[$tax['id']] = [
									'name'=>$tax['name'],
									'percentage'=>$tax['percentage']
								];
		}

		// country
		$country = $this->add('xepan\base\Model_Country');
		$country->addCondition('status','Active');
		$country_list = $country->getRows();

		// state
		$state = $this->add('xepan\base\Model_State');
		$state->addCondition('status','Active');
		$state_list = $state->getRows();

		// tnc list
		$tnc_list = $this->add('xepan\commerce\Model_TNC')->getRows();

		// currency list
		$currency_list = $this->add('xepan\accounts\Model_Currency')->addCondition('status','Active')->getRows();

		$unit_list = $this->add('xepan\commerce\Model_Unit')->getRows();

		// round amount standard
		$round_amount_standard = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'round_amount_standard'=>'DropDown'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$round_amount_standard->tryLoadAny();
		
		// echo "<pre>";
		// print_r($qsp_data['common_tax_and_amount']);
		// echo "</pre>";
		// exit;
		
		// echo "<pre>";
		// print_r($qsp_data);
		// echo "</pre>";
		// die();
		$this->js(true)->_load('jquery.livequery');
		$this->js(true)->_load('pos')->xepan_pos([
								'show_custom_fields'=>true,
								'qsp'=>$qsp_data,
								'taxation'=>$taxation,
								'document_type'=>$this->document_type,
								'country'=>$country_list,
								'state'=>$state_list,
								'tnc'=>$tnc_list,
								'currency'=>$currency_list,
								'nominal'=>$nominal_list,
								'unit_list'=>$unit_list,
								'round_standard'=>$round_amount_standard['round_amount_standard'],
								'common_tax_and_amount'=>$common_tax_and_amount,
								'default_currency_id'=>$this->app->epan->default_currency->id,
								'item_list'=>[],
								'document_id'=>$_GET['document_id']
							]);

		$this->js(true)->_selector('#page-wrapper')->addClass('container nav-small');
		
		$this->template->trySet('document_type',$this->document_type);

		$detail_page = [
						'salesinvoice'=>'xepan_commerce_salesinvoicedetail',
						'salesorder'=>'xepan_commerce_salesorderdetail',
						'purchaseorder'=>'xepan_commerce_purchaseorderdetail',
						'purchaseinvoice'=>'xepan_commerce_purchaseinvoicedetail',
						'quotationdetail'=>'xepan_commerce_quotationdetail'
					];
		$s_editor_url = $this->app->url($detail_page[strtolower($this->document_type)],['action'=>$_GET['action'],'document_id'=>$_GET['document_id']]);
		$this->template->trySetHtml('standard_editor','<a class="btn btn-primary" href="'.$s_editor_url.'">Standard Editor</a>');
		// $this->template->trySet('document_id',$_GET['document_id']);
		
		// export attachment
		$vp = $this->add('VirtualPage');
		$vp->set(function($p){
			$detail_order_id = $p->app->stickyGET('detail_order_id');
			$attachments = $p->add('xepan\commerce\Model_QSP_DetailAttachment');
			$attachments->addCondition('qsp_detail_id',$detail_order_id);
			
			$grid = $p->add('xepan\base\Grid',null,null,['view\qsp\attachments']);
			$grid->setModel($attachments);
		});

		$this->on('click','.order-export-attachments',function($js,$data)use($vp){
			return $js->univ()->dialogURL("EXPORT ATTACHMENTS",$this->api->url($vp->getURL(),['detail_order_id'=>$data['id']]));
		});
	}

	function updateReadOnlyDeptCF($read_only_cf_array,$qsp_extra_info_json){
		
		$qsp_extra_info_array = json_decode($qsp_extra_info_json,true);
		
		foreach ($read_only_cf_array as $dept_id => &$dept_cf_detail) {
			// first remove pre_selected department
			$dept_cf_detail['pre_selected'] = 0;
			if(isset($qsp_extra_info_array[$dept_id])){
				$dept_cf_detail['pre_selected'] = 1;
				
				foreach ($dept_cf_detail as $cf_id => &$cf_array) {
					if(in_array($cf_id, ['department_name','pre_selected','production_level']) OR !is_int($cf_id)) continue;
					
					if(isset($qsp_extra_info_array[$dept_id][$cf_id])){
						$qsp_cf_detail = $qsp_extra_info_array[$dept_id][$cf_id];

						$cf_array['custom_field_name'] = $qsp_cf_detail['custom_field_name'];
						$cf_array['custom_field_value_id'] = $qsp_cf_detail['custom_field_value_id'];
						$cf_array['custom_field_value_name'] = $qsp_cf_detail['custom_field_value_name'];											
					}
				}
			}
		}

		return $read_only_cf_array;
	}

	function defaultTemplate(){
		return ['page\pos'];
	}

}