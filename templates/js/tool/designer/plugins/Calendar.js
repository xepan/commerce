xShop_Calendar_Editor = function(parent,designer){
	var self = this;
	this.parent = parent;
	this.current_calendar_component = undefined;
	this.designer_tool = designer;

	font_list = [ 'Abel', 'Aclonica'];

	var base_url = this.designer_tool.options.base_url;
	var page_url = base_url;

	this.element = $('<div id="xshop-designer-calendar-editor" class="xshop-options-editor " style="display:block"> </div>').appendTo(this.parent);

	this.editor_close_btn = $('<div class="xshop-designer-tool-editor-option-close"><i class="atk-box atk-box-small pull-right glyphicon glyphicon-remove"></i></div>').appendTo(this.element);
	$(this.editor_close_btn).click(function(event){
		self.element.hide();
	});

  	this.vertical_tab_container = $('<div id="xepan-designer-vertical-tab" class="xepan-designer-calendar-designer-options-panel"></div>').appendTo(this.element);
  	this.vertical_tab = $('<ul></ul>').appendTo(this.vertical_tab_container);
  	
  	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------ Tabs -------------------------
	//___________________________________________________________________________|
	this.header_tab = $('<li><a href="#calendar-header-options"  class="xshop-calendar-editor-header">Header</a></li>').appendTo(this.vertical_tab);
	this.week_tab = $('<li><a href="#calendar-week-options"  class="xshop-calendar-editor-header">Week</a></li>').appendTo(this.vertical_tab);
	this.date_tab = $('<li><a href="#calendar-date-options"  class="xshop-calendar-editor-header">Date</a></li>').appendTo(this.vertical_tab);
	this.event_tab = $('<li><a href="#calendar-event-options"  class="xshop-calendar-editor-header">Event</a></li>').appendTo(this.vertical_tab);
	this.other_tab = $('<li><a href="#calendar-calendar-options"  class="xshop-calendar-editor-header">Calendar</a></li>').appendTo(this.vertical_tab);

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Header Style Options-------------------------
//___________________________________________________________________________|
	// header_font_size:16,
	this.header_options = $('<div id="calendar-header-options"> </div>').appendTo(this.vertical_tab_container);
	this.col1 = $('<div class="designer-tool-calendar-option"></div>').appendTo(this.header_options);
	
	this.header_font_size_label = $('<div title="Header Font Size"><label for="header_font_size">Font Size :</label></div>').appendTo(this.col1);
	this.header_align_label = $('<div title="Header Text Align"><label for="header_align">Text Align :</label></div>').appendTo(this.col1);
	this.header_align = $('<select id="header_align">Header Align</select>').appendTo(this.header_align_label);
	this.header_font_size = $('<select id="header_font_size">Header Size</select>').appendTo(this.header_font_size_label);
	
	align_options = '';
		align_options += '<option value="left" class="atk-move-left">left</option>';
		align_options += '<option value="center" class="atk-move-center">center</option>';
		align_options += '<option value="right" class="atk-move-right">right</option>';

	$(align_options).appendTo(this.header_align);
	
	$(this.header_align).change(function(event){
		self.current_calendar_component.options.header_align = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	$.each(this.designer_tool.pointtopixel,function(point,pixel){
		$('<option value="'+pixel+'">'+point+'</option>').appendTo(self.header_font_size);
	});
	$(this.header_font_size).change(function(event){
		self.current_calendar_component.options.header_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	//header font color: default Value Black
	this.header_color_label = $('<div title="Header Text Color" class="xshop-designer-calendar-color-picker"><label for="header_font_color">Text Color : </label></div>').appendTo(this.col1);
	this.header_color_picker = $('<input id="header_font_color" class="xepan-designer-calendar-color-picker">').appendTo(this.header_color_label);
	$(this.header_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	self.current_calendar_component.options.header_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        }
	});

	//Header Background Color
	this.header_bg_color_label = $('<div title="Header Background Color" class="xshop-designer-calendar-color-picker"><label for="header_bg_color">Background : </label></div>').appendTo(this.col1);
	this.header_bg_color_picker = $('<input id="header_bg_color" class="xepan-designer-calendar-color-picker">').appendTo(this.header_bg_color_label);
	$(this.header_bg_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	self.current_calendar_component.options.header_bg_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        }
	});

	//Header Bold
	// this.h_btn_set = $('<div class="btn-group btn-group-xs xshop-calendar-align" role="group" aria-label="Text Alignment"></div>').appendTo(this.col1);
	// this.h_bold = $('<div class="btn" title="Right"><span class="glyphicon glyphicon-bold"></span></div>').appendTo(this.h_btn_set);
	// $(this.h_bold).click(function(){
	// 	if($(this).hasClass('active')){
	// 		$(this).removeClass('active');
	// 		self.current_calendar_component.options.header_bold = "false";
	// 	}else{
	// 		$(this).addClass('active');
	// 		self.current_calendar_component.options.header_bold = "true";
	// 	}

	// 	//Render Current Selected Calendar
	// 	$('.xshop-designer-tool').xepan_xshopdesigner('check');
	// 	self.current_calendar_component.render(self.designer_tool);
	// });
	this.h_btn_set = $('<div title="Header Bold"><label for="xshop-designer-calendar-header-bold">Header Bold: </label></div>').appendTo(this.col1);
	this.h_bold = $('<select><option value="false">No</option> <option value="true">Yes</option></select>').appendTo(this.h_btn_set);
	$(this.h_bold).change(function(){
		self.current_calendar_component.options.header_bold = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);
	});

	//Header Show / Hideglyphicon glyphicon-eye-open
	// this.showhide_btn_set = $('<div class="btn-group btn-group-xs xshop-calendar-align" role="group" ></div>').appendTo(this.col1);
	// this.showhide_btn = $('<div class="btn" title="Show/Hide "><span class="glyphicon glyphicon-eye-open"></span></div>').appendTo(this.h_btn_set);
	// $(this.showhide_btn).click(function(){
	// 	if($(this).hasClass('active')){
	// 		$(this).removeClass('active');
	// 		$(this).children('span').removeClass('glyphicon-eye-open');
	// 		$(this).children('span').addClass('glyphicon-eye-close');
	// 		self.current_calendar_component.options.header_show = false;
	// 	}else{
	// 		$(this).addClass('active');
	// 		$(this).children('span').removeClass('glyphicon-eye-close');
	// 		$(this).children('span').addClass('glyphicon-eye-open');
	// 		self.current_calendar_component.options.header_show = true;
	// 	}

		// Render Current Selected Calendar
		// $('.xshop-designer-tool').xepan_xshopdesigner('check');
		// self.current_calendar_component.render(self.designer_tool);
	// });
	
	this.showhide_btn_set = $('<div title="Show/ Hide Header"><label for="xshop-designer-calendar-header-showhide">Header: </label></div>').appendTo(this.col1);
	this.showhide_btn = $('<select><option value="false">Hide</option> <option value="true">Show</option></select>').appendTo(this.showhide_btn_set);
	$(this.showhide_btn).change(function(){
		self.current_calendar_component.options.header_show = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);
	});

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Day Name/ Week Style Options-----------------------
//___________________________________________________________________________|
	// day_name_font_size:12,
	// this.col3 = $('<div class=""><b class="xshop-calendar-editor-header">Day Name</b></div>').appendTo(this.row1);
	this.week_options = $('<div id="calendar-week-options"> </div>').appendTo(this.vertical_tab_container);

	this.col3 = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.week_options);

	this.day_name_font_size_label = $('<div><label for="day_name_font_size">Font Size :</label></div>').appendTo(this.col3);
	this.day_name_font_size = $('<select>Day Name Size</select>').appendTo(this.day_name_font_size_label);
	
	$.each(this.designer_tool.pointtopixel,function(point,pixel){
		$('<option value="'+pixel+'">'+point+'</option>').appendTo(self.day_name_font_size);
	});

	$(this.day_name_font_size).change(function(event){
		self.current_calendar_component.options.day_name_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	//Day Name Font Color
	this.day_name_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="day_name_font_color">Color : </label></div>').appendTo(this.col3);
	this.day_name_color_picker = $('<input id="day_name_font_color" class="xepan-designer-calendar-color-picker">').appendTo(this.day_name_color_label);
	$(this.day_name_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.day_name_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});

	//Day Name Background Color
	this.day_name_bg_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="day_name_bg_color">BG Color : </label></div>').appendTo(this.col3);
	this.day_name_bg_color_picker = $('<input id="day_name_bg_color" class="xepan-designer-calendar-color-picker">').appendTo(this.day_name_bg_color_label);
	$(this.day_name_bg_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	self.current_calendar_component.options.day_name_bg_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        }
	});

	//Day name Bold
	this.w_btn_set = $('<div title="Week Bold"><label for="xshop-designer-calendar-week-bold">Week Bold: </label></div>').appendTo(this.col3);
	this.w_bold = $('<select><option value="false">No</option> <option value="true">Yes</option></select>').appendTo(this.w_btn_set);
	
	$(this.w_bold).change(function(){
		self.current_calendar_component.options.day_name_bold = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	// Week Height
	this.day_name_height_div = $('<div></div>').appendTo(this.col3);
	this.day_name_height_label = $('<label for="xshop-designer-calendar-week-height" style="float:left;">Height :</label>').appendTo(this.day_name_height_div);
	this.day_name_cell_height = $('<input type="number" id="xshop-designer-calendar-week-height"  min="10" max="80" value="20" style="padding:0;font-size:12px;float:left;width:60px !important" />').appendTo(this.day_name_height_div);

	$(this.day_name_cell_height).change(function(event){
		self.current_calendar_component.options.day_name_cell_height = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);

	});

	// Week Horizontal Alignment
	this.week_halignment_label = $('<div><label for="xcalendar-week-h-alignment">H-Align :</label></div>').appendTo(this.col3);
	this.week_halignment = $('<select><option value="left">Left</option> <option value="center">Center</option><option value="right">Right</option></select>').appendTo(this.week_halignment_label);
	$(this.week_halignment).change(function(){
		self.current_calendar_component.options.day_name_h_align = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});


	this.week_valignment_label = $('<div><label for="xcalendar-week-v-alignment">V-Align :</label></div>').appendTo(this.col3);
	this.week_valignment = $('<select><option value="top">Top</option> <option value="middle">Middle</option><option value="bottom">Bottom</option></select>').appendTo(this.week_valignment_label);
	$(this.week_valignment).change(function(){
		self.current_calendar_component.options.day_name_v_align = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Day Date Style Options-----------------------
//___________________________________________________________________________|
	// day_date_font_size:12,
	this.date_options = $('<div id="calendar-date-options"> </div>').appendTo(this.vertical_tab_container);

	this.col2 = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.date_options);
	this.day_date_font_size_label = $('<div><label for="day_date_font_size">Font Size :</label></div>').appendTo(this.col2);
	this.day_date_font_size = $('<select id="day_date_font_size">Day Date Size</select>').appendTo(this.day_date_font_size_label);
	
	$.each(this.designer_tool.pointtopixel,function(point,pixel){
		$('<option value="'+pixel+'">'+point+'</option>').appendTo(self.day_date_font_size);
	});
	// for (var i = 7; i < 50; i++) {
	// 	$('<option value="'+i+'">'+i+'</option>').appendTo(this.day_date_font_size);
	// };
	$(this.day_date_font_size).change(function(event){
		self.current_calendar_component.options.day_date_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	//Day Date Font Color
	this.day_date_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="day_date_font_color">Color : </label></div>').appendTo(this.col2);
	this.day_date_color_picker = $('<input id="day_date_font_color" class="xepan-designer-calendar-color-picker">').appendTo(this.day_date_color_label);
	$(this.day_date_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.day_date_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});


	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------Cell Block Height----------------------------
	//___________________________________________________________________________|
	//Height temporary disable no need of cell height
	// this.height_div = $('<div></div>').appendTo(this.col2);
	// this.height_label = $('<label for="xshop-designer-calendar-height" style="float:left;">Height :</label>').appendTo(this.height_div);
	// this.cell_height = $('<input type="number" id="xshop-designer-calendar-height"  min="10" max="80" value="20" style="padding:0;font-size:12px;float:left;width:60px !important" />').appendTo(this.height_div);

	// $(this.cell_height).change(function(event){
	// 	self.current_calendar_component.options.calendar_cell_heigth = $(this).val();
	// 	$('.xshop-designer-tool').xepan_xshopdesigner('check');
	// 	self.current_calendar_component.render(self.designer_tool);
	// });
	
	//Cell Block BG Color
	this.cell_bg_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="xshop-designer-calendar-cell-bg-color">BG Color : </label></div>').appendTo(this.col2);
	this.cell_bg_color = $('<input id="xshop-designer-calendar-cell-bg-color" class="xepan-designer-calendar-color-picker">').appendTo(this.cell_bg_color_label);
	$(this.cell_bg_color).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.calendar_cell_bg_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});

	//Day Date Horizental Alignment Style Options
	this.alignment_label = $('<div><label for="xcalendar-alignment">H-Align :</label></div>').appendTo(this.col2);
	this.date_halignment = $('<select><option value="left">Left</option> <option value="center">Center</option><option value="right">Right</option></select>').appendTo(this.alignment_label);
	$(this.date_halignment).change(function(){
		self.current_calendar_component.options.alignment = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	//Day Date Vertical Alignment Style Options
	this.valignment_label = $('<div><label for="xcalendar-alignment">V-Align :</label></div>').appendTo(this.col2);
	this.date_valignment = $('<select><option value="top">Top</option> <option value="middle">Middle</option><option value="bottom">Bottom</option></select>').appendTo(this.valignment_label);
	$(this.date_valignment).change(function(){
		self.current_calendar_component.options.valignment = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Event Style Options--------------------------
//___________________________________________________________________________|
	// event_font_size:10,
	// this.col4 = $('<div class=""><b class="xshop-calendar-editor-header">Event</b></div>').appendTo(this.row1);
	this.event_options = $('<div id="calendar-event-options"> </div>').appendTo(this.vertical_tab_container);

	this.col4 = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.event_options);
	
	this.event_font_size_label = $('<div><label for="day_name_font_size">Font Size :</label></div>').appendTo(this.col4);
	this.event_font_size = $('<select >Event Size</select>').appendTo(this.event_font_size_label);
	

	$.each(this.designer_tool.pointtopixel,function(point,pixel){
		$('<option value="'+pixel+'">'+point+'</option>').appendTo(self.event_font_size);
	});
	// for (var i = 7; i < 50; i++) {
	// 	$('<option value="'+i+'">'+i+'</option>').appendTo(this.event_font_size);
	// };

	$(this.event_font_size).change(function(event){
		self.current_calendar_component.options.event_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	//Event Font Color
	this.event_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="event_font_color">Color : </label></div>').appendTo(this.col4);
	this.event_color_picker = $('<input id="event_font_color" class="xepan-designer-calendar-color-picker">').appendTo(this.event_color_label);
	$(this.event_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: "vendor/xepan/commerce/templates/css/tool/designer/images/ui-colorpicker.png",
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.event_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render(self.designer_tool);
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});

	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------Calendar Height/Width/x/y Options------------
	//___________________________________________________________________________|
	this.calendar_options = $('<div id="calendar-calendar-options"> </div>').appendTo(this.vertical_tab_container);

	this.cal_col = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.calendar_options);
	
	this.calendar_x_label = $('<div class=""><label for="xshop-designer-calendar-positionx">x: </label></div>').appendTo(this.cal_col);
	this.calendar_x = $('<input name="x" id="xshop-designer-calendar-positionx" class="xshop-designer-calendar-inputx" />').appendTo(this.calendar_x_label);
	$(this.calendar_x).change(function(){
		self.current_calendar_component.options.x = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);
	});

	this.calendar_y_label = $('<div class=""><label for="xshop-designer-calendar-positiony">y: </label></div>').appendTo(this.cal_col);
	this.calendar_y = $('<input name="y" id="xshop-designer-calendar-positiony" class="xshop-designer-calendar-inputy" />').appendTo(this.calendar_y_label);
	$(this.calendar_y).change(function(){
		self.current_calendar_component.options.y = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);
	});

	this.calendar_width_label = $('<div class=""><label for="xshop-designer-calendar-width">width: </label></div>').appendTo(this.cal_col);
	this.calendar_width = $('<input name="width" id="xshop-designer-calendar-width" class="xshop-desigber-calendar-width"/>').appendTo(this.calendar_width_label);
	$(this.calendar_width).change(function(){
		self.current_calendar_component.options.width = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);	
	});

	this.calendar_height_label = $('<div class=""><label for="xshop-designer-calendar-height">height: </label></div>').appendTo(this.cal_col);
	this.calendar_height = $('<input name="height" id="xshop-designer-calendar-height" class="xshop-desigber-calendar-height"/>').appendTo(this.calendar_height_label);
	$(this.calendar_height).change(function(){
		self.current_calendar_component.options.height = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);
	});
	
	//set Calendar border or not
	this.calendar_border_label = $('<div class=""><label for="xshop-designer-calendar-border">border: </label></div>').appendTo(this.cal_col);
	this.calendar_border = $('<select> <option value="1">Show</option><option value="0">Hide</option></select>').appendTo(this.calendar_border_label);
	$(this.calendar_border).change(function(){
		self.current_calendar_component.options.border = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render(self.designer_tool);
	});

	//Calendar up/down options
	this.calendar_up_down = $('<div class="xshop-designer-calendar-up-down-btn"></div>').appendTo(this.cal_col);
	this.calendar_up = $('<span class="xshop-designer-calendar-up-btn icon-angle-circled-up atk-size-mega xshop-designer-calendar-up-btn" title="Bring to Front" ></span>').appendTo(this.calendar_up_down);
	this.calendar_down = $('<span class="xshop-designer-calendar-down-btn icon-angle-circled-down atk-size-mega xshop-designer-calendar-up-btn" title="Send to Back" ></span>').appendTo(this.calendar_up_down);

	//Bring To Front
	this.calendar_up.click(function(){
		current_calendar = $(self.current_calendar_component.element);
		current_zindex = current_calendar.css('z-index');
		if( current_zindex == 'auto'){
			current_zindex = 0;
		}
		current_calendar.css('z-index', parseInt(current_zindex)+1);
		self.current_calendar_component.options.zindex = current_calendar.css('z-index');
		if($('span.xshop-designer-calendar-down-btn').hasClass('xepan-designer-button-disable')){
			$('span.xshop-designer-calendar-down-btn').removeClass('xepan-designer-button-disable');
		}
	});

	//Send to Back
	this.calendar_down.click(function(){
		current_calendar = $(self.current_calendar_component.element);
		current_zindex = current_calendar.css('z-index');
		if( current_zindex == 'auto' || (parseInt(current_zindex)-1) < 0){
			current_zindex = 0;
		}else 
			current_zindex = (parseInt(current_zindex)-1);

		current_calendar.css('z-index', current_zindex);
		self.current_calendar_component.options.zindex = current_zindex;
		if(current_zindex == 0 ){
			// console.log($('span.xshop-designer-text-down-btn'));
			$('span.xshop-designer-calendar-down-btn').addClass('xepan-designer-button-disable');
		}
	});

	/*Header Font Family*/
	this.calendar_font_family_label = $('<div><label for="calendar_font_family">Font Family :</label></div>').appendTo(this.cal_col);
	this.calendar_font_family = $('<select id="calendar_font_family" >Header Font Family</select>').appendTo(this.calendar_font_family_label);
	
	WebFont.load({
            google: {
                families: font_list
            },
            fontinactive: function(familyName, fvd) {
                console.log("Sorry " + familyName + " font family can't be loaded at the moment. Retry later.");
            },
            active: function() {
                // do some stuff with font   
                // $('#stuff').attr('style', "font-family:'Abel'");
                // var text = new fabric.Text("Text Here", {
                //     left: 200,
                //     top: 30,
                //     fontFamily: 'Abel',
                //     fill: '#000',
                //     fontSize: 60
                // });

                // canvas.add(text);
            }
        });

	$.each(font_list,function(index,value){
		$('<option value="'+value+'">'+value+'</option>').appendTo(self.font_selector);
	});
	// // get all fonts via ajax
	// $.ajax({
	// 	url: base_url+'?page=xepan_commerce_designer_fonts',
	// 	type: 'GET',
	// 	data: {param1: 'value1'},
	// })
	// .done(function(ret) {
	// 	$(ret).appendTo(self.calendar_font_family);
	// 	// console.log("success");
	// })
	// .fail(function() {
	// 	// console.log("error");
	// })
	// .always(function() {
	// 	// console.log("complete");
	// });

	$(this.calendar_font_family).change(function(event){
		self.current_calendar_component.options.calendar_font_family = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});

	this.text_rotate_angle_label = $('<div><label for="xshop-designer-calendar-angle">Angle :</label></div>').appendTo(this.cal_col);
	this.text_rotate_angle = $('<input name="angle" type="number" id="xshop-designer-calendar-angle" class="xshop-designer-calendar-input-angle" />').appendTo(this.text_rotate_angle_label);
	$(this.text_rotate_angle).change(function(){
		self.current_calendar_component.options.rotation_angle = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);
	});


