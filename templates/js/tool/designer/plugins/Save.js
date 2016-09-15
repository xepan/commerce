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
		tool_btn = $('<div class="btn xshop-render-tool-save-btn pull-right"><i class="glyphicon glyphicon-floppy-saved"></i><br>Save</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));

		tool_btn.click(function(event){
			self.layout_array = {};
			image_array = {};
			var generate_image = false;
			var current_working_page = self.designer_tool.current_page;
			var current_working_layout = self.designer_tool.current_layout;
			var current_designer_tool = self.designer_tool;

			if(self.designer_tool.options.designer_mode){
				dialog_image = $('<div class="xepan-designer-canvas-image-dialog"></div>').appendTo(self.parent);
				dialog_image.dialog({autoOpen: true, modal: true, width:800,height:500},'saved image preview');
				generate_image = $('<div class="btn btn-primary btn-block">generate image and save it</div>').appendTo(dialog_image);
			}

			$.each(self.designer_tool.pages_and_layouts,function(page_name,layouts){
				self.layout_array[page_name]= new Object;
				image_array[page_name] = new Object();
				$.each(layouts,function(layout_name,layout){
					self.layout_array[page_name][layout_name]=new Object;
					self.layout_array[page_name][layout_name]['components']=[];
					image_array[page_name][layout_name] = new Object();
					$.each(layout.components,function(index,component){
						//Setup Image Path Relative
						if(component.options.type == "Image"){
							url = component.options.url;
							component.options.url = url.substr(url.indexOf("websites/"));
						}
						self.layout_array[page_name][layout_name]['components'].push(JSON.stringify(component.options));

					});

					background_options = self.designer_tool.pages_and_layouts[page_name][layout_name]['background'].options;
					//Setup Image Path Relative
					if(background_options.url){
						background_options.url = background_options.url.substr(background_options.url.indexOf("websites/"));
						// console.log(background_options.url);
					}				
					self.layout_array[page_name][layout_name]['background'] = JSON.stringify(background_options);

					if(self.designer_tool.options.designer_mode){
						canvas_wrapper = $('<div class="image-canvas" style="width:700px;position:relative;" data-pagename="'+page_name+'" data-layoutname="'+layout_name+'">').appendTo(dialog_image).css('float','left');
						layout_canvas = $('<div style="width:700px;position:relative;padding:10px;">').appendTo(canvas_wrapper);
						layout_canvas.xepan_xshopdesigner({
									'width':self.designer_tool.options.width,
									'height':self.designer_tool.options.height,
									'trim':0,
									'unit':self.designer_tool.options.unit,
									'designer_mode': false,
									'design':self.designer_tool.options.design,
									'show_cart':'0',
									'start_page': page_name,
									'start_layout':layout_name,
									'printing_mode':false,
									'show_canvas':true,
									'show_tool_bar':false,
									'show_pagelayout_bar':false,
									'mode':"Primary"
								});
					}

				});
			});
			
			$(generate_image).click(function(){
				$('.xepan-designer-canvas-image-dialog .image-canvas canvas').each(function(index,canvas){
					page_name = $(canvas).closest('.image-canvas').data('pagename');
					layout_name = $(canvas).closest('.image-canvas').data('layoutname');

					$(canvas).closest('.image-canvas');
					img_data = canvas.toDataURL();
					image_array[page_name][layout_name] = img_data;
					$(canvas).closest('.xshop-desiner-tool-canvas').hide();
					$('<img>').attr('src',img_data).appendTo($(canvas).closest('.xshop-designer-tool-workplace'));
					$('<div><i class="glyphicon glyphicon-ok" style="color:green;">&nbsp;</i>'+page_name+' : '+layout_name+'</div>').appendTo($(canvas).closest('.xshop-designer-tool-workplace')).css('position');
				});

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
							image_array:JSON.stringify(image_array)
						},
				}).done(function(ret){
					if($.isNumeric(ret)){
						$.univ().successMessage('Design and Image Saved');
					}else
						$.univ().errorMessage('not saved, try again');

				}).always(function(ret){
					$(dialog_image).prepend('<div class="btn btn-block btn-success">Design Saved</div>');
					$(generate_image).hide();
					// $(dialog_image).dialog('close');
				});
			});

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
							calendar_event:JSON.stringify(self.designer_tool.options.calendar_event)
						},
				})
				.done(function(ret) {
					if($.isNumeric(ret)){
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
		});
	}
}