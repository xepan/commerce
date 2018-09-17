<?php

namespace xepan\commerce;

class page_quickqsp extends \Page {

	public $title = "xEpan POS";
	public $document_type = "";
	public $document_id = null;

	public $item_page_url = "index.php?page=xepan_commerce_pos_item";
	public $item_amount_page_url = "index.php?page=xepan_commerce_pos_getamount";
	public $item_detail_page_url = 'index.php?page=xepan_commerce_pos_itemcustomfield';
	public $item_shipping_page_url = 'index.php?page=xepan_commerce_pos_shippingamount';
	public $customer_page_url = "index.php?page=xepan_commerce_pos_contact";
	public $save_page_url = "index.php?page=xepan_commerce_pos_save";
	public $readmode = false;
	function init(){
		parent::init();
		
		$this->document_type = $this->document_type?:$this->app->stickyGET('document_type');
		$this->document_id = $this->document_id?:$this->app->stickyGET('document_id');

		if($_GET['readmode'])
			$this->readmode = true;

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

		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		//load saved design and pass it to widge
		$qsp_data=[];
		$common_tax_and_amount = [];
		if($this->document_id){
			$document = $this->add('xepan\commerce\Model_QSP_Master');
			$document->addCondition('id',$this->document_id);
			$document->tryLoadAny();

			if($document->loaded()){
				$this->document_type = $document['type'];

				$master_data = $document->getRows();
				unset(
						$master_data[0]['sub_type'],
						$master_data[0]['search_string']
					);

				$qsp_data = $master_data[0];
				$qsp_data['created_at'] = date('Y-m-d H:i:s',strtotime($qsp_data['created_at']));
				$qsp_data['due_date'] = $qsp_data['due_date']?(date('Y-m-d H:i:s',strtotime($qsp_data['due_date']))):"";
				
				// $qsp_data['nominal_id'] = $master_data['nominal_id'];
				// get all qsp_detail
				$qsp_details_model = $this->add('xepan\commerce\Model_QSP_Detail')
						->addCondition('qsp_master_id',$document->id);
				$qsp_details_model->addExpression('qsp-detail-id')->set(function($m,$q){
					return $q->expr('[0]',[$m->getElement('id')]);
				});
				$qsp_details_model->addExpression('is_productionable')->set(function($m,$q){
					return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('is_productionable')]);
				});
				
				$qsp_details_model->addExpression('is_production_phases_fixed')->set(function($m,$q){
					return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('is_production_phases_fixed')]);
				});

				$detail_data = $qsp_details_model->getRows();
				
				// add read_only_custom_field_values
				foreach ($detail_data as $key => &$qsp_item) {
					$item = $this->add('xepan\commerce\Model_Item')->load($qsp_item['item_id']);
					$qsp_item['hsn_sac'] = $item['hsn_sac'];
					// $qsp_item['name'] = $item['name']."::".$item['sku'];
					$qsp_item['item'] = $item['name']."::".$item['sku'];
					
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
			if($this->document_type != "PurchaseInvoice")
				$qsp_data['document_no'] = $document = '-';//$this->add('xepan\commerce\Model_'.$this->document_type)->newNumber();
						
			$qsp_data['nominal_id'] = $default_nominal_id;
			$qsp_data['exchange_rate'] = 1;

			$serial = "";
			if($this->document_type == "SalesOrder"){
				$serial = $qsp_config['sale_order_serial'];
			}

			if($this->document_type == "SalesInvoice"){
				$serial = $qsp_config['sale_invoice_serial'];
			}

			if($this->document_type == "Quotation"){
				$serial = $qsp_config['quotation_serial'];
			}
			
			$qsp_data['serial'] = $serial;
		}
		
		$all_tax = $this->add('xepan\commerce\Model_Taxation')->getRows();
		$taxation = [];
		foreach ($all_tax as $tax) {
			$taxation[$tax['id']] = [
									'name'=>$tax['name'],
									'percentage'=>$tax['percentage'],
									'sub_tax'=>$tax['sub_tax'],
									'show_in_qsp'=>$tax['show_in_qsp']
								];
		}

		// country
		$country = $this->add('xepan\base\Model_Country');
		$country->addCondition('status','Active');
		$country_list = $country->getRows();

		// state
		$state = $this->add('xepan\base\Model_State');
		$state->addCondition('status','Active');
		$state->addCondition('country_status','Active');
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
		
		// collect document other info
		$qsp_data['document_other_info'] = $this->add('xepan\base\Model_Document')->getDocumentOtherInfo($this->document_type,$this->document_id);
		// echo "<pre>";
		// print_r($qsp_data['common_tax_and_amount']);
		// echo "</pre>";
		// exit;
		
		// echo "<pre>";
		// print_r($qsp_data);
		// echo "</pre>";
		// die();
		$show_shipping_address = $qsp_config['show_shipping_address_in_pos']?:0;

		$this->template->trySet('new_pos_url','?page=xepan_commerce_quickqsp&document_type='.$this->document_type.'&action=add');
		$this->template->trySet('document_type',$this->document_type);

		if(!$show_shipping_address)
			$this->template->tryDel('shipping_address');
		
		$this->js(true)
				->_load('jquery.livequery')
				->_load('bootstrap-datetimepicker')
				// ->_load('tinymce.min')
				// ->_load('jquery.tinymce.min')
				// ->_load('xepan_richtext_admin')
        		->_css('libs/bootstrap-datetimepicker')
        		;

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
								'document_id'=>$_GET['document_id'],
								'individual_item_discount'=>$qsp_config['discount_per_item'],
								'apply_tax_on_discounted_amount'=>$qsp_config['tax_on_discounted_amount'],
								
								'item_page_url'=>$this->item_page_url,
								'item_detail_page_url'=>$this->item_detail_page_url,
								'item_amount_page_url'=>$this->item_amount_page_url,
								'item_shipping_page_url'=>$this->item_shipping_page_url,
								'customer_page_url'=>$this->customer_page_url,
								'save_page_url'=>$this->save_page_url,
								'show_shipping_address'=>$show_shipping_address

							]);

		$this->js(true)->_selector('#page-wrapper')->addClass('container nav-small');

		if($this->readmode){
			$this->js(true)->_selector('input, select, textarea')->prop('disabled', true);
			$this->js(true)->_selector('.col-remove, .item-extrainfo-btn, .add-new-item, .row.header')->remove();
		}

		$this->template->trySet('document_type',$this->document_type);

		$detail_page = [
						'salesinvoice'=>'xepan_commerce_salesinvoicedetail',
						'salesorder'=>'xepan_commerce_salesorderdetail',
						'purchaseorder'=>'xepan_commerce_purchaseorderdetail',
						'purchaseinvoice'=>'xepan_commerce_purchaseinvoicedetail',
						'quotationdetail'=>'xepan_commerce_quotationdetail'
					];
		$s_editor_url = $this->app->url($detail_page[strtolower($this->document_type)],['action'=>$_GET['action'],'document_id'=>$_GET['document_id']]);
		// $this->template->trySetHtml('standard_editor','<a class="btn btn-primary" href="'.$s_editor_url.'">Standard Editor</a>');
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