//```````````````````````````````````````````````````````````````````````````|
//------------------------------Month Style Options--------------------------
//___________________________________________________________________________|
	//Month
	// $('<hr>').appendTo(this.element);
	this.row2 = $('<div class="xepan-designer-calendar-customer-options-panel" style="display:block;margin:0;clear:both;"></div>').appendTo(this.element);
	this.col5 = $('<div title="Sequence of Calendar"></div>').appendTo(this.row2);
	
	this.month_label = $('<label for="month">Sequence :</label>').appendTo(this.col5);
	this.month = $('<select id="month"></select>').appendTo(this.col5);
	options = '<option value="00">Select</option>';
	options += '<option value="01">01</option>';
	options += '<option value="02">02</option>';
	options += '<option value="03">03</option>';
	options += '<option value="04">04</option>';
	options += '<option value="05">05</option>';
	options += '<option value="06">06</option>';
	options += '<option value="07">07</option>';
	options += '<option value="08">08</option>';
	options += '<option value="09">09</option>';
	options += '<option value="10">10</option>';
	options += '<option value="11">11</option>';
	options += '<option value="12">12</option>';
	$(options).appendTo(this.month);

	$(this.month).change(function(event){
		if($(this).val() == "00"){
			self.current_calendar_component.options.month = self.current_calendar_component.options.starting_month;
		}else{
			self.current_calendar_component.options.month = $(this).val();
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render(self.designer_tool);

	});	


//```````````````````````````````````````````````````````````````````````````|
//------------------------------Starting Month Style Options-----------------
//___________________________________________________________________________|
	//Choose Your Calendar's Starting Month 
	// this.col6 = $('<div class="xdesigner-starting-month" title="Starting Month of Calendar "></div>').appendTo(this.row2);
	// this.starting_month = $('<label for="startDate">Starting Month :</label>').appendTo(this.col6);
	// this.starting_month_text = $('<input name="startDate" id="xshop-designer-startDate" class="xshop-designer-calendar-month-picker" />').appendTo(this.col6);
	// this.starting_month_datepicker = $('.xshop-designer-calendar-month-picker').datepicker( {
 //        changeMonth: true,
 //        changeYear: true,
 //        showButtonPanel: true,
 //        dateFormat: 'MM yy',
 //        onClose: function(dateText, inst) { 
 //            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
 //            var month = parseInt(month) + 1;
 //            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();

 //            $(this).attr('month',month);
 //            $(this).attr('year',year);
 //            $(this).datepicker('setDate', new Date(year, month, 0));
 //            self.designer_tool.options.calendar_starting_month = month;
 //            self.designer_tool.options.calendar_starting_year = year;
 //            self.current_calendar_component.options.starting_date = $(this).val();
 //    		self.current_calendar_component.options.starting_month = month;
 //    		self.current_calendar_component.options.starting_year = year;
 //    		if(!self.current_calendar_component.options.month)
 //    			self.current_calendar_component.options.month = month;
	// 		$('.xshop-designer-tool').xepan_xshopdesigner('check');
	// 		self.current_calendar_component.render(self.designer_tool);

 //        }
 //    });
	
 //    $(".xshop-designer-calendar-month-picker").focus(function () {
	// 	$(".ui-datepicker-calendar").hide();
	// 	$("#ui-datepicker-div").position({
	// 		my: "center top",
	// 		at: "center bottom",
	// 		of: $(this)
	// 	});
	// });



//```````````````````````````````````````````````````````````````````````````|
//------------------------------Add Event Style Options-----------------
//___________________________________________________________________________| 
    //Calendar Events
	this.col7 = $('<div title="Manage Your Events"></div>').appendTo(this.row2);
	this.event_label = $('<label>Events </label>').appendTo(this.col7);
    event_btn = $('<button class="atk-button atk-swatch-blue">Manage</button>').appendTo(this.col7);
	
	event_frame = $('<div id="xshop-designer-calendar-events-dialog" class="xshop-designer-calendar-events-frame"></div>').appendTo(this.element);

	form_row = $('<div class="atk-row atk-padding-small row">').appendTo(event_frame);
	form_col1 = $('<div class="col-md-4">').appendTo(form_row);
	this.event_date = $('<input type="text" name="event_date" id="xshop-designer-calendar-event-date" PlaceHolder="Date"/>').appendTo(form_col1);
	form_col2 = $('<div class="col-md-6">').appendTo(form_row);
	this.event_message = $('<input type="text" name="event" id="xshop-designer-calendar-event" PlaceHolder="Event"/>').appendTo(form_col2);
	form_col3 = $('<div class="col-md-2">').appendTo(form_row);
	this.event_add = $(' <button type="button">Add</button> ').appendTo(form_col3);
	// this.event_count = $('<span class="badge1 xshop-designer-calendar-event-count"  title="Total Event Count"></span>').appendTo(event_label);
	// this.event_date = $('<input type="text" name="event_date" id="xshop-designer-calendar-event-date" PlaceHolder="Date"/>').appendTo(event_frame);
	// this.event_message = $('<input type="text" name="event" id="xshop-designer-calendar-event" PlaceHolder="Event"/>').appendTo(event_frame);
	// this.event_add = $(' <button type="button">Add</button> ').appendTo(event_frame);
	

	$(this.event_date).datepicker({
		dateFormat: 'dd-MM-yy'
	});

	event_dialog = event_frame.dialog({
	 	autoOpen: false,
		width: 500,
		modal: true,
		open:function(){

			// console.log("open dialog");
			// console.log(self.designer_tool.options.calendar_event);

			$('div').remove('#xshop-designer-calendar-events');
			table = '<div id="xshop-designer-calendar-events" class="panel panel-default"><div class="atk-table atk-table-zebra atk-table-bordered"><div class="atk-box-small atk-align-center"><h3>Your All Events</h3></div><table style="width:100%;"><thead><tr><th>Date</th><th>Message</th><th>Actions</th></tr></thead><tbody>';

			if(self.designer_tool.options.calendar_event == null || self.designer_tool.options.calendar_event == undefined || self.designer_tool.options.calendar_event == ""){
				self.designer_tool.options.calendar_event = {};
			}

			$.each(self.designer_tool.options.calendar_event,function(index,month_events){
				$.each(month_events,function(date,message){
					table += '<tr current_month='+self.current_calendar_component.options.month+' selected_date='+date+' ><td>'+index +' - '+date+'</td><td>'+message+'</td><td><a class="atk-effect-danger xshop-designer-calendar-event-delete" href="#">Delete</a></td></tr>';
				});
			});
			table +='</tbody></table></div></div>';
			$(table).appendTo(this);
			$('.xshop-designer-calendar-event-delete').click(function(event){

				selected_date = $(this).closest('tr').attr('selected_date');
				current_month = $(this).closest('tr').attr('current_month');
				delete (self.designer_tool.options.calendar_event[current_month][selected_date]);
				$(this).closest('tr').hide();
				self.current_calendar_component.render(self.designer_tool);
			});

		},
		close:function(){
			// $('.xshop-designer-calendar-event-count').empty();
			// $('.xshop-designer-calendar-event-count').text(' '+self.getCalendarEvent());
		}
	});

	$(event_btn).click(function(event){
		event_dialog.dialog("open");
	});

	$(this.event_add).click(function(event){

		
		if(self.event_date.val() == "" || self.event_date.val() === undefined ){
			alert("event date cannot be empty");
			return
		}
		
		if(self.event_message.val() == "" || self.event_message.val() === undefined ){
			alert("event cannot be empty");
			return
		}


		curr_month = self.current_calendar_component.options.month;
		// if(curr_month == "" || curr_month === undefined){
		// 	alert('calendar Sequence is not defined');
		// 	return;
		// }

		event_date = self.event_date.val();
		event_date = event_date.split("-");
	    selected_event_date = parseInt(event_date[0]);
	    curr_month = selected_event_month = event_date[1];
	    selected_event_year = event_date[2];

		if(self.designer_tool.options.calendar_event == undefined || self.designer_tool.options.calendar_event == "" )
			self.designer_tool.options.calendar_event = {};

		if(self.designer_tool.options.calendar_event[selected_event_month] == undefined)
			self.designer_tool.options.calendar_event[selected_event_month]= new Object;

		self.designer_tool.options.calendar_event[selected_event_month][selected_event_date] = new Object;
		self.designer_tool.options.calendar_event[selected_event_month][selected_event_date] = self.event_message.val();
		self.current_calendar_component.render(self.designer_tool);
		$(event_dialog).dialog('close');
		$(self.event_message).val("");
		self.event_date.val("");
	});
	
	this.getCalendarEvent = function(){
		// console.log(self.current_calendar_component);
		count = 0 ;
		$.each(self.designer_tool.options.calendar_event,function(index,month_events){
				$.each(month_events,function(date,message){
					count += 1;
				});
			});

		return count;
	};



//```````````````````````````````````````````````````````````````````````````|
//------------------------------Delete Button--------------------------------
//___________________________________________________________________________|

	this.col8 = $('<div title="Remove Calendar"></div>').appendTo(this.row2);
	this.calendar_remove_label = $('<label>Remove </label>').appendTo(this.col8);
	this.calendar_remove = $('<button class="atk-button atk-swatch-red" title="Remove Selected Calendar"><span class="glyphicon glyphicon-trash"></span></button>').appendTo(this.col8);
	this.calendar_remove.click(function(){
		dt  = self.current_calendar_component.designer_tool;
		$.each(dt.pages_and_layouts[dt.current_page][dt.current_layout].components, function(index,cmp){
			if(cmp === dt.current_selected_component){
				// console.log(self.pages_and_layouts);
				// $(dt.current_selected_component.element).remove();
				dt.pages_and_layouts[dt.current_page][dt.current_layout].components.splice(index,1);
				dt.current_selected_component.element.forEachObject(function(o){ dt.canvasObj.remove(o) });
			    dt.canvasObj.remove(dt.current_selected_component.element);
			    dt.canvasObj.discardActiveGroup().renderAll();
				dt.current_selected_component = null;
				dt.option_panel.hide();
			}
		});
	});

//added some class for vertical tabs
$( "#xepan-designer-vertical-tab" ).tabs().addClass( "ui-helper-clearfix" ); // for vertical tabs ui-tabs-vertical 
$( "#xepan-designer-vertical-tab li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Add Hide Show Button-------------------------
//___________________________________________________________________________| 

	this.col9 = $('<div title="Show/ Hide Setting"></div>').appendTo(this.row2);
	this.setting_label = $('<label>Settings</label>').appendTo(this.col9);
	this.hide_show_btn = $('<button class="atk-button btn-warning" title="Show/Hide options"> <i class="icon-atkcog"></i> </button>').appendTo(this.col9);
	hide_show_frame = $('<div id="xshop-designer-calendar-options-dialog" class="xshop-designer-calendar-options-frame"></div>').appendTo(this.element);
	
	// hide_header_text_align
	// hide_header_bg_color
	// hide_header_text_bold
	// hide_header_show_hide_btn
	// hide_header_height
	//header height removed <li class="list-group-item" data_variable="hide_header_height">Text Height<input data_variable="hide_header_height" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li>
	options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Calendar Options</div><div class="panel-body"><li class="list-group-item" data_variable="hide_all_option">Hide All Options<input data_variable="hide_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div></div>').appendTo(hide_show_frame);
	
	header_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Header options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_header_all_option">Hide Header All Options<input data_variable="hide_header_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div></div>').appendTo(hide_show_frame);
	day_date_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Week options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_week_all_option">Hide Week All Options<input data_variable="hide_week_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div></div>').appendTo(hide_show_frame);
	day_name_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Date options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_date_all_option">Hide Date All Options<input data_variable="hide_date_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div></div>').appendTo(hide_show_frame);
	event_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Event options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_event_all_option">Hide Event All Options<input data_variable="hide_event_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div></div>').appendTo(hide_show_frame);
	other_calendar_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Other calendar options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_other_all_option">Hide Other All Options<input data_variable="hide_other_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div></div>').appendTo(hide_show_frame);
	
	// all settings options with
	// header_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Header options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_header_all_option">Hide Header All Options<input data_variable="hide_header_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div><ul class="list-group"><li class="list-group-item" data_variable="hide_header_font_size">Font Size<input data_variable="hide_header_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_font_color">Text Color<input data_variable="hide_header_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_text_align">Text Align<input data_variable="hide_header_text_align" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_bg_color">Background Color<input data_variable="hide_header_bg_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_text_bold">Header Bold<input data_variable="hide_header_text_bold" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_show_hide_btn">Header Display<input data_variable="hide_header_show_hide_btn" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	// day_date_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Week options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_week_all_option">Hide Week All Options<input data_variable="hide_week_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div><ul class="list-group"><li class="list-group-item" data_variable="hide_day_date_font_size">Font Size<input data_variable="hide_day_date_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_date_font_color">Font Color<input data_variable="hide_day_date_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_date_background_color">Bg Color<input data_variable="hide_day_date_background_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_date_font_height">Cell Height<input data_variable="hide_day_date_font_height" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_date_text_bold">Text Bold<input data_variable="hide_day_date_text_bold" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	// day_name_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Date options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_date_all_option">Hide Date All Options<input data_variable="hide_date_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div><ul class="list-group"><li class="list-group-item" data_variable="hide_day_name_font_size">Font Size<input data_variable="hide_day_name_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_font_color">Font Color<input data_variable="hide_day_name_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_font_bg_color">Background Color<input data_variable="hide_day_name_font_bg_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_cell_height">Cell Height<input data_variable="hide_day_name_cell_height" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_text_horizontal_align">Text Horizontal Alignment<input data_variable="hide_day_name_text_horizontal_align" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_text_vertical_align">Text Vertical Alignment<input data_variable="hide_day_name_text_vertical_align" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	// event_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Event options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_event_all_option">Hide Event All Options<input data_variable="hide_event_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div><ul class="list-group"><li class="list-group-item" data_variable="hide_event_font_size">Font Size	<input data_variable="hide_event_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_event_font_color">Font Color	<input data_variable="hide_event_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	// other_calendar_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Other calendar options to show/hide</div><div class="panel-body"><li class="list-group-item" data_variable="hide_other_all_option">Hide Other All Options<input data_variable="hide_other_all_option" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></div><ul class="list-group"><li class="list-group-item" data_variable="hide_calendar_option_x">Calendar Position x<input data_variable="hide_calendar_option_x" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_option_y">Calendar Position y<input data_variable="hide_calendar_option_y" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_option_width">Calendar width<input data_variable="hide_calendar_option_width" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_option_height">Calendar Height<input data_variable="hide_calendar_option_height" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_option_border">Calendar Border<input data_variable="hide_calendar_option_border" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_option_font_family">Calendar Font Family<input data_variable="hide_calendar_option_font_family" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_option_bring_to_front_and_back">Calendar bring to front and send to back<input data_variable="hide_calendar_option_bring_to_front_and_back" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_month">Hide Month/Sequence<input data_variable="hide_calendar_month" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_starting_month">Starting Month<input data_variable="hide_calendar_starting_month" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_remove_btn">Remove Button<input data_variable="hide_calendar_remove_btn" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_calendar_manage_event">Event Management<input data_variable="hide_calendar_manage_event" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);

	$('.xshop-calendar-editor-options-to-show  li').click(function(event){
		option = $(this).attr('data_variable');
		current_value = eval('self.current_calendar_component.options.'+option);
		if(current_value == true)
			current_value=false;
		else
			current_value=true;

		eval('self.current_calendar_component.options.'+option+' = '+current_value+';');
		$(this).find(':checkbox').prop('checked', current_value);
	});

	option_display_dialog = hide_show_frame.dialog({
	 	autoOpen: false,
		width: 500,
		modal: true,
		height:400,
		open:function(){
			// console.log(self.current_calendar_component.options);
			// hide all option
			$('input[data_variable="hide_all_option"]').prop('checked',self.current_calendar_component.options.hide_all_option);
			
			//Show to default/Save Options
			//Header Options
			$('input[data_variable="hide_header_all_option"]').prop('checked',self.current_calendar_component.options.hide_header_all_option);
			$('input[data_variable="hide_header_font_size"]').prop('checked',self.current_calendar_component.options.hide_header_font_size);
			$('input[data_variable="hide_header_font_color"]').prop('checked',self.current_calendar_component.options.hide_header_font_color);
			$('input[data_variable="hide_header_text_align"]').prop('checked',self.current_calendar_component.options.hide_header_text_align);
			$('input[data_variable="hide_header_bg_color"]').prop('checked',self.current_calendar_component.options.hide_header_bg_color);
			$('input[data_variable="hide_header_text_bold"]').prop('checked',self.current_calendar_component.options.hide_header_text_bold);
			$('input[data_variable="hide_header_show_hide_btn"]').prop('checked',self.current_calendar_component.options.hide_header_show_hide_btn);
			// $('input[data_variable="hide_header_height"]').prop('checked',self.current_calendar_component.options.hide_header_height);
			
			//Day Date/Date / week options
			// hide_week_all_option
			// hide_day_date_font_size
			// hide_day_date_font_color
			// hide_day_date_font_height
			// hide_day_date_background_color
			// hide_day_date_text_bold
			$('input[data_variable="hide_week_all_option"]').prop('checked',self.current_calendar_component.options.hide_week_all_option);
			$('input[data_variable="hide_day_date_font_size"]').prop('checked',self.current_calendar_component.options.hide_day_date_font_size);
			$('input[data_variable="hide_day_date_font_color"]').prop('checked',self.current_calendar_component.options.hide_day_date_font_color);
			$('input[data_variable="hide_day_date_font_height"]').prop('checked',self.current_calendar_component.options.hide_day_date_font_height);
			$('input[data_variable="hide_day_date_background_color"]').prop('checked',self.current_calendar_component.options.hide_day_date_background_color);
			$('input[data_variable="hide_day_date_text_bold"]').prop('checked',self.current_calendar_component.options.hide_day_date_text_bold);
			
			//Day Name/Week/date
			// hide_date_all_option
			// hide_day_name_font_size
			// hide_day_name_font_color
			// hide_day_name_font_bg_color
			// hide_day_name_cell_height
			// hide_day_name_text_horizontal_align
			// hide_day_name_text_vertical_align
			$('input[data_variable="hide_date_all_option"]').prop('checked',self.current_calendar_component.options.hide_date_all_option);
			$('input[data_variable="hide_day_name_font_size"]').prop('checked',self.current_calendar_component.options.hide_day_name_font_size);
			$('input[data_variable="hide_day_name_font_color"]').prop('checked',self.current_calendar_component.options.hide_day_name_font_color);
			$('input[data_variable="hide_day_name_font_bg_color"]').prop('checked',self.current_calendar_component.options.hide_day_name_font_bg_color);
			$('input[data_variable="hide_day_name_cell_height"]').prop('checked',self.current_calendar_component.options.hide_day_name_cell_height);
			$('input[data_variable="hide_day_name_text_horizontal_align"]').prop('checked',self.current_calendar_component.options.hide_day_name_text_horizontal_align);
			$('input[data_variable="hide_day_name_text_vertical_align"]').prop('checked',self.current_calendar_component.options.hide_day_name_text_vertical_align);

			//Event
			$('input[data_variable="hide_event_all_option"]').prop('checked',self.current_calendar_component.options.hide_event_all_option);
			$('input[data_variable="hide_event_font_size"]').prop('checked',self.current_calendar_component.options.hide_event_font_size);
			$('input[data_variable="hide_event_font_color"]').prop('checked',self.current_calendar_component.options.hide_event_font_color);

			//Calendar Options
			// hide_calendar_option_x
			// hide_calendar_option_y
			// hide_calendar_option_width
			// hide_calendar_option_height
			// hide_calendar_option_border
			// hide_calendar_option_font_family
			// hide_calendar_option_bring_to_front_and_back
			// hide_calendar_month
			// hide_calendar_starting_month
			// hide_calendar_remove_btn
			// hide_calendar_manage_event
			$('input[data_variable="hide_calendar_option_x"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_x);
			$('input[data_variable="hide_calendar_option_y"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_y);
			$('input[data_variable="hide_calendar_option_width"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_width);
			$('input[data_variable="hide_calendar_option_height"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_height);
			$('input[data_variable="hide_calendar_option_border"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_border);
			$('input[data_variable="hide_calendar_option_font_family"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_font_family);
			$('input[data_variable="hide_calendar_option_bring_to_front_and_back"]').prop('checked',self.current_calendar_component.options.hide_calendar_option_bring_to_front_and_back);
			
			$('input[data_variable="hide_calendar_month"]').prop('checked',self.current_calendar_component.options.hide_calendar_month);
			$('input[data_variable="hide_calendar_starting_month"]').prop('checked',self.current_calendar_component.options.hide_calendar_starting_month);
			$('input[data_variable="hide_calendar_remove_btn"]').prop('checked',self.current_calendar_component.options.hide_calendar_remove_btn);
			$('input[data_variable="hide_calendar_manage_event"]').prop('checked',self.current_calendar_component.options.hide_calendar_manage_event);
		}
	});

	$(this.hide_show_btn).click(function(){
		option_display_dialog.dialog("open");
	});

    //Set from Saved Values
	this.setCalendarComponent = function(component){
		// console.log(component);
		this.current_calendar_component  = component;

		//header options
		$(this.header_font_size).val(component.options.header_font_size);
		$(this.header_color_picker).colorpicker('setColor',component.options.header_font_color);
		$(this.header_align).val(component.options.header_align);
		$(this.header_bg_color_picker).colorpicker('setColor',component.options.header_bg_color);
		$(this.h_bold).val(component.options.header_bold);
		$(this.showhide_btn).val(component.options.header_show);

		//week options
		$(this.day_name_font_size).val(component.options.day_name_font_size);
		$(this.day_name_color_picker).val(component.options.day_name_font_color);
		$(this.day_name_bg_color_picker).colorpicker('setColor',component.options.day_name_bg_color);
		if(component.options.day_name_bold){
			$(this.w_bold).addClass('active');
		}
		$(this.day_name_cell_height).val(component.options.day_name_cell_height);
		
		//date options
		$(this.day_date_font_size).val(component.options.day_date_font_size);
		$(this.day_date_color_picker).colorpicker('setColor',component.options.day_date_font_color);
		$(this.cell_height).val(component.options.calendar_cell_heigth);
		$(this.cell_bg_color).colorpicker('setColor',component.options.calendar_cell_bg_color);
		
		if(component.options.alignment === "right"){
			$(this.align_right_btn).addClass('active');
		}else if(component.options.alignment === "center"){
			$(this.align_center_btn).addClass('active');
		}else
			$(this.align_left_btn).addClass('active');

		if(component.options.valignment === "top"){
			$(this.valign_top_btn).addClass('active');
		}else if(component.options.alignment === "bottom"){
			$(this.valign_bottom_btn).addClass('active');
		}else
			$(this.valign_middle_btn).addClass('active');
		

		//event options
		$(this.event_font_size).val(component.options.event_font_size);
		$(this.event_color_picker).colorpicker('setColor',component.options.event_font_color);

		// console.log(component.options.calendar_cell_heigth);
		// console.log(component.options.calendar_cell_bg_color);
		// console.log(component.options.alignment);
		// console.log(component.options.valignment);
		//other options
		$(this.calendar_x).val(component.options.x);
		$(this.calendar_y).val(component.options.y);
		$(this.calendar_width).val(component.options.width);
		$(this.calendar_height).val(component.options.height);
		$(this.calendar_border).val(component.options.border);
		$(this.calendar_font_family).val(component.options.calendar_font_family);

		$(this.designer_mode).val(component.options.designer_mode);
		$(this.load_design).val(component.options.load_design);
		
		$(this.month).val(component.options.month);

		$(this.events).val(component.options.events);
		$(this.starting_date).val(component.options.starting_date);
		if(this.designer_tool.options.calendar_starting_month)
			$(this.starting_month_datepicker).datepicker('setDate',new Date(this.designer_tool.options.calendar_starting_year,parseInt(this.designer_tool.options.calendar_starting_month),0));
		else
			$(this.starting_month_datepicker).datepicker('setDate',new Date(component.options.starting_year,parseInt(component.options.starting_month),0));

		$(this.starting_year).val(component.options.starting_year);
		$(this.type).val(component.options.type);
		// $(this.event_count).html(self.getCalendarEvent());

		// if(component.designer_tool.options.designer_mode == false){
		// 	if(component.options.hide_all_option == undefined || component.options.hide_all_option || component.options.hide_all_option === null)
		// 		this.vertical_tab_container.hide();

		// 	// Header Hide/Show Option
		// 	if(component.options.hide_header_all_option == undefined || component.options.hide_header_all_option || component.options.hide_header_all_option === null)
		// 		this.header_options.hide();

		// 	// Week Hide/Show Option
		// 	if(component.options.hide_week_all_option == undefined || component.options.hide_week_all_option || component.options.hide_week_all_option === null)
		// 		this.week_options.hide();

		// 	// Date Hide/Show Option
		// 	if(component.options.hide_date_all_option == undefined || component.options.hide_date_all_option || component.options.hide_date_all_option === null)
		// 		this.date_options.hide();

		// 	// Event Hide/Show Option
		// 	if(component.options.hide_event_all_option == undefined || component.options.hide_event_all_option || component.options.hide_event_all_option === null)
		// 		this.event_options.hide();

		// 	// Calenda Hide/Show Option
		// 	if(component.options.hide_other_all_option == undefined || component.options.hide_other_all_option || component.options.hide_other_all_option === null)
		// 		this.calendar_options.hide();

		// 	this.col9.hide(); // hide setting button
		// }

	}
}


Calendar_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;
	this.week = {0:'Sun',1:'Mon',2:'Tue',3:'Wed',4:'Thu',5:'Fri',6:'Sat'};
	this.month_array = {"01":"January","02":"February","03":"March","04":"April","05":"May","06":"June","07":"July","08":"August","09":"September","10":"Octomber","11":"November","12":"December","1":"January","2":"February","3":"March","4":"April","5":"May","6":"June","7":"July","8":"August","9":"September"};
	this.options = {

		header_font_size:32,
		header_font_color:'#000000',
		calendar_font_family:'freemono',
		header_bold:false,
		header_bg_color:undefined,
		header_show:true,
		header_align:'left',
		day_date_font_size:20,
		day_date_font_color:'#00000',
		day_date_font_family:'freemono',
		day_name_font_size:25,
		day_name_font_color:'#00000',
		day_name_font_family:'freemono',
		day_name_bold:false,
		day_name_cell_height:20,
		day_name_h_align:'left',
		day_name_v_align:'middle',

		event_font_size:5,
		event_font_family:'freemono',
		event_font_color:'#00000',
		day_name_bg_color:'#FFFFFF',
		calendar_cell_heigth:20,
		calendar_cell_bg_color:'#F0F0F0',
		alignment: "center",
		valignment:'middle',
		border:1,
		rotation_angle:0,

		month:undefined,
		width:400,
		height:250,

		starting_date:undefined, //Show Only Date and Month // Default Value currentYear-1st Jan Month
		starting_month:undefined,
		starting_year:undefined,

		resizable:undefined,
		movable:undefined,
		colorable:undefined,
		editor:undefined,
		designer_mode:false,
		x:0,
		y:0,
		zindex:0,
		events:{},
		type: 'Calendar',

		movable:false,

		hide_all_option:true,
		hide_header_all_option:true,
		hide_header_font_size:true,
		hide_header_font_color:true,
		hide_header_text_align:true,
		hide_header_bg_color:true,
		hide_header_text_bold:true,
		hide_header_show_hide_btn:true,

		hide_week_all_option:true,
		hide_day_date_font_size:true,
		hide_day_date_font_color:true,
		hide_day_date_background_color:true,
		hide_day_date_font_height:true,
		hide_day_date_text_bold:true,

		// date opti0on
		hide_date_all_option:true,
		hide_day_name_font_size:true,
		hide_day_name_font_color:true,
		hide_day_name_font_bg_color:true,
		hide_day_name_cell_height:true,
		hide_day_name_text_horizontal_align:true,
		hide_day_name_text_vertical_align:true,

		//other option
		hide_calendar_option_x:true,
		hide_calendar_option_y:true,
		hide_calendar_option_width:true,
		hide_calendar_option_height:true,
		hide_calendar_option_border:true,
		hide_calendar_option_font_family:true,
		hide_calendar_option_bring_to_front_and_back:true,
		hide_calendar_month:true,
		hide_calendar_starting_month:true,
		hide_calendar_remove_btn:true,
		hide_calendar_manage_event:true,

		base_url:undefined,
		page_url:undefined,
	};
	
	this.init = function(designer,canvas,editor){
		this.designer_tool = designer;
		this.canvas = canvas;
		if(editor !== undefined)
			this.editor = editor;
		
		this.options.base_url = this.designer_tool.options.base_url;
		this.options.page_url = this.designer_tool.options.base_url;

	}

	this.initExisting = function(params){

	}

	this.renderTool = function(parent){
		var self=this;
		if(self.options.base_url == undefined){
			self.options.base_url = self.designer_tool.options.base_url;
			self.options.page_url = self.designer_tool.options.base_url;
		}
		
		this.parent = parent;
		var calendar_starting_month = self.designer_tool.options.calendar_starting_month;
		var calendar_starting_year = self.designer_tool.options.calendar_starting_year;

		calender_button_group = $('<div class="btn-group"></div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));
		tool_btn = $('<button type="button" class="btn "><i class="glyphicon glyphicon-calendar"></i><br/>Calendar</button>').appendTo(calender_button_group);
				
		this.editor = new xShop_Calendar_Editor(parent.find('.xshop-designer-tool-topbar-options'),self.designer_tool);
		// CREATE NEW Calendar COMPONENT ON CANVAS Default 
		tool_btn.click(function(event){
			self.designer_tool.current_selected_component = undefined;
			// create new CalendarComponent type object
			var new_calendar = new Calendar_Component();
			new_calendar.init(self.designer_tool,self.canvas, self.editor);
			self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].components.push(new_calendar);
			new_calendar.render(self.designer_tool);
		});

		calendar_starting_month_picker = $('<div class="btn"></div>').appendTo(calender_button_group);
		this.starting_month_text = $('<input name="startDate" id="xshop-designer-startDate" class="xshop-designer-calendar-month-picker" />').appendTo(calendar_starting_month_picker);
		calendar_starting_month_label = $('<div>Starting Month</div>').appendTo(calendar_starting_month_picker);
		
		calendar_starting_month_label.click(function(){
			$(self.starting_month_text).datepicker('show');
		});
		
		var month_array = {"01":"January","02":"February","03":"March","04":"April","05":"May","06":"June","07":"July","08":"August","09":"September","10":"Octomber","11":"November","12":"December","1":"January","2":"February","3":"March","4":"April","5":"May","6":"June","7":"July","8":"August","9":"September"};
		if(calendar_starting_month == undefined || calendar_starting_year == undefined){
			dateObj = new Date();
			calendar_starting_month = dateObj.getUTCMonth() + 1;
			calendar_starting_year = dateObj.getUTCFullYear();
		}

		$(this.starting_month_text).val(month_array[calendar_starting_month] +" "+calendar_starting_year);

		this.starting_month_datepicker = $('.xshop-designer-calendar-month-picker').datepicker( {
	        changeMonth: true,
	        changeYear: true,
	        showButtonPanel: true,
	        dateFormat: 'MM yy',
	        onClose: function(dateText, inst) { 
	            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
	            var month = parseInt(month) + 1;
	            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();

	            $(this).attr('month',month);
	            $(this).attr('year',year);
	            $(this).datepicker('setDate', new Date(year, month, 0));
	            self.designer_tool.options.calendar_starting_month = month;
	            self.designer_tool.options.calendar_starting_year = year;
				$('.xshop-designer-tool').xepan_xshopdesigner('check');
				self.designer_tool.render();
				// render all calendar layout
				$('.xshop-designer-pagelayout').remove();
				self.designer_tool.bottom_bar.renderTool();
	        }
	    });

	    $(".xshop-designer-calendar-month-picker").focus(function () {
			$(".ui-datepicker-calendar").hide();
			$("#ui-datepicker-div").position({
				my: "center top",
				at: "center bottom",
				of: $(this)
			});
		});			
	}

	this.render = function(designer_tool_obj){
		self = this;

		if(designer_tool_obj) self.designer_tool = designer_tool_obj;
		canvas = designer_tool_obj.canvasObj;

		if(this.element){
			// self.designer_tool.canvasObj.getActiveObject().remove();
			dt = self.designer_tool;
			dt.current_selected_component.element.forEachObject(function(o){ dt.canvasObj.remove(o) });
		    dt.canvasObj.remove(dt.current_selected_component.element);
		    dt.canvasObj.discardActiveGroup().renderAll();
		}


		if(!self.options.width){
			if(canvas.getWidth() < canvas.getHeight())
				self.options.width = canvas.getWidth()*0.75/ self.designer_tool._getZoom();
			else
				self.options.height = canvas.getHeight()*0.75/ self.designer_tool._getZoom();
		}

		this.calendar = group = new fabric.Group();

		this.calendar.width=self.options.width / self.designer_tool._getZoom();
	  	
		this.selectedMonth = 'January';
	    this.selectedYear = '2016';
	    
	    this.monthDay = 0;
	    
		this.selectedDate = new Date(this.selectedMonth + " 1, " + this.selectedYear);
	    this.thisMonth = this.selectedDate.getMonth() + 1;
	    
	    this.prevMonthLastDate = getLastDayOfMonth(this.thisMonth - 1);
	    this.thisMonthLastDate = getLastDayOfMonth(this.thisMonth);
	    this.thisMonthFirstDay = this.selectedDate.getDay();
	    this.thisMonthFirstDate = this.selectedDate.getDate();
	    
	    if (this.thisMonth == 12)
	      this.nextMonthFirstDay = 1;
	    else
	      this.nextMonthFirstDay = this.thisMonth + 1;
	          
	    this.dateOffset = this.thisMonthFirstDay;

	  	self.text_objects=[];
	    self.drawCalendar();

	    this.element = this.calendar;
		this.element.component = self;

	  	self.designer_tool.canvasObj.add(group);


	  	group.on('selected', function(e){
	  		$('.ui-selected').removeClass('ui-selected');
            $(this).addClass('ui-selected');
            $('.xshop-options-editor').hide();

            self.editor.element.show();

            self.editor.calendar_x.val(self.designer_tool.option2screen(self.options.x));
            self.editor.calendar_y.val(self.designer_tool.option2screen(self.options.y));
            
            self.editor.calendar_width.val(self.designer_tool.option2screen(self.options.width));
            self.editor.calendar_height.val(self.designer_tool.option2screen(self.options.height));

            self.designer_tool.option_panel.fadeIn(500);

            self.designer_tool.current_selected_component = self;
            self.current_calendar_component = self;
            self.designer_tool.option_panel.css('z-index',70);
            self.designer_tool.option_panel.addClass('xshop-text-options');

            self.designer_tool.option_panel.offset(
	        							{
	        								top:self.designer_tool.canvasObj._offset.top + group.top - self.designer_tool.option_panel.height(),
	        								left:self.designer_tool.canvasObj._offset.left + group.left
	        							}
	        						);
            
            self.editor.calendar_border.val(self.options.border);

            self.editor.setCalendarComponent(self);
            
            if(self.designer_tool.options.designer_mode){
            	self.designer_tool.freelancer_panel.FreeLancerComponentOptions.element.show();
	            self.designer_tool.freelancer_panel.setComponent(self.designer_tool.current_selected_component);
            }else{
            	self.editor.hide_show_btn.hide();
            }

	        // event.stopPropagation();
			//check For the Z-index
        	if(self.options.zindex == 0){
        		$('span.xshop-designer-calendar-down-btn').addClass('xepan-designer-button-disable');
        	}else
        		$('span.xshop-designer-calendar-down-btn').removeClass('xepan-designer-button-disable');
        		
	  	});

	  	// group.on('scaling',function(e){
	  	// 	$.each(self.text_objects,function(index,obj){
	  	// 		obj.fontSize = obj.fontSize + (obj.fontSize / e.scaleX);
	  	// 	});
	  	// });
	},

	this.drawCalendar= function() {
		self = this;

		this.drawHeader();
    	for(j = 0; j < 6; ++j) {
      		this.drawWeek(j);
    	}
  	},

  	this.drawHeader= function(){
  		self = this;
  		// draw month and year
  		header_width = self.options.width * self.designer_tool._getZoom();
  		
  		var header_y_offset = self.options.y*self.designer_tool._getZoom();
  		var header_x_offset = self.options.x*self.designer_tool._getZoom();

  		var header_text_height = header_y_offset;
  		// show header
  		if(self.options.header_bg_color==="#")
  			self.options.header_bg_color = "";

  		if(self.options.header_show == "true"){	
	  		var header  = new fabric.Rect({
			  		left: header_x_offset,
			  		top: header_y_offset,
			  		width: header_width - 2,
			  		fill:self.options.header_bg_color,
			  		evented: false
			 	});
		  	self.calendar.addWithUpdate(header);


		  	var scaleXVar = self.calendar.width / (self.options.width * self.designer_tool._getZoom());

		  	var header_bold_value = 'normal';
		  	if(self.options.header_bold === "true")
		  		header_bold_value = 'bold';

		  	var text = new fabric.Text(''+self.selectedMonth + ' - ' +self.selectedYear, {
				left: header_x_offset,
				top: header_y_offset,
				fontSize: self.options.header_font_size,
				fontFamily: 'sans-serif',
				fill: self.options.header_font_color,
				scaleX : scaleXVar,
				scaleY : scaleXVar,
				fontWeight: header_bold_value,
			  	evented: false,
			});

		  	header.height = text.height;
		  	
		  	// header position
		  	var header_left = header_x_offset;
		  	switch(self.options.header_align){
		  		case "center":
		  			header_left = header_x_offset + (self.calendar.width / 2) - (text.width / 2);
		  		break;
		  		case "right":
		  			header_left = header_x_offset + self.calendar.width - text.width;
		  		break;
		  	}

		  	text.left = header_left;
		  	self.calendar.addWithUpdate(text);

		  	self.text_objects.push(text);
	  	 	
	  	 	header_text_height = text.height
  		}
	  	// reset the y offset for removing the space between heade month year and week name
		self.week_cell_y_offset = 0;
		// draw week
  		week_cell_width = self.options.width * self.designer_tool._getZoom() / 7;
  		week_cell_height = self.options.day_name_cell_height * self.designer_tool._getZoom();

		self.week_cell_y_offset = this.calendar.top + header_text_height + (5*self.designer_tool._getZoom());

		$.each(self.week,function(index,name){
			self.x_offset = self.options.x*self.designer_tool._getZoom() + week_cell_width * index;

		  	var week  = new fabric.Rect({
		  		left: self.x_offset,
		  		top: self.week_cell_y_offset,
		  		width: week_cell_width - 2,
		  		height: week_cell_height - 2,
		  		fill:self.options.day_name_bg_color,
		  		evented: false
		 	});
		  	self.calendar.addWithUpdate(week);


		  	var week_bold_value = 'normal';
		  	if(self.options.day_name_bold === "true")
		  		week_bold_value = 'bold';

		  	var text = new fabric.Text(''+name, { 
				left: self.x_offset,
				top: self.week_cell_y_offset,
				fontSize: self.options.day_name_font_size,
				fontFamily: 'sans-serif',
				fill: self.options.day_name_font_color,
				scaleX : self.designer_tool._getZoom(),
				scaleY : self.designer_tool._getZoom(),
				evented: false,
				fontWeight: week_bold_value,
			});

		  	// week text alignment
		  	var week_left = self.x_offset;
		  	switch(self.options.day_name_h_align){
		  		case "center":
		  			week_left = self.x_offset + (week.width / 2) - (text.width / 2);
		  		break;
		  		case "right":
		  			week_left = self.x_offset + week.width - text.width;
		  		break;
		  	}
		  	text.left = week_left;

		  	var week_top = self.week_cell_y_offset;		  	
		  	switch(self.options.day_name_v_align){
		  		case "middle":
		  			week_top = self.week_cell_y_offset + (week.height / 2) - (text.height / 2);
		  		break;
		  		case "bottom":
		  			week_top = self.week_cell_y_offset + week.height - text.height;
		  		break;
		  	}
		  	text.top = week_top;

		  	self.calendar.addWithUpdate(text);
		  	self.week_cell_height = week.height;
		});
  	},

  	this.drawWeek= function(j) {
  		self = this;
		for(i=0; i<7; ++i) {
			this.drawDay(i, j);
		}
	},

	this.drawDay= function(i, j) {
		self = this;
	  cell_width = this.options.width * this.designer_tool._getZoom() / 7;
	  //tobe removed
	  cell_height = this.options.calendar_cell_heigth * this.designer_tool._getZoom();
	  // this.x_offset = cell_width * i;
	  // this.y_offset = cell_width * j;
	  this.x_offset = this.options.x*this.designer_tool._getZoom() + cell_width * i ;
	  this.y_offset = (self.week_cell_y_offset + self.week_cell_height) + (cell_width * j);
	  var day  = new fabric.Rect({
	  		left: this.x_offset,
	  		top: this.y_offset,
	  		width: cell_width - 2,
	  		height: cell_width - 2,
	  		fill:self.options.calendar_cell_bg_color,
	  		evented: false
	  });

	  this.calendar.addWithUpdate(day);

	  // First week
	  if (j == 0) {
	    if (i < this.thisMonthFirstDay) {
	      this.drawDayNumber(this.prevMonthLastDate - (this.dateOffset - i) + 1, '#909090', false);
	    }
	    else if (i == this.thisMonthFirstDay) {
	      this.monthDay = 1;
	      this.drawDayNumber(this.thisMonthFirstDate + (this.dateOffset - i), self.options.day_date_font_color, true);
	    }
	    else {
	      ++this.monthDay;
	      this.drawDayNumber(this.monthDay, self.options.day_date_font_color, true);
	    }
	  }     
	  // Last weeks
	  else if (this.thisMonthLastDate <= this.monthDay) {
	    ++this.monthDay;
	    this.drawDayNumber(this.monthDay - this.thisMonthLastDate, '#909090' , false);
	  }
	  // Other weeks
	  else {
	    ++this.monthDay;
	    this.drawDayNumber(this.monthDay, self.options.day_date_font_color, true);
	  }
	},

	this.drawDayNumber = function(dayNumber, color, replace_by_event=true) {
		self = this;

		var has_event = false;
		if(replace_by_event){
			if(self.designer_tool.options.calendar_event != undefined && self.designer_tool.options.calendar_event != null ){
				month_name = self.month_array[self.options.month];
				if(self.designer_tool.options.calendar_event[month_name] && self.designer_tool.options.calendar_event[month_name][dayNumber]){
					dayNumber = self.designer_tool.options.calendar_event[month_name][dayNumber];
					has_event = true;
				}
			}
		}


	  var text = new fabric.Text(''+dayNumber, { 
			left: this.x_offset,
			top: this.y_offset,
			fontSize: this.options.day_date_font_size,
			fontFamily: 'sans-serif',
			// scaleX : self.designer_tool._getZoom(),
			// scaleY : self.designer_tool._getZoom(),
			fill: color,
			scaleX : this.designer_tool._getZoom(),
			scaleY : this.designer_tool._getZoom(),
			evented: false,
			textAlign: self.options.alignment,
		});

	  //Date Alignment
	  var date_left = self.x_offset;
	  self.options.alignment = "center";
	  	switch(self.options.alignment){
	  		case "center":
	  			date_left = self.x_offset + (cell_width / 2) - (text.width / 2);
	  		break;
	  		case "right":
	  			date_left = self.x_offset + cell_width - text.width;
	  		break;
		}
	  text.left = date_left;

	  var date_top = self.y_offset;
	  	switch(self.options.valignment){
	  		case "middle":
	  			date_top = self.y_offset + (cell_width / 2) - (text.height / 2);
	  		break;
	  		case "bottom":
	  			date_top = self.y_offset + cell_width - text.height;
	  		break;
		}
	  	text.top = date_top;

	  	// event text color selection
	  	if(has_event){
	  		text.fill = self.options.event_font_color;
	  		text.fontSize = self.options.event_font_size;
	  		has_event = false;
	  	}
	  this.calendar.addWithUpdate(text);
	}
}

function getLastDayOfMonth(month, year)
{
  var day;

  switch (month)
  {
    case 0 : // prevents error when checking for previous month in jan
    case 1 :
    case 3 :
    case 5 :
    case 7 :
    case 8 :
    case 10:
    case 12:
    case 13: // prevents error when checking for next month in december
      day = 31;
      break;
    case 4 :
    case 6 :
    case 9 :
    case 11:
      day = 30;
      break;
    case 2 :
      if( ( (year % 4 == 0) && ( year % 100 != 0) ) 
               || (year % 400 == 0) )
        day = 29;
      else
        day = 28;
      break;

  }

  return day;
}