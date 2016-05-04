xShop_Image_Editor = function(parent,component){
	var self = this;
	this.parent = parent;
	this.designer_tool = component.designer_tool;
	this.current_image_component = undefined;	
	
	var base_url = component.designer_tool.options.base_url;
	var page_url = base_url;

	this.element = $('<div id="xshop-designer-image-editor" style="display:block" class="xshop-options-editor"></div>').appendTo(this.parent);
	this.row1 = $('<div class="atk-row xepan-component-designer-info" style="display:block;margin:0;"> </div>').appendTo(this.element);

	// this.image_col = $('<div class="atk-col-3 atk-box-small atk-box-designer"></div>').appendTo(this.row1);
					
	this.image_x_label = $('<div class="atk-move-left"><label for="xshop-designer-image-positionx">x: </label></div>').appendTo(this.row1);
	this.image_x = $('<input name="x" id="xshop-designer-image-positionx" class="xshop-designer-image-inputx" style="width:45px !important" />').appendTo(this.image_x_label);
	// $(this.image_x).val(self.current_image_component.options.x);
	$(this.image_x).change(function(){
		self.current_image_component.options.x = self.current_image_component.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render();
	});
	this.image_y_label = $('<div class="atk-move-left"><label for="xshop-designer-image-positiony">y: </label></div>').appendTo(this.row1);
	this.image_y = $('<input name="y" id="xshop-designer-image-positiony" class="xshop-designer-image-inputy" style="width:45px !important" />').appendTo(this.image_y_label);
	$(this.image_y).change(function(){
		self.current_image_component.options.y = self.current_image_component.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render();
	});

	this.image_width_label = $('<div class="atk-move-left"><label for="xshop-designer-image-width">W: </label></div>').appendTo(this.row1);
	this.image_width = $('<input name="W" title="width" id="xshop-designer-image-width" class="xshop-designer-image-width" style="width:45px !important" />').appendTo(this.image_width_label);
	$(this.image_width).change(function(){
		self.current_image_component.options.width = self.current_image_component.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render();
	});

	this.image_height_label = $('<div class="atk-move-left"><label for="xshop-designer-image-height">H: </label></div>').appendTo(this.row1);
	this.image_height = $('<input name="H" title="height" id="xshop-designer-image-height" class="xshop-designer-image-height" style="width:45px !important" />').appendTo(this.image_height_label);
	$(this.image_height).change(function(){
		self.current_image_component.options.height = self.current_image_component.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render();
	});
	

	this.editor_close_btn = $('<div class="" style="padding:0;margin:0;padding:0;margin:0;position:absolute;top:-25px;right:0;"><i class="atk-box-small pull-right glyphicon glyphicon-remove"></i></div>').appendTo(this.element);

	this.image_button_set = $('<div class="btn-group" role="group"></div>').appendTo(this.element);
	// this.image_manager = $('<div class="btn "><span class="glyphicon glyphicon-film"></span></div>').appendTo(this.image_button_set);
	this.image_edit = $('<div class="btn xshop-designer-image-edit-btn"><i class="icon-edit atk-size-tera"></i><br/><span class="atk-size-micro">Image</span></div>').appendTo(this.image_button_set);
	this.image_crop_resize = $('<div class="btn xshop-designer-image-crop-btn"><i class="icon-crop atk-size-tera"></i><br/><span class="atk-size-micro">Crop</span></div>').appendTo(this.image_button_set);
	
	// this.image_mask = $('<div class="btn xshop-designer-image-mask-btn"><i class="glyphicon glyphicon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Mask</span></div>').appendTo(this.image_button_set);
	this.image_mask_apply = $('<div class="btn xshop-designer-image-mask-apply-btn"><i class="glyphicon glyphicon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Apply Mask</span></div>').appendTo(this.image_button_set);
	this.image_mask_edit = $('<div class="btn xshop-designer-image-mask-edit-btn"><i class="glyphicon glyphicon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Edit Mask</span></div>').appendTo(this.image_button_set);
	// this.image_duplicate = $('<div class="btn "><span class="glyphicon glyphicon-">Duplicate</span></div>').appendTo(this.image_button_set);
	// this.image_manager = $('<div class="btn "><span class="glyphicon glyphicon-film"></span></div>').appendTo(this.image_button_set);
	this.image_lock = $('<div class="btn xshop-designer-image-lock-btn"><i class="icon-lock atk-size-tera"></i><br/><span class="atk-size-micro">Lock</span></div>').appendTo(this.image_button_set);
	this.image_up_down = $('<div class="btn xshop-designer-image-up-down-btn"></div>').appendTo(this.image_button_set);
	this.image_up = $('<div class="xshop-designer-image-up-btn icon-angle-circled-up atk-size-mega xshop-designer-image-up-btn" title="Bring to Front" ></div>').appendTo(this.image_up_down);
	this.image_down = $('<div class="xshop-designer-image-down-btn icon-angle-circled-down atk-size-mega xshop-designer-image-up-btn" title="Send to Back" ></div>').appendTo(this.image_up_down);
	this.image_remove = $('<div class="btn xshop-designer-image-remove-btn"><i class="icon-trash atk-size-tera"></i><br/><span class="atk-size-micro">Remove</span></div>').appendTo(this.image_button_set);

	$(this.editor_close_btn).click(function(event){
		self.element.hide();
	});
	// this.image_mask.click(function(event){
	// 	self.current_image_component.options.mask_added=true;
	// 	options ={modal:false,
	// 				width:800
	// 			};
	// 	$.univ().frameURL('Add Mask Images From...','index.php?page=xShop_page_designer_itemimages',options);
	// });

	this.image_mask_apply.click(function(event){
		self.current_image_component.options.apply_mask=true;
		$(self.current_image_component.element).find('img[is_mask_image=1]').hide();
		self.current_image_component.render();
	});

	this.image_mask_edit.click(function(event){
		self.current_image_component.options.apply_mask=false;
		$(self.current_image_component.element).find('img[is_mask_image=1]').show();
		self.current_image_component.render();
	});

	this.image_remove.click(function(){
		dt  = self.current_image_component.designer_tool;
		$.each(dt.pages_and_layouts[dt.current_page][dt.current_layout].components, function(index,cmp){
			if(cmp === dt.current_selected_component){
				// console.log(self.pages_and_layouts);
				$(dt.current_selected_component.element).remove();
				dt.pages_and_layouts[dt.current_page][dt.current_layout].components.splice(index,1);
				dt.current_selected_component = null;
				dt.option_panel.hide();
			}
		});
	});

	//Lock the Image 
	this.image_lock.click(function(){
		current_image = $(self.current_image_component.element);		
		if(current_image.hasClass('xepan-designer-lock-component')){
			$('.xepan-designer-image-unlock-btn').remove();
			current_image.removeClass('xepan-designer-lock-component');
		}else{
			current_image.addClass('xepan-designer-lock-component');
			unlock = $('<div class="xepan-designer-image-unlock-btn atk-label atk-size-mega atk-swatch-blue" title="Click here to unlock the image"><i class="icon-lock-open"></i></div>').appendTo(current_image);
			unlock.click(function(){
				$('.xepan-designer-image-unlock-btn').remove();
				current_image.removeClass('xepan-designer-lock-component');	
			});		
		}
		
	});

	//Bring To Front
	this.image_up.click(function(){
		current_image = $(self.current_image_component.element);
		current_zindex = current_image.css('z-index');
		if( current_zindex == 'auto'){
			current_zindex = 0;
		}
		current_image.css('z-index', parseInt(current_zindex)+1);
		self.current_image_component.options.zindex = current_image.css('z-index');
		if($('div.xshop-designer-image-down-btn').hasClass('xepan-designer-button-disable')){
			$('div.xshop-designer-image-down-btn').removeClass('xepan-designer-button-disable');
		}
	});

	//Send to Back
	this.image_down.click(function(){
		current_image = $(self.current_image_component.element);
		current_zindex = current_image.css('z-index');
		if( current_zindex == 'auto' || (parseInt(current_zindex)-1) < 0){
			current_zindex = 0;
		}else 
			current_zindex = (parseInt(current_zindex)-1);

		current_image.css('z-index', current_zindex);
		self.current_image_component.options.zindex = current_zindex;
		if(current_zindex == 0 ){
			$('div.xshop-designer-image-down-btn').addClass('xepan-designer-button-disable');
		}
	});

	//Hide Default Mask Edit and Apply option
	this.image_mask_apply.hide();
	this.image_mask_edit.hide();


	this.image_crop_resize.click(function(event){
		// var self =this;
		// console.log(self.current_image_component);
		event.preventDefault();
		event.stopPropagation();
		url = self.current_image_component.options.url;		
		o = self.current_image_component.options;
		
		xx= $('<div class="xshop-designer-image-crop"></div>').appendTo(self.element);
		crop_image = $('<img class="xshop-img" src='+url+'></img>').appendTo(xx);
		x = $('<div></div>').appendTo(crop_image);
		y = $('<div></div>').appendTo(crop_image);
		width = $('<div></div>').appendTo(crop_image);
		height = $('<div></div>').appendTo(crop_image);
		
		xx.dialog({
			minWidth: 800,
			modal:true,
			open: function( event, ui ) {
				$(crop_image).cropper({
					aspectRatio: false,
				    multiple: true,
				    data: {
					    x: o.crop == true? o.crop_x: 0,
					    y: o.crop == true? o.crop_y: 0,
					    width: o.crop == true? o.crop_width: $(crop_image).width(),
					    height: o.crop == true? o.crop_height: $(crop_image).height()
					  },  
					done: function(data) {
						$(x).val(Math.round(data.x));
						$(y).val(Math.round(data.y));
						$(width).val(Math.round(data.width));
						$(height).val(Math.round(data.height));
					    // console.log(Math.round(data.width));
					  }
				});
				var $titlebar = $.find('.ui-dialog-titlebar');
			},

			close: function( event, ui ) {
				console.log(self.current_image_component.canvas);
			},

			buttons: {
				Continue: function(){
					self.current_image_component.options.crop = true;
					self.current_image_component.options.crop_x = $(x).val();
					self.current_image_component.options.crop_y = $(y).val();
					self.current_image_component.options.crop_width = $(width).val();
					self.current_image_component.options.crop_height = $(height).val();
					self.current_image_component.render();
					$(this).dialog('close');
				}
			}
		});
		// console.log(self.current_image_component);
		//TODO CROP and RESIZE The Image not No
	});

	this.image_edit.click(function(event){
		options = {modal:false,
					width:800,
				};
		$.univ().frameURL('Add Images From...',base_url+'?page=xepan_commerce_designer_itemimages',options);

	});

	// this.image_duplicate.click(function(event){
	// 	//TODO CROP and RESIZE The Image not No
	// });

	this.setImageComponent = function(component){
		this.current_image_component  = component;
	}
}

