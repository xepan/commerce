
jQuery.widget("ui.xepan_pos",{
	selectorAutoComplete: ".item-field",
	selectorUpdateAmount: ".qty-field, .price-field, .discount-field, .tax-field",
	selectorExtraInfoBtn:'.item-extrainfo-btn',
	item_ajax_url:'index.php?page=xepan_commerce_pos_item',
	item_detail_ajax_url:'index.php?page=xepan_commerce_pos_itemcustomfield',

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
			gross_amount: 0,
			discount_amount: 0,
			tax_amount: 0,
			net_amount: 0
		},
		taxation:[],
		apply_tax_on_discounted_amount:1
	},

	_create : function(){
		this.loadQSP();
		this.addRow();		
		this.setUpEvents();
	},

	loadQSP: function(){
		var self = this;
		saved_qsp = JSON.parse(self.options.qsp);

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
		
		var taxation = JSON.parse(self.options.taxation);
		var tax_option = '<option value="0">Please Select</option>';
		$.each(taxation,function(tax_id,tax_detail){
			tax_option += '<option value="'+tax_id+'">'+tax_detail.name+'</option>';
		});

		var rowTemp = [
		'<tr data-sno="1" class="col-data">',
        	'<td class="col-sno">'+next_sno+'</td>',
        	'<td class="col-item">',

          		'<div class="input-group">',
          			'<input  data-field="item-item" placeholder="Item/ Particular" class="item-field"/>',
					'<span data-field="item-extra-nfo-btn" class="item-extrainfo-btn input-group-addon"><i class="fa fa-navicon"></i></span>',
				'</div>',
        		'<input  type="hidden" data-field="item-item_id" placeholder="Item id" class="item-id-field" />',
      			'<input type="hidden" data-field="item-extra_info" placeholder="Item custom field" class="item-custom-field"/>',
      			'<input type="hidden" data-field="item-read_only_custom_field_values" placeholder="Item read only custom field" class="item-read-only-custom-field"/>',
          		'<input data-field="item-narration" placeholder="Narration" class="narration-field"/>',
        	'</td>',
        	'<td class="col-qty">',
          		'<input data-field="item-quantity" placeholder="Quantity" class="qty-field" value="1"/>',
        	'</td>',
        	'<td class="col-price">',
          		'<input data-field="item-price" placeholder="Unit Price" class="price-field"/>',
        	'</td>',
        	'<td class="col-tax">',
      			'<select class="form-control tax-field">',
      			" "+tax_option,
      			'</select>',
        	'</td>',
        	'<td class="col-discount">',
        		'<input data-field="item-discount" placeholder="Discount %/ amount" class="discount-field" value="0"/>',
        	'</td>',
        	'<td class="col-amount">',
          		'<input data-field="item-total_amount" placeholder="Amount" class="amount-field" readOnly/>',
        	'</td>',
        	'<td class="col-remove"><span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x"></i></span>',
        	'</td>',
      	'</tr>'
		].join("");

		if(self.options.show_custom_fields){
			// hide custom fiedl text area or some other fields
		}

		var new_row = $(rowTemp).appendTo($.find('table.addeditem'));
		
		$.each(qsp_item,function(field_name,value){
			// $('[data-field=item-'+field_name+']').css('border','2px solid red');
			if($.type(value) == 'array' || $.type(value) == 'object')
				value = JSON.stringify(value);

			$(new_row).find('[data-field=item-'+field_name+']').val(value);
		});

	},

	fetchItem: function(){

	},

	deleteRow: function(){


	},

	addLiveEvents: function(){
		var self = this;

		// MAKE ITEM FIELD AUTO COMLETE
		$(self.selectorAutoComplete).livequery(function(){ 
		    // use the helper function hover to bind a mouseover and mouseout event 
		    $(this).autocomplete({
				source:self.item_ajax_url, 
				function( request, response ) {
			    	$.ajax( {
			    		url: self.item_ajax_url,
			    		dataType: "jsonp",
			    		data: {
			    			term: request.term
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

					// on selct get custom field of item
					$.ajax({
						url:self.item_detail_ajax_url,
						data:{
							item_id:ui.item.id
						},
						success: function( data ) {
							// console.log(data);
							item_data = JSON.parse(data);
							// console.log(item_data);
							$tr.find('.item-read-only-custom-field').val(JSON.stringify(item_data.cf));
							$tr.find('.col-tax select').val(item_data.tax_id);
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
			if($(this).hasClass('tax-field')){
				$(this).change(function(){
					self.updateAmount($(this));
				});
			}else{
				$(this).keyup(function(){
					self.updateAmount($(this));
					self.updateTotalAmount();
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
			});
		});
	},

	updateAmount: function($tr_field_obj){
		console.log("updateAmount called");
		console.log($tr_field_obj);
		
		parent = $tr_field_obj.closest('tr');
		price_field = $(parent).find('.price-field');
		qty_field = $(parent).find('.qty-field');
		tax_field = $(parent).find('.tax_field');
		discount_field = $(parent).find('.discount-field');

		price = price_field.val();
		qty = qty_field.val();
		discount_val = discount_field.val();
		tax_id = tax_field.val();

		amount_excluding_tax = (price * qty)?(price * qty):0;
		
		// discount_amount = ;
		tax_percentage = 0;
		if(tax_id){
			tax_percentage = self.options.taxation['tax_id'].percentage;
			alert(tax_percentage);
		}

		amount = 0;
		$(parent).find('.amount-field').val(amount);

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

		  	  			'<input data-deptid="'+dept_id+'" class="pos-department-checkbox" value="'+detail['pre_selected']+'" '+(detail['pre_selected']?'checked=""':" ")+'  type="checkbox">&nbsp;'+
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

				var selected_dept_cf = [];
				// check validation
				// for each of panel/accordian
				var all_clear = true;
				$('#posform').find('.pos-department-customfield-panel').each(function(index){
					//check department checkbox is checked
					var dept_checkbox = $(this).find('input.pos-department-checkbox');
					if(!$(dept_checkbox).is(':checked')) return;

					var selected_dept_id = $(dept_checkbox).attr('data-deptid');
					selected_dept_cf[selected_dept_id] = [];

					//for each of CF input is not selected
						//so error
					$(this).find('.pos-form-group').each(function(index){
						field = $(this).find('.pos-form-field');
						selected_value = $(field).val();
						if( selected_value == "" || selected_value == null || selected_value == undefined){
							$(this).addClass('pos-field-error');
							$(this).find('.error-message').remove();
							$('<div class="error-message">please select mandatory field</div>').appendTo($(this));
							
							if(all_clear) all_clear = false;
							return false;
						}

						selected_dept_cf[selected_dept_id][$(this).attr('data-cfid')] = selected_value;
					});
					//check if same production lavel department checkbox is checked
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

				$tr.find('.item-read-only-custom-field').val(JSON.stringify(custom_field_json));
				dialog.dialog( "close" );
				},
				Cancel: function() {
				  dialog.dialog( "close" );
				}
			},
			close: function() {
				$(this).remove();
			}
		});
	},

	getFormFields: function(dept_cf_detail){

		html = "";

		$.each(dept_cf_detail,function(cf_id,cf_details){
			if(cf_id  === "department_name" || cf_id === "pre_selected" || cf_id === "production_level" ) return; 

			switch(cf_details['display_type']){
				case "DropDown":
					html = '<div class="form-group pos-form-group" data-cfid="'+cf_id+'">'+
								'<label>'+cf_details['custom_field_name']+'</label>';
					html += '<select class="form-control pos-form-field">';
					
					html += '<option value="">Please Select</option>';
					
					$.each(cf_details['value'],function(value_id,value_name){
						selected = "";
						if(cf_details['custom_field_value_id'] == value_id)
							selected = "selected";
						// alert(cf_details['custom_field_value_id']+" = "+value_id);
						html += '<option '+selected+' value="'+value_id+'">'+value_name+'</option>';
					});

					html += '</select>';
					html += '</div>';
				break;

				case "Line":
					html += '<div class="form-group" data-cfid="'+cf_id+'">'+
							'<label>'+cf_details['custom_field_name']+'</label>'+
						'<input type="text" class="pos-form-field">'+
					'</div>';
				break;
			}
		});

		return html;
	},

	updateTotalAmount: function(){
		var self = this;

		//for each of amount field
		self.options.gross_amount = 0;
		// $(this.element).find('input.amount-field').css('border','2px solid red');
		
		$(this.element).find('input.amount-field').each(function(index){
			amount = parseFloat($(this).val());
			if(amount > 0)
		    	self.options.gross_amount += amount;
		});

		net_amount = self.options.gross_amount - (self.options.discount_amount)?self.options.discount_amount:0;
		$(this.element).find('.pos-gross-amount').html(self.options.gross_amount);
		$(this.element).find('.pos-net-amount').html(self.options.gross_amount);
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

	savePOS: function(){

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