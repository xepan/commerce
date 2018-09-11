// tinymce.baseURL = "./vendor/tinymce/tinymce";
// xepan_pos_tinymce_options=
// {		
// 		selector:'.narration-field',
// 		inline:false,
// 		menubar: false,
// 		forced_root_block: 'span',
// 		plugins: ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
//                 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
//                 "save table contextmenu directionality emoticons template paste textcolor colorpicker imagetools"],
// 		statusbar:false,
// 		toolbar:false,
// 		importcss_append: false,
// 		verify_html: false,
// 		theme_url: 'vendor/tinymce/tinymce/themes/modern/theme.min.js',
// 		theme: 'modern',
// 		height:'50',
// 		placeholder:'Narration',
// 		setup:function(ed){
// 			ed.on('change',function(ed){
// 				tinyMCE.triggerSave();
// 			});
// 		}
// 	};

jQuery.widget("ui.xepan_pos",{
	selectorAutoComplete: ".item-field",
	selectorUpdateAmount: ".amount-calc-factor",
	selectorExtraInfoBtn:'.item-extrainfo-btn',
	item_ajax_url:'index.php?page=xepan_commerce_pos_item',
	item_amount_ajax_url:'index.php?page=xepan_commerce_pos_getamount',
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
		show_shipping_address:1,
		individual_item_discount:0,
		document_type:undefined,
		country:[],
		state:[],
		tnc:[],
		currency:[],
		nominal:[],
		unit_list:[],
		common_tax_and_amount:[],
		default_currency_id:0,
		item_list:[],
		document_id:null
	},

	_create : function(){
		
		var self = this;
		self.item_ajax_url = self.options.item_page_url;
		self.item_amount_ajax_url = self.options.item_amount_page_url;
		self.customer_ajax_url = self.options.customer_page_url;
		self.item_detail_ajax_url = self.options.item_detail_page_url;
		self.item_shipping_ajax_url = self.options.item_shipping_page_url;
		self.save_pos_url = self.options.save_page_url;
		
		this.setupEnvironment();
		this.loadQSP();

		if(!self.options.qsp.details.length)
			this.addRow();
		
		// this.setUpEvents();

		this.updateTotalAmount();
		this.showCommonTaxAndAmount();

		// load item list
	 	$.ajax({
    		url: self.item_ajax_url,
          	success: function( data ) {
            	self.item_list = JSON.parse(data);
            	self.setUpEvents();
          	}
		});
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
		if(self.options.show_shipping_address == 1){

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
		}

		
		if(saved_qsp.document_no=='-')
			var $qsp_no = $('<input type="text" class="qsp_number pos-master-mandatory">').appendTo($('.qsp_number-form-row'));
		else
			var $qsp_no = $('<input class="qsp_number pos-master-mandatory">').appendTo($('.qsp_number-form-row'));
		$qsp_no.val(saved_qsp.document_no);
		
		var $qsp_serial = $('<input class="qsp_number_serial">').appendTo($('.qsp_number-serial-form-row'));
		$qsp_serial.val(saved_qsp.serial);


		var $qsp_created_date = $('<input class="qsp_created_date pos-master-mandatory" value="'+saved_qsp.created_at+'">').appendTo($('.qsp_created_date-form-row'));
		var $qsp_due_date = $('<input class="qsp_due_date pos-master-mandatory" value="'+saved_qsp.due_date+'">').appendTo($('.qsp_due_date-form-row'));
			
		$qsp_created_date.appendDtpicker({'minuteInterval':5});
		// $qsp_created_date.datepicker({dateFormat: 'yy-mm-dd'});
		$qsp_created_date.appendDtpicker("setDate",new Date(saved_qsp.created_at));
		// $qsp_due_date.datepicker({dateFormat: 'yy-mm-dd'});
		$qsp_due_date.appendDtpicker({'minuteInterval':5});
		$qsp_due_date.appendDtpicker("setDate",new Date(saved_qsp.due_date));

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

		// exchange rate
		var $exchange_rate = $('<label>@</label>').appendTo($('.pos-currency-form-row'));
		$('<input class="pos-master-mandatory pos-exchange-rate" value="'+saved_qsp.exchange_rate+'"/>').appendTo($exchange_rate);
		
		if(self.options.document_type == "SalesInvoice" || self.options.document_type == "PurchaseInvoice"){
			var $nominal = $('<select class="pos-nominal pos-master-mandatory">').appendTo($('.pos-nominal-form-row'));
			var nominal_list = '<option value="0" selected="selected" >Select Nominal</option>';
			$.each(self.options.nominal, function(index, obj) {
				nominal_list += '<option value="'+obj.id+'">'+obj.name+'</option>';
			});
			$(nominal_list).appendTo($nominal);
			$nominal.val((saved_qsp.nominal_id)?saved_qsp.nominal_id:0);
		}else{
			$('.pos-nominal-form-row').hide();
		}

		// comman vat and it's amount
		self.showCommonTaxAndAmount();
	},

	showCommonTaxAndAmount: function(){
		var self = this;
		var comman_tax_amount = {};
		var gst_tax_detail = {};

		$(this.element).find('tr.col-data').each(function(index, row) {
			// check item selected or not
			var row_item_id = $(row).find('.item-id-field').val();
			if(row_item_id <= 0 ){
				// self.displayError($(parent).find('.col-item > div.input-group'));
				return;
			}

			var hsn_sac_no = $(row).find('.item-hsn-sac').val();

			if(gst_tax_detail[hsn_sac_no] == undefined)
				gst_tax_detail[hsn_sac_no] = {};

			price_field = $(row).find('.price-field');
			qty_field = $(row).find('.qty-field');
			tax_field = $(row).find('.tax-field');
			discount_field = $(row).find('.discount-field');
			shipping_field = $(row).find('.shipping-charge');
			express_shipping_field = $(row).find('.express-shipping-charge');

			treat_sale_price_as_amount_field = $(row).find('.treat_sale_price_as_amount');

			var price = parseFloat(price_field.val());
			var qty = parseFloat(qty_field.val());
			var discount_val = discount_field.val()?discount_field.val():0;

			// treat sale price as amount 
			if($(treat_sale_price_as_amount_field).val() == 1){
				qty = 1;
			}
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
				if(self.options.shipping_inclusive_tax){
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


				// gst tax
				// if taxation has sub tax then
				if( gst_tax_detail[hsn_sac_no].net_amount_sum == undefined){
					gst_tax_detail[hsn_sac_no].net_amount_sum = 0;
				}

				gst_tax_detail[hsn_sac_no].net_amount_sum = parseFloat(gst_tax_detail[hsn_sac_no].net_amount_sum) + parseFloat(amount);

				if( gst_tax_detail[hsn_sac_no].total_taxation_sum == undefined ){
					gst_tax_detail[hsn_sac_no].total_taxation_sum = 0;
				}

				sub_tax = taxation[tax_id].sub_tax;
				if(sub_tax != undefined || sub_tax != null ){
					var sub_tax_array = sub_tax.split(',');
					// for each sub tax
					$.each(sub_tax_array, function(index, val) {
						var tax_detail = val.split('-');
						var sub_tax_id = tax_detail[0];

						tax = taxation[sub_tax_id];
						if(!(sub_tax_id in gst_tax_detail[hsn_sac_no])){

							// if( gst_tax_detail[hsn_sac_no].net_amount_sum == undefined){
							// 	gst_tax_detail[hsn_sac_no].net_amount_sum = 0;
							// }
							
							// if( gst_tax_detail[hsn_sac_no].total_taxation_sum == undefined ){
							// 	gst_tax_detail[hsn_sac_no].total_taxation_sum = 0;
							// }

							if( gst_tax_detail[hsn_sac_no][sub_tax_id] == undefined ){
								gst_tax_detail[hsn_sac_no][sub_tax_id] = {};
								gst_tax_detail[hsn_sac_no][sub_tax_id].tax_name = tax.name;
								gst_tax_detail[hsn_sac_no][sub_tax_id].taxation_sum = 0;
								gst_tax_detail[hsn_sac_no][sub_tax_id].tax_rate = tax.percentage;
							}

						}

						tax_percentage = parseFloat(tax.percentage);
						var discount_amount = discount_val;
						discount_amount = parseFloat(discount_amount);

						var amount = (price * qty)?(price * qty):0;
						if(self.options.apply_tax_on_discounted_amount)
							amount = amount - discount_amount;
				
						// is_shipping_inclusive_tax
						if(self.options.shipping_inclusive_tax){
							amount = amount + shipping_charge;
						}

						var tax_amount = (amount * tax_percentage)/100;


						// gst_tax_detail[hsn_sac_no].net_amount_sum = parseFloat(gst_tax_detail[hsn_sac_no].net_amount_sum) + parseFloat(amount);

						gst_tax_detail[hsn_sac_no].total_taxation_sum = parseFloat(gst_tax_detail[hsn_sac_no].total_taxation_sum) + parseFloat(tax_amount);
						gst_tax_detail[hsn_sac_no][sub_tax_id].taxation_sum = parseFloat(gst_tax_detail[hsn_sac_no][sub_tax_id].taxation_sum) + parseFloat(tax_amount);
						
						// console.log(hsn_sac_no+' net amount setted to = '+gst_tax_detail[hsn_sac_no].net_amount_sum);
					});
				}else{
					var tax = taxation[tax_id];

					if(!(tax_id in gst_tax_detail[hsn_sac_no])){
						// gst_tax_detail[hsn_sac_no].net_amount_sum = 0;
						// gst_tax_detail[hsn_sac_no].total_taxation_sum = 0;

						gst_tax_detail[hsn_sac_no][tax_id] = {};
						gst_tax_detail[hsn_sac_no][tax_id].tax_name = tax.name;
						gst_tax_detail[hsn_sac_no][tax_id].taxation_sum = 0;
						gst_tax_detail[hsn_sac_no][tax_id].tax_rate = tax.percentage;
					}

					tax_percentage = parseFloat(tax.percentage);
					var discount_amount = discount_val;
					discount_amount = parseFloat(discount_amount);
					
					var amount = (price * qty)?(price * qty):0;
					if(self.options.apply_tax_on_discounted_amount)
						amount = amount - discount_amount;
				
					// is_shipping_inclusive_tax
					if(self.options.shipping_inclusive_tax){
						amount = amount + shipping_charge;
					}

					var tax_amount = (amount * tax_percentage)/100;
				
					// gst_tax_detail[hsn_sac_no].net_amount_sum = parseFloat(gst_tax_detail[hsn_sac_no].net_amount_sum) + parseFloat(amount);
					gst_tax_detail[hsn_sac_no].total_taxation_sum = parseFloat(gst_tax_detail[hsn_sac_no].total_taxation_sum) + parseFloat(tax_amount);
					gst_tax_detail[hsn_sac_no][tax_id].taxation_sum = parseFloat(gst_tax_detail[hsn_sac_no][tax_id].taxation_sum) + parseFloat(tax_amount);
				}


			}
		});

		var html_str = "";
		$.each(comman_tax_amount, function(name, val) {
			html_str += '<div class="pos-common-tax">'+val.name+' = '+val.taxation_sum.toFixed(2)+' on '+val.net_amount_sum.toFixed(2)+'</div>';
		});
		$(self.element).find('.pos-common-tax-amount-wrapper').html(html_str);

		// gst html
		var gst_html = '<table style="width:100%; text-align:center;" border="1">';
		
		var detail_row = "";
		var header_col = [];
		header_col.push('HSN/SAC');
		header_col.push('Taxable Value');

		var tax_sum_array = {};
		// header column rows
		$.each(gst_tax_detail, function(hsn_sac_no, obj) {
			$.each(obj, function(sub_tax_id, st_detail) {
				if($.isNumeric(sub_tax_id)){
					if($.inArray(st_detail.tax_name, header_col) == -1){
						header_col.push(st_detail.tax_name);
						tax_sum_array[st_detail.tax_name] = 0;
					}
				}
			});
		});

		// draw header 
		var header_row = "<tr>";
		$.each(header_col, function(index, val){
			var temp = ['HSN/SAC','Taxable Value'];
			if($.inArray( val, temp ) == -1){
				header_row += "<th style='text-align:center;'>"+val+"<table style='width:100%;text-align:center;'><tbody><tr><td style='width:50%;width:50%;border:1px solid black;border-bottom:0px;border-left:0px;'>Rate %</td><td style='border-top:1px solid black;'>Amount</td></tr></tbody></table></th>";
			}else{
				header_row += "<th style='text-align:center;'>"+val+"</th>";
			}
		});

		header_row += "</tr>";

		// detail row
		$.each(gst_tax_detail, function(hsn_sac_no, obj) {	
			detail_row += "<tr>";
			// for hsn_no
			detail_row += "<td>"+hsn_sac_no+"</td>";
			detail_row += "<td>"+obj.net_amount_sum+"</td>";

			// subtax
			$.each(header_col, function(index, tax_name) {

				var temp = ['HSN/SAC','Taxable Value'];
				if($.inArray( tax_name, temp ) == -1){

					sub_tax_found = false;
					$.each(obj, function(sub_tax_id, st_detail) {
						if($.isNumeric(sub_tax_id)){

							if(tax_name === st_detail.tax_name){
								sub_tax_found = true;
								detail_row += "<td><table style='width:100%;text-align:center;'><tr><td style='width:50%;border-right:1px solid black;'>"+st_detail.tax_rate+"</td><td>"+st_detail.taxation_sum+"</td></tr></table></td>";
								tax_sum_array[tax_name] = tax_sum_array[tax_name] + st_detail.taxation_sum;
								return false;
							}
						}

					});
					if(!sub_tax_found)
						detail_row += "<td><table style='width:100%;text-align:center;'><tr><td style='width:50%;border-right:1px solid black;'>0</td><td>0</td></tr></table></td>";
				}
			});

			// add column for multiple sub tax
			detail_row += "</tr>";
		});

		// total row
		var total_row = "<tr style='font-weight:bold;'>";
		total_row += "<td colspan='2' style='text-align:right;'>Total: </td>";
		$.each(header_col, function(index, tax_name) {

			var temp = ['HSN/SAC','Taxable Value'];
			if($.inArray( tax_name, temp ) == -1){
				total_row += "<td><table style='width:100%;text-align:center;'><tr><td style='width:50%;border-right:1px solid black;'></td><td>"+tax_sum_array[tax_name]+"</td></tr></table></td>";
			}

		});

		total_row += "</tr>";

		gst_html += header_row;
		gst_html += detail_row;
		gst_html += total_row;

		gst_html += "</table>";
		$(self.element).find('.pos-tax-detail-wrapper').html(gst_html);
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
		thead_html += '<th class="col-unit">Unit</th>';
		thead_html += '<th class="col-price">Price</th>';

		th_amount_excludig_tax = '<th class="col-amount-excluting-tax">Amount<br/>Excludig Tax</th>';
		th_discount = '<th class="col-discount">Discount</th>';
		th_tax = '<th class="col-tax">Tax</th>';
		th_shipping = '<th class="col-shipping">Shipping <br/>Charge</th>';

		if(self.options.apply_tax_on_discounted_amount){
			
			if(self.options.individual_item_discount)
				thead_html += th_discount;

			if(self.options.shipping_inclusive_tax){
        		thead_html += th_shipping;
        		thead_html += th_amount_excludig_tax;
				thead_html += th_tax;
			}else{
        		thead_html += th_amount_excludig_tax;
				thead_html += th_tax;
        		thead_html += th_shipping;
			}
		}else{
			if(self.options.shipping_inclusive_tax){
        		thead_html += th_shipping;
        		thead_html += th_amount_excludig_tax;
				thead_html += th_tax;
			}else{
        		thead_html += th_amount_excludig_tax;
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

		// load document info
		
		var document_other_info = saved_qsp['document_other_info'];
		if(document_other_info != undefined && document_other_info != null){
			self.addOtherInfo(document_other_info);
		}

	},


	addOtherInfo: function(document_other_info){
		var self = this;
		var other_info_section = $.find('.pos-document-info-form');

		var other_info_form = "";
		$.each(document_other_info,function(field_name,detail){
			if(!$.trim(field_name).length) return; // actually continue
			other_info_form += self.getFieldHtml(field_name,detail);
		});

		if(other_info_form.length){
			$('<div id="xepan-pos-other-info" class="well well-sm"><h3>Document Other Info </h3><br/><form class="atk-form"><div class="row document-other-info-section">'+other_info_form+'</div></form></div>').appendTo($(other_info_section));
		}

		$.each(document_other_info,function(field_name,detail){
			if(!$.trim(detail.conditional_binding).length) return; // actually continue

			var str = detail.conditional_binding;
			var condition = JSON.parse(str);
			$("[data-field='"+field_name+"']").univ().bindConditionalShow(condition ,'div.pos-other-form-group');
			
		});

	},

	getFieldHtml: function(field_name,detail){
		var field_html = '<div class="form-group pos-form-group pos-other-form-group col-md-6 col-lg-6 col-sm-12 col-xs-12" data-shortname="'+field_name+'" style="height:55px;">'+
							'<label>'+field_name+'</label>';

		var field_value = "";
		if((detail.value != undefined || detail.value != "null" || detail.value != null)){
			field_value = detail.value;
		}
		var mandatory_class = "";
		if(detail.is_mandatory == 1 || detail.is_mandatory == "true")
			mandatory_class = "pos-master-mandatory";

		switch(detail.type){
			case "DropDown":
				field_html += '<select class="pos-form-field doc-other-info-field '+mandatory_class+'" data-field="'+field_name+'">';
				field_html += '<option value="" >Please Select</option>';
				
				$.each( detail.possible_values.split(','),function(value_id,value_name){
					selected = "";
					if(detail.value == value_name)
						selected = "selected";
					field_html += '<option data-other-value-name="'+value_name+'" '+selected+' value="'+value_name+'">'+value_name+'</option>';
				});
				field_html += '</select>';
			break;

			case "Line":
				field_html += '<input data-field="'+field_name+'" class="pos-qsp-field doc-other-info-field '+mandatory_class+'" value="'+field_value+'"/>';
			break;

			case "Text":
				field_html += '<textarea style="height:45px;" data-field="'+field_name+'" class="pos-qsp-field doc-other-info-field '+mandatory_class+'">'+field_value+'</textarea>';
			break;

			case "DatePicker":
				field_html += '<input data-other-value="'+field_value+'" data-field="'+field_name+'" class="pos-qsp-field doc-other-info-field other-datepicker '+mandatory_class+'" value="'+field_value+'"/>';
			break;
		}

		field_html += '</div>';

		return field_html;
	},

	addRow: function(qsp_item = []){
		// console.log("called row");
		var self = this;
		next_sno = $.find('table.addeditem tr.col-data').length + 1;
		
		var taxation = self.options.taxation;
		// var taxation = JSON.parse(self.options.taxation);
		var tax_option = '<option value="0" selected="selected">Please Select</option>';
		$.each(taxation,function(tax_id,tax_detail){
			if(tax_detail.show_in_qsp == '0' || tax_detail.show_in_qsp == 0 ) return true;
			
			tax_option += '<option value="'+tax_id+'">'+tax_detail.name+':'+tax_detail.percentage+'</option>';
		});

		var rowTemp = '<tr data-sno="1" class="col-data">';
        	rowTemp += '<td class="col-sno">'+next_sno+'</td>';
        	rowTemp += '<td class="col-item"><div class="input-group"><input data-field="item-item" placeholder="Item/ Particular" class="item-field pos-qsp-field" data-is_productionable="" data-is_production_phases_fixed="" /><span data-field="item-extra-nfo-btn" class="item-extrainfo-btn input-group-addon"><i class="fa fa-navicon"></i></span></div><input  type="hidden" data-field="item-item_id" placeholder="Item id" class="item-id-field pos-qsp-field" /><input type="hidden" data-field="item-extra_info" placeholder="Item custom field" class="item-custom-field pos-qsp-field"/><input type="hidden" data-field="item-read_only_custom_field_values" placeholder="Item read only custom field" class="item-read-only-custom-field pos-qsp-field"/><input type="hidden" data-field="item-qsp-detail-id" class="pos-qsp-detail-id pos-qsp-field"/><input type="hidden" data-field="item-hsn_sac" class="pos-qsp-field item-hsn-sac"/><small>Narration:</small><br/><input data-field="item-narration" placeholder="Narration" class="narration-field pos-qsp-field"/><br/><div class="pos-extra-info-wrapper"></div></td>';
        	rowTemp += '<td class="col-qty"><input data-field="item-quantity" placeholder="Quantity" class="qty-field amount-calc-factor pos-qsp-field" value="0"/><input type="hidden" data-field="item-treat_sale_price_as_amount" class="treat_sale_price_as_amount pos-qsp-field" /></td>';
        	rowTemp += '<td class="col-unit"><select data-field="item-qty_unit_id" placeholder="Unit" class="qty-unit-field pos-qsp-field"></select></td>';
        	rowTemp += '<td class="col-price"><input data-field="item-price" placeholder="Unit Price" class="price-field amount-calc-factor pos-qsp-field"/></td>';

        var td_amount_excluding_tax = '<td class="col-amount-excluting-tax"><input readOnly data-field="item-amount_excluding_tax" placeholder="amount" class="amount-excluding-tax-field pos-qsp-field"/></td>';
    	var td_tax = '<td class="col-tax"><select data-field="item-taxation_id" id="tax-field" class="form-control tax-field amount-calc-factor pos-qsp-field" value="0">'+tax_option+'</select><input data-field="item-tax_amount" placeholder="tax amount" class="tax-amount-field " value="0" readOnly/></td>';
    	var td_discount = '<td class="col-discount"><input data-field="item-discount" placeholder="Discount %/ amount" class="discount-field amount-calc-factor pos-qsp-field" value="0"/></td>';
    	var td_shipping = '<td class="col-shipping"><input data-field="item-shipping_charge" placeholder="shipping charge" class="shipping shipping-charge amount-calc-factor pos-qsp-field" value="0"><input data-field="item-shipping_duration" placeholder="shipping duration" class="shipping shipping-duration pos-qsp-field"><input data-field="item-express_shipping_charge" placeholder="express shipping charge" class="express-shipping express-shipping-charge amount-calc-factor pos-qsp-field" value="0"><input data-field="item-express_shipping_duration" placeholder="express duration" class="express-shipping express-shipping-duration pos-qsp-field"></td>';
 		       
        // managing td column according to apply tax on discount or shipping
        if(self.options.apply_tax_on_discounted_amount){
			if(self.options.individual_item_discount)
				rowTemp += td_discount

			if(self.options.shipping_inclusive_tax){
        		rowTemp += td_shipping;
        		rowTemp += td_amount_excluding_tax;
				rowTemp += td_tax;
			}else{
        		rowTemp += td_amount_excluding_tax;
				rowTemp += td_tax;
        		rowTemp += td_shipping;
			}
		}else{
			if(self.options.shipping_inclusive_tax){
        		rowTemp += td_shipping;
        		rowTemp += td_amount_excluding_tax;
				rowTemp += td_tax;
			}else{
        		rowTemp += td_amount_excluding_tax;
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

			if(field_name == "is_productionable")
				$(new_row).find('[data-is_productionable]').attr('data-is_productionable',value);
			if(field_name == "is_production_phases_fixed")
				$(new_row).find('[data-is_production_phases_fixed]').attr('data-is_production_phases_fixed',value);
		});

		$(new_row).find('.express-shipping').hide();

		self.addExtraInfo(new_row);
		if(qsp_item.export_design != undefined){
			$(qsp_item.export_design).appendTo($(new_row).find('.col-item'));
		}

		if(qsp_item.export_attachments != undefined){
			$(qsp_item.export_attachments).appendTo($(new_row).find('.col-item'));
		}
		
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

				if(cf_details['custom_field_name'] && cf_details['custom_field_value_name']){
					extra_info_html += '<div class="pos-department-cf-detail">'+cf_details['custom_field_name']+" : "+cf_details['custom_field_value_name']+'</div>';
				}
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
				source:self.item_list,
					// function( request, response ) {
			  //   	$.ajax( {
			  //   		url: self.item_ajax_url,
			  //   		dataType: "json",
			  //   		data: {
			  //   			term: request.term,
			  //   			country_id: self.options.qsp.shipping_country_id,
			  //   			state_id: self.options.qsp.shipping_state_id
					// 	},
			  //         	success: function( data ) {
			  //           	response( data );
			  //         	}
			  //       });
			    // },
				minLength:0,
				select: function( event, ui ) {
					// after select auto fill qty and price

					$tr = $(this).closest('.col-data');
					$tr.find('.price-field').val(ui.item.price);
					$tr.find('.item-id-field').val(ui.item.id);
					$tr.find('.item-custom-field').val(ui.item.custom_field);
					$tr.find('.item-read-only-custom-field').val(ui.item.read_only_custom_field);
					$tr.find('.col-tax select.tax-field').val(ui.item.tax_id);
					$tr.find('.item-hsn-sac').val(ui.item.hsn_sac);
					$tr.find('.treat_sale_price_as_amount').val(ui.item.treat_sale_price_as_amount);
					$tr.find('.item-field').attr('data-is_productionable',ui.item.is_productionable);
					$tr.find('.item-field').attr('data-is_production_phases_fixed',ui.item.is_production_phases_fixed);

					self.updateAmount($tr.find('.qty-field'));

					// on selct get custom field of item

					// console.log('GST');
					// console.log(self.options.qsp);

					$.ajax({
						url:self.item_detail_ajax_url,
						data:{
							item_id:ui.item.id,
							country_id: self.options.qsp.shipping_country_id,
			    			state_id: self.options.qsp.shipping_state_id
						},
						success: function( data ) {
							item_data = JSON.parse(data);
							$tr.find('.price-field').val(item_data.price);
							$tr.find('.item-id-field').val(item_data.id);
							$tr.find('.item-custom-field').val(item_data.custom_field);
							$tr.find('.item-read-only-custom-field').val(item_data.read_only_custom_field);
							$tr.find('.col-tax select.tax-field').val(item_data.tax_id);
							$tr.find('.item-read-only-custom-field').val(JSON.stringify(item_data.cf));
							$tr.find('.col-tax select.tax-field').val(item_data.tax_id);

							self.updateUnit($tr,item_data.qty_unit_group_id,item_data.qty_unit_id);

							if(item_data.is_productionable == 1)
								self.showCustomFieldForm($tr);

			          	},
			          	error: function(XMLHttpRequest, textStatus, errorThrown) {
			              alert("Error getting prospect list: " + textStatus);
			            }
					});
			    }
			}).focus(function(){
				$(this).autocomplete('search', $(this).val());
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
					// get price 
					if($(this).hasClass('qty-field')){
						// get price of item based on item custom fields
						$tr = $(this).closest('tr');
						var cf_fields = $tr.find('.item-custom-field').val();
						$.ajax({
				    		url: self.item_amount_ajax_url,
				    		data:{
				    			'item_id':$tr.find('.item-id-field').val(),
				    			'custom_field':cf_fields,
				    			'qty':$tr.find('.qty-field').val()
				    		},
				          	success: function( data ) {
				            	var price_list = JSON.parse(data);
				            	$tr.find('.price-field').val(price_list.sale_price).trigger('keyup');
				          	}
						});
					}

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
				var item_id = $tr.find('.item-field').attr('data-is_productionable');

				if(item_id == "1" || item_id == 1 )
					self.showCustomFieldForm($tr);
				else
					$.univ().errorMessage('selected Item/Product is not producationable');
			});
		});

		// document other info live event
		$('.other-datepicker').livequery(function(){
			$('.other-datepicker').appendDtpicker("setDate",new Date($(this).val()));
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
				$(this).closest('.pos-field-error')
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

		$('.narration-field').livequery(function(){
			// tinymce.init(xepan_pos_tinymce_options);
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
				
				change_from_customer_for_billing = 1;
				change_from_customer_for_shipping = 1;
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
			if(!change_from_customer_for_billing){
				self.options.qsp.billing_state_id = 0;
				change_from_customer_for_billing = 0;
			}
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
			if(!change_from_customer_for_shipping){
				self.options.qsp.shipping_state_id = 0;
				change_from_customer_for_shipping = 0;
			}

			self.updateAllShippingAmount();
		});

		// shipping state change event
		$('.pos-customer-shipping-state').change(function(event) {
			self.options.qsp.shipping_state_id = $(this).val();
			self.updateAllShippingAmount();
		});

		$('.custom-field-dtpicker').livequery(function(){
			$(this).appendDtpicker({'minuteInterval':5});
		});

		$('.custom-field-dpicker').livequery(function(){
			$(this).datepicker({dateFormat: 'yy-mm-dd'});
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
		treat_sale_price_as_amount_field = $(parent).find('.treat_sale_price_as_amount')

		var price = parseFloat(price_field.val());
		var qty = parseFloat(qty_field.val());

		if($(treat_sale_price_as_amount_field).val() == 1){
			qty = 1;
		}

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
		var amount_excluding_tax = amount;
		if(self.options.apply_tax_on_discounted_amount)
			amount = amount - discount_amount;
		
		// is_shipping_inclusive_tax
		if(self.options.shipping_inclusive_tax){
			amount = amount + shipping_charge;
			amount_excluding_tax = amount;
		}

		var tax_amount = (amount * tax_percentage)/100;

		amount = amount + tax_amount;
		
		if(!self.options.shipping_inclusive_tax){
			amount = amount + shipping_charge;
		}

		if(!self.options.apply_tax_on_discounted_amount){
			amount = amount - discount_amount;
		}

		$(parent).find('.amount-excluding-tax-field').val(amount_excluding_tax.toFixed(2));
		$(parent).find('.amount-field').val(amount.toFixed(2));
		$(parent).find('.tax-amount-field').val(tax_amount.toFixed(2));

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

		var is_production_phases_fixed = $tr.find('.item-field').attr('data-is_production_phases_fixed');
		// checking if dept_count has one and no custom field then return
		var dept_count = 0;
		var dept_cf_count = 0;
		$.each(custom_field_json,function(dept_id,detail){
			dept_count = dept_count + 1;
			
			if(dept_id == 0){
				$.each(detail,function(cf_id,cf_details){
					if(cf_id === "department_name" || cf_id === "pre_selected" || cf_id === "production_level" ) return; 
					
					dept_cf_count = dept_cf_count + 1;
				});
			}
		});

		if(dept_cf_count == 0 && dept_count == 1){
			return;
		} 

		// alert(ShareInfoLength = custom_field_json.shareInfo.length);
		// shareInfoLen = Object.keys(custom_field_json.shareInfo[0]).length;

		form = "<div id='posform'>";
		$.each(custom_field_json,function(dept_id,detail){
			// if item has fixed production phase and pre selected is false then return true;
			if(is_production_phases_fixed && (!detail['pre_selected'] || detail['department_name'] == "No Department")) return true; // actually continue

			form +=
				'<div data-deptid="'+dept_id+'" class="accordion panel-group col-md-4 col-sm-4 col-lg-4 pos-department-customfield-panel">'+
				'<div class="panel panel-primary">'+
				
				//panel heading with checkbox
				'<div class="panel-heading" style="padding:5px 0px 5px 5px;" >'+
	 				'<h4 class="panel-title">'+

		  	  			'<input data-deptname="'+detail['department_name']+'" data-deptid="'+dept_id+'" class="pos-department-checkbox" value="'+detail['pre_selected']+'" '+(detail['pre_selected']?'checked=""':" ")+'  type="checkbox" '+(is_production_phases_fixed?'disabled':"")+' >&nbsp;'+
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

						if(custom_field_json[dept_id][cf_id]['value'][selected_value] == undefined)
							custom_field_json[dept_id][cf_id]['custom_field_value_name'] = selected_value;
						else
							custom_field_json[dept_id][cf_id]['custom_field_value_name'] = custom_field_json[dept_id][cf_id]['value'][selected_value];
							
					});
				});

				// console.log("extra info");
				// console.log(selected_dept_cf);
				// console.log(JSON.stringify(selected_dept_cf));

				var cf_fields = JSON.stringify(selected_dept_cf);
				$tr.find('.item-read-only-custom-field').val(JSON.stringify(custom_field_json));
				$tr.find('.item-custom-field').val(cf_fields);
				
				// get price of item based on item custom fields
				$.ajax({
		    		url: self.item_amount_ajax_url,
		    		data:{
		    			'item_id':$tr.find('.item-id-field').val(),
		    			'custom_field':cf_fields,
		    			'qty':$tr.find('.qty-field').val()
		    		},
		          	success: function( data ) {
		            	var price_list = JSON.parse(data);
		            	$tr.find('.price-field').val(price_list.sale_price);
		            	self.updateAmount($tr.find('.price-field'));
		            	dialog.dialog( "close" );
		          	}
				});

				self.addExtraInfo($tr);
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

	displayError: function($obj,$msg = null){
		$obj.addClass('pos-field-error');
		// $obj.parent().addClass('pos-field-error');
		// $obj.parent().find('.error-message').remove();
		$obj.find('.error-message').remove();
		if($msg == null)
			$msg = "* please select mandatory field";
		$('<div class="error-message">'+$msg+'</div>').appendTo($obj);
		// $('<div class="error-message">'+$msg+'</div>').insertAfter($obj);
		$($obj).find('input').focus();
		$($obj).find('select').focus();
		$($obj).find('textarea').focus();
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
					var value = cf_details['custom_field_value_id'];
					if(value == undefined && cf_details['custom_field_value_name'] != undefined)
						value = cf_details['custom_field_value_name'];

					html += '<div class="form-group pos-form-group" data-cfid="'+cf_id+'">'+
							'<label>'+cf_details['custom_field_name']+'</label>'+
						'<input value="'+value+'" type="text" data-cfname="'+cf_details['custom_field_name']+'" class="pos-form-field">'+
					'</div>';
				break;

				case "Date":
					var value = cf_details['custom_field_value_id'];
					if(value == undefined && cf_details['custom_field_value_name'] != undefined)
						value = cf_details['custom_field_value_name'];

					html += '<div class="form-group pos-form-group" data-cfid="'+cf_id+'">'+
							'<label>'+cf_details['custom_field_name']+'</label>'+
						'<input value="'+value+'" type="text" data-cfname="'+cf_details['custom_field_name']+'" class="pos-form-field custom-field-dpicker">'+
					'</div>';
				break;
				case "DateAndTime":
					var value = cf_details['custom_field_value_id'];
					if(value == undefined && cf_details['custom_field_value_name'] != undefined)
						value = cf_details['custom_field_value_name'];

					html += '<div class="form-group pos-form-group" data-cfid="'+cf_id+'">'+
							'<label>'+cf_details['custom_field_name']+'</label>'+
						'<input value="'+value+'" type="text" data-cfname="'+cf_details['custom_field_name']+'" class="pos-form-field custom-field-dtpicker">'+
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
		qsp_data['document_other_info'] = {};

		// check validation for master field
		var all_clear = true;
		var field = $(self.element).find('.pos-master-mandatory');

		$.each($(self.element).find('.pos-master-mandatory'), function(index, field) {
			selected_value = $(field).val();
			$msg = null;
			// due date must be greater then created or qual to created date
			if($(field).hasClass('qsp_due_date')){
				// c_d = $('.qsp_created_date').appendDtpicker("getDate");
				// d_d = $('.qsp_due_date').appendDtpicker("getDate");
				c_d = $('.qsp_created_date').val();
				d_d = $('.qsp_due_date').val();
				
				if( d_d == null ||c_d > d_d){
					selected_value = null;
					$msg = "Due date must be greater then created date";
				}
			}

			// console.log("Value = "+$(field).attr('class')+" = "+selected_value);
			if( selected_value == "" || selected_value == null || selected_value == undefined || selected_value == 0){
				$field_row = $(field).closest('div');
				self.displayError($field_row,$msg);

				if(all_clear) all_clear = false;
				return false;
			}
		});

		if(!all_clear){
			return;
		}

		// master data
		var qsp_number = $('.qsp_number').val();
		var qsp_serial = $('.qsp_number_serial').val();

		var qsp_created_date = $('.qsp_created_date').val();
		// var qsp_created_date = $('.qsp_created_date').appendDtpicker("getDate");
		// qsp_created_date = $.datepicker.formatDate("yy-mm-dd H:i:s", qsp_created_date);
		// alert(qsp_created_date);

		var qsp_due_date = $('.qsp_due_date').val();
		// var qsp_due_date = $('.qsp_due_date').datepicker("getDate");
		// qsp_due_date = $.datepicker.formatDate("yy-mm-dd", qsp_due_date);

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
		qsp_data['master'].serial = qsp_serial;
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
		qsp_data['master'].exchange_rate = $('.pos-exchange-rate').val();

		qsp_data['master'].gross_amount = self.options.gross_amount;
		qsp_data['master'].discount_amount = self.options.qsp.discount_amount;
		qsp_data['master'].round_amount = self.options.round_amount;
		qsp_data['master'].net_amount = self.options.net_amount;
		qsp_data['master'].document_id = self.options.document_id;
		qsp_data['master'].branch_id = self.options.qsp.branch_id;

		// detail rows
		$(self.element).find('.col-data').each(function(index,row){
			if($(row).find('.item-id-field').val() <= 0) return;

			var temp = {};
			$(row).find('.pos-qsp-field').each(function(field,field_object){
				temp[$(field_object).attr('data-field').replace("item-",'')] = $(field_object).val();
			});
			qsp_data['detail'][index] = temp;
		});

		// document Other info
		$(self.element).find('.doc-other-info-field').each(function(index,field){
			var o_field_name = $(this).data('field');
			var o_field_value = $(this).val();
			qsp_data['document_other_info'][o_field_name] = o_field_value;
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
				$('.qsp_number').val(ret.master_data.document_no);
				self.options.document_id = ret.master_data.id;
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
	// console.log(item);
	return $("<li></li>")
		.data("item.autocomplete", item)
		// this is autocomplete list that is generated
		.append("<a class='item-autocomplete-list'> " + item.name +
			"</a>")
		.appendTo(ul)
		;
};