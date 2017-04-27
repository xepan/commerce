
jQuery.widget("ui.xepan_pos",{
	selectorAutoComplete: ".item-field",
	selectorUpdateAmount: ".amount-calc-factor",
	selectorExtraInfoBtn:'.item-extrainfo-btn',
	item_ajax_url:'index.php?page=xepan_commerce_pos_item',
	customer_ajax_url:'index.php?page=xepan_commerce_pos_contact',
	item_detail_ajax_url:'index.php?page=xepan_commerce_pos_itemcustomfield',
	item_shipping_ajax_url:'index.php?page=xepan_commerce_pos_shippingamount',
	save_pos_url:'index.php?page=xepan_commerce_pos_save',
	options : {
		show_custom_fields: true,
		item_ajax_calling:true,
		qsp:{
			details: [
				// {
				// 	id:123, //it is qsp_detail_id
				// 	item_id: 1234,
				// 	narration: "narration",
				// 	qty: 123,
				// 	price : 123,
				// 	custom_fields : {
				// 		cf_field_1 : "adas",
				// 		cf_field_2 : "asdasD"
				// 	}
				// },
				// {
				// 	item_id: 121,
				// 	narration: "narration",
				// 	qty: 123,
				// 	price : 123,
				// 	custom_fields : {
				// 		cf_field_1 : "aa",
				// 		cf_field_2 : "asdas"
				// 	}
				// }
			],
			// all qsp master fields comes here
			contact_id:0,
			billing_country_id:0,
			billing_state_id:0,
			shipping_country_id:0,
			shipping_state_id:0,
			gross_amount: 0,
			discount_amount: 0,
			round_amount: 0,
			tax_amount: 0,
			net_amount: 0
		},
		taxation:[],
		apply_tax_on_discounted_amount:1,
		shipping_inclusive_tax:0,
		individual_item_discount:0,
		document_type:undefined,
		country:[],
		state:[],
		tnc:[],
		currency:[],
		nominal:[],
		unit_list:[],
		common_tax_and_amount:[],
		default_currency_id:0
	},

	_create : function(){
		var self = this;
		this.setupEnvironment();
		this.loadQSP();
		
		if(!self.options.qsp.details.length)
			this.addRow();
		
		this.setUpEvents();

		this.updateTotalAmount();
		this.showCommonTaxAndAmount();
	},

	setupEnvironment: function(){
		var self = this;

		self.setupMasterSection();
		self.setupDetailSection();	

	},

	setupMasterSection: function(){
		var self = this;
		// var saved_qsp = JSON.parse(self.options.qsp);
		var saved_qsp = self.options.qsp;

		var field_customer = $('<input class="pos-customer-autocomplete pos-master-mandatory">').appendTo($.find('.pos-customer-form-row'));
		$(field_customer).val(saved_qsp.contact);		

		var country_list = '<option value="0" selected="selected">Select Country </option>';
		$.each(self.options.country, function(index, country_obj) {
			country_list += '<option value="'+country_obj.id+'">'+country_obj.name+'</option>';
		});

		// billing section
		var $billing_country = $('<select class="pos-customer-billing-country pos-master-mandatory">').appendTo($('.pos-customer-billing-country-form-row'));
		$(country_list).appendTo($billing_country);
		if(saved_qsp.billing_country_id){
			$billing_country.val(saved_qsp.billing_country_id);
		}

		var $billing_state = $('<select class="pos-customer-billing-state pos-master-mandatory">').appendTo($('.pos-customer-billing-state-form-row'));
		var s_option_list = '<option value="0" selected="selected">Select State </option>';

		if(saved_qsp.billing_country_id){
			var list = self.getState(saved_qsp.billing_country_id);
			$.each(list, function(index, s_obj) {
				 /* iterate through array or object */
				s_option_list += '<option value="'+s_obj.id+'">'+s_obj.name+'</option>';
			});
			$(s_option_list).appendTo($billing_state);
		}
		if(saved_qsp.billing_state_id){
			$billing_state.val(saved_qsp.billing_state_id);
		}

		var $billing_city = $('<input class="pos-customer-billing-city pos-master-mandatory">').appendTo($('.pos-customer-billing-city-form-row'));
		$billing_city.val(saved_qsp.billing_city);
		var $billing_address = $('<input class="pos-customer-billing-address pos-master-mandatory">').appendTo($('.pos-customer-billing-address-form-row'));
		$billing_address.val(saved_qsp.billing_address);
		var $billing_pincode = $('<input class="pos-customer-billing-pincode pos-master-mandatory">').appendTo($('.pos-customer-billing-pincode-form-row'));
		$billing_pincode.val(saved_qsp.billing_pincode);
		
		// shipping section
		var $shipping_country = $('<select class="pos-customer-shipping-country pos-master-mandatory">').appendTo($('.pos-customer-shipping-country-form-row'));
		$(country_list).appendTo($shipping_country);
		if(saved_qsp.shipping_country_id){
			$shipping_country.val(saved_qsp.shipping_country_id);
		}

		var $shipping_state = $('<select class="pos-customer-shipping-state pos-master-mandatory">').appendTo($('.pos-customer-shipping-state-form-row'));
		var s_option_list = '<option value="0" selected="selected">Select State </option>';

		if(saved_qsp.shipping_country_id){
			var list = self.getState(saved_qsp.shipping_country_id);
			$.each(list, function(index, s_obj) {
				 /* iterate through array or object */
				s_option_list += '<option value="'+s_obj.id+'">'+s_obj.name+'</option>';
			});
			$(s_option_list).appendTo($shipping_state);
		}
		if(saved_qsp.shipping_state_id){
			$shipping_state.val(saved_qsp.shipping_state_id);
		}

		var $shipping_city = $('<input class="pos-customer-shipping-city pos-master-mandatory">').appendTo($('.pos-customer-shipping-city-form-row'));
		$shipping_city.val(saved_qsp.shipping_city);
		var $shipping_address = $('<input class="pos-customer-shipping-address pos-master-mandatory">').appendTo($('.pos-customer-shipping-address-form-row'));
		$shipping_address.val(saved_qsp.shipping_address);
		var $shipping_pincode = $('<input class="pos-customer-shipping-pincode pos-master-mandatory">').appendTo($('.pos-customer-shipping-pincode-form-row'));
		$shipping_pincode.val(saved_qsp.shipping_pincode);
		
		var $qsp_no = $('<input class="qsp_number pos-master-mandatory">').appendTo($('.qsp_number-form-row'));
		$qsp_no.val(saved_qsp.document_no);

		var $qsp_created_date = $('<input class="qsp_created_date pos-master-mandatory" value="'+saved_qsp.created_at+'">').appendTo($('.qsp_created_date-form-row'));
		var $qsp_due_date = $('<input class="qsp_due_date pos-master-mandatory" value="'+saved_qsp.due_date+'">').appendTo($('.qsp_due_date-form-row'));
		
		$qsp_created_date.datepicker({dateFormat: 'yy-mm-dd'});
		$qsp_created_date.datepicker("setDate",new Date(saved_qsp.created_at));
		$qsp_due_date.datepicker({dateFormat: 'yy-mm-dd'});
		$qsp_due_date.datepicker("setDate",new Date(saved_qsp.due_date));

		var $narration = $('<textarea class="pos-narration">').appendTo($('.pos-narration-form-row'));
		$narration.val(saved_qsp.narration);
		
		// disable or enable total_discount input box
		$('.pos-discount-amount').val(saved_qsp.discount_amount);
		self.options.discount_amount = saved_qsp.discount_amount;
		if(self.options.individual_item_discount){
			$(".pos-discount-amount").attr('disabled',true);
		}else{
			$(".pos-discount-amount").change(function(event) {
				self.options.qsp.discount_amount = $(this).val();
				self.updateTotalAmount();
			});
		}

		// set round amount
		$('.pos-round-amount').val(parseFloat(saved_qsp.round_amount).toFixed(2));

		//term and condition
		var $tnc = $('<select class="pos-tnc pos-master-mandatory">').appendTo($('.pos-tnc-form-row'));
		var tnc_list = '<option value="0" selected="selected">Select T&C</option>';
		$.each(self.options.tnc, function(index, obj) {
			/* iterate through array or object */
			tnc_list += '<option value="'+obj.id+'">'+obj.name+'</option>';
		});
		$(tnc_list).appendTo($tnc);
		$tnc.val((saved_qsp.tnc_id)?saved_qsp.tnc_id:0);
		$('<div class="pos-tnc-detail">'+self.options.qsp['tnc_text']+'</div>').appendTo($('.pos-tnc-form-row'));
		
		$tnc.change(function(event) {
			var tnc_text = "";
			var tnc_id = $(this).val();
			$(this).closest('div.pos-tnc-form-row').find('.pos-tnc-detail').remove();
			$.each(self.options.tnc, function(index, obj){
				if(obj.id == tnc_id)
					tnc_text = obj.content;
			});
			$('<div class="pos-tnc-detail">'+tnc_text+'</div>').appendTo($(this).closest('div.pos-tnc-form-row'));
		});

		var $currency = $('<select class="pos-currency pos-master-mandatory">').appendTo($('.pos-currency-form-row'));
		var curr_list = '<option value="0" selected="selected">Select Currency</option>';
		$.each(self.options.currency, function(index, obj) {
			 /* iterate through array or object */
			curr_list += '<option value="'+obj.id+'">'+obj.name+'</option>';
		});
		$(curr_list).appendTo($currency);
		$currency.val((saved_qsp.currency_id)?saved_qsp.currency_id:0);

		var $nominal = $('<select class="pos-nominal pos-master-mandatory">').appendTo($('.pos-nominal-form-row'));
		var nominal_list = '<option value="0" selected="selected" >Select Nominal</option>';
		$.each(self.options.nominal, function(index, obj) {
			nominal_list += '<option value="'+obj.id+'">'+obj.name+'</option>';
		});
		$(nominal_list).appendTo($nominal);
		$nominal.val((saved_qsp.nominal_id)?saved_qsp.nominal_id:0);

		// comman vat and it's amount
		self.showCommonTaxAndAmount();
	},

	showCommonTaxAndAmount: function(){
		var self = this;
		var comman_tax_amount = {};

		$(this.element).find('tr.col-data').each(function(index, row) {
			// check item selected or not
			var row_item_id = $(row).find('.item-id-field').val();
			if(row_item_id <= 0 ){
				// self.displayError($(parent).find('.col-item > div.input-group'));
				return;
			}

			price_field = $(row).find('.price-field');
			qty_field = $(row).find('.qty-field');
			tax_field = $(row).find('.tax-field');
			discount_field = $(row).find('.discount-field');
			shipping_field = $(row).find('.shipping-charge');
			express_shipping_field = $(row).find('.express-shipping-charge');

			var price = parseFloat(price_field.val());
			var qty = parseFloat(qty_field.val());
			var discount_val = discount_field.val()?discount_field.val():0;

			//to do according to checkbox of express shipping
			var shipping_charge = shipping_field.val()?shipping_field.val():0;
			shipping_charge = parseFloat(shipping_charge);

			var tax_id = $(tax_field).val();

			var tax_percentage = 0;
			if(tax_id > 0){
				var taxation = self.options.taxation;
				// var taxation = JSON.parse(self.options.taxation);
				tax_name = taxation[tax_id].name;

				if(!(tax_name in comman_tax_amount) ){
					comman_tax_amount[tax_name] = {};
					comman_tax_amount[tax_name].name = tax_name;
					comman_tax_amount[tax_name].taxation_sum = 0;
					comman_tax_amount[tax_name].net_amount_sum = 0;
				}

				tax_percentage = parseFloat(taxation[tax_id].percentage);

				// todo calculate here according to % or amount
				var discount_amount = discount_val;
				discount_amount = parseFloat(discount_amount);

				var amount = (price * qty)?(price * qty):0;
				if(self.options.apply_tax_on_discounted_amount)
					amount = amount - discount_amount;
				
				// is_shipping_inclusive_tax
				if(!self.options.shipping_inclusive_tax){
					amount = amount + shipping_charge;
				}

				var tax_amount = (amount * tax_percentage)/100;
				
				comman_tax_amount[tax_name].net_amount_sum = parseFloat(comman_tax_amount[tax_name].net_amount_sum) + parseFloat(amount);
				comman_tax_amount[tax_name].taxation_sum = parseFloat(comman_tax_amount[tax_name].taxation_sum) + parseFloat(tax_amount);
				// todo for taxation amount if discount including tax
				// amount = amount + tax_amount;
				// if(self.options.shipping_inclusive_tax){
				// 	amount = amount + shipping_charge;
				// }
				// if(!self.options.apply_tax_on_discounted_amount){
				// 	amount = amount - discount_amount;
				// }
			}
		});

		var html_str = "";
		$.each(comman_tax_amount, function(name, val) {
			html_str += '<div class="pos-common-tax">'+val.name+' = '+val.taxation_sum+' on '+val.net_amount_sum+'</div>';
		});
		$(self.element).find('.pos-common-tax-amount-wrapper').html(html_str);
	},

	getState: function(country_id){
		var self = this;
		var s_list = [];

		if(country_id){
			$.each(self.options.state, function(index, state) {
				if(state.country_id == country_id)
					s_list.push(state);
			});			
		}else{
			$.each(self.options.state, function(index, state) {
				s_list.push(state);
			});
		}
		return s_list;
	},

	updateShippingAmount: function($td_field_obj){
		var self = this;
		var $tr = $td_field_obj.closest('tr');

		$.ajax({
			url:self.item_shipping_ajax_url,
			data:{
				item_id:$tr.find('.item-id-field').val(),
				sale_amout: $tr.find('.price-field').val(),
				qty: $tr.find('.qty-field').val(),
				country_id: self.options.qsp.shipping_country_id,
    			state_id: self.options.qsp.shipping_state_id
			},
			success: function( data ) {
				item_data = JSON.parse(data);
				$tr.find('.col-shipping .shipping-charge').val(item_data.shipping_charge);
				$tr.find('.col-shipping .shipping-duration').val(item_data.shipping_duration);
				$tr.find('.col-shipping .express-shipping-charge').val(item_data.express_shipping_charge);
				$tr.find('.col-shipping .express-shipping-duration').val(item_data.express_shipping_duration);
          	},
          	error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Error getting prospect list: " + textStatus);
            }
		});

	},

	setupDetailSection: function(){
		var self = this;

		thead_html = '<tr class="col-heading">';
		thead_html += '<th class="col-sno">S.No</th>';
		thead_html += '<th class="col-item">Item/Particular</th>';
		thead_html += '<th class="col-qty">Qty</th>';
		thead_html += '<th class="col-unit">unit</th>';
		thead_html += '<th class="col-price">Price</th>';

		th_discount = '<th class="col-discount">Discount %/ amount</th>';
		th_tax = '<th class="col-tax">Tax</th>';
		th_shipping = '<th class="col-shipping">Shipping Charge</th>';

		if(self.options.apply_tax_on_discounted_amount){
			
			if(self.options.individual_item_discount)
				thead_html += th_discount;

			if(!self.options.shipping_inclusive_tax){
        		thead_html += th_shipping;
				thead_html += th_tax;
			}else{
				thead_html += th_tax;
        		thead_html += th_shipping;
			}
		}else{
			if(!self.options.shipping_inclusive_tax){
        		thead_html += th_shipping;
				thead_html += th_tax;
			}else{
				thead_html += th_tax;
        		thead_html += th_shipping;
			}

			if(self.options.individual_item_discount)
				thead_html += th_discount;
		}

				
		thead_html += '<th class="col-amount">Amount</th>';
        thead_html += '<th class="col-action"></th>';
        thead_html += '</tr>';
		
		$(thead_html).appendTo($.find('table.addeditem'));
	},

	loadQSP: function(){
		var self = this;
		saved_qsp = self.options.qsp;
		// saved_qsp = JSON.parse(self.options.qsp);

		// setting qsp master all value to it's to data-field attribute values
		$.each(saved_qsp,function(field_name,field_value){
			if($.type(field_value) === 'array' || $.type(field_value) === 'object') return;			
			$('[data-field='+field_name+']').html(field_value);
		});

		// adding item row
		var details = saved_qsp['details'];
		if((details != undefined || details != null) && details.length){
			$.each(details,function(key,qsp_item){
				self.addRow(qsp_item);
			});
		} 
	},

	addRow: function(qsp_item = []){
		// console.log("called row");
		var self = this;
		next_sno = $.find('table.addeditem tr.col-data').length + 1;
		
		var taxation = self.options.taxation;
		// var taxation = JSON.parse(self.options.taxation);
		var tax_option = '<option value="0" selected="selected">Please Select</option>';
		$.each(taxation,function(tax_id,tax_detail){
			tax_option += '<option value="'+tax_id+'">'+tax_detail.name+'</option>';
		});

		var rowTemp = '<tr data-sno="1" class="col-data">';
        	rowTemp += '<td class="col-sno">'+next_sno+'</td>';
        	rowTemp += '<td class="col-item"><div class="input-group"><input  data-field="item-item" placeholder="Item/ Particular" class="item-field pos-qsp-field"/><span data-field="item-extra-nfo-btn" class="item-extrainfo-btn input-group-addon"><i class="fa fa-navicon"></i></span></div><input  type="hidden" data-field="item-item_id" placeholder="Item id" class="item-id-field pos-qsp-field" /><input type="hidden" data-field="item-extra_info" placeholder="Item custom field" class="item-custom-field pos-qsp-field"/><input type="hidden" data-field="item-read_only_custom_field_values" placeholder="Item read only custom field" class="item-read-only-custom-field pos-qsp-field"/><input type="hidden" data-field="item-qsp-detail-id" class="pos-qsp-detail-id pos-qsp-field"/><input data-field="item-narration" placeholder="Narration" class="narration-field pos-qsp-field"/><div class="pos-extra-info-wrapper">no one custom field</div></td>';
        	rowTemp += '<td class="col-qty"><input data-field="item-quantity" placeholder="Quantity" class="qty-field amount-calc-factor pos-qsp-field" value="0"/></td>';
        	rowTemp += '<td class="col-unit"><select data-field="item-qty_unit_id" placeholder="Unit" class="qty-unit-field pos-qsp-field"></select></td>';
        	rowTemp += '<td class="col-price"><input data-field="item-price" placeholder="Unit Price" class="price-field amount-calc-factor pos-qsp-field"/></td>';

    	var td_tax = '<td class="col-tax"><select data-field="item-taxation_id" id="tax-field" class="form-control tax-field amount-calc-factor pos-qsp-field" value="0">'+tax_option+'</select></td>';
    	var td_discount = '<td class="col-discount"><input data-field="item-discount" placeholder="Discount %/ amount" class="discount-field amount-calc-factor pos-qsp-field" value="0"/></td>';
    	var td_shipping = '<td class="col-shipping"><input data-field="item-shipping_charge" placeholder="shipping charge" class="shipping shipping-charge amount-calc-factor pos-qsp-field" value="0"><input data-field="item-shipping_duration" placeholder="shipping duration" class="shipping shipping-duration pos-qsp-field"><input data-field="item-express_shipping_charge" placeholder="express shipping charge" class="express-shipping express-shipping-charge amount-calc-factor pos-qsp-field" value="0"><input data-field="item-express_shipping_duration" placeholder="express duration" class="express-shipping express-shipping-duration pos-qsp-field"></td>';
 		       
        // managing td column according to apply tax on discount or shipping
        if(self.options.apply_tax_on_discounted_amount){
			if(self.options.individual_item_discount)
				rowTemp += td_discount

			if(!self.options.shipping_inclusive_tax){
        		rowTemp += td_shipping;
				rowTemp += td_tax;
			}else{
				rowTemp += td_tax;
        		rowTemp += td_shipping;
			}
		}else{
			if(!self.options.shipping_inclusive_tax){
        		rowTemp += td_shipping;
				rowTemp += td_tax;
			}else{
				rowTemp += td_tax;
        		rowTemp += td_shipping;
			}

			if(self.options.individual_item_discount)
				rowTemp += td_discount;
		}

    	rowTemp += '<td class="col-amount"><input data-field="item-total_amount" placeholder="Amount" class="amount-field pos-qsp-field" readOnly/></td>';
    	rowTemp += '<td class="col-remove"><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x"></i></span></td>';
  		rowTemp += '</tr>';

		if(self.options.show_custom_fields){
			// hide custom fiedl text area or some other fields
		}

		var new_row = $(rowTemp).appendTo($.find('table.addeditem'));

		// appending qty unit fields
		self.updateUnit($(new_row),0);

        // set saved or default selected value
		$.each(qsp_item,function(field_name,value){
			// $('[data-field=item-'+field_name+']').css('border','2px solid red');
			if($.type(value) == 'array' || $.type(value) == 'object')
				value = JSON.stringify(value);

			$(new_row).find('[data-field=item-'+field_name+']').val(value);
		});

		$(new_row).find('.express-shipping').hide();

		self.addExtraInfo(new_row);
	},

	updateUnit: function($row_obj, unit_group_id=0,unit_id=0){
		var self = this;
		var unit_ops = self.getUnitsOfGroup(unit_group_id);
        $row_obj.find('.qty-unit-field').html(unit_ops).val(unit_id);

     //    console.log(unit_ops);
   		// console.log("unit id = "+unit_id);
   		// console.log("unit group id = "+unit_group_id);
   	},

	addExtraInfo: function($tr){

		if(!$tr.find('.item-read-only-custom-field').val().length)  return;

		var custom_field_json = JSON.parse($tr.find('.item-read-only-custom-field').val());
		var extra_info_html = "";

		$.each(custom_field_json, function(index, dept_cf_detail) {
			var dept_name = dept_cf_detail['department_name'];
			var has_cf_check = 0;

			$.each(dept_cf_detail,function(cf_id,cf_details){

				if(!(cf_id == "pre_selected" && cf_details == 1)){
					if(!cf_details['custom_field_value_id'] && !cf_details['custom_field_value_name'] ) return;

					if(cf_id  === "department_name" || cf_id === "pre_selected" || cf_id === "production_level" ){
						return;
					}
				}

				if(!has_cf_check){
					extra_info_html += '<div class="pos-department-name">'+dept_name+'</div>';
					has_cf_check = 1;
				}
				if(cf_details['custom_field_name'] && cf_details['custom_field_value_name'])
					extra_info_html += '<div class="pos-department-cf-detail">'+cf_details['custom_field_name']+" : "+cf_details['custom_field_value_name']+'</div>';
			});
		});

		$tr.find('.pos-extra-info-wrapper').html(extra_info_html);
	},

	getUnitsOfGroup: function(group_id = 0){
		var self = this;
		// console.log('unit function called');
		var unit_options = "<option value='0' selected='selected'>Select Unit</option>";
        $.each(self.options.unit_list, function(index, obj) {
        	if(group_id > 0 && obj.unit_group_id != group_id){
        		// console.log('grpup id not same');
        		return;
        	}
        	unit_options += '<option value="'+obj.id+'">'+obj.name_with_group+'</option>';
        });
		return unit_options;        
	},

	deleteRow: function(){


	},

	addLiveEvents: function(){
		var self = this;

		// MAKE ITEM FIELD AUTO COMLETE
		$(self.selectorAutoComplete).livequery(function(){ 
		    // use the helper function hover to bind a mouseover and mouseout event 
		    $(this).autocomplete({
				source:function( request, response ) {
			    	$.ajax( {
			    		url: self.item_ajax_url,
			    		dataType: "json",
			    		data: {
			    			term: request.term,
			    			country_id: self.options.qsp.shipping_country_id,
			    			state_id: self.options.qsp.shipping_state_id
						},
			          	success: function( data ) {
			            	response( data );
			          	}
			        });
			    },
				minLength:1,
				select: function( event, ui ) {
					// after select auto fill qty and price
					$tr = $(this).closest('.col-data');
					$tr.find('.price-field').val(ui.item.price);
					$tr.find('.item-id-field').val(ui.item.id);
					$tr.find('.item-custom-field').val(ui.item.custom_field);
					$tr.find('.item-read-only-custom-field').val(ui.item.read_only_custom_field);
					$tr.find('.col-tax select.tax-field').val(ui.item.tax_id);

					self.updateUnit($tr,ui.item.qty_unit_group_id,ui.item.qty_unit_id);
					// on selct get custom field of item
					$.ajax({
						url:self.item_detail_ajax_url,
						data:{
							item_id:ui.item.id
						},
						success: function( data ) {
							item_data = JSON.parse(data);
							$tr.find('.item-read-only-custom-field').val(JSON.stringify(item_data.cf));
							// $tr.find('.col-tax select').val(item_data.tax_id);
							self.showCustomFieldForm($tr);
			          	},
			          	error: function(XMLHttpRequest, textStatus, errorThrown) {
			              alert("Error getting prospect list: " + textStatus);
			            }
					});
			    }
			});
		    // ,function(){
		    	// if field not found then
		    // }
		});

		// // ADD QTY OR PRICE CHANGE EVENT
		$(self.selectorUpdateAmount).livequery(function(){

			// update shipping charge if qty and it's amount is changed
			if($(this).hasClass('item-field') || $(this).hasClass('qty-field')){
				$(this).change(function(event) {
					self.updateShippingAmount($(this));
				});
			}

			if($(this).hasClass('tax-field')){
				$(this).change(function(){
					self.updateAmount($(this));
				});
			}else{
				$(this).keyup(function(){
					self.updateAmount($(this));
				});
			}
		});

		// EDIT CUSTOM FIELD
		$(self.selectorExtraInfoBtn).livequery(function(){
			$(this).click(function(){
				$tr = $(this).closest('tr');
				self.showCustomFieldForm($tr);
			});
		});

		// Remove Error Box after change
		$('.pos-form-field').livequery(function(){
			$(this).change(function(){
				$(this).closest('.pos-form-group')
					.removeClass('pos-field-error')
					.find('.error-message')
					.remove()
					;
			});
		});

		// Remove Error Box from pos master after change
		$('.pos-master-mandatory').livequery(function(){
			$(this).change(function(){
				$(this).closest('div')
					.removeClass('pos-field-error')
					.find('.error-message')
					.remove()
					;
			});
		});
		
		// Remove Error Box from pos detail after change
		$('tr.col-data .pos-qsp-field').livequery(function(){
			$(this).change(function(){
				$(this).closest('td')
					.removeClass('pos-field-error')
					.find('.error-message')
					.remove()
					;
			});
		});

		// Checkbox
		$('.pos-department-checkbox').livequery(function(){
			$(this).change(function(){
				if(!$(this).is(':checked')){
					$(this).closest('.pos-department-customfield-panel')
						.find('.pos-form-group')
						.removeClass('pos-field-error')
						.find('.error-message')
						.remove();
				}
			});
		});

		// remove column after
		$('.col-remove').livequery(function(){
			$(this).click(function(){
				$(this).closest('.col-data').remove();
				self.updateTotalAmount();
				self.showCommonTaxAndAmount();
			});
		});

		// save button
		$('#xepan-pos-save').click(function(){
			self.savePOS();
		});

		//customer/contact/supplier autocomplte
		$('.pos-customer-autocomplete').autocomplete({
			source:function( request, response ) {
				$.ajax( {
					url: self.customer_ajax_url,
					dataType: "json",
					data: {
						term: request.term,
						document_type:self.options.document_type
					},
					success: function( data ) {
						response( data );
					}
				});
			},
			minLength:1,
			select: function( event, ui ) {
				event.preventDefault();
				
				// removing classes
				$(this).closest('div')
					.removeClass('pos-field-error')
					.find('.error-message')
					.remove()
					;

				$(this).val(ui.item.name);
				self.options.qsp.contact_id = ui.item.id;
				self.options.qsp.billing_country_id = ui.item.billing_country_id;
				self.options.qsp.billing_state_id = ui.item.billing_state_id;
				self.options.qsp.shipping_country_id = ui.item.shipping_country_id;
				self.options.qsp.shipping_state_id = ui.item.shipping_state_id;
				
				$('.pos-customer-billing-country').val(ui.item.billing_country_id).trigger('change');
				// manually call trigger event so that state list is up
				$('.pos-customer-billing-state').val(ui.item.billing_state_id);
				$('.pos-customer-billing-city').val(ui.item.billing_city);
				$('.pos-customer-billing-address').val(ui.item.billing_address);
				$('.pos-customer-billing-pincode').val(ui.item.billing_pincode);

				$('.pos-customer-shipping-country').val(ui.item.shipping_country_id).trigger('change');
				$('.pos-customer-shipping-state').val(ui.item.shipping_state_id);
				$('.pos-customer-shipping-city').val(ui.item.shipping_city);
				$('.pos-customer-shipping-address').val(ui.item.shipping_address);
				$('.pos-customer-shipping-pincode').val(ui.item.shipping_pincode);

				// update all detail row shipping amount
				self.updateAllShippingAmount();
			}
		});

		// billing country change event
		$('.pos-customer-billing-country').change(function(){
			self.options.qsp.billing_country_id = $(this).val();
			$(this).attr('data-country-id',$(this).val());
			
			$('.pos-customer-billing-state').html("");
			var s_list = self.getState($(this).val());
			var s_option_list = '<option>Select State </option>';
			$.each(s_list, function(index, s_obj) {
				s_option_list += '<option value="'+s_obj.id+'">'+s_obj.name+'</option>';
			});
			$(s_option_list).appendTo($('.pos-customer-billing-state'));
			self.options.qsp.billing_state_id = 0;
		});

		// state field change event
		$('.pos-customer-billing-state').change(function(event) {
			self.options.qsp.billing_state_id = $(this).val();
		});

		//shipping county change event
		$('.pos-customer-shipping-country').change(function(){
			self.options.qsp.shipping_country_id = $(this).val();
			$(this).attr('data-country-id',$(this).val());

			$('.pos-customer-shipping-state').html("");
			var s_list = self.getState($(this).val());
			var s_option_list = '<option>Select State </option>';
			$.each(s_list, function(index, s_obj) {
				s_option_list += '<option value="'+s_obj.id+'">'+s_obj.name+'</option>';
			});
			$(s_option_list).appendTo($('.pos-customer-shipping-state'));
			self.options.qsp.shipping_state_id = 0;

			self.updateAllShippingAmount();
		});

		// shipping state change event
		$('.pos-customer-shipping-state').change(function(event) {
			self.options.qsp.shipping_state_id = $(this).val();
			self.updateAllShippingAmount();
		});
	},

	updateAllShippingAmount: function(){
		var self = this;
		$(self.element).find('.col-data').each(function(index,row){
			var row_item_id = $(row).find('.item-id-field').val();
			if(row_item_id <= 0 ){
				return;
			}
			self.updateShippingAmount($(row).find('.qty-field'));
			self.updateAmount($(row).find('.qty-field'));
		});
	},

	updateAmount: function($td_field_obj){
		var self = this;

		parent = $td_field_obj.closest('tr');

		// check item selected or not
		var row_item_id = $(parent).find('.item-id-field').val();
		if(row_item_id <= 0 ){
			self.displayError($(parent).find('.col-item > div.input-group'));
			return;
		}

		price_field = $(parent).find('.price-field');
		qty_field = $(parent).find('.qty-field');
		tax_field = $(parent).find('.tax-field');
		discount_field = $(parent).find('.discount-field');
		shipping_field = $(parent).find('.shipping-charge');
		express_shipping_field = $(parent).find('.express-shipping-charge');

		var price = parseFloat(price_field.val());
		var qty = parseFloat(qty_field.val());
		var discount_val = discount_field.val()?discount_field.val():0;

		//to do according to checkbox of express shipping
		var shipping_charge = shipping_field.val()?shipping_field.val():0;
		shipping_charge = parseFloat(shipping_charge);


		var tax_id = $(tax_field).val();

		var tax_percentage = 0;
		if(tax_id > 0){
			var taxation = self.options.taxation;
			// var taxation = JSON.parse(self.options.taxation);
			tax_percentage = parseFloat(taxation[tax_id].percentage);
		}

		// todo calculate here according to % or amount
		var discount_amount = discount_val;
		discount_amount = parseFloat(discount_amount);

		var amount = (price * qty)?(price * qty):0;
		if(self.options.apply_tax_on_discounted_amount)
			amount = amount - discount_amount;
		
		// is_shipping_inclusive_tax
		if(!self.options.shipping_inclusive_tax){
			amount = amount + shipping_charge;
		}

		var tax_amount = (amount * tax_percentage)/100;

		amount = amount + tax_amount;
		
		if(self.options.shipping_inclusive_tax){
			amount = amount + shipping_charge;
		}

		if(!self.options.apply_tax_on_discounted_amount){
			amount = amount - discount_amount;
		}

		$(parent).find('.amount-field').val(amount.toFixed(2));
		self.updateTotalAmount();
		self.showCommonTaxAndAmount();
	},

	updateAmountInWords: function(){
		var self = this;
		$(self.element).find('.pos-net-amount-in-words').html(self.amountInWords(self.options.qsp.net_amount.toFixed(2)));
	},

	showCustomFieldForm: function($tr){
		var self = this;
		var custom_field_json = JSON.parse($tr.find('.item-read-only-custom-field').val());

		form = "<div id='posform'>";
		$.each(custom_field_json,function(dept_id,detail){
			form +=
				'<div data-deptid="'+dept_id+'" class="accordion panel-group col-md-4 col-sm-4 col-lg-4 pos-department-customfield-panel">'+
				'<div class="panel panel-primary">'+
				
				//panel heading with checkbox
				'<div class="panel-heading" style="padding:5px 0px 5px 5px;" >'+
	 				'<h4 class="panel-title">'+

		  	  			'<input data-deptname="'+detail['department_name']+'" data-deptid="'+dept_id+'" class="pos-department-checkbox" value="'+detail['pre_selected']+'" '+(detail['pre_selected']?'checked=""':" ")+'  type="checkbox">&nbsp;'+
	    				'<label for="">'+detail['department_name']+'</label>'+
	 			 	'</h4>'+
				'</div>'+

				// panel body
				'<div class="panel-collapse">'+
      				'<div class="panel-body">'+
					self.getFormFields(detail)+
      				'</div>'+
    			'</div>'+

				'</div>'+
				'</div>';
		});

		form += "</div>";
		dialog = $(form).dialog({
			autoOpen: true,
	      	height: 500,
			modal: true,
			buttons: {
				Ok:function(){

				var selected_dept_cf = {};
				// check validation
				// for each of panel/accordian
				var all_clear = true;
				$('#posform').find('.pos-department-customfield-panel').each(function(index){
					//check department checkbox is checked
					var dept_checkbox = $(this).find('input.pos-department-checkbox');
					if(!$(dept_checkbox).is(':checked')) return;

					var selected_dept_id = $(dept_checkbox).attr('data-deptid');
					selected_dept_cf[selected_dept_id] = {};
					selected_dept_cf[selected_dept_id].department_name = $(dept_checkbox).attr('data-deptname');

					//for each of CF input is not selected
						//so error
					$(this).find('.pos-form-group').each(function(index){
						field = $(this).find('.pos-form-field');
						selected_value = $(field).val();
						if( selected_value == "" || selected_value == null || selected_value == undefined){
							self.displayError($(this));
							
							if(all_clear) all_clear = false;
							return false;
						}

						selected_dept_cf[selected_dept_id][$(this).attr('data-cfid')] = {};
						selected_dept_cf[selected_dept_id][$(this).attr('data-cfid')].custom_field_name = $(field).attr('data-cfname');
						selected_dept_cf[selected_dept_id][$(this).attr('data-cfid')].custom_field_value_id = selected_value;
						
						var cf_value_name = selected_value;
						if(field.prop('type') == 'select-one'){
							cf_value_name = $(this).find(".pos-form-field option:selected").text();
						}

						selected_dept_cf[selected_dept_id][$(this).attr('data-cfid')].custom_field_value_name = cf_value_name;
					});
					//todo check if same production lavel department checkbox is checked
						// if yes display error
				});

				if(!all_clear){
					return;
				} 

				// logic for update read_only values according to selected values
				// temporary selected dept_cf_id lists = ['dept_id' => ['cf_id_1'=>'cf_value_id_1', 'cf_id_2'=>'cf_value_id_2']]
				$.each(custom_field_json,function(dept_id,detail){
					
					//change pre_selected value
					if(selected_dept_cf[dept_id] == undefined){
						custom_field_json[dept_id]['pre_selected'] = 0;
					}else{
						custom_field_json[dept_id]['pre_selected'] = 1;
					}

					$('.pos-department-customfield-panel[data-deptid='+dept_id+']').find('.pos-form-group').each(function(index){
						cf_id = parseInt($(this).attr('data-cfid'));
						field = $(this).find('.pos-form-field');
						selected_value = $(field).val();

						custom_field_json[dept_id][cf_id]['custom_field_value_id'] = selected_value;
						custom_field_json[dept_id][cf_id]['custom_field_value_name'] = custom_field_json[dept_id][cf_id]['value'][selected_value];
					});
				});

				// console.log("extra info");
				// console.log(selected_dept_cf);
				// console.log(JSON.stringify(selected_dept_cf));

				$tr.find('.item-read-only-custom-field').val(JSON.stringify(custom_field_json));
				$tr.find('.item-custom-field').val(JSON.stringify(selected_dept_cf));
				self.addExtraInfo($tr);
				dialog.dialog( "close" );
				}
				// Cancel: function() {
				//   dialog.dialog( "close" );
				// }
			},
			close: function() {
				$(this).remove();
			}
		});
	},

	displayError: function($obj){
		$obj.addClass('pos-field-error');
		$obj.find('.error-message').remove();
		$('<div class="error-message">* please select mandatory field</div>').appendTo($obj);
	},

	getFormFields: function(dept_cf_detail){

		html = "";

		$.each(dept_cf_detail,function(cf_id,cf_details){
			if(cf_id  === "department_name" || cf_id === "pre_selected" || cf_id === "production_level" ) return; 

			switch(cf_details['display_type']){
				case "DropDown":
					html += '<div class="form-group pos-form-group" data-cfname="'+cf_details['custom_field_name']+'" data-cfid="'+cf_id+'">'+
								'<label>'+cf_details['custom_field_name']+'</label>';
					html += '<select class="form-control pos-form-field" data-cfname="'+cf_details['custom_field_name']+'">';
					
					html += '<option value="" data-cf-value-name="" >Please Select</option>';
					
					$.each(cf_details['value'],function(value_id,value_name){
						selected = "";
						if(cf_details['custom_field_value_id'] == value_id)
							selected = "selected";
						// alert(cf_details['custom_field_value_id']+" = "+value_id);
						html += '<option data-cf-value-name="'+value_name+'" '+selected+' value="'+value_id+'">'+value_name+'</option>';
					});

					html += '</select>';
					html += '</div>';
				break;

				case "Line":
					html += '<div class="form-group" data-cfid="'+cf_id+'">'+
							'<label>'+cf_details['custom_field_name']+'</label>'+
						'<input type="text" data-cfname="'+cf_details['custom_field_name']+'" class="pos-form-field">'+
					'</div>';
				break;
			}
		});

		return html;
	},

	updateTotalAmount: function(){
		var self = this;

		//for each of amount field
		self.options.qsp.gross_amount = 0;
		// $(this.element).find('input.amount-field').css('border','2px solid red');
		var total_amount = 0;
		$(this.element).find('input.amount-field').each(function(index){
			amount = parseFloat($(this).val());
			if(amount > 0)
		    	total_amount += amount;
		});
		self.options.qsp.gross_amount = total_amount;
		$(this.element).find('.pos-total-amount').html(total_amount.toFixed(2));

		var total_discount = 0;
		if(self.options.qsp.individual_item_discount){
			$(this.element).find('input.discount-field').each(function(index){
				d_amount = parseFloat($(this).val());
				if(d_amount > 0)
					total_discount += d_amount;
			});

			self.options.qsp.discount_amount = total_discount;
			// self.options.qsp.net_amount = self.options.qsp.gross_amount;
		}else{
			self.options.qsp.discount_amount = parseFloat($('.pos-discount-amount').val()?$('.pos-discount-amount').val():0);
			// self.options.qsp.net_amount = self.options.qsp.gross_amount - self.options.qsp.discount_amount;
			self.options.qsp.gross_amount = self.options.qsp.gross_amount - self.options.qsp.discount_amount;
		}

		// round amount calculation
        var rounded_gross_amount = 0;
        switch(self.options.round_standard){
         case 'Up' :
            rounded_gross_amount =  Math.ceil(self.options.qsp.gross_amount);
            break; 
         case 'Down' :
            rounded_gross_amount =  Math.floor(self.options.qsp.gross_amount);
            break;
         case 'Standard' :
         default :
           rounded_gross_amount =  Math.round(self.options.qsp.gross_amount);
            break; 
        }

        if(rounded_gross_amount === NaN || rounded_gross_amount == undefined) rounded_gross_amount = 0;

        self.options.qsp.round_amount = self.options.qsp.gross_amount - rounded_gross_amount;
        var round_amount_signed = (Math.round(self.options.qsp.round_amount * 100)/100).toFixed(2);
        self.options.qsp.round_amount = Math.abs(round_amount_signed);

        self.options.qsp.net_amount = self.options.qsp.gross_amount - round_amount_signed;

		$(this.element).find('.pos-gross-amount').html(self.options.qsp.gross_amount.toFixed(2));
		$(this.element).find('.pos-discount-amount').val(self.options.qsp.discount_amount.toFixed(2));
		$(this.element).find('.pos-round-amount').val(self.options.qsp.round_amount);
		$(this.element).find('.pos-net-amount').html(self.options.qsp.net_amount.toFixed(2));
		
		self.updateAmountInWords();
	},

	setUpEvents: function (){
		var self = this;
		
		// CLEAR POS
		$(this.element).find('.clear-pos').click(function(ev){
			self.clearPOS();
		});

		// ADD NEW ITEM
		$(this.element).find('.add-new-item').click(function(ev){
			self.addRow();
		});

		// ADD RESPECTIVE EVENTS TO ALL FIELD ACCORDING TO POS OR QSP
		self.addLiveEvents();
	},

	clearPOS: function(){
		alert ('Cleared');
	},

	/*
	* [
		'master'=>[
			'customer'=>[id,name,shipping,billing]
			'invoice_number'=>
			'narration'=>,
			'created_date'=>,
			'due_date'=>
			'gross_amount'=>
			'total_discount'=>
			'round_amount'=>
			'net_amount',
			'terms_and_condition',
			'currency',
			'nominal',
			],
		'detail'=>[
			0 => ['item_id','narration','custom_field','qty','price','discount_amount','shipping_charge','tax','amount']
			1 => ['item_id','custom_field','qty','price','discount_amount','shipping_charge','tax','amount']
			2 => ['item_id','custom_field','qty','price','discount_amount','shipping_charge','tax','amount']
		]
	]
	*/
	savePOS: function(){
		var self = this;
		var qsp_data = {};
		qsp_data['master'] = {};
		qsp_data['detail'] = {};

		// check validation for master field
		var all_clear = true;
		var field = $(self.element).find('.pos-master-mandatory');

		$.each($(self.element).find('.pos-master-mandatory'), function(index, field) {
			selected_value = $(field).val();

			// due date must be greater then created or qual to created date
			if($(field).hasClass('qsp_due_date')){
				c_d = $('.qsp_created_date').datepicker("getDate");
				d_d = $('.qsp_due_date').datepicker("getDate");

				if( d_d == null ||c_d > d_d)
					selected_value = null;
			}

			if( selected_value == "" || selected_value == null || selected_value == undefined){
				$field_row = $(field).closest('div');
				self.displayError($field_row);

				if(all_clear) all_clear = false;
				return false;
			}
		});

		if(!all_clear){
			return;
		}

		// master data
		var qsp_number = $('.qsp_number').val();
		var qsp_created_date = $('.qsp_created_date').datepicker("getDate");
		qsp_created_date = $.datepicker.formatDate("yy-mm-dd", qsp_created_date);

		var qsp_due_date = $('.qsp_due_date').datepicker("getDate");
		qsp_due_date = $.datepicker.formatDate("yy-mm-dd", qsp_due_date);
		var contact_id = self.options.qsp.contact_id;
		var narration = $('.pos-narration').val();
		var tnc_id = $('.pos-tnc').val();
		var currency_id = $('.pos-currency').val();
		var nominal_id = $('.pos-nominal').val();
		
		var b_country_id = self.options.qsp.billing_country_id;
		var b_state_id = self.options.qsp.billing_state_id;
		var b_city = $('.pos-customer-billing-city').val();
		var b_address = $('.pos-customer-billing-address').val();
		var b_pincode = $('.pos-customer-billing-pincode').val();
		
		var s_country_id = self.options.qsp.shipping_country_id;
		var s_state_id = self.options.qsp.shipping_state_id;
		var s_city = $('.pos-customer-shipping-city').val();
		var s_address = $('.pos-customer-shipping-address').val();
		var s_pincode = $('.pos-customer-shipping-pincode').val();

		qsp_data['master'].qsp_no = qsp_number;
		qsp_data['master'].created_date = qsp_created_date;
		qsp_data['master'].due_date = qsp_due_date;
		qsp_data['master'].contact_id = contact_id;
		qsp_data['master'].narration = narration;
		qsp_data['master'].tnc_id = tnc_id;
		qsp_data['master'].currency_id = currency_id;
		qsp_data['master'].nominal_id = nominal_id;
		
		qsp_data['master'].billing_name = "";
		qsp_data['master'].billing_country_id = b_country_id;
		qsp_data['master'].billing_state_id = b_state_id;
		qsp_data['master'].billing_city = b_city;
		qsp_data['master'].billing_address = b_address;
		qsp_data['master'].billing_pincode = b_pincode;

		qsp_data['master'].shipping_name = "";
		qsp_data['master'].shipping_country_id = s_country_id;
		qsp_data['master'].shipping_state_id = s_state_id;
		qsp_data['master'].shipping_city = s_city;
		qsp_data['master'].shipping_address = s_address;
		qsp_data['master'].shipping_pincode = s_pincode;

		qsp_data['master'].is_shipping_inclusive_tax = self.options.shipping_inclusive_tax;
		qsp_data['master'].exchange_rate = 1;

		qsp_data['master'].gross_amount = self.options.gross_amount;
		qsp_data['master'].discount_amount = self.options.discount_amount;
		qsp_data['master'].round_amount = self.options.round_amount;
		qsp_data['master'].net_amount = self.options.net_amount;

		
		// detail rows
		$(self.element).find('.col-data').each(function(index,row){
			if($(row).find('.item-id-field').val() <= 0) return;

			var temp = {};
			$(row).find('.pos-qsp-field').each(function(field,field_object){
				temp[$(field_object).attr('data-field').replace("item-",'')] = $(field_object).val();
			});
			qsp_data['detail'][index] = temp;
		});

		$.ajax({
			url: self.save_pos_url,
			type: 'POST',
			data: {
					qsp_data: JSON.stringify(qsp_data),
					qsp_type:self.options.document_type
				},
		})
		.done(function(ret) {
			var ret = $.parseJSON(ret);

			if(ret.status == "success"){
				$.univ().successMessage(ret.message);
			}else
				$.univ().errorMessage(ret.message);

		})
		.fail(function() {
			$.univ().errorMessage('failed, not saved');
		});
		// .always(function() {
		// 	console.log("complete");
		// });
	},

	convertNumberToWords: function(amount) {
	    var words = new Array();
	    words[0] = '';
	    words[1] = 'One';
	    words[2] = 'Two';
	    words[3] = 'Three';
	    words[4] = 'Four';
	    words[5] = 'Five';
	    words[6] = 'Six';
	    words[7] = 'Seven';
	    words[8] = 'Eight';
	    words[9] = 'Nine';
	    words[10] = 'Ten';
	    words[11] = 'Eleven';
	    words[12] = 'Twelve';
	    words[13] = 'Thirteen';
	    words[14] = 'Fourteen';
	    words[15] = 'Fifteen';
	    words[16] = 'Sixteen';
	    words[17] = 'Seventeen';
	    words[18] = 'Eighteen';
	    words[19] = 'Nineteen';
	    words[20] = 'Twenty';
	    words[30] = 'Thirty';
	    words[40] = 'Forty';
	    words[50] = 'Fifty';
	    words[60] = 'Sixty';
	    words[70] = 'Seventy';
	    words[80] = 'Eighty';
	    words[90] = 'Ninety';
	    amount = amount.toString();
	    var atemp = amount.split(".");
	    var number = atemp[0].split(",").join("");
	    var n_length = number.length;
	    var words_string = "";
	    if (n_length <= 9) {
	        var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	        var received_n_array = new Array();
	        for (var i = 0; i < n_length; i++) {
	            received_n_array[i] = number.substr(i, 1);
	        }
	        for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
	            n_array[i] = received_n_array[j];
	        }
	        for (var i = 0, j = 1; i < 9; i++, j++) {
	            if (i == 0 || i == 2 || i == 4 || i == 7) {
	                if (n_array[i] == 1) {
	                    n_array[j] = 10 + parseInt(n_array[j]);
	                    n_array[i] = 0;
	                }
	            }
	        }
	        value = "";
	        for (var i = 0; i < 9; i++) {
	            if (i == 0 || i == 2 || i == 4 || i == 7) {
	                value = n_array[i] * 10;
	            } else {
	                value = n_array[i];
	            }
	            if (value != 0) {
	                words_string += words[value] + " ";
	            }
	            if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
	                words_string += "Crore ";
	            }
	            if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
	                words_string += "Lakh ";
	            }
	            if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
	                words_string += "Thousand ";
	            }
	            if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
	                words_string += "Hundred and ";
	            } else if (i == 6 && value != 0) {
	                words_string += "Hundred ";
	            }
	        }
	        words_string = words_string.split("  ").join(" ");
	    }
	    return words_string;
	},

	amountInWords: function(n) {
		var self = this;
		var currency;
		$.each(self.options.currency, function(index, obj){
			if(obj.id === self.options.default_currency_id){
				currency = obj;
				return false;
			}
		});

		var nums = n.toString().split('.')
		var whole = self.convertNumberToWords(nums[0])
		if (nums.length == 2) {
			var fraction = self.convertNumberToWords(nums[1])
			if(fraction.length)
				return whole + currency.integer_part +'and ' + fraction + currency.fractional_part;
			else
				return whole + currency.integer_part;
		}else {
			return whole + currency.integer_part;
		}
	}
});


$.ui.autocomplete.prototype._renderItem = function(ul, item){

	return $("<li></li>")
		.data("item.autocomplete", item)
		// this is autocomplete list that is generated
		.append("<a class='item-autocomplete-list'> " + item.name +

			"<span class='item-extra-info' style='display:none;'>"+
				"<div><h4>Item Information</h4></div>"+
				"<div><strong>SKU: </strong>"+item.sku+"</div>"+
				"<div>"+item.description+"</div>"+
			"</span>"+
			"</a>")
		.appendTo(ul)
		;
};