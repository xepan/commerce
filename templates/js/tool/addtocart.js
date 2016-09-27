jQuery.widget("ui.xepan_xshop_addtocart",{

	options:{
		selected_custom_field_values: {},
		fields_and_their_types:{},
		item_id: undefined,
		item_member_design_id: '0',
		is_designable: false,
		is_uploadable: false,
		file_upload_id: undefined,

		show_price: false,
		show_qty: false,
		qty_from_set_only: false,
		qty_set: {},
		
		show_custom_fields: false,
		custom_fields:{},
		base_url:undefined
	},

	_create: function(){
		return;
		var self = this;
		// console.log(self.options);

		if(this.options.show_custom_fields){
			this.populateCustomFields();
		}

		if(this.options.show_qty=='1'){
			this.populateQtyFields();
		}

		if(this.options.show_cart_btn == '1')
			this.populateAddToCartButton();
		// console.log(self.options);
	},

	populateCustomFields: function(){
		var self = this;

		$.each(self.options.custom_fields, function(custom_field, custom_field_details){
			self.options.fields_and_their_types[custom_field] = custom_field_details.type;
			switch(custom_field_details.type){
				case 'Color':
					color_box = $('<div class="xshop-item-custom-field xshop-item-custom-color-box '+custom_field+'"><div class="xshop-item-custom-field-name">'+custom_field+'</div></div>').appendTo(self.element);
					$.each(custom_field_details.values, function (color_code, filter_info_object){
					box = $('<div class="xshop-item-custom-field-value '+ color_code.replace('#','') +'"></div>').appendTo(color_box);
					box.css('width','20px');
					box.css('height','20px');
					box.css('float','left');
					box.css('background-color',color_code);
					box.click(function(event){
						if($(this).hasClass('disabled')){
							alert('Oops');
							return;
						}
						$(this).parent().find('.xshop-item-custom-field-value').removeClass('selected');
						$(this).addClass('selected');
						self.custom_field_clicked(custom_field,color_code);
					});
				});
				break;
				case 'DropDown':
					title =$('<div class="xshop-item-custom-field xshop-item-custom-dropdown-box"><div class="xshop-item-custom-field-name">'+custom_field+'</div></div>').appendTo(self.element);
					select = $('<select class="xshop-item-custom-field-select '+custom_field+'"></select>').appendTo(title);
					opt = $('<option value="xshop-undefined" class="xshop-item-custom-field-value">Select</options>').appendTo(select);
					$.each(custom_field_details.values, function (custom_field_value, filter_info_object){
						opt = $('<option value="'+custom_field_value+'" class="xshop-item-custom-field-value '+custom_field_value+'">'+custom_field_value+'</options>').appendTo(select);
					});
					select.selectmenu({
						change: function(event,data){
							self.custom_field_clicked(custom_field,data.item.value);
						}
					});
				break;
			}


		});


	},

	custom_field_clicked : function(custom_field, value_selected){
		var self = this;

		// console.log(custom_field + ' :: ' + value_selected);
		// return;
		// 1. set as current selected value in widget level scope
		self.options.selected_custom_field_values[custom_field] = value_selected;
		
		// 2. look for any filter to activate
			//enable all fields first
			$(self.element).find('.xshop-item-custom-field-value').attr('disabled',false).removeClass('disabled');
			$(self.element).find('option.xshop-item-custom-field-value').parent().selectmenu('refresh');
			// filter out all selected customfields values
			$.each(self.options.custom_fields, function(custom_field, custom_field_details){
				if(self.options.selected_custom_field_values[custom_field] !=undefined && self.options.selected_custom_field_values[custom_field] !='xshop-undefined'){
					if(custom_field_details['values'][self.options.selected_custom_field_values[custom_field]]['filter_count'] != '0'){
						filters =  custom_field_details['values'][self.options.selected_custom_field_values[custom_field]]['filters'];
						$.each(filters, function(index,a_filter){
							$.each(a_filter, function(filed_to_filter,value_to_filter){
								switch(self.options.fields_and_their_types[filed_to_filter]){
									case 'Color':
										// console.log('.'+filed_to_filter+' .'+ value_to_filter.replace('#',''));
										$(self.element).find('.'+filed_to_filter+' .'+ value_to_filter.replace('#','')).addClass('disabled');
									break;
									case 'DropDown':
										if(self.options.selected_custom_field_values[filed_to_filter]==value_to_filter){
											$("."+filed_to_filter+" option[value='"+value_to_filter+"']").parent().val('xshop-undefined');
										}
										$("."+filed_to_filter+" option[value='"+value_to_filter+"']").attr('disabled', true).parent().selectmenu('refresh');
										// $(self.element).find('.'+filed_to_filter).attr('disabled',true).addClass('disabled');

									break;
								}
								if(self.options.selected_custom_field_values[filed_to_filter] == value_to_filter)
									self.options.selected_custom_field_values[filed_to_filter] = undefined;
								// console.log('filterting '+ filed_to_filter + ' = ' + value_to_filter);
							});
						});
					}
				}
			});
	
		// 3. rate changer function
		self.getRate();

		// alert('TODOs here');
	},

	populateQtyFields: function(){
		var self=this;
		// if qty_from_set_only is true
		if(self.options.qty_from_set_only !='0'){
			// add dropdown and add options from qty_sets

			qty_field_container = $('<div class="xshop-item-custom-field xshop-item-custom-dropdown-box">').appendTo(self.element);
			qty_field_name = $('<div class="xshop-item-custom-field-name">Qty</div>').appendTo(qty_field_container);
			qty_field = $('<select class="xshop-add-to-cart-qty"></select>').appendTo(qty_field_container);
			$.each(self.options.qty_set,function(index,qty){
				var display_name=qty.qty;
				if(qty.name != qty.qty) display_name = qty.name + ' :: '+ qty.qty;

				$('<option value="'+qty.qty+'">'+display_name+'</option>').appendTo(qty_field);
			});
			qty_field.selectmenu({
				change: function(event,ui){
					self.getRate();
				}
			});
		}else{
			// add input box with spinner may be ...
			title = $('<div class="xshop-item-custom-field xshop-item-custom-qty-box"><div class="xshop-item-custom-field-name">Qty</div></div>').appendTo(self.element);
			qty_field = $('<input id="xshop-add-to-cart-qty" class="xshop-add-to-cart-qty" value="1" type="number" placeHolder="Quantity"/>').appendTo(title);
			qty_field.univ().numericField();
		}
		// add unique class under the self.element to read qty

		$(qty_field).bind('change',function(){
			self.getRate();
		});
		// $(qty_field).bind('blur',function(){
		// 	self.getRate();
		// });
	},

	getRate: function(){
		var self=this;
		var all_custom_fields_selected = true;

		if(!self.options.show_price){
			return
		}

		// check for all custom field value selected or not
		$.each(self.options.custom_fields, function(custom_field, custom_field_details){
			if(self.options.selected_custom_field_values[custom_field] == undefined || self.options.selected_custom_field_values[custom_field] == 'xshop-undefined') all_custom_fields_selected = false;
		});

		// if(!all_custom_fields_selected) return; // ??????????????

		var qty_to_add = 1;
		// if show_qty is on ?????????????
		if(self.options.show_qty == '1'){
			qty_to_add = $(self.element).find('.xshop-add-to-cart-qty').val();
			// set qty_to_add = val of qty field value
		}

		// console.log("Qty set");
		// console.log('id'+self.options.item_id);
		// console.log('Qty to add '+qty_to_add);
		// console.log('custom '+JSON.stringify(self.options.selected_custom_field_values));

		$.ajax({
			url: self.options.base_url+'?page=xepan_commerce_getrate',
			type: 'GET',
			datatype: "json",
			data: { 
				item_id: self.options.item_id,
				qty: qty_to_add,
				custome_fields: JSON.stringify(self.options.selected_custom_field_values)
			},
		})
		.done(function(ret) {
			rates = ret.split(',');
			// console.log($(self.element).closest('.xshop-item').find('.xshop-item-old-price'));
			if(rates[0] != rates[1]){
				$(self.element).closest('.xshop-item').find('.xshop-item-old-price').html(rates[0]);
			}else{
				$(self.element).closest('.xshop-item').find('.xshop-item-old-price').html('');
			}
			$(self.element).closest('.xshop-item').find('.xshop-item-price').html(rates[1]);
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	},

	populateAddToCartButton: function(){
		var self= this;

		add_to_cart_btn = $('<button class="xshop-item-add-to-cart-btn btn btn-default">Add To Cart</button>').appendTo(self.element);
		$(add_to_cart_btn).click(function(event){
			var all_custom_fields_selected = true;
			var missed_custom_fields = [];
			$.each(self.options.custom_fields, function(custom_field, custom_field_details){
				if(self.options.selected_custom_field_values[custom_field] == undefined || self.options.selected_custom_field_values[custom_field] == 'xshop-undefined'){
					all_custom_fields_selected = false;
					missed_custom_fields.push(custom_field);
				}
			});

			if(!all_custom_fields_selected){
				alert(missed_custom_fields.join(',') + ' Must be selected');
				return;
			}

			var qty_to_add = 1;
			// if show_qty is on ?????????????
			if(self.options.show_qty == '1'){
				qty_to_add = $(self.element).find('.xshop-add-to-cart-qty').val();
				// set qty_to_add = val of qty field value
				if(isNaN(qty_to_add) || qty_to_add == '') {
					alert('Qty is not proper');
					return;
				}
			}


			// if I am designable and there is no item_member_design_id
			if(!self.options.is_uploadable && self.options.is_designable =='1' && (self.options.item_member_design_id == undefined || self.options.item_member_design_id) == '0'){
				// This design needs to be saved first
				$.univ().errorMessage('check for design dirty !!!');
				$.univ().alert('Please Save your Design First');
				return;
			}

			$.ajax({
				url: self.options.base_url+'?page=xepan_commerce_addtocart',
				type: 'POST',
				datatype: "json",
				data: { 
					item_id: self.options.item_id,
					qty: qty_to_add,
					custome_fields: JSON.stringify(self.options.selected_custom_field_values),
					item_member_design_id: self.options.item_member_design_id,
					file_upload_id: self.options.file_upload_id
				},
			})
			.done(function(ret) {
				$.univ().successMessage('Item Added To Cart');
				$('.xshop-cart').trigger('reload');
				// console.log('CartPage');
				// console.log(JSON.stringify(self.options.selected_custom_field_values));
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});

		});
	}
	
});