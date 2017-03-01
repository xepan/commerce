
jQuery.widget("ui.xepan_pos",{
	selectorAutoComplete: ".item-field",
	selectorQtyAndPrice: ".qty-field, .price-field",
	item_ajax_url:'index.php?page=xepan_commerce_pos_item',
	item_detail_ajax_url:'index.php?page=xepan_commerce_pos_itemcustomfield',

	options : {
		show_custom_fields: true,
		item_ajax_calling:true,
		qsp:{
			customer: {
				id: null,
				address: "Address Here"
			},
			details: [
				{
					item_id: 1234,
					narration: "narration",
					qty: 123,
					price : 123,
					custom_fields : {
						cf_field_1 : "adas",
						cf_field_2 : "asdasD"
					}
				},
				{
					item_id: 121,
					narration: "narration",
					qty: 123,
					price : 123,
					custom_fields : {
						cf_field_1 : "aa",
						cf_field_2 : "asdas"
					}
				}

			],

			gross_amount: 0,
			discount_amount: 0,
			tax_amount: 0,
			net_amount: 0
		}
	},

	_create : function(){
		this.addRow();		
		this.setUpEvents();
	},

	addRow: function(){
		var self = this;
		next_sno = $.find('table.addeditem tr.col-data').length + 1;
		
		if(self.options.show_custom_fields){
			var rowTemp = [
			'<tr data-sno="1" class="col-data">',
            	'<td class="col-sno">'+next_sno+'</td>',
            	'<td class="col-item">',
            		
              		'<div class="input-group">',
              			'<input data-field="item" placeholder="Item/ Particular" class="item-field"/>',
						'<span data-field="item-extrainfo" class="input-group-addon"><i class="fa fa-navicon"></i></span>',
					'</div>',
            		'<input type="hidden" data-field="item-id" placeholder="Item id" class="item-id-field" />',
          			'<input type="text" data-field="item-custom-field" placeholder="Item custom field" class="item-custom-field"/>',
            	'</td>',
	            '<td class="col-narration">',
              		'<input data-field="narration" placeholder="Narration" class="narration-field"/>',
            	'</td>',
            	'<td class="col-qty">',
              		'<input data-field="quantity" placeholder="Quantity" class="qty-field"/>',
            	'</td>',
            	'<td class="col-price">',
              		'<input data-field="price" placeholder="Unit Price" class="price-field"/>',
            	'</td>',
            	'<td class="col-amount">',
              		'<input data-field="amount" min placeholder="Amount" class="amount-field" readOnly/>',
            	'</td>',
            	'<td class="col-action"><span class="fa-stack col-remove"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x"></i></span>',
            	'</td>',
          	'</tr>'
			].join("");

		}else{
			var rowTemp = [
				'<tr data-sno="1" class="col-data">',
	            	'<td class="col-sno">'+next_sno+'</td>',
	            	'<td class="col-item">',
	              		'<input data-field="item" placeholder="Item/ Particular" class="item-field"/>',
	            	'</td>',
		            '<td class="col-narration">',
	              		'<input data-field="narration" placeholder="Narration" class="narration-field"/>',
	            	'</td>',
	            	'<td class="col-qty">',
	              		'<input data-field="quantity" placeholder="Quantity" class="qty-field"/>',
	            	'</td>',
	            	'<td class="col-price">',
	              		'<input data-field="price" placeholder="Unit Price" class="price-field"/>',
	            	'</td>',
	            	'<td class="col-amount">',
	              		'<input data-field="amount" min placeholder="Amount" class="amount-field" readOnly/>',
	            	'</td>',
	            	'<td class="col-action"><span class="fa-stack col-remove"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x"></i></span>',
	            	'</td>',
	          	'</tr>'
				].join("");
		}

		$(rowTemp).appendTo($.find('table.addeditem'));
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
					

					// on selct get custom field of item
					$.ajax({
						url:self.item_detail_ajax_url,
						data:{
							item_id:ui.item.id
						},
						success: function( data ) {
							$tr.find('.item-custom-field').val(data);
							self.showCustomFieldForm($tr);
			          	},
			          	error: function(XMLHttpRequest, textStatus, errorThrown) {
			              alert("Error getting prospect list: " + textStatus);
			            }
					});
			    }
			});
		    // ,funciton(){
		    	// if field not found then
		    // }
		});

		// // ADD QTY OR PRICE CHANGE EVENT
		$(self.selectorQtyAndPrice).livequery(function(){
			$(this).keyup(function(){
				parent = $(this).closest('tr');
				price_field = $(parent).find('.price-field');
				qty_field = $(parent).find('.qty-field');

				// qty = $(this).val();
				price = price_field.val();
				qty = qty_field.val();
				amount = (price * qty)?(price * qty):0;
				$(parent).find('.amount-field').val(amount);
				
				self.updateTotalAmount();
			});
		});
	},

	showCustomFieldForm: function($tr){
		var self = this;
		custom_field_json = JSON.parse($tr.find('.item-custom-field').val());
		
		form = "<div id='posform'>";
		$.each(custom_field_json,function(dept_id,detail){
			form +=
				'<div class="accordion panel-group col-md-4 col-sm-4 col-lg-4 xepan-field-item-customfield-panel">'+
				'<div class="panel panel-primary">'+
				
				//panel heading with checkbox
				'<div class="panel-heading" style="padding:5px 0px 5px 5px;" >'+
	 				'<h4 class="panel-title">'+

		  	  			'<input value="'+detail['pre_selected']+'" '+(detail['pre_selected']?'checked=""':" ")+'  type="checkbox">&nbsp;'+
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
					// 

				},
				Cancel: function() {
				  dialog.dialog( "close" );
				}
			},
			close: function() {

			}
		});
	},

	getFormFields: function(dept_cf_detail){

		html = "";

		$.each(dept_cf_detail,function(cf_id,cf_details){
			if(cf_id  === "department_name" || cf_id === "pre_selected" ) return; 

			switch(cf_details['display_type']){
				case "DropDown":
					html = '<div class="form-group">'+
								'<label>'+cf_details['custom_field_name']+'</label>';
					html += '<select class="form-control">';
					
					html += '<option value="">Please Select</option>';
					
					$.each(cf_details['value'],function(value_id,value_name){
						selected = "";
						if(cf_details['custom_field_value_id'] == value_id)
							selected = "select";

						html += '<option"'+selected+'" value="'+value_id+'">'+value_name+'</option>';
					});

					html += '</select>';
					html += '</div>';
				break;

				case "Line":
					html += '<div class="form-group">'+
							'<label>'+cf_details['custom_field_name']+'</label>'+
						'<input type="text">'+
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