xShop_Text_Editor = function(parent,component){
	var self = this;
	this.parent = parent;
	this.current_text_component = undefined;

	this.element = $('<div id="xshop-designer-text-editor" class="xshop-options-editor" style="display:block"> </div>').appendTo(this.parent);
	// add font_selection with preview
	this.font_selector = $('<select class="text-editor-font-family"></select>').appendTo(this.element);
	// get all fonts via ajax
	var base_url = component.designer_tool.options.base_url;
	var page_url = base_url;

	var font_list = component.designer_tool.font_family;

	// WebFont.load({
 //            google: {
 //                families: font_list
 //            },
 //            fontinactive: function(familyName, fvd) {
 //                console.log("Sorry " + familyName + " font family can't be loaded at the moment. Retry later.");
 //            },
 //            active: function() {
 //                // do some stuff with font   
 //                // $('#stuff').attr('style', "font-family:'Abel'");
 //                // var text = new fabric.Text("Text Here", {
 //                //     left: 200,
 //                //     top: 30,
 //                //     fontFamily: 'Abel',
 //                //     fill: '#000',
 //                //     fontSize: 60
 //                // });

 //                // canvas.add(text);
 //            }
 //        });

	$.each(font_list,function(index,value){
		$('<option style="font-family:'+value+'" value="'+value+'">'+value+'</option>').appendTo(self.font_selector);
	});

	$(this.font_selector).change(function(event){
		design_dirty = true;

		self.current_text_component.options.font = $(this).val();
		$('.xshop-designer-text-input').css('font-family',$(this).val());
		// $('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});
	
	// font size
	this.font_size = $('<select class="text-editor-font-size"></select>').appendTo(this.element);

	$.each(component.designer_tool.pointtopixel,function(point,pixel){
		$('<option value="'+pixel+'">'+point+'</option>').appendTo(self.font_size);
	});

	// for (var i = 0; i < component.designer_tool.pointtopixel.length(); i++) {

	// };

	$(this.font_size).change(function(event){
		design_dirty = true;

		self.current_text_component.options.font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
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
		design_dirty = true;

		if(!self.current_text_component.options.bold){
			$(this).addClass('active');
			self.current_text_component.options.bold = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.bold = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});

	/*Italic Text Render*/
	$(this.text_italic_btn).click(function(event){
		design_dirty = true;

		if(!self.current_text_component.options.italic){
			$(this).addClass('active');
			self.current_text_component.options.italic = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.italic = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});

	//Underline Text
	$(this.text_underline_btn).click(function(event){
		design_dirty = true;

		self.current_text_component.options.stokethrough = false;
		if(!self.current_text_component.options.underline){
			$(this).addClass('active');
			self.current_text_component.options.underline = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.underline = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});
	
	//Stroke Through
	$(this.text_strokethrough_btn).click(function(event){
		
		design_dirty = true;

		self.current_text_component.options.underline = false;
		if(!self.current_text_component.options.stokethrough){
			$(this).addClass('active');
			self.current_text_component.options.stokethrough = true;
		}else{
			$(this).removeClass('active');
			self.current_text_component.options.stokethrough = false;
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});

	//Text Duplicate
	this.text_duplicate_btn.click(function(event){

		design_dirty = true;
		// self.current_selected_component = undefined;
		// create new TextComponent type object
		// console.log("old one ");
		// console.log(self.current_text_component);
		var new_text = new Text_Component();
		new_text.init(self.current_text_component.designer_tool,self.current_text_component.canvas, self.current_text_component.editor);
		
		// new_text.options = self.current_text_component.options;
		// if directly do same as above line then both object new and old refere to same option so setting option for new compomenent by each
		$.each(self.current_text_component.options,function(index,value){
			new_text.options[index] = value;
		});
		new_text.options.movable = true;
		new_text.options.x = 5;
		new_text.options.y = 5;
		// // // feed default values for its parameters
		// // // add this Object to canvas components array
		// // // console.log(self.designer_tool.current_page);

		self.current_text_component.designer_tool.pages_and_layouts[self.current_text_component.designer_tool.current_page][self.current_text_component.designer_tool.current_layout].components.push(new_text);
		new_text.render(self.designer_tool);
		// self.current_text_component = new_text;
		// $('.ui-selected').removeClass('ui-selected');
	 	//$(self).addClass('ui-selected');

		// console.log("new one ");
		// console.log(new_text);
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
		design_dirty = true;

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
		self.current_text_component.render(self.designer_tool);
	});
	
	//RIGHT Text Alignment
	$(this.text_align_right_btn).click(function(){
		design_dirty = true;

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
		self.current_text_component.render(self.designer_tool);
	});

	//CENTER Text Alignment
	$(this.text_align_center_btn).click(function(){
		design_dirty = true;

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
		self.current_text_component.render(self.designer_tool);
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
		design_dirty = true;

		self.current_text_component.options.indent_left != self.current_text_component.options.indent_left;
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});

	// Angle
	this.text_button_set = $('<div class="btn-group btn-group-xs" role="group" aria-label="Text Rotate"></div>').appendTo(this.element);
	this.text_rotate_angle_label = $('<div><label for="xshop-designer-text-rotate"></label></div>').appendTo(this.text_button_set);
	this.text_rotate_angle = $('<input name="angle" type="number" id="xshop-designer-text-angle" class="xshop-designer-text-input-angle"  />').appendTo(this.text_rotate_angle_label);
	$(this.text_rotate_angle).change(function(){
		design_dirty = true;
		// self.current_text_component.options.x = self.current_text_component.designer_tool.screen2option($(this).val());
		self.current_text_component.options.rotation_angle = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_text_component.render(self.designer_tool);
	});

	// this.text_rotate_clockwise_btn = $('<div class="btn"><span class="glyphicon glyphicon-repeat" style="-moz-transform: scaleX(-1);-o-transform: scaleX(-1);-webkit-transform: scaleX(-1);transform: scaleX(-1);filter: FlipH;-ms-filter:FlipH;"></span></div>').appendTo(this.text_button_set);
	// this.text_rotate_anticlockwise_btn = $('<div class="btn"><span class="glyphicon glyphicon-repeat"></span></div>').appendTo(this.text_button_set);

	//Rotation AntiClockWise Difference with -5 deg
	// $(this.text_rotate_anticlockwise_btn).click(function(event){
	// 	var angle_rotate = self.current_text_component.options.rotation_angle;
	// 	if(angle_rotate==0){
	// 		$(this).removeClass('active');
	// 		angle_rotate = 360;
	// 	}else{
	// 		$(this).addClass('active');
	// 	}
	// 	self.current_text_component.options.rotation_angle = angle_rotate-5;
	// 	$('.xshop-designer-tool').xepan_xshopdesigner('check');
	// 	self.current_text_component.render(self.designer_tool);

	// });

	//Rotation ClockWise Difference with +5 deg
	// $(this.text_rotate_clockwise_btn).click(function(event){
	// 	var angle_rotate = self.current_text_component.options.rotation_angle;
	// 	if(angle_rotate==360){
	// 		$(this).removeClass('active');
	// 		angle_rotate = 0;
	// 	}else{
	// 		$(this).addClass('active');
	// 	}
	// 	self.current_text_component.options.rotation_angle = angle_rotate+5;
	// 	$('.xshop-designer-tool').xepan_xshopdesigner('check');
	// 	self.current_text_component.render(self.designer_tool);		
	// });

	//Send to Back and Bring to Front
	this.text_up_down = $('<div class="btn xshop-designer-text-up-down-btn"></div>').appendTo(this.element);
	this.text_up = $('<span class="xshop-designer-text-up-btn icon-angle-circled-up atk-size-mega xshop-designer-text-up-btn" title="Bring to Front" ></span>').appendTo(this.text_up_down);
	this.text_down = $('<span class="xshop-designer-text-down-btn icon-angle-circled-down atk-size-mega xshop-designer-text-up-btn" title="Send to Back" ></span>').appendTo(this.text_up_down);
	
	//Bring To Front
	this.text_up.click(function(){
		design_dirty = true;

		var component_count = self.current_text_component.designer_tool.canvasObj.getObjects().length;
		current_text = self.current_text_component.element;
		var zin = parseInt(self.current_text_component.options.zindex);
		if(component_count > zin)
			zin =  zin+1;

		current_text.moveTo(zin);
		// var zin = self.current_text_component.designer_tool.canvasObj.getObjects().indexOf(current_text);
		self.current_text_component.options.zindex = zin;
		// console.log("Front "+zin);
	});

	//Send to Back
	this.text_down.click(function(){
		design_dirty = true;
		current_text = self.current_text_component.element;
		var zin = parseInt(self.current_text_component.options.zindex) - 1;
		if(zin < 1 )
			zin = 1;

		current_text.moveTo(zin);
		// var zin = self.current_text_component.designer_tool.canvasObj.getObjects().indexOf(current_text);
		self.current_text_component.options.zindex = zin;
		// console.log("Back "+zin);
	});



	// Color
	this.text_color_picker = $('<input id="xshop-colorpicker-full" type="text">').appendTo(this.element);
	$(this.text_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage:"vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        // parts:  [   'header', 'map', 'bar', 'hex',
        //          	'preview',
        //             'swatches', 'footer'
        //         ],
        ok: function(event, color){
        	// console.log(color);
        	design_dirty = true;
        	self.current_text_component.options.color_cmyk = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_text_component.options.color_formatted = '#'+color.formatted;
        	self.current_text_component.render(self.designer_tool);
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        }
	});
	
	//Remove BTN
	this.text_remove = $('<div class="btn"><span class="glyphicon glyphicon-trash"></span></div>').appendTo(this.element);
	this.text_remove.click(function(){
		design_dirty = true;
		dt  = self.current_text_component.designer_tool;
		$.each(dt.pages_and_layouts[dt.current_page][dt.current_layout].components, function(index,cmp){
			
			if(cmp === dt.current_selected_component){
				$(dt.current_selected_component.element).remove();
				dt.pages_and_layouts[dt.current_page][dt.current_layout].components.splice(index,1);
				dt.canvasObj.getActiveObject().remove();
				dt.option_panel.hide();
				dt.current_selected_component = null;
				return true;
			}
		});
	});

	this.editor_close_btn = $('<div class="xshop-designer-tool-editor-option-close"><i class="atk-box atk-box-small pull-right glyphicon glyphicon-remove"></i></div>').appendTo(this.element);
	this.editor_close_btn.click(function(event){
		self.element.hide();
	});

	div = $('<div class="xshop-designer-text-input-outer-div" ></div>').appendTo(this.element);
	this.text_input = $('<textarea class="xshop-designer-text-input" rows="1" autofocus></textarea>').appendTo(div);

	$(this.text_input).delayKeyup(function(el){
		design_dirty = true;
		self.current_text_component.options.text = $(el).val();
		if(self.current_text_component.designer_tool.options.designer_mode){
			self.current_text_component.options.default_value= $(el).val();
		}
		self.current_text_component.options.text = $(el).val();
		self.current_text_component.render(self.designer_tool);

		if(self.current_text_component.options.text_label != undefined){
			cookie_json = {};
			if($.cookie('xepan-designer-cookiedata') != undefined)
				cookie_json =  JSON.parse($.cookie('xepan-designer-cookiedata'));			
			cookie_json[self.current_text_component.options.text_label] = $(el).val();
			$.cookie('xepan-designer-cookiedata', JSON.stringify(cookie_json));
		}
	},1);

	this.row1 = $('<div class="atk-row xshop-designer-tool-editing-helper text" style="display:block;margin:0;"> </div>').appendTo(this.element);

	this.text_x_label = $('<div class="atk-move-left"><label for="xshop-designer-text-positionx">x: </label></div>').appendTo(this.row1);
	this.text_x = $('<input name="x" id="xshop-designer-text-positionx" class="xshop-designer-text-inputx"  />').appendTo(this.text_x_label);
	$(this.text_x).change(function(){
		design_dirty = true;
		// self.current_text_component.options.x = self.current_text_component.designer_tool.screen2option($(this).val());
		self.current_text_component.options.x = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_text_component.render(self.designer_tool);
	});
	this.text_y_label = $('<div class="atk-move-left"><label for="xshop-designer-text-positiony">y: </label></div>').appendTo(this.row1);
	this.text_y = $('<input name="y" id="xshop-designer-text-positiony" class="xshop-designer-text-inputy"  />').appendTo(this.text_y_label);
	$(this.text_y).change(function(){
		design_dirty = true;
		// self.current_text_component.options.y = self.current_text_component.designer_tool.screen2option($(this).val());
		self.current_text_component.options.y = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_text_component.render(self.designer_tool);
	});

	this.text_label = $('<div class="atk-move-left"><label for="xshop-designer-text-label">label: </label></div>').appendTo(this.row1);
	this.text_label_input = $('<input name="label" id="xshop-designer-text-label" class="xshop-designer-text-label"  />').appendTo(this.text_label);
	$(this.text_label_input).change(function(){
		design_dirty = true;
		self.current_text_component.options.text_label = $(this).val();
		// $('.xshop-designer-tool').xepan_xshopdesigner('check');
		// self.current_text_component.render(self.designer_tool);
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
		if(!this.current_text_component.options.editable){
			this.text_input.hide();
			this.text_remove.hide();
		}else{
			this.text_input.show();
			this.text_remove.show();
		}

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
		// $(this.text_rotate_anticlockwise_btn).removeClass('active');
		// if( component.options.rotation_angle == '0'){
		// 	$(this.text_rotate_clockwise_btn).removeClass('active');
		// }else
		// 	$(this.text_rotate_clockwise_btn).addClass('active');
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
		width:100,
		height:100,
		text:'Enter Text',
		font: "OpenSans",
		font_size: '20',
		color_cmyk:"0,0,0,100",
		color_formatted:"#000000",
		bold: false,
		italic:false,
		underline:false,
		stokethrough:false,
		rotation_angle:0,
		locked: false,
		alignment_left:true,
		alignment_center:false,
		alignment_right:false,
		alignment_justify:false,
		// Designer properties
		movable: true,
		resizable: true,
		colorable: true,
		editable: true,
		default_value:'Enter Text',
		zindex:1,
		auto_fit: false,
		frontside:true,
		backside:false,
		multiline: false,
		// System properties
		type: 'Text',
		base_url:undefined,
		page_url:undefined,
		text_label:undefined
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
			design_dirty = true;
			self.designer_tool.current_selected_component = undefined;
			// create new TextComponent type object
			var new_text = new Text_Component();
			new_text.init(self.designer_tool,self.canvas, self.editor);
			// feed default values for its parameters
			// add this Object to canvas components array
			// console.log(self.designer_tool.current_page);

			self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].components.push(new_text);
			new_text.render(self.designer_tool);
		});

		var idx = $.inArray("Text", self.designer_tool.options.ComponentsIncludedToBeShow);
		if (idx == -1) {
			$(tool_btn).remove();
		}

	}

	this.render = function(designer_tool_obj){
		// text:self.options.text,
		// color: self.options.color_formatted,
		// font: self.options.font,
		// font_size: self.options.font_size,
		// bold: self.options.bold,
		// italic: self.options.italic,
		// underline:self.options.underline,
		// rotation_angle:self.options.rotation_angle,
		// alignment_left:self.options.alignment_left,
		// alignment_right:self.options.alignment_right,
		// alignment_center:self.options.alignment_center,
		// alignment_justify:self.options.alignment_justify,
		// zoom: self.designer_tool.zoom,
		// stokethrough:self.options.stokethrough,
		// width: self.options.width,
		// zindex:self.options.zindex
		var self = this;
		if(designer_tool_obj) self.designer_tool = designer_tool_obj;

		if(!self.designer_tool.isSavedDesign() && !self.designer_tool.options.designer_mode){
			if($.cookie('xepan-designer-cookiedata') != undefined && (self.options.text_label != undefined && self.options.text_label != null && self.options.text_label != "")){
				cookie_json =  JSON.parse($.cookie('xepan-designer-cookiedata'));
				self.options.text = cookie_json[self.options.text_label];
			}
		}

		if(this.element){
			this.element.set({
				text: self.options.text,
				left: self.options.x * self.designer_tool._getZoom(), 
				top: self.options.y * self.designer_tool._getZoom(),
				// width: self.options.width * self.designer_tool._getZoom(),
				fontSize: self.options.font_size,
				fontFamily: self.options.font,
				fontWeight: self.options.bold ? 'bold':'normal',
				textDecoration: self.options.underline?'underline': self.options.stokethrough ? 'line-through':null,
				// lockUniScaling : true,
				// scaleX : self.designer_tool._getZoom(),
				// scaleY : self.designer_tool._getZoom(),
				fill: self.options.color_formatted,
				textAlign: self.options.alignment_right?'right': self.options.alignment_center? 'center': self.options.alignment_justify?'justify':'left',
				fontStyle: self.options.italic?'italic':'normal',
				angle: self.options.rotation_angle
			});
			
			self.designer_tool.canvasObj.renderAll();
			
			self.designer_tool.updateModifications();
			
			return;
		}

		if(self.options.base_url == undefined){
			self.options.base_url = self.designer_tool.options.base_url;
			self.options.page_url = self.designer_tool.options.base_url;
		}

		// console.log(self.options);
		// console.log(self.designer_tool._toPixel(self.options.x));
		// console.log(self.options.rotation_angle+ " == "+self.options.angle);

		var text = new fabric.Textbox(self.options.text, { 
			left: self.options.x * self.designer_tool._getZoom(), 
			top: self.options.y * self.designer_tool._getZoom(),
			width: self.options.width,
			fontSize: self.options.font_size,
			fontFamily: self.options.font,
			fontWeight: self.options.bold ? 'bold':'normal',
			textDecoration: self.options.underline?'underline': self.options.stokethrough ? 'line-through':null,
			scaleX : self.designer_tool._getZoom(),
			scaleY : self.designer_tool._getZoom(),
			fill: self.options.color_formatted,
			textAlign: self.options.alignment_right?'right': self.options.alignment_center? 'center': self.options.alignment_justify?'justify':'left',
			fontStyle: self.options.italic?'italic':'normal',
			angle:self.options.rotation_angle,
			lockScalingX: false,
			lockScalingY: true,
			editable: false

		});

		text.setControlsVisibility({
		    mt: true, // middle top disable
		    mb: true, // midle bottom
		    ml: true, // middle left
		    mr: true, // I think you get it
		    mtr: false
		});

		if(!this.options.movable){
			text.set({
				lockMovementX: true,
				lockMovementY: true,
				lockRotation: true
			});
		}

		if(!this.options.resizable){
			text.set({
				lockScalingX: true,
				lockScalingY: true,
			});
		}


		text.on('selected', function(e){
	        $('.xshop-options-editor').hide();
	        self.editor.element.show();

	        self.designer_tool.option_panel.fadeIn('300',function(){
	        	$('.xshop-designer-text-input').focus();
	        	$('.xshop-designer-text-input').css('font-family',self.options.font);
	        });

	        self.designer_tool.option_panel.css('z-index',7000);
	        self.designer_tool.option_panel.addClass('xshop-text-options');
	        
	        self.designer_tool.option_panel.offset(
	        							{
	        								top:self.designer_tool.canvasObj._offset.top + text.top - self.designer_tool.option_panel.height() - (10 * self.designer_tool._getZoom()),
	        								left:self.designer_tool.canvasObj._offset.left + text.left
	        							}
	        						);


	        if(!self.designer_tool.options.designer_mode){
					self.editor.text_x.hide();
					self.editor.text_x_label.hide();
					self.editor.text_y.hide();
					self.editor.text_y_label.hide();
					self.editor.text_label.hide();
				}else{
					self.editor.text_x.val(self.options.x);
					self.editor.text_y.val(self.options.y);
					// console.log(self.options.text_label);
					self.editor.text_label_input.val(self.options.text_label);
				}


	        self.editor.setTextComponent(self);
	        
	        if(self.designer_tool.options.designer_mode){
	            self.designer_tool.freelancer_panel.FreeLancerComponentOptions.element.show();
	            self.designer_tool.freelancer_panel.setComponent(self.designer_tool.current_selected_component);
	        }
	        
	        if (e.stopPropagation) {
		      e.stopPropagation();
		    }
		    //IE8 and Lower
		    else {
		      e.cancelBubble = true;
		    }
	    	
	    	// check For the Z-index
	    	if(self.options.zindex == 0){
	    		$('span.xshop-designer-text-down-btn').addClass('xepan-designer-button-disable');
	    	}else
	    		$('span.xshop-designer-text-down-btn').removeClass('xepan-designer-button-disable');
		});
		this.element = text;
		this.element.component = self;
		self.designer_tool.canvasObj.add(text);
	}
}