xShop_Text_Editor = function(parent,component){
	var self = this;
	this.parent = parent;
	this.current_text_component = undefined;

	this.element = $('<div id="xshop-designer-text-editor" class="xshop-options-editor" style="display:block"> </div>').appendTo(this.parent);
	// add font_selection with preview
	this.font_selector = $('<select class="btn btn-xs"></select>').appendTo(this.element);
	// get all fonts via ajax
	var base_url = component.designer_tool.options.base_url;
	var page_url = base_url;

	$.ajax({
		url: page_url+'?page=xepan_commerce_designer_fonts',
		type: 'GET',
		data: {param1: 'value1'},
	})
	.done(function(ret) {
		$(ret).appendTo(self.font_selector);
		// console.log("success");
	})
	.fail(function() {
		// console.log("error");
	})
	.always(function() {
		// console.log("complete");
	});

	$(this.font_selector).change(function(event){
		self.current_text_component.options.font = $(this).val();
		// $('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});
	
	// font size
	this.font_size = $('<select class="btn btn-xs"></select>').appendTo(this.element);

	for (var i = 7; i < 50; i++) {
		$('<option value="'+i+'">'+i+'</option>').appendTo(this.font_size);
	};

	$(this.font_size).change(function(event){
		self.current_text_component.options.font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});

	// B/I/U
	this.text_button_set = $('<div class="btn-group btn-group-xs" role="group" aria-label="Bold/Italic/Underline"></div>').appendTo(this.element);
	this.text_bold_btn = $('<div class="btn"><span class="glyphicon glyphicon-bold"></span></div>').appendTo(this.text_button_set);
	this.text_italic_btn = $('<div class="btn"><span class="glyphicon glyphicon-italic"></span></div>').appendTo(this.text_button_set);
	this.text_underline_btn = $('<div class="btn"><span class="icon-underline"></span></div>').appendTo(this.text_button_set);
	this.text_strokethrough_btn = $('<div class="btn" style="display:none;"><span class="icon-strike"></span></div>').appendTo(this.text_button_set);
	this.text_duplicate_btn = $('<div class="btn" title="Duplicate selected text"><span class="icon-docs"></span></div>').appendTo(this.text_button_set);
	/*Bold Text Render*/
	$(this.text_bold_btn).click(function(event){
		if(!self.current_text_component.options.bold){
			$(this).addClass('active');
			self.current_text_component.options.bold = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.bold = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});

	/*Italic Text Render*/
	$(this.text_italic_btn).click(function(event){
		if(!self.current_text_component.options.italic){
			$(this).addClass('active');
			self.current_text_component.options.italic = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.italic = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});

	//Underline Text
	$(this.text_underline_btn).click(function(event){
		self.current_text_component.options.stokethrough = false;
		if(!self.current_text_component.options.underline){
			$(this).addClass('active');
			self.current_text_component.options.underline = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.underline = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});
	
	//Stroke Through
	$(this.text_strokethrough_btn).click(function(event){
		self.current_text_component.options.underline = false;
		if(!self.current_text_component.options.stokethrough){
			$(this).addClass('active');
			self.current_text_component.options.stokethrough = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.stokethrough = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});

	//Text Duplicate
	this.text_duplicate_btn.click(function(event){
		// self.current_selected_component = undefined;
		// create new TextComponent type object
		var new_text = new Text_Component();
		new_text.init(self.current_text_component.designer_tool,self.current_text_component.canvas, self.current_text_component.editor);
		new_text.options = self.current_text_component.options;
		// // // feed default values for its parameters
		// // // add this Object to canvas components array
		// // // console.log(self.designer_tool.current_page);

		self.current_text_component.designer_tool.pages_and_layouts[self.current_text_component.designer_tool.current_page][self.current_text_component.designer_tool.current_layout].components.push(new_text);
		new_text.render(true);
		self.current_text_component = new_text;
		// console.log(self.current_text_component.designer_tool);
	});



	// L/M/R/J align
	this.text_button_set = $('<div class="btn-group btn-group-xs" role="group" aria-label="Text Alignment"></div>').appendTo(this.element);
	this.text_align_left_btn = $('<div class="btn"><span class="glyphicon glyphicon-align-left"></span></div>').appendTo(this.text_button_set);
	this.text_align_center_btn = $('<div class="btn"><span class="glyphicon glyphicon-align-center"></span></div>').appendTo(this.text_button_set);
	this.text_align_right_btn = $('<div class="btn"><span class="glyphicon glyphicon-align-right"></span></div>').appendTo(this.text_button_set);
	// this.text_align_justify_btn = $('<div class="btn"><span class="glyphicon glyphicon-align-justify"></div>').appendTo(this.text_button_set);
	
	//LEFT Text Alignment
	$(this.text_align_left_btn).click(function(){
		if(!self.current_text_component.options.alignment_left){
			$(this).addClass('active');
			self.current_text_component.options.alignment_left = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.alignment_left = false;
		}

		$(self.text_align_center_btn).removeClass('active');
		self.current_text_component.options.alignment_center = false;

		$(self.text_align_justify_btn).removeClass('active');
		self.current_text_component.options.alignment_justify = false;
		
		$(self.text_align_right_btn).removeClass('active');
		self.current_text_component.options.alignment_right = false;
		
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});
	
	//RIGHT Text Alignment
	$(this.text_align_right_btn).click(function(){
		if(!self.current_text_component.options.alignment_right){
			$(this).addClass('active');
			self.current_text_component.options.alignment_right = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.alignment_right = false;
		}

		$(self.text_align_left_btn).removeClass('active');
		self.current_text_component.options.alignment_left = false;
		$(self.text_align_justify_btn).removeClass('active');
		self.current_text_component.options.alignment_justify = false;
		$(self.text_align_center_btn).removeClass('active');
		self.current_text_component.options.alignment_center = false;
		
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});

	//CENTER Text Alignment
	$(this.text_align_center_btn).click(function(){
		if(!self.current_text_component.options.alignment_center){
			$(this).addClass('active');
			self.current_text_component.options.alignment_center = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.alignment_center = false;
		}

		$(self.text_align_left_btn).removeClass('active');
		self.current_text_component.options.alignment_left = false;

		$(self.text_align_justify_btn).removeClass('active');
		self.current_text_component.options.alignment_justify = false;
		
		$(self.text_align_right_btn).removeClass('active');
		self.current_text_component.options.alignment_right = false;

		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});


	//Text Justify Alignmemt
	// $(this.text_align_justify_btn).click(function(){
	// 	if(!self.current_text_component.options.alignment_justify){
	// 		$(this).addClass('active');
	// 		self.current_text_component.options.alignment_justify = true;
	// 	}else{
	// 		$(this).removeClass('active');
	// 		self.current_text_component.options.alignment_justify = false;
	// 	}

	// 	self.current_text_component.options.alignment_left = false;
	// 	self.current_text_component.options.alignment_center = false;
	// 	self.current_text_component.options.alignment_right = false;
	// 	$('.xshop-designer-tool').xepan_xshopdesigner('check');
	// 	self.current_text_component.render();
	// });
	//Ordered List
	this.text_button_set = $('<div class="btn-group btn-group-xs" role="group" aria-label="Orderd List"></div>').appendTo(this.element);
	// this.text_order_list_ul_btn = $('<div class="btn"><span class="glyphicon glyphicon-list"></span></div>').appendTo(this.text_button_set);
	// this.text_indent_left_btn = $('<div class="btn"><span class="glyphicon glyphicon-indent-left"></span></div>').appendTo(this.text_button_set);
	// this.text_indent_right_btn = $('<div class="btn"><span class="glyphicon glyphicon-indent-right"></div>').appendTo(this.text_button_set);
	// this.text_symbol_btn = $('<div class="btn"><span class="glyphicon glyphicon-plus"></div>').appendTo(this.text_button_set);
	
	// Text Indent Left
	$(this.text_indent_left_btn).click(function(){
		self.current_text_component.options.indent_left != self.current_text_component.options.indent_left;
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();
	});

	// Angle
	this.text_button_set = $('<div class="btn-group btn-group-xs" role="group" aria-label="Text Alignment"></div>').appendTo(this.element);
	this.text_rotate_anticlockwise_btn = $('<div class="btn"><span class="glyphicon glyphicon-repeat" style="-moz-transform: scaleX(-1);-o-transform: scaleX(-1);-webkit-transform: scaleX(-1);transform: scaleX(-1);filter: FlipH;-ms-filter: "FlipH";"></span></div>').appendTo(this.text_button_set);
	this.text_rotate_clockwise_btn = $('<div class="btn"><span class="glyphicon glyphicon-repeat"></span></div>').appendTo(this.text_button_set);

	//Rotation AntiClockWise Difference with -5 deg
	$(this.text_rotate_anticlockwise_btn).click(function(event){
		var angle_rotate = self.current_text_component.options.rotation_angle;
		if(angle_rotate==0){
			$(this).removeClass('active');
			angle_rotate = 360;
		}else{
			$(this).addClass('active');
		}
		self.current_text_component.options.rotation_angle = angle_rotate-5;
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();

	});

	//Rotation ClockWise Difference with +5 deg
	$(this.text_rotate_clockwise_btn).click(function(event){
		var angle_rotate = self.current_text_component.options.rotation_angle;
		if(angle_rotate==360){
			$(this).removeClass('active');
			angle_rotate = 0;
		}else{
			$(this).addClass('active');
		}
		self.current_text_component.options.rotation_angle = angle_rotate+5;
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render();		
	});

	//Send to Back and Bring to Front
	this.text_up_down = $('<div class="btn xshop-designer-text-up-down-btn"></div>').appendTo(this.element);
	this.text_up = $('<span class="xshop-designer-text-up-btn icon-angle-circled-up atk-size-mega xshop-designer-text-up-btn" title="Bring to Front" ></span>').appendTo(this.text_up_down);
	this.text_down = $('<span class="xshop-designer-text-down-btn icon-angle-circled-down atk-size-mega xshop-designer-text-up-btn" title="Send to Back" ></span>').appendTo(this.text_up_down);
	
	//Bring To Front
	this.text_up.click(function(){
		current_text = $(self.current_text_component.element);
		current_zindex = current_text.css('z-index');
		if( current_zindex == 'auto'){
			current_zindex = 0;
		}
		current_text.css('z-index', parseInt(current_zindex)+1);
		self.current_text_component.options.zindex = current_text.css('z-index');
		if($('span.xshop-designer-text-down-btn').hasClass('xepan-designer-button-disable')){
			$('span.xshop-designer-text-down-btn').removeClass('xepan-designer-button-disable');
		}
	});

	//Send to Back
	this.text_down.click(function(){
		current_text = $(self.current_text_component.element);
		current_zindex = current_text.css('z-index');
		if( current_zindex == 'auto' || (parseInt(current_zindex)-1) < 0){
			current_zindex = 0;
		}else 
			current_zindex = (parseInt(current_zindex)-1);

		current_text.css('z-index', current_zindex);
		self.current_text_component.options.zindex = current_zindex;
		if(current_zindex == 0 ){
			// console.log($('span.xshop-designer-text-down-btn'));
			$('span.xshop-designer-text-down-btn').addClass('xepan-designer-button-disable');
		}
	});


	// Color
	this.text_color_picker = $('<input id="xshop-colorpicker-full" type="text" style="display:block">').appendTo(this.element);
	$(this.text_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage:base_url,
        ok: function(event, color){
        	// console.log(color);
        	self.current_text_component.options.color_cmyk = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_text_component.options.color_formatted = '#'+color.formatted;
        	self.current_text_component.render();
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        }
	});
	
	//Remove BTN
	this.text_remove = $('<div class="btn"><span class="glyphicon glyphicon-trash"></span></div>').appendTo(this.element);
	this.text_remove.click(function(){
		dt  = self.current_text_component.designer_tool;
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

	this.editor_close_btn = $('<div class="" style="padding:0;margin:0;position:absolute ;top:-25px;right:0;"><i class="atk-box atk-box-small pull-right glyphicon glyphicon-remove"></i></div>').appendTo(this.element);
	this.editor_close_btn.click(function(event){
		self.element.hide();
	});

	div = $('<div class="xshop-designer-text-input-outer-div" ></div>').appendTo(this.element);
	this.text_input = $('<textarea class="xshop-designer-text-input" rows="1"></textarea>').appendTo(div);

	$(this.text_input).delayKeyup(function(el){
		self.current_text_component.options.text = $(el).val();
		if(self.current_text_component.designer_tool.options.designer_mode){
			self.current_text_component.options.default_value= $(el).val();
		}
		self.current_text_component.options.text= $(el).val();
		self.current_text_component.render();
	},500);

	this.row1 = $('<div class="atk-row" style="display:block;margin:0;"> </div>').appendTo(this.element);

	this.text_x_label = $('<div class="atk-move-left"><label for="xshop-designer-text-positionx">x: </label></div>').appendTo(this.row1);
	this.text_x = $('<input name="x" id="xshop-designer-text-positionx" class="xshop-designer-text-inputx"  />').appendTo(this.text_x_label);
	$(this.text_x).change(function(){
		self.current_text_component.options.x = self.current_text_component.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_text_component.render();
	});
	this.text_y_label = $('<div class="atk-move-left"><label for="xshop-designer-text-positiony">y: </label></div>').appendTo(this.row1);
	this.text_y = $('<input name="y" id="xshop-designer-text-positiony" class="xshop-designer-text-inputy"  />').appendTo(this.text_y_label);
	$(this.text_y).change(function(){
		self.current_text_component.options.y = self.current_text_component.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_text_component.render();
	});

	this.setTextComponent = function(component){
		this.current_text_component  = component;
		$(this.font_size).val(component.options.font_size);
		$(this.font_selector).val(component.options.font);
		$(this.text_color_picker).val(component.options.color_formatted);
		$(this.text_color_picker).colorpicker('setColor',component.options.color_formatted);
		
		if(!this.current_text_component.options.colorable)
			this.text_color_picker.next('button').hide();
		this.text_input.val(this.current_text_component.options.text);
		if(!this.current_text_component.options.editable)
			this.text_input.hide();

		//Alignment Center
		( component.options.alignment_center == true) ? $(this.text_align_center_btn).addClass('active') : $(this.text_align_center_btn).removeClass('active');
		
		//Alignment left
		( component.options.alignment_left == true) ? $(this.text_align_left_btn).addClass('active') : $(this.text_align_left_btn).removeClass('active');

		//Alignment Right
		( component.options.alignment_right == true) ? $(this.text_align_right_btn).addClass('active') : $(this.text_align_right_btn).removeClass('active');

		//Bold 
		( component.options.bold == true) ? $(this.text_bold_btn).addClass('active') : $(this.text_bold_btn).removeClass('active');
		
		//Italic 
		( component.options.italic == true) ? $(this.text_italic_btn).addClass('active') : $(this.text_italic_btn).removeClass('active');

		//underline 
		( component.options.underline == true) ? $(this.text_underline_btn).addClass('active') : $(this.text_underline_btn).removeClass('active');

		//strokethrough
		( component.options.stokethrough == true) ? $(this.text_stokethrough_btn).addClass('active') : $(this.text_stokethrough_btn).removeClass('active');

		//Angle
		$(this.text_rotate_anticlockwise_btn).removeClass('active');
		if( component.options.rotation_angle == '0'){
			$(this.text_rotate_clockwise_btn).removeClass('active');
		}else
			$(this.text_rotate_clockwise_btn).addClass('active');
	}

}

Text_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;
	this.xhr = undefined;

	this.options = {
		x:0,
		y:0,
		width:'100',
		height:'100',
		text:'Enter Text',
		font: "OpenSans",
		font_size: '12',
		color_cmyk:"0,0,0,100",
		color_formatted:"#000000",
		bold: false,
		italic:false,
		underline:false,
		stokethrough:false,
		rotation_angle:0,
		locked: false,
		alignment_left:false,
		alignment_center:false,
		alignment_right:false,
		alignment_justify:true,
		// Designer properties
		movable: true,
		colorable: true,
		editable: true,
		default_value:'Enter Text',
		zindex:0,
		resizable: true,
		auto_fit: false,
		frontside:true,
		backside:false,
		multiline: false,
		// System properties
		type: 'Text',
		base_url:undefined,
		page_url:undefined
	};

	this.init = function(designer,canvas, editor){
		this.designer_tool = designer;
		this.canvas = canvas;
		if(editor !== undefined)
			this.editor = editor;

		this.options.base_url = designer.options.base_url;
		this.options.page_url = designer.options.base_url;

	}

	this.renderTool = function(parent){
		var self=this;
		this.parent = parent;
		tool_btn = $('<div class="btn btn-deault"><i class="glyphicon glyphicon-text-height"></i><br>Text</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));
		this.editor = new xShop_Text_Editor(parent.find('.xshop-designer-tool-topbar-options'),self);

		// CREATE NEW TEXT COMPONENT ON CANVAS
		tool_btn.click(function(event){
			self.designer_tool.current_selected_component = undefined;
			// create new TextComponent type object
			var new_text = new Text_Component();
			new_text.init(self.designer_tool,self.canvas, self.editor);
			// feed default values for its parameters
			// add this Object to canvas components array
			// console.log(self.designer_tool.current_page);

			self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].components.push(new_text);
			new_text.render(true);
		});


	}

	this.render = function(place_in_center){
		var self = this;
		if(self.options.base_url == undefined){
			self.options.base_url = self.designer_tool.options.base_url;
			self.options.page_url = self.designer_tool.options.base_url;
		}

		if(this.element == undefined){
			this.element = $('<div style="position:absolute" class="xshop-designer-component"><span><img></img></span></div>').appendTo(this.canvas);
			this.element.draggable({
				containment: self.designer_tool.safe_zone,
				smartguides:".xshop-designer-component",
			    tolerance:5,
				stop:function(e,ui){
					var position = ui.position;
					self.options.x = self.designer_tool.screen2option(position.left);
					self.options.y = self.designer_tool.screen2option(position.top);
					
				}	
			}).resizable({
				containment: self.designer_tool.safe_zone,
				handles: "e",
				autoHide: true,
				stop: function(e,ui){
					self.options.width = self.designer_tool.screen2option(ui.size.width);
					self.render();
				}
			});
			;

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

			$(this.element).data('component',self);
			$(this.element).click(function(event) {
	            $('.ui-selected').removeClass('ui-selected');
	            $(this).addClass('ui-selected');
	            $('.xshop-options-editor').hide();
	            self.editor.element.show();
	            self.designer_tool.option_panel.fadeIn(500);
	            //For Auto Select Text Box
	            $('.xshop-designer-text-input').select();
	            self.designer_tool.current_selected_component = self;
	            self.designer_tool.option_panel.css('z-index',70);
	            self.designer_tool.option_panel.addClass('xshop-text-options');
	            self.designer_tool.option_panel.css('top',0);

	            designer_currentTarget = $(event.currentTarget);
	            height_diff = parseInt($(self.designer_tool.option_panel).height() + 10);
	            top_value = parseInt($(designer_currentTarget).offset().top) - parseInt(height_diff);

	            self.designer_tool.option_panel.css('top',top_value);
	            self.designer_tool.option_panel.css('left',$(designer_currentTarget).offset().left);
	            if(!self.designer_tool.options.designer_mode){
						self.editor.text_x.hide();
						self.editor.text_x_label.hide();
						self.editor.text_y.hide();
						self.editor.text_y_label.hide();
					}else{
						self.editor.text_x.val(self.options.x);
						self.editor.text_y.val(self.options.y);
					}


	            self.editor.setTextComponent(self);
	            
	            if(self.designer_tool.options.designer_mode){
		            self.designer_tool.freelancer_panel.FreeLancerComponentOptions.element.show();
		            self.designer_tool.freelancer_panel.setComponent($(this).data('component'));
	            }
		        event.stopPropagation();
	        	
	        	//check For the Z-index
            	if(self.options.zindex == 0){
            		$('span.xshop-designer-text-down-btn').addClass('xepan-designer-button-disable');
            	}else
            		$('span.xshop-designer-text-down-btn').removeClass('xepan-designer-button-disable');
			});
		}else{
			this.element.show();
		}

		this.element.css('top',self.options.y  * self.designer_tool.zoom);
		this.element.css('left',self.options.x * self.designer_tool.zoom);
		// this.element.find('img').width((this.element.find('img').width() * self.designer_tool.delta_zoom /100));
		// this.element.find('img').height((this.element.find('img').height() * self.designer_tool.delta_zoom/100));

		if(this.xhr != undefined)
			this.xhr.abort();

		this.xhr = $.ajax({
			url: self.options.page_url+'index.php?page=xepan_commerce_designer_rendertext',
			type: 'GET',
			data: {default_value: self.options.default_value,
					text:self.options.text,
					color: self.options.color_formatted,
					font: self.options.font,
					font_size: self.options.font_size,
					bold: self.options.bold,
					italic: self.options.italic,
					underline:self.options.underline,
					rotation_angle:self.options.rotation_angle,
					alignment_left:self.options.alignment_left,
					alignment_right:self.options.alignment_right,
					alignment_center:self.options.alignment_center,
					alignment_justify:self.options.alignment_justify,
					zoom: self.designer_tool.zoom,
					stokethrough:self.options.stokethrough,
					width: self.options.width,
					zindex:self.options.zindex
					},
		})
		.done(function(ret) {
			self.element.find('img').attr('src','data:image/png;base64, '+ ret);
			// $(ret).appendTo(self.element.find('span').html(''));
			self.xhr=undefined;
			if(place_in_center === true){
				window.setTimeout(function(){
					// self.element.center(self.designer_tool.canvas);
					self.options.x = self.element.css('left').replace('px','') / self.designer_tool.zoom;
					self.options.y = self.element.css('top').replace('px','') / self.designer_tool.zoom;
				},200);
			}
		})
		.fail(function(ret) {
			// evel(ret);
			console.log("Text Error");
		})
		.always(function() {
			console.log("complete");
		});
		

		// this.element.text(this.text);
		// this.element.css('left',this.x);
		// this.element.css('top',this.y);
	}
}