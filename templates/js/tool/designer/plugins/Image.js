xShop_Image_Editor = function(parent,component){
	var self = this;
	this.parent = parent;
	this.designer_tool = component.designer_tool;
	this.current_image_component = undefined;	
	
	var base_url = component.designer_tool.options.base_url;
	var page_url = base_url;

	this.element = $('<div id="xshop-designer-image-editor" style="display:block" class="xshop-options-editor"></div>').appendTo(this.parent);
	this.row1 = $('<div class="atk-row xshop-designer-tool-editing-helper image" style="display:block;margin:0;"> </div>').appendTo(this.element);

	// this.image_col = $('<div class="atk-col-3 atk-box-small atk-box-designer"></div>').appendTo(this.row1);
					
	this.image_x_label = $('<div class="atk-move-left"><label for="xshop-designer-image-positionx">x: </label></div>').appendTo(this.row1);
	this.image_x = $('<input name="x" id="xshop-designer-image-positionx" class="xshop-designer-image-inputx" style="width:45px !important" />').appendTo(this.image_x_label);
	// $(this.image_x).val(self.current_image_component.options.x);
	$(this.image_x).change(function(){
		design_dirty = true;
		// self.current_image_component.options.x = self.current_image_component.designer_tool.screen2option($(this).val());
		self.current_image_component.options.x = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render(self.designer_tool);
	});
	this.image_y_label = $('<div class="atk-move-left"><label for="xshop-designer-image-positiony">y: </label></div>').appendTo(this.row1);
	this.image_y = $('<input name="y" id="xshop-designer-image-positiony" class="xshop-designer-image-inputy" style="width:45px !important" />').appendTo(this.image_y_label);
	$(this.image_y).change(function(){
		design_dirty = true;
		// self.current_image_component.options.y = self.current_image_component.designer_tool.screen2option($(this).val());
		self.current_image_component.options.y = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render(self.designer_tool);
	});

	this.image_width_label = $('<div class="atk-move-left"><label for="xshop-designer-image-width">W: </label></div>').appendTo(this.row1);
	this.image_width = $('<input name="W" title="width" id="xshop-designer-image-width" class="xshop-designer-image-width" style="width:45px !important" />').appendTo(this.image_width_label);
	$(this.image_width).change(function(){
		design_dirty = true;
		self.current_image_component.options.width = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render(self.designer_tool);
	});

	this.image_height_label = $('<div class="atk-move-left"><label for="xshop-designer-image-height">H: </label></div>').appendTo(this.row1);
	this.image_height = $('<input name="H" title="height" id="xshop-designer-image-height" class="xshop-designer-image-height" style="width:45px !important" />').appendTo(this.image_height_label);
	$(this.image_height).change(function(){
		design_dirty = true;

		self.current_image_component.options.height = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_image_component.render(self.designer_tool);
	});
	

	this.editor_close_btn = $('<div class="xshop-designer-tool-editor-option-close"><i class="atk-box-small pull-right glyphicon glyphicon-remove"></i></div>').appendTo(this.element);

	this.image_button_set = $('<div class="btn-group" role="group"></div>').appendTo(this.element);
	// this.image_manager = $('<div class="btn "><span class="glyphicon glyphicon-film"></span></div>').appendTo(this.image_button_set);
	// for show or hide the insert button for image options 
	this.text_input = this.image_edit = $('<div class="btn xshop-designer-image-edit-btn"><i class="icon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Replace</span></div>').appendTo(this.image_button_set);
	this.image_crop_resize = $('<div class="btn xshop-designer-image-crop-btn"><i class="icon-crop atk-size-tera"></i><br/><span class="atk-size-micro">Crop</span></div>').appendTo(this.image_button_set);
	
	// this.image_mask = $('<div class="btn xshop-designer-image-mask-btn"><i class="glyphicon glyphicon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Mask</span></div>').appendTo(this.image_button_set);
	this.image_mask_apply = $('<div class="btn xshop-designer-image-mask-apply-btn"><i class="glyphicon glyphicon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Apply Mask</span></div>').appendTo(this.image_button_set);
	this.image_mask_edit = $('<div class="btn xshop-designer-image-mask-edit-btn"><i class="glyphicon glyphicon-picture atk-size-tera"></i><br/><span class="atk-size-micro">Edit Mask</span></div>').appendTo(this.image_button_set);
	// this.image_duplicate = $('<div class="btn "><span class="glyphicon glyphicon-">Duplicate</span></div>').appendTo(this.image_button_set);
	// this.image_manager = $('<div class="btn "><span class="glyphicon glyphicon-film"></span></div>').appendTo(this.image_button_set);
	this.image_unlock = $('<div class="btn xshop-designer-image-lock-btn"><i class="icon-lock-open atk-size-tera"></i><br/><span class="atk-size-micro">Unlock</span></div>').appendTo(this.image_button_set);
	this.image_lock = $('<div class="btn xshop-designer-image-lock-btn"><i class="icon-lock atk-size-tera"></i><br/><span class="atk-size-micro">Lock</span></div>').appendTo(this.image_button_set);
	this.image_up_down = $('<div class="btn xshop-designer-image-up-down-btn"></div>').appendTo(this.image_button_set);
	this.image_up = $('<div class="xshop-designer-image-up-btn icon-angle-circled-up atk-size-mega xshop-designer-image-up-btn" title="Bring to Front" ></div>').appendTo(this.image_up_down);
	this.image_down = $('<div class="xshop-designer-image-down-btn icon-angle-circled-down atk-size-mega xshop-designer-image-up-btn" title="Send to Back" ></div>').appendTo(this.image_up_down);
	this.image_remove = $('<div class="btn xshop-designer-image-remove-btn"><i class="icon-trash atk-size-tera"></i><br/><span class="atk-size-micro">Remove</span></div>').appendTo(this.image_button_set);
	this.rotate_button_set = $('<div class="btn xshop-designer-image-rotate-btn"></div>').appendTo(this.image_button_set);
	
	// Angle
	this.text_rotate_angle = $('<input name="angle" type="number" id="xshop-designer-image-angle" class="xshop-designer-image-input-angle" />').appendTo(this.rotate_button_set);
	this.text_rotate_angle_label = $('<br/><span class="atk-size-micro">Angle</span>').appendTo(this.rotate_button_set);
	$(this.text_rotate_angle).change(function(){
		design_dirty = true;
		// self.current_text_component.options.x = self.current_text_component.designer_tool.screen2option($(this).val());
		self.current_image_component.options.rotation_angle = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_image_component.render(self.designer_tool);
	});


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
		design_dirty = true;
		self.current_image_component.options.apply_mask=true;
		$(self.current_image_component.element).find('img[is_mask_image=1]').hide();
		self.current_image_component.render(self.designer_tool);
	});

	this.image_mask_edit.click(function(event){
		design_dirty = true;
		self.current_image_component.options.apply_mask=false;
		$(self.current_image_component.element).find('img[is_mask_image=1]').show();
		self.current_image_component.render(self.designer_tool);
	});

	this.image_remove.click(function(){
		design_dirty = true;
		dt  = self.current_image_component.designer_tool;
		$.each(dt.pages_and_layouts[dt.current_page][dt.current_layout].components, function(index,cmp){
			if(cmp === dt.current_selected_component){
				// console.log(self.pages_and_layouts);
				$(dt.current_selected_component.element).remove();
				dt.pages_and_layouts[dt.current_page][dt.current_layout].components.splice(index,1);
				dt.canvasObj.getActiveObject().remove();
				dt.option_panel.hide();
				dt.current_selected_component = null;
				return true;
			}
		});
	});

	//Lock the Image
	//unLock the Image

	this.image_unlock.click(function(){
		design_dirty = true;
		self.current_image_component.options.locked = false;
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_image_component.render(self.designer_tool);
		$(this).hide();
		self.image_lock.show();
	});

	this.image_lock.click(function(){
		design_dirty = true;
		self.current_image_component.options.locked = true;
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_image_component.render(self.designer_tool);
		$(this).hide();
		self.image_unlock.show();
	});

	if(self.current_image_component){
		design_dirty = true;
		if(self.current_image_component.options.locked){
			self.image_lock.hide();
			self.image_unlock.show();
		}else{
			self.image_lock.show();
			self.image_unlock.hide();
		}

	}

	//Bring To Front
	this.image_up.click(function(){
		design_dirty = true;
		var component_count = self.current_image_component.designer_tool.canvasObj.getObjects().length;	
		current_image = self.current_image_component.element;
		var zin = parseInt(self.current_image_component.options.zindex);
		if(component_count > zin)
			zin =  zin+1;

		current_image.moveTo(zin);
		self.current_image_component.options.zindex = zin;
		if($('div.xshop-designer-image-down-btn').hasClass('xepan-designer-button-disable')){
			$('div.xshop-designer-image-down-btn').removeClass('xepan-designer-button-disable');
		}

		// console.log(" Image Front "+zin);
	});

	//Send to Back
	this.image_down.click(function(){
		design_dirty = true;
		current_image = self.current_image_component.element;
		var zin = parseInt(self.current_image_component.options.zindex) - 1;
		if(zin < 1 )
			zin = 1;
		
		current_image.moveTo(zin);
		self.current_image_component.options.zindex = zin;
		if(zin == 0 ){
			$('div.xshop-designer-image-down-btn').addClass('xepan-designer-button-disable');
		}

		// console.log(" Image Back "+zin);
	});

	//Hide Default Mask Edit and Apply option
	this.image_mask_apply.hide();
	this.image_mask_edit.hide();


	this.image_crop_resize.click(function(event){
		// var self =this;
		// console.log(self.current_image_component);
		design_dirty = true;
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
				// console.log("crop window close");
				// console.log(self.current_image_component.canvas);
			},

			buttons: {
				Continue: function(){
					self.current_image_component.options.crop = true;
					self.current_image_component.options.crop_x = $(x).val();
					self.current_image_component.options.crop_y = $(y).val();
					self.current_image_component.options.crop_width = $(width).val();
					self.current_image_component.options.crop_height = $(height).val();
					self.current_image_component.render(self.designer_tool);
					$(this).dialog('close');
				}
			}
		});
		// console.log(self.current_image_component);
		//TODO CROP and RESIZE The Image not No
	});

	this.image_edit.click(function(event){
		options = {
			modal:true,
			height:500
		};
		frame = $.univ().frameURL('Manage Your Images','?page=xepan_commerce_designer_itemimages',options).addClass('xepan-designer-image-dialog');
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
		width:false,
		height:false,
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
		replace_mode:'crop_in_ratio',
		default_url:'templates/images/logo.png',
		zindex:1,
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
		new_image.render(self.designer_tool,true);
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
			mask_image.render(self.designer_tool,true);
			self.mask = mask_image;	
			self.options.mask_added = true;

			$(mask_image.element).appendTo(self.element);
			mask_image.render(self.designer_tool);

			$(mask_image.element).draggable("option", "containment", self.element);
			return mask_image;
		}

		self.mask.render(self.designer_tool);

		return mask_image;
	}

	this.renderTool = function(parent){
		var self=this;
		this.parent = parent;
		self.options.base_url = self.designer_tool.options.base_url;
		self.options.page_url = self.designer_tool.options.base_url;

		tool_btn = $('<div class="btn btn-deault xshop-designer-image-toolbtn "><i class="glyphicon glyphicon-picture"></i><br>Image</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset')).data('tool',self);
		this.editor = new xShop_Image_Editor(parent.find('.xshop-designer-tool-topbar-options'),self);

		// CREATE NEW TEXT COMPONENT ON CANVAS
		tool_btn.click(function(event){
			design_dirty = true;
			// if(self.designer_tool.current_selected_component != undefined && self.designer_tool.current_selected_component.options.type != 'Image')
				self.designer_tool.current_selected_component = undefined;

			options = {
				modal:true,
				height:500
			};
			frame = $.univ().frameURL('Manage Your Images','?page=xepan_commerce_designer_itemimages',options).addClass('xepan-designer-image-dialog');
		});

		var idx = $.inArray("Image", self.designer_tool.options.ComponentsIncludedToBeShow);
		if (idx == -1) {
			$(tool_btn).hide();
		}
	}


	this.render = function(designer_tool_obj,is_new_image){
		var self = this;
		self.show_xywh = true;
		if(designer_tool_obj) self.designer_tool = designer_tool_obj;

		if(self.options.base_url == undefined){
			self.options.base_url = self.designer_tool.options.base_url;
			self.options.page_url = self.designer_tool.options.base_url;
		}

		if(this.element){
			// console.log(self.options);
			self.designer_tool.canvasObj.getActiveObject().remove();
		}

		var canvas = self.designer_tool.canvasObj;
		if(is_new_image){
			console.log("is new image");
			self.options.crop_x= false;
			self.options.crop_y=false;
			self.options.crop_width=false;
			self.options.crop_height=false;
			self.options.crop =false;

			// if(self.options.replace_mode != "fit_to_scale"){
			// 	self.options.width=undefined;
			// 	self.options.height=undefined;
			// }
			
		}
		var imgRaw = new Image();

		imgRaw.onload = function(event){
			
			var canvas_temp = fabric.util.createCanvasElement();
			canvas_temp.width = imgRaw.width;
			canvas_temp.height = imgRaw.height;
			canvas_temp.getContext('2d').drawImage(this, self.options.crop_x?self.options.crop_x:0, self.options.crop_y?self.options.crop_y:0, self.options.crop_width?self.options.crop_width:imgRaw.width, self.options.crop_height?self.options.crop_height:imgRaw.height, 0, 0, imgRaw.width, imgRaw.height);

			imgRaw.onload = function(event) {

				var img = new fabric.Image(imgRaw);

				// var ratio = Math.min(self.options.width / imgRaw.width, self.options.height / imgRaw.height);
    // 			self.options.width = imgRaw.width*ratio;
    // 			self.options.height = imgRaw.height*ratio;

    			if(self.options.crop){

					if(parseInt(self.options.crop_width) > parseInt(self.options.crop_height) ){
						self.options.height = ( self.options.crop_height / self.options.crop_width) * self.options.width;
						// self.options.width = self.options.crop_width / self.designer_tool._getZoom();
					}else{
						self.options.width = ( self.options.crop_width / self.options.crop_height ) * self.options.height;
						// self.options.height = self.options.crop_height / self.designer_tool._getZoom();
					} 
    			}

				img.set({
					left: self.options.x * self.designer_tool._getZoom(), 
					top: self.options.y * self.designer_tool._getZoom(),
					angle : self.options.rotation_angle
				});

				if(self.options.locked){
					img.set({
						lockMovementX: true,
						lockMovementY: true,
						lockScalingX: true,
						lockScalingY: true,
						lockRotation: true,
						hasControls: false,
						hasBorders:false
					});
				}

				if(!self.options.movable){
					img.set({
						lockMovementX: true,
						lockMovementY: true,
						lockRotation: true
					});
				}

				if(!self.options.resizable){
					img.set({
						lockScalingX: true,
						lockScalingY: true,
					});
				}

				// set evented off on image
				if(!self.options.editable && !self.designer_tool.options.designer_mode){
					img.set({
						evented:false
					});
				}

				img.on('scaling',function(e){
					self.editor.image_width.val(self.options.width);
					self.editor.image_height.val(self.options.height);
				});

				img.on('moving',function(e){
					self.editor.image_x.val(self.options.x);
					self.editor.image_y.val(self.options.y);
				});

				img.on('selected',function(e){
					$('.ui-selected').removeClass('ui-selected');
		            $(this).addClass('ui-selected');
		            $('.xshop-options-editor').hide();

		            if(self.options.locked){
		            	self.editor.image_unlock.show();
		            	self.editor.image_lock.hide();
		            }else{
		            	self.editor.image_unlock.hide();
		            	self.editor.image_lock.show();
		            }

		            if(!self.options.editable){
						self.editor.text_input.hide();
						self.editor.image_crop_resize.hide();
						self.editor.image_remove.hide();
						self.editor.rotate_button_set.hide();
					}else{
						self.editor.text_input.show();
						self.editor.image_crop_resize.show();
						self.editor.image_remove.show();
						self.editor.rotate_button_set.show();
					}

		            self.editor.element.show();

		            //using callback function for hide and show the apply and edit mask option
		            self.designer_tool.option_panel.show('fast',function(event){
		            	//check For the Z-index
		            	if(self.options.zindex == 0){
		            		$('div.xshop-designer-image-down-btn').addClass('xepan-designer-button-disable');
		            	}else
		            		$('div.xshop-designer-image-down-btn').removeClass('xepan-designer-button-disable');
		            });

		            if(self.designer_tool.options.designer_mode){
			            self.designer_tool.freelancer_panel.FreeLancerComponentOptions.element.show();
			            self.designer_tool.freelancer_panel.setComponent(self.designer_tool.current_selected_component);
		            	$('.xshop-designer-tool-editing-helper.image').show();
		            }

		            if(self.show_xywh){
		            	$('.xshop-designer-tool-editing-helper.image').show();

		            	// console.log(self.options.movable);
		            	if(self.options.movable){
		            		self.editor.image_x.show();
		            		self.editor.image_x_label.show();
		            		self.editor.image_y.show();
		            		self.editor.image_y_label.show();
		            	}else{
		            		self.editor.image_x.hide();
		            		self.editor.image_x_label.hide();
		            		self.editor.image_y.hide();
		            		self.editor.image_y_label.hide();
		            	}

		            	if(self.options.resizable){
		            		self.editor.image_width.show();
		            		self.editor.image_width_label.show();
		            		self.editor.image_height.show();
		            		self.editor.image_height_label.show();
		            	}else{
		            		self.editor.image_width.hide();
		            		self.editor.image_width_label.hide();
		            		self.editor.image_height.hide();
		            		self.editor.image_height_label.hide();
		            	}
		            }

		            self.designer_tool.option_panel.fadeIn(500);
		            self.designer_tool.option_panel.css('z-index',70);
		            self.designer_tool.option_panel.addClass('xshop-text-options');

		            self.designer_tool.option_panel.offset(
		            							{
		            								top:self.designer_tool.canvasObj._offset.top - self.designer_tool.option_panel.height() - (10 * self.designer_tool._getZoom()),
			        								left:self.designer_tool.canvasObj._offset.left + img.left
		            							}
		            						);

		            // if designer mode is open
		            // setting up x and y position of image 
		            // if(self.designer_tool.options.designer_mode){
			            self.editor.image_x.val(self.options.x);
			            self.editor.image_y.val(self.options.y);
			            self.editor.image_width.val(self.options.width);
			            self.editor.image_height.val(self.options.height);
		            // }

		            self.editor.setImageComponent(self);
			        
			        if (e.stopPropagation) {
				      e.stopPropagation();
				    }
				    //IE8 and Lower
				    else {
				      e.cancelBubble = true;
				    }
				});

				self.element = img;
				self.element.component = self;

				// canvas.add(img);
				canvas.insertAt(img,self.options.zindex);

				if(!self.options.width){
					if(canvas.getWidth() < canvas.getHeight())
						img.scaleToWidth(canvas.getWidth()*0.75);
					else
						img.scaleToHeight(canvas.getHeight()*0.75);

					self.options.width = img.getWidth() / self.designer_tool._getZoom();
					self.options.height = img.getHeight() / self.designer_tool._getZoom();
					// console.log('undefines first time');
					// console.log("width ="+img.getWidth());
    	// 			console.log("height ="+img.getHeight());
    	// 			console.log(self.designer_tool._getZoom());

				}else{
					img.width = self.options.width * self.designer_tool._getZoom();
					img.height = self.options.height * self.designer_tool._getZoom();
				}


				// console.log("move Image "+self.options.zindex);
				if(self.options.type == "Image")
					var ret = img.moveTo(self.options.zindex);
				// console.log("------------------------");
				canvas.renderAll();
			
			}
			imgRaw.src = canvas_temp.toDataURL('image/png');

		}

		imgRaw.src = self.options.url;
		// self.designer_tool.canvasObj.renderAll();
		// canvas.renderAll();
		// console.log("Render All Object ");
		// console.log(imgRaw);
		// console.log(canvas.getObjects());
		// $.each(canvas.getObjects(),function(index,object){
		// 	console.log(object);
		// });
		// console.log('-----------------');
		return;

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
				containment:"parent",
				aspectRatio: true,
				autoHide: true,
				handles: "se",
				stop:function(e,ui){
					// self.options.x = ui.position.left / self.designer_tool.zoom;
					// self.options.y = ui.position.top / self.designer_tool.zoom;
					self.options.width = self.designer_tool.screen2option(ui.size.width) ;
					self.options.height = self.designer_tool.screen2option(ui.size.height) ;
					self.editor.image_width.val(ui.size.width);
					self.editor.image_height.val(ui.size.height);
					self.render(self.designer_tool);
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

			// image show or hide the editor options
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
	            	$('.xshop-designer-tool-editing-helper.image').show();
	            }

	            self.designer_tool.option_panel.fadeIn(500);
	            self.designer_tool.current_selected_component = self;
	            self.designer_tool.option_panel.css('z-index',70);
	            self.designer_tool.option_panel.addClass('xshop-text-options');
	           	top_value = parseInt($(this).offset().top) - parseInt($('#xshop-designer-image-editor').height() +10);

	            self.designer_tool.option_panel.offset(
	            							{
	            								top:top_value,
	            								left:$(this).offset().left
	            							}
	            						);
	
	            // if designer mode is open
	            // setting up x and y position of image 
	            // if(self.designer_tool.options.designer_mode){
		            self.editor.image_x.val(self.options.x);
		            self.editor.image_y.val(self.options.y);
		            self.editor.image_width.val(self.options.width);
		            self.editor.image_height.val(self.options.height);
	            // }

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
			url: '?page=xepan_commerce_designer_renderimage&cut_page=1',
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
				// increasing image everyt ime saved.. so commented below two lines
				// self.options.height = self.element.height();
				// self.options.width = self.element.width();

				// console.log("after image render");
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