Image_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;
	this.xhr = undefined;
	this.mask = undefined

	this.options = {
		x:0,
		y:0,
		width:'0',
		height:'0',
		url:'templates/images/logo.png',
		crop_x: false,
		crop_y:false,
		crop_width:false,
		crop_height:false,
		crop:false,
		replace_image: false,
		rotation_angle:0,
		locked: false,
		alignment_left:false,
		alignment_center:false,
		alignment_right:false,
		// Designer properties
		movable: true,
		colorable: true,
		editable: true,
		default_url:'templates/images/logo.png',
		zindex:0,
		resizable: true,
		auto_fit: false,
		frontside:true,
		backside:false,
		multiline: false,
		// System properties
		type: 'Image',
		//Mask the image
		is_mask_image: false,
		mask_added: false,
		apply_mask: false,
		mask_options: {},
		base_url:undefined,
		page_url:undefined
	};

	this.init = function(designer,canvas, editor){
		this.designer_tool = designer;
		this.canvas = canvas;
		if(editor !== undefined)
			this.editor = editor;
	}

	this.initExisting = function(params){
		// alert('Hi called');
	}

	this.addImage = function(image_url, is_masked){
		var self=this;
		//create new ImageComponent type object
		var new_image = new Image_Component();
		new_image.init(self.designer_tool,self.canvas, self.editor);
		// feed default values for its parameters
		//Set Options
		new_image.options.x=0;
		new_image.options.y=0;
		new_image.options.url = image_url;
		if(is_masked === true) new_image.options.is_mask_image = true;
		// console.log(new_image);
		// add this Object to canvas components array
		// console.log(self.designer_tool.current_page);
		self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].components.push(new_image);
		new_image.render(true);
		return new_image;
	}

	this.isMaskOptionsAdded = function(){
		var self=this;
		return self.options.mask_added && self.options.mask_options.url;
	},

	this.isMaskAppended = function(){
		var self=this;
		return self.element.find('img[is_mask_image=1]').length;
	},

	this.updateMask = function(){
		var self=this;
		if(self.isMaskOptionsAdded() && !self.isMaskAppended()){
			//create new ImageComponent type object
			var mask_image = new Image_Component();
			mask_image.init(self.designer_tool,self.canvas, self.editor);
			// feed default values for its parameters
			//Set Options
			mask_image.options.url = self.options.mask_options.url;
			mask_image.options = self.options.mask_options;
			mask_image.options.is_mask_image = true;
			mask_image.options.x = 0;
			mask_image.options.y = 0;
			mask_image.render(true);
			self.mask = mask_image;	
			self.options.mask_added = true;

			$(mask_image.element).appendTo(self.element);
			mask_image.render();

			$(mask_image.element).draggable("option", "containment", self.element);
			return mask_image;
		}

		self.mask.render();

		return mask_image;
	}

	this.renderTool = function(parent){
		var self=this;
		this.parent = parent;
		console.log(self.designer_tool);
		self.options.base_url = self.designer_tool.options.base_url;
		self.options.page_url = self.designer_tool.options.base_url;

		tool_btn = $('<div class="btn btn-deault xshop-designer-image-toolbtn "><i class="glyphicon glyphicon-picture"></i><br>Image</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset')).data('tool',self);
		this.editor = new xShop_Image_Editor(parent.find('.xshop-designer-tool-topbar-options'),self);

		// CREATE NEW TEXT COMPONENT ON CANVAS
		tool_btn.click(function(event){
			if(self.designer_tool.current_selected_component != undefined && self.designer_tool.current_selected_component.options.type != 'Image')
				self.designer_tool.current_selected_component = undefined;

			options ={modal:false,
					width:800	
				};
			$.univ().frameURL('Add Images From...',self.options.page_url+'?page=xepan_commerce_designer_itemimages',options);
		});
	}


	this.render = function(is_new_image){
		var self = this;
		if(self.options.base_url == undefined){
			self.options.base_url = self.designer_tool.options.base_url;
			self.options.page_url = self.designer_tool.options.base_url;
		}

		if(this.element == undefined){
			// self.options.width = self.designer_tool.px_width / 2;

			this.element = $('<div style="position:absolute" class="xshop-designer-component"><span class="xepan-designer-dropped-image"><img is_mask_image="'+(self.options.is_mask_image==true?'1':'0')+'"></img></span></div>').appendTo(this.canvas);
			this.element.draggable({
				containment: self.designer_tool.safe_zone,
				smartguides:".xshop-designer-component",
				tolerance:5,
				stop:function(e,ui){
					var position = ui.position;
					self.options.x = self.designer_tool.screen2option(position.left);
					self.options.y = self.designer_tool.screen2option(position.top);
					
					self.editor.image_x.val(position.left);
					self.editor.image_y.val(position.top);
				}
			}).resizable({
				aspectRatio: true,
				autoHide: true,
				handles: "se",//,s,e
				
				stop:function(e,ui){
					// self.options.x = ui.position.left / self.designer_tool.zoom;
					// self.options.y = ui.position.top / self.designer_tool.zoom;
					self.options.width = self.designer_tool.screen2option(ui.size.width) ;
					self.options.height = self.designer_tool.screen2option(ui.size.height) ;
					self.editor.image_width.val(ui.size.width);
					self.editor.image_height.val(ui.size.height);
					self.render();
				}
			});

			$(this.element).data('component',this);
			

			//Apply FreeLancer Options on Component
			if(!self.options.movable){
				self.element.draggable('disable');
			}

			if(!self.options.colorable){
				self.editor.text_color_picker.next('button').hide();
			}

			if(!self.options.editable){
				self.editor.text_input.hide();
			}

			if(!self.options.resizable){
				self.element.resizable('disable');
			}
			//

			$(this.element).click(function(event) {
	            $('.ui-selected').removeClass('ui-selected');
	            $(this).addClass('ui-selected');
	            $('.xshop-options-editor').hide();
	            self.editor.element.show();
	            //using callback function for hide and show the apply and edit mask option
	            self.designer_tool.option_panel.show('fast',function(event){
	            	if(self.options.mask_added == true && self.options.mask_options.url != undefined){
	            		$('div.xshop-designer-image-mask-apply-btn').show();
	            		$('div.xshop-designer-image-mask-edit-btn').show();
	            	}else{
	            		$('div.xshop-designer-image-mask-apply-btn').hide();
	            		$('div.xshop-designer-image-mask-edit-btn').hide();
	            	}

	            	if(self.options.mask_added == true || self.options.is_mask_image){
	            		$('div.xshop-designer-image-mask-btn').hide();
	            	}else{
	            		$('div.xshop-designer-image-mask-btn').show();

	            	}

	            	//check For the Z-index
	            	if(self.options.zindex == 0){
	            		$('div.xshop-designer-image-down-btn').addClass('xepan-designer-button-disable');
	            	}else
	            		$('div.xshop-designer-image-down-btn').removeClass('xepan-designer-button-disable');
	            });

	            if(self.designer_tool.options.designer_mode){
		            self.designer_tool.freelancer_panel.FreeLancerComponentOptions.element.show();
		            self.designer_tool.freelancer_panel.setComponent($(this).data('component'));
	            }else{
	            	$('.xepan-component-designer-info').hide();
	            }
	            self.designer_tool.current_selected_component = self;
	            self.designer_tool.option_panel.css('z-index',70);
	            self.designer_tool.option_panel.css('top',0);
	            self.designer_tool.option_panel.addClass('xshop-text-options');

	           	designer_currentTarget = $(event.currentTarget);
	           	top_value = parseInt(designer_currentTarget.offset().top) - parseInt($('#xshop-designer-image-editor').height() +10);

	            self.designer_tool.option_panel.css('top',top_value);
	            self.designer_tool.option_panel.css('left',$(designer_currentTarget).offset().left);
	            self.editor.image_x.val(self.options.x);
	            self.editor.image_y.val(self.options.y);
	            self.editor.image_width.val(self.options.width);
	            self.editor.image_height.val(self.options.height);
	            self.editor.setImageComponent(self);
		        event.stopPropagation();
			});
		}else{
			this.element.show();
		}

		if(is_new_image == undefined){
			this.element.css('top',self.designer_tool.option2screen(self.options.y));
			this.element.css('left',self.designer_tool.option2screen(self.options.x));
			this.element.css('width',self.designer_tool.option2screen(self.options.width));
			this.element.css('height',self.designer_tool.option2screen(self.options.height));
		}

		if(this.xhr != undefined)
			this.xhr.abort();

		this.xhr = $.ajax({
			url: self.options.page_url+'?page=xepan_commerce_designer_renderimage&cut_page=1',
			type: 'GET',
			data: {
					default_value: self.options.default_value,
					crop:self.options.crop,
					crop_x: self.options.crop_x,
					crop_y: self.options.crop_y,
					crop_height: self.options.crop_height,
					crop_width: self.options.crop_width,
					replace_image: self.options.replace_image,
					rotation_angle:self.options.rotation_angle,
					url:self.options.url,
					zoom: self.designer_tool.zoom,
					width:self.options.width,
					height:self.options.height,
					max_width: self.designer_tool.safe_zone.width()/1.5,
					max_height: self.designer_tool.safe_zone.height()/1.5,
					auto_fit: is_new_image===true,
					mask:self.options.mask_options,
					mask_added:self.options.mask_added,
					apply_mask:self.options.apply_mask,
					is_mask_image:self.options.is_mask_image,
					x: self.options.x,
					y: self.options.y,
					zindex:self.options.zindex
				},
		})
		.done(function(ret) {
			if(self.options.is_mask_image)
				self.element.find('img[is_mask_image=1]').attr('src','data:image/png;base64, '+ ret);
			else
				self.element.find('img[is_mask_image=0]').attr('src','data:image/png;base64, '+ ret);

			window.setTimeout(function(){
				self.element.height(self.element.find('img[is_mask_image=0]').height());
				self.element.width(self.element.find('img[is_mask_image=0]').width());
				// console.log(self.element.find('img[is_mask_image=0]').height());
				// console.log(self.element.find('img[is_mask_image=0]').width());

			},300);

			// self.element.width(self.designer_tool.screen2option(self.element.find('img').width()));
			// $(image).closest('div.xshop-designer-component').width($(image).width());
			// xshop-designer-component
			
			if(is_new_image===true){
				window.setTimeout(function(){
					self.options.width = self.designer_tool.screen2option(self.element.find('img').width());
					self.options.height = self.designer_tool.screen2option(self.element.find('img').height());
					// console.log(self.element.find('img').width());
				},200);
			}
			self.xhr=undefined;
		})
		.fail(function(ret) {
			// evel(ret);
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});	

		if(self.options.mask_added) self.updateMask();

		// this.element.text(this.text);
		// this.element.css('left',this.x);
		// this.element.css('top',this.y);
	}

}