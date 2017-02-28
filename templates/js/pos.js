jQuery.widget("ui.xepan_pos",{
	selectorAutoComplete: ".item-field",
	selectorPriceQty: ".price-field, .qty-field",

	options : {
		show_custom_fields: true,
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

			gross_amount: 122,
			discount: 12121,
			tax_amount: 1212,
			net_amount: 12323
		}
	},

	_create : function(){
		this.addRow();		
		this.setUpEvents();
	},

	addRow: function(){
		var self = this;
		next_sno = $.find('table.addeditem tr.col-data').length + 1;
		
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
              		'<input data-field="amount" placeholder="Amount" class="amount-field"/>',
            	'</td>',
            	'<td class="col-action"><span class="fa-stack col-remove"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x"></i></span>',
            	'</td>',
          	'</tr>'
			].join("");

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
				source: [ "c++", "java", "php", "coldfusion", "javascript", "asp", "ruby" ]
		    })
		    // ,funciton(){
		    	// if field not found then
		    // }
		});

		// // ADD QTY CHANGE EVENT
		$(self.selectorPriceQty).livequery(function(){
			$(this).change(function(){
				
				var qty = $(this).find('.qty-field').val()?$(this).find('.qty-field').val():0;
				var price = ($(this).find('.price-field').val())?$(this).find('.price-field').val():0;
				if($(this).hasClass('.qty-field')){
					qty = $(this).val();
				}

				if($(this).hasClass('.price-field')){
					price = $(this).val();
				}
			
				alert(price + " = q ="+qty);
				
				$(this).find('.amount-field').val( price * qty );

				// self.updateTotalAmount();
			});

		});
	},

	updateTotalAmount: function(){
		var self = this;
		//for each of amount field
		self.options.gross_amount = 0;
		$('input.amount-field').each(function(index){
		    self.options.gross_amount += $(this).val();
		// 	console.log(self.options.gross_amount);
		});

		$('.pos-gross-amount').html(self.options.gross_amount);
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