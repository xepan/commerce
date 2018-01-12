Save_Component = function (params){
	var self = this;
	this.parent=undefined;
	this.options= {
		base_url:undefined
	},
	// this.text = params.text != undefined?params.text:'Enter Text';
	this.init = function(designer,canvas){
		this.designer_tool = designer;
		this.canvas = canvas;
		self.options.base_url = designer.options.base_url;
	}

	this.initExisting = function(params){

	}

	this.getUrlParameter = function(params) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === params) {
	            return sParameterName[1] === undefined ? true : sParameterName[1];
	        }
    	}
	}

	this.renderTool = function(parent){
		var self = this;
		this.page = undefined;
		this.layout = undefined;
		this.parent = parent;
		tool_btn = $('<div class="btn xshop-render-tool-save-btn pull-right" style="border:0px solid red !important;"><i class="glyphicon glyphicon-floppy-saved"></i><br>Save</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));

		self.designer_tool.all_page_and_layout = self.designer_tool.pages_and_layouts;
		tool_btn.click(function(event){

			// console.log(self.designer_tool.layout_finalized);

			self.layout_array = {};
			image_array = {};
			var generate_image = false;
			var current_working_page = self.designer_tool.current_page;
			var current_working_layout = self.designer_tool.current_layout;
			var current_designer_tool = self.designer_tool;

			if(self.designer_tool.options.designer_mode){
				dialog_image = $('<div class="xepan-designer-canvas-image-dialog"></div>').appendTo(self.parent);
				$(dialog_image).find('.xepan-designer-canvas-image-dialog .image-canvas .canvas_widget').remove();

				dialog_image.dialog({
					autoOpen: true, 
					modal: true, 
					width:800,
					height:500,
					// beforeClose: function(e,ui){
						// if(!confirm('This will reload page, un saved designed will be lost, Are you sure?')){
						// 	return false;
						// }
						// $.univ().location(window.location.href);
						// return false;
					// }
					// open:funciton(){

					// }
				},'saved image preview');
				generate_image = $('<div class="btn btn-primary btn-block disabled">Wait... Generating Images</div>').appendTo(dialog_image);
			}
			

			var temp_page_and_layout = self.designer_tool.pages_and_layouts;
			
			var layouts_count = 0;
			var canvas_drawn = 0;
			var ajax_saved_run = 0;

			$.each(temp_page_and_layout,function(page_name,layouts){

				self.layout_array[page_name]= new Object;
				image_array[page_name] = new Object();
				$.each(layouts,function(layout_name,layout){
					if(layout_name == "sequence_no"){
						// console.log("saved insde"+layout_name);
						return;
					}

					layouts_count++;

					self.layout_array[page_name][layout_name]=new Object;
					self.layout_array[page_name][layout_name]['components']=[];
					image_array[page_name][layout_name] = new Object();

					// var array = [{id:'12', name:'Smith', value:1},{id:'13', name:'Jones', value:2}];
					// var array = layout.components;
					layout.components.sort(function(a, b){

						// console.log("sort");
						// console.log(a.options.zindex);
						// console.log(a.options.url);
						// console.log("b");
						// console.log(b.options.zindex);
						// console.log(b.options.url);
						
					    var a1= a.options.zindex, b1= b.options.zindex;
					    if(a1 == b1) return 0;
					    return a1> b1? 1: -1;
					});

					// console.log(array);
					self.zindex_count = 0;
					$.each(layout.components,function(index,component){
						//Setup Image Path Relative
						if(component.options.type == "Image"){
							url = component.options.url;
							component.options.url = url.substr(url.indexOf("websites/"));
						}
						// console.log(self.zindex_count);
						// console.log(component.options.url);
						options_to_save = component.options;
						options_to_save.zindex = self.zindex_count;
						self.zindex_count += 1;

						self.layout_array[page_name][layout_name]['components'].push(JSON.stringify(options_to_save));
					});

					background_options = layouts[layout_name]['background'].options;
					// background_options = self.designer_tool.pages_and_layouts[page_name][layout_name]['background'].options;
					//Setup Image Path Relative
					if(background_options.url){
						background_options.url = background_options.url.substr(background_options.url.indexOf("websites/"));
						// console.log(background_options.url);
					}				
					self.layout_array[page_name][layout_name]['background'] = JSON.stringify(background_options);
					self.layout_array[page_name]['sequence_no'] = layouts['sequence_no'];

					if(self.designer_tool.options.designer_mode){
						canvas_wrapper = $('<div class="image-canvas" style="width:700px;position:relative;" data-pagename="'+page_name+'" data-layoutname="'+layout_name+'">').appendTo(dialog_image).css('float','left');
						layout_canvas = $('<div style="width:700px;position:relative;padding:10px;" class="canvas_widget">').appendTo(canvas_wrapper);
						
						// self.designer_tool.options.design = JSON.stringify(self.layout_array);
						// self.designer_tool.loadDesign();
						
						layout_canvas.xepan_xshopdesigner({
									'width':self.designer_tool.options.width,
									'height':self.designer_tool.options.height,
									'trim':0,
									'unit':self.designer_tool.options.unit,
									'designer_mode': false,
									'design':JSON.stringify(self.layout_array),
									'show_cart':'0',
									'start_page': page_name,
									'start_layout':layout_name,
									'printing_mode':false,
									'show_canvas':true,
									'generating_image':true,
									'show_tool_bar':false,
									'show_pagelayout_bar':false,
									'mode':"Primary",
									'show_safe_zone':false,
									'calendar_starting_month':self.designer_tool.options.calendar_starting_month,
									'calendar_starting_year':self.designer_tool.options.calendar_starting_year,
									'calendar_event':JSON.stringify(self.designer_tool.options.calendar_event),
									'canvas_render_callback': function(){
										canvas_drawn++;
										if(canvas_drawn>=layouts_count){
											$(generate_image).removeClass('disabled').html('Save Design/Images And Reload Page');
										}
									}
								});
					}

				});
			});

			$(generate_image).click(function(){
				// console.log('click called');
				if($(this).hasClass('disabled')){
					$.univ().errorMessage('Please wait till all canvas dwarn');
					return;
				}

				$(this).html('Please Wait... Saving your Images and Design.');

				all_save = true;
				delete_all_previous_image = "Yes";

				// console.log($('.xepan-designer-canvas-image-dialog .image-canvas .canvas_widget'));
				// return;
				$('.xepan-designer-canvas-image-dialog .image-canvas .canvas_widget').each(function(index,canvas){
					page_name = $(canvas).closest('.image-canvas').data('pagename');
					layout_name = $(canvas).closest('.image-canvas').data('layoutname');

					canvasObj = $(canvas).xepan_xshopdesigner('getCanvasObj');

					if( parseInt(canvasObj.width) > 900)
						var multiplier_factor = 1;
					else{
						// var multiplier_factor = 3;
						var multiplier_factor = Math.ceil(900 /canvasObj.width);
					}

					img_data = canvasObj.toDataURL({
												    multiplier: multiplier_factor
												});
					$(canvas).remove();
					// image_array[page_name][layout_name] = img_data;
					var single_image = {};
					single_image[page_name] = new Object();
					single_image[page_name][layout_name] = img_data;
					$(canvas).closest('.xshop-desiner-tool-canvas').hide();
					// $('<img>').attr('src',img_data).appendTo($(canvas).closest('.xshop-designer-tool-workplace'));
					// $('<div><i class="glyphicon glyphicon-ok" style="color:green;">&nbsp;</i>'+page_name+' : '+layout_name+'</div>').appendTo($(canvas).closest('.xshop-designer-tool-workplace')).css('position');
					// console.log("Rakesh");
					// console.log(checksum_str.length);
					image_base_64_str = JSON.stringify(single_image);

					$.ajax({
					url: 'index.php?page=xepan_commerce_designer_save',
					cache:false,
					async:false,
					type: 'POST',
					datatype: "json",
					data: { xshop_item_design:JSON.stringify(self.layout_array),//json object
							item_id:self.designer_tool.options.item_id,//designed item id
							designer_mode:self.designer_tool.options.designer_mode,
							item_member_design_id:self.designer_tool.options.item_member_design_id,
							px_width : self.designer_tool.px_width,
							selected_layouts_for_print : JSON.stringify(self.designer_tool.layout_finalized),
							calendar_starting_month:self.designer_tool.options.calendar_starting_month,
							calendar_starting_year:self.designer_tool.options.calendar_starting_year,
							calendar_event:JSON.stringify(self.designer_tool.options.calendar_event),
							image_array:image_base_64_str,
							checksum:image_base_64_str.length,
							delete_all_image:delete_all_previous_image,
							mode:self.designer_tool.options.mode,
							ComponentsIncludedToBeShow:self.designer_tool.options.ComponentsIncludedToBeShow,
							BackgroundImage_tool_label:self.designer_tool.options.BackgroundImage_tool_label,
							
							show_pagelayout_bar:self.designer_tool.options.show_pagelayout_bar,
							show_canvas:self.designer_tool.options.show_canvas,
							show_layout_bar:self.designer_tool.options.show_layout_bar,
							show_paginator:self.designer_tool.options.show_paginator,
							show_tool_calendar_starting_month:self.designer_tool.options.show_tool_calendar_starting_month
						},
					}).done(function(ret){
						if($.isNumeric(ret)){

							ajax_saved_run++;
							if(ajax_saved_run >= layouts_count){
								$.univ().successMessage('Design saved and Image generated');
								dialog_image.dialog('close');
								dialog_image.remove();
							}
						}else{
							all_save = false;
							$.univ().errorMessage('not saved, try again '+ret);
						}
					});

					delete_all_previous_image = "No";
				});

				// if(all_save){
				// 	$(dialog_image).prepend('<div class="btn btn-block btn-success">Design Saved</div>');
				// 	$(generate_image).hide();
				// }
				// .always(function(ret){
				// 	// $(dialog_image).dialog('close');
				// });
			});

			// console.log("save inside");
			// console.log(self.layout_array);
			if(!self.designer_tool.options.designer_mode){
				// console.log("save  ");
				// console.log(self.designer_tool.layout_finalized);
			$.ajax({
					url: 'index.php?page=xepan_commerce_designer_save',
					type: 'POST',
					datatype: "json",
					data: { xshop_item_design:JSON.stringify(self.layout_array),//json object
							item_id:self.designer_tool.options.item_id,//designed item id
							designer_mode:self.designer_tool.options.designer_mode,
							item_member_design_id:self.designer_tool.options.item_member_design_id,
							px_width : self.designer_tool.px_width,
							selected_layouts_for_print : JSON.stringify(self.designer_tool.layout_finalized),
							calendar_starting_month:self.designer_tool.options.calendar_starting_month,
							calendar_starting_year:self.designer_tool.options.calendar_starting_year,
							calendar_event:JSON.stringify(self.designer_tool.options.calendar_event),
							mode:self.designer_tool.options.mode,
							ComponentsIncludedToBeShow:self.designer_tool.options.ComponentsIncludedToBeShow,
							BackgroundImage_tool_label:self.designer_tool.options.BackgroundImage_tool_label,
							
							show_pagelayout_bar:self.designer_tool.options.show_pagelayout_bar,
							show_canvas:self.designer_tool.options.show_canvas,
							show_layout_bar:self.designer_tool.options.show_layout_bar,
							show_paginator:self.designer_tool.options.show_paginator,
							show_tool_calendar_starting_month:self.designer_tool.options.show_tool_calendar_starting_month
						},
				})
				.done(function(ret) {
					if($.isNumeric(ret)){
						design_dirty=false;
						page = self.getUrlParameter('page');

						if(self.getUrlParameter('xsnb_design_template') === "true"){
							$.univ().successMessage('Saved Successfully');
						}else if(!self.getUrlParameter('item_member_design')){
							self.designer_tool.options.item_member_design_id = ret;
							old_url = window.location.href;
							new_url = old_url.split( '&' )[0];
							$.univ().successMessage('loading your saved design');
							$.univ.location(old_url+'&item_member_design='+ret);
						}else{
							// temporary refresing the page
							self.designer_tool.options.item_member_design_id = ret;
							$.univ().successMessage('saved successfully');
							
							self.designer_tool.options.design = JSON.stringify(self.layout_array);
							self.designer_tool.loadDesign();
							$('.xshop-designer-tool-bottombar').remove();
							self.designer_tool.setupPageLayoutBar();
							$('.xshop-designer-tool-bottombar').show();

							//After save display all page
							if(self.designer_tool.options.mode == "multi-page-single-layout"){
								$(self.designer_tool.canvas).hide();
								$(self.designer_tool.canvas).parent('.xshop-designer-tool-workplace').hide();
								$(".xshop-designer-tool-workplace-previous-wrapper").hide();
								$(".xshop-designer-tool-workplace-next-wrapper").hide();
							}else
								self.designer_tool.render();
						}

					}
					else if(ret.indexOf('false')===0){
						// eval(ret);
						// $.univ().errorMessage('Not Saved, some thing wrong');
					}else{
						// eval(ret);
						//todo delete because cart tool is depricated in xepan2
						//cart tool moved to separate tool
						if(!isNaN(+ret)){
							self.designer_tool.options.item_member_design_id = ret;
							// if(self.designer_tool.cart != undefined || self.designer_tool.cart != '0'){
							// 	self.designer_tool.cart.xepan_xshop_addtocart('option','item_member_design_id',ret);
							// 	// console.log(self.designer_tool.cart.options);
							// }
							// // window.history.pushState('page', 'saved_page', 'replace url');
							// $.univ().successMessage('Saved Successfully');
						}else{
							eval(ret);
						}
					}
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});
			}
		});
	}
}