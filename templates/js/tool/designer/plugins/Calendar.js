xShop_Calendar_Editor = function(parent,designer){
	var self = this;
	this.parent = parent;
	this.current_calendar_component = undefined;
	this.designer_tool = designer;

	var base_url = this.designer_tool.options.base_url;
	var page_url = base_url;

	this.element = $('<div id="xshop-designer-calendar-editor" class="xshop-options-editor " style="display:block"> </div>').appendTo(this.parent);
	this.editor_close_btn = $('<div style="padding:0;margin:0;width:100%;"><i class="atk-box-small pull-right glyphicon glyphicon-remove"></i></div>').appendTo(this.element);

	$(this.editor_close_btn).click(function(event){
		self.element.hide();
	});

  	this.vertical_tab_container = $('<div id="xepan-designer-vertical-tab"></div>').appendTo(this.element);
  	this.vertical_tab = $('<ul></ul>').appendTo(this.vertical_tab_container);
  	
  	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------ Tabs -------------------------
	//___________________________________________________________________________|
	$('<li><a href="#calendar-header-options"  class="xshop-calendar-editor-header">Header</a></li>').appendTo(this.vertical_tab);
	$('<li><a href="#calendar-week-options"  class="xshop-calendar-editor-header">Week</a></li>').appendTo(this.vertical_tab);
	$('<li><a href="#calendar-date-options"  class="xshop-calendar-editor-header">Date</a></li>').appendTo(this.vertical_tab);
	$('<li><a href="#calendar-event-options"  class="xshop-calendar-editor-header">Event</a></li>').appendTo(this.vertical_tab);
	$('<li><a href="#calendar-calendar-options"  class="xshop-calendar-editor-header">Calendar</a></li>').appendTo(this.vertical_tab);

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Header Style Options-------------------------
//___________________________________________________________________________|
	// header_font_size:16,
	this.header_options = $('<div id="calendar-header-options"> </div>').appendTo(this.vertical_tab_container);
	this.col1 = $('<div class="designer-tool-calendar-option"></div>').appendTo(this.header_options);
	
	this.header_font_size_label = $('<div title="Header Font Size"><label for="header_font_size">Font Size :</label></div>').appendTo(this.col1);
	this.header_align_label = $('<div title="Header Text Align"><label for="header_align">Text Align :</label></div>').appendTo(this.col1);
	this.header_align = $('<select id="header_align" class="btn btn-xs">Header Align</select>').appendTo(this.header_align_label);
	this.header_font_size = $('<select id="header_font_size" class="btn btn-xs">Header Size</select>').appendTo(this.header_font_size_label);
	
	align_options = '';
		align_options += '<option value="left" class="atk-move-left">left</option>';
		align_options += '<option value="center" class="atk-move-center">center</option>';
		align_options += '<option value="right" class="atk-move-right">right</option>';

	$(align_options).appendTo(this.header_align);
	
	$(this.header_align).change(function(event){
		self.current_calendar_component.options.header_align = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

	options = '';
	for (var i = 7; i < 50; i++) {
		options += '<option value="'+i+'">'+i+'</option>';
	};
	$(options).appendTo(this.header_font_size);
	$(this.header_font_size).change(function(event){
		self.current_calendar_component.options.header_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

	//header font color: default Value Black
	this.header_color_label = $('<div title="Header Text Color" class="xshop-designer-calendar-color-picker"><label for="header_font_color">Text Color : </label></div>').appendTo(this.col1);
	this.header_color_picker = $('<input id="header_font_color" style="display:none;">').appendTo(this.header_color_label);
	$(this.header_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	self.current_calendar_component.options.header_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
        }
	});

	//Header Background Color
	this.header_bg_color_label = $('<div title="Header Background Color" class="xshop-designer-calendar-color-picker"><label for="header_bg_color">Background Color : </label></div>').appendTo(this.col1);
	this.header_bg_color_picker = $('<input id="header_bg_color" style="display:none;">').appendTo(this.header_bg_color_label);
	$(this.header_bg_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	self.current_calendar_component.options.header_bg_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
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
	// 	self.current_calendar_component.render();
	// });
	this.h_btn_set = $('<div title="Header Bold"><label for="xshop-designer-calendar-header-bold">Header Bold: </label></div>').appendTo(this.col1);
	this.h_bold = $('<select><option value="false">No</option> <option value="true">Yes</option></select>').appendTo(this.h_btn_set);
	$(this.h_bold).change(function(){
		self.current_calendar_component.options.header_bold = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render();
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
		// self.current_calendar_component.render();
	// });
	
	this.showhide_btn_set = $('<div title="Show/ Hide Header"><label for="xshop-designer-calendar-header-showhide">Header Show/ Hide: </label></div>').appendTo(this.col1);
	this.showhide_btn = $('<select><option value="false">Hide</option> <option value="true">Show</option></select>').appendTo(this.showhide_btn_set);
	$(this.showhide_btn).change(function(){
		self.current_calendar_component.options.header_show = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render();
	});

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Day Name Style Options-----------------------
//___________________________________________________________________________|
	// day_name_font_size:12,
	// this.col3 = $('<div class=""><b class="xshop-calendar-editor-header">Day Name</b></div>').appendTo(this.row1);
	this.week_options = $('<div id="calendar-week-options"> </div>').appendTo(this.vertical_tab_container);

	this.col3 = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.week_options);

	this.day_name_font_size_label = $('<div><label for="day_name_font_size">Font Size :</label></div>').appendTo(this.col3);
	this.day_name_font_size = $('<select class="btn btn-xs">Day Name Size</select>').appendTo(this.day_name_font_size_label);
	for (var i = 7; i < 50; i++) {
		$('<option value="'+i+'">'+i+'</option>').appendTo(this.day_name_font_size);
	};
	$(this.day_name_font_size).change(function(event){
		self.current_calendar_component.options.day_name_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

	//Day Name Font Color
	this.day_name_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="day_name_font_color">Color : </label></div>').appendTo(this.col3);
	this.day_name_color_picker = $('<input id="day_name_font_color" style="display:none;">').appendTo(this.day_name_color_label);
	$(this.day_name_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.day_name_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});

	//Day Name Background Color
	this.day_name_bg_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="day_name_bg_color">BG Color : </label></div>').appendTo(this.col3);
	this.day_name_bg_color_picker = $('<input id="day_name_bg_color" style="display:none;">').appendTo(this.day_name_bg_color_label);
	$(this.day_name_bg_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.day_name_bg_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});

	//Day name Bold
	this.w_btn_set = $('<div class="btn-group btn-group-xs xshop-calendar-align" role="group" ></div>').appendTo(this.col3);
	this.w_bold = $('<div class="btn" title="Right"><span class="glyphicon glyphicon-bold"></span></div>').appendTo(this.w_btn_set);
	$(this.w_bold).click(function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			self.current_calendar_component.options.day_name_bold = false;
		}else{
			$(this).addClass('active');
			self.current_calendar_component.options.day_name_bold = true;
		}

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------Week Block Height----------------------------
	//___________________________________________________________________________|

	//Height
	this.day_name_height_div = $('<div></div>').appendTo(this.col3);
	this.day_name_height_label = $('<label for="xshop-designer-calendar-week-height" style="float:left;">Height :</label>').appendTo(this.day_name_height_div);
	this.day_name_cell_height = $('<input type="number" id="xshop-designer-calendar-week-height"  min="10" max="80" value="20" style="padding:0;font-size:12px;float:left;width:60px !important" />').appendTo(this.day_name_height_div);

	$(this.day_name_cell_height).change(function(event){
		self.current_calendar_component.options.day_name_cell_height = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

	});



//```````````````````````````````````````````````````````````````````````````|
//------------------------------Day Date Style Options-----------------------
//___________________________________________________________________________|
	// day_date_font_size:12,
	this.date_options = $('<div id="calendar-date-options"> </div>').appendTo(this.vertical_tab_container);

	this.col2 = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.date_options);
	this.day_date_font_size_label = $('<div><label for="day_date_font_size">Font Size :</label></div>').appendTo(this.col2);
	this.day_date_font_size = $('<select id="day_date_font_size"class="btn btn-xs">Day Date Size</select>').appendTo(this.day_date_font_size_label);
	for (var i = 7; i < 50; i++) {
		$('<option value="'+i+'">'+i+'</option>').appendTo(this.day_date_font_size);
	};
	$(this.day_date_font_size).change(function(event){
		self.current_calendar_component.options.day_date_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

	//Day Date Font Color
	this.day_date_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="day_date_font_color">Color : </label></div>').appendTo(this.col2);
	this.day_date_color_picker = $('<input id="day_date_font_color" style="display:none;">').appendTo(this.day_date_color_label);
	$(this.day_date_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.day_date_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});


	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------Cell Block Height----------------------------
	//___________________________________________________________________________|
	//Height
	this.height_div = $('<div></div>').appendTo(this.col2);
	this.height_label = $('<label for="xshop-designer-calendar-height" style="float:left;">Height :</label>').appendTo(this.height_div);
	this.cell_height = $('<input type="number" id="xshop-designer-calendar-height"  min="10" max="80" value="20" style="padding:0;font-size:12px;float:left;width:60px !important" />').appendTo(this.height_div);

	$(this.cell_height).change(function(event){
		self.current_calendar_component.options.calendar_cell_heigth = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

	});	
	
	//```````````````````````````````````````````````````````````````````````````|
	//------------------------------Cell Block BG Color--------------------------
	//___________________________________________________________________________|
	this.cell_bg_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="xshop-designer-calendar-cell-bg-color">BG Color : </label></div>').appendTo(this.col2);
	this.cell_bg_color = $('<input id="xshop-designer-calendar-cell-bg-color" style="display:none;">').appendTo(this.cell_bg_color_label);
	$(this.cell_bg_color).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.calendar_cell_bg_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
        	// console.log('#'+color.formatted);
        	// console.log(parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100));
        }
	});

	//```````````````````````````````````````````````````````````````````````````|
	//----------------------------Day Date Horizental Alignment Style Options-----
	//___________________________________________________________________________|

	this.alignment_label = $('<div><label for="xcalendar-alignment">H-Align :</label></div>').appendTo(this.col2);
	this.alignment_btn_set = $('<div class="btn-group btn-group-xs xshop-calendar-align" role="group" aria-label="Text Alignment"></div>').appendTo(this.alignment_label);
	this.align_left_btn = $('<div class="btn" title="Left"><span class="glyphicon glyphicon-align-left"></span></div>').appendTo(this.alignment_btn_set);
	this.align_center_btn = $('<div class="btn" title="Center"><span class="glyphicon glyphicon-align-center"></span></div>').appendTo(this.alignment_btn_set);
	this.align_right_btn = $('<div class="btn" title="Right"><span class="glyphicon glyphicon-align-right"></span></div>').appendTo(this.alignment_btn_set);

	$(this.align_left_btn).click(function(){
		$(this).addClass('active');
		self.current_calendar_component.options.alignment = "left";

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

		//Remove active Align Class form other options
		$(self.align_right_btn).removeClass('active');
		$(self.align_center_btn).removeClass('active');

	});

	$(this.align_center_btn).click(function(){
		$(this).addClass('active');
		self.current_calendar_component.options.alignment = "center";

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

		//Remove active Align Class form other options
		$(self.align_right_btn).removeClass('active');
		$(self.align_left_btn).removeClass('active');
	});

	$(this.align_right_btn).click(function(){
		$(this).addClass('active');
		self.current_calendar_component.options.alignment = "right";

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

		//Remove active Align Class form other options
		$(self.align_left_btn).removeClass('active');
		$(self.align_center_btn).removeClass('active');
	});

	//```````````````````````````````````````````````````````````````````````````|
	//----------------------------Day Date Vertical Alignment Style Options-------
	//___________________________________________________________________________|

	this.valignment_label = $('<div><label for="xcalendar-alignment">V</label></div>').appendTo(this.col2);
	this.valignment_btn_set = $('<div class="btn-group btn-group-xs xshop-calendar-valign" role="group" aria-label="Text Alignment"></div>').appendTo(this.valignment_label);
	this.valign_top_btn = $('<div class="btn" title="Top"><span class="glyphicon glyphicon-align-left"></span></div>').appendTo(this.valignment_btn_set);
	this.valign_middle_btn = $('<div class="btn" title="Middle"><span class="glyphicon glyphicon-align-center"></span></div>').appendTo(this.valignment_btn_set);
	this.valign_bottom_btn = $('<div class="btn" title="Bottom"><span class="glyphicon glyphicon-align-right"></span></div>').appendTo(this.valignment_btn_set);

	$(this.valign_top_btn).click(function(){
		$(this).addClass('active');
		self.current_calendar_component.options.valignment = "top";

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

		//Remove active Align Class form other options
		$(self.valign_middle_btn).removeClass('active');
		$(self.valign_bottom_btn).removeClass('active');

	});

	$(this.valign_middle_btn).click(function(){
		$(this).addClass('active');
		self.current_calendar_component.options.valignment = "middle";

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

		//Remove active Align Class form other options
		$(self.valign_top_btn).removeClass('active');
		$(self.valign_bottom_btn).removeClass('active');

	});

	$(this.valign_bottom_btn).click(function(){
		$(this).addClass('active');
		self.current_calendar_component.options.valignment = "bottom";

		//Render Current Selected Calendar
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

		//Remove active Align Class form other options
		$(self.valign_top_btn).removeClass('active');
		$(self.valign_middle_btn).removeClass('active');

	});


//```````````````````````````````````````````````````````````````````````````|
//------------------------------Event Style Options--------------------------
//___________________________________________________________________________|
	// event_font_size:10,
	// this.col4 = $('<div class=""><b class="xshop-calendar-editor-header">Event</b></div>').appendTo(this.row1);
	this.event_options = $('<div id="calendar-event-options"> </div>').appendTo(this.vertical_tab_container);

	this.col4 = $('<div class=" atk-box-small designer-tool-calendar-option"></div>').appendTo(this.event_options);
	
	this.event_font_size_label = $('<div><label for="day_name_font_size">Font Size :</label></div>').appendTo(this.col4);
	this.event_font_size = $('<select class="btn btn-xs">Event Size</select>').appendTo(this.event_font_size_label);
	for (var i = 7; i < 50; i++) {
		$('<option value="'+i+'">'+i+'</option>').appendTo(this.event_font_size);
	};
	$(this.event_font_size).change(function(event){
		self.current_calendar_component.options.event_font_size = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

	//Event Font Color
	this.event_color_label = $('<div class="xshop-designer-calendar-color-picker"><label for="event_font_color">Color : </label></div>').appendTo(this.col4);
	this.event_color_picker = $('<input id="event_font_color" style="display:none;">').appendTo(this.event_color_label);
	$(this.event_color_picker).colorpicker({
		parts:          'full',
        alpha:          false,
        showOn:         'both',
        buttonColorize: true,
        showNoneButton: true,
        buttonImage: base_url,
        ok: function(event, color){
        	// self.current_calendar_component.options.header_font_color = parseInt((color.cmyk.c)*100)+','+parseInt((color.cmyk.m)*100)+','+parseInt((color.cmyk.y)*100)+','+parseInt((color.cmyk.k)*100);
        	self.current_calendar_component.options.event_font_color = '#'+color.formatted;
        	$('.xshop-designer-tool').xepan_xshopdesigner('check');
        	self.current_calendar_component.render();
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
    	self.current_calendar_component.render();
	});

	this.calendar_y_label = $('<div class=""><label for="xshop-designer-calendar-positiony">y: </label></div>').appendTo(this.cal_col);
	this.calendar_y = $('<input name="y" id="xshop-designer-calendar-positiony" class="xshop-designer-calendar-inputy" />').appendTo(this.calendar_y_label);
	$(this.calendar_y).change(function(){
		self.current_calendar_component.options.y = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render();
	});

	this.calendar_width_label = $('<div class=""><label for="xshop-designer-calendar-width">width: </label></div>').appendTo(this.cal_col);
	this.calendar_width = $('<input name="width" id="xshop-designer-calendar-width" class="xshop-desigber-calendar-width"/>').appendTo(this.calendar_width_label);
	$(this.calendar_width).change(function(){
		self.current_calendar_component.options.width = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render();	
	});

	this.calendar_height_label = $('<div class=""><label for="xshop-designer-calendar-height">height: </label></div>').appendTo(this.cal_col);
	this.calendar_height = $('<input name="height" id="xshop-designer-calendar-height" class="xshop-desigber-calendar-height"/>').appendTo(this.calendar_height_label);
	$(this.calendar_height).change(function(){
		self.current_calendar_component.options.height = self.designer_tool.screen2option($(this).val());
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render();
	});
	
	//set Calendar border or not
	this.calendar_border_label = $('<div class=""><label for="xshop-designer-calendar-border">border: </label></div>').appendTo(this.cal_col);
	this.calendar_border = $('<select> <option value="1">Show</option><option value="0">Hide</option></select>').appendTo(this.calendar_border_label);
	$(this.calendar_border).change(function(){
		self.current_calendar_component.options.border = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
    	self.current_calendar_component.render();
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
	this.header_font_family_label = $('<div><label for="header_font_family">Font Family :</label></div>').appendTo(this.cal_col);
	this.header_font_family = $('<select id="header_font_family" class="btn btn-xs">Header Font Family</select>').appendTo(this.header_font_family_label);
	
	// get all fonts via ajax
	$.ajax({
		url: base_url+'?page=xepan_commerce_designer_fonts',
		type: 'GET',
		data: {param1: 'value1'},
	})
	.done(function(ret) {
		$(ret).appendTo(self.header_font_family);
		// console.log("success");
	})
	.fail(function() {
		// console.log("error");
	})
	.always(function() {
		// console.log("complete");
	});

	$(this.header_font_family).change(function(event){
		self.current_calendar_component.options.header_font_family = $(this).val();
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();
	});

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Month Style Options--------------------------
//___________________________________________________________________________|
	//Month
	// $('<hr>').appendTo(this.element);
	this.row2 = $('<div class="atk-row" style="display:block;margin:0;clear:both;"></div>').appendTo(this.element);
	this.col5 = $('<div class="atk-col-3"></div>').appendTo(this.row2);
	
	this.month_label = $('<label for="month">Month :</label>').appendTo(this.col5);
	this.month = $('<select id="month" class="btn btn-xs"></select>').appendTo(this.month_label);
	options = '<option value="00">Starting</option>';
	options += '<option value="01">January</option>';
	options += '<option value="02">February</option>';
	options += '<option value="03">March</option>';
	options += '<option value="04">April</option>';
	options += '<option value="05">May</option>';
	options += '<option value="06">June</option>';
	options += '<option value="07">July</option>';
	options += '<option value="08">August</option>';
	options += '<option value="09">September</option>';
	options += '<option value="10">October</option>';
	options += '<option value="11">November</option>';
	options += '<option value="12">December</option>';
	$(options).appendTo(this.month);

	$(this.month).change(function(event){
		if($(this).val() == "00"){
			self.current_calendar_component.options.month = self.current_calendar_component.options.starting_month;
		}else{
			self.current_calendar_component.options.month = $(this).val();
		}
		$('.xshop-designer-tool').xepan_xshopdesigner('check');
		self.current_calendar_component.render();

	});	


//```````````````````````````````````````````````````````````````````````````|
//------------------------------Starting Month Style Options-----------------
//___________________________________________________________________________|
	//Choose Your Calendar's Starting Month 
	this.col6 = $('<div class="atk-col-5 xdesigner-starting-month"></div>').appendTo(this.row2);
	this.starting_month = $('<label for="startDate">Starting Month :</label>').appendTo(this.col6);
	this.starting_month_text = $('<input name="startDate" id="xshop-designer-startDate" class="xshop-designer-calendar-month-picker" />').appendTo(this.col6);
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
            self.current_calendar_component.options.starting_date = $(this).val();
    		self.current_calendar_component.options.starting_month = month;
    		self.current_calendar_component.options.starting_year = year;
    		if(!self.current_calendar_component.options.month)
    			self.current_calendar_component.options.month = month;
			$('.xshop-designer-tool').xepan_xshopdesigner('check');
			self.current_calendar_component.render();

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



//```````````````````````````````````````````````````````````````````````````|
//------------------------------Add Event Style Options-----------------
//___________________________________________________________________________| 
    //Calendar Events
    // <div class="atk-buttonset">
    	//<button class="atk-button">Button</button>
    	//<button class="atk-button">Button</button>
    	//<button class="atk-button">Button</button>
    //</div>
	this.col7 = $('<div class="atk-col-4"></div>').appendTo(this.row2);
	this.buttonset = $('<div class="atk-buttonset"></div>').appendTo(this.col7);
    event_btn = $('<button class="atk-button atk-swatch-blue"><i class="glyphicon glyphicon-star-empty"></i>Add Events </button>').appendTo(this.buttonset);
	
	event_frame = $('<div id="xshop-designer-calendar-events-dialog" class="xshop-designer-calendar-events-frame"></div>').appendTo(this.element);

	form_row = $('<div class="atk-row atk-padding-small">').appendTo(event_frame);
	form_col1 = $('<div class="atk-col-4">').appendTo(form_row);
	this.event_date = $('<input type="text" name="event_date" id="xshop-designer-calendar-event-date" PlaceHolder="Date"/>').appendTo(form_col1);
	form_col2 = $('<div class="atk-col-6">').appendTo(form_row);
	this.event_message = $('<input type="text" name="event" id="xshop-designer-calendar-event" PlaceHolder="Event"/>').appendTo(form_col2);
	form_col3 = $('<div class="atk-col-2">').appendTo(form_row);
	this.event_add = $(' <button type="button">Add</button> ').appendTo(form_col3);
	this.event_count = $('<span class="badge1 xshop-designer-calendar-event-count"  title="Total Event Count"></span>').appendTo(event_btn);
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

			$('div').remove('#xshop-designer-calendar-events');
			table = '<div id="xshop-designer-calendar-events" class="atk-box"><div class="atk-table atk-table-zebra atk-table-bordered"><div class="atk-box-small atk-align-center"><h3>Your All Events</h3></div><table><thead><tr><th>Date</th><th>Message</th><th>Actions</th></tr></thead><tbody>';
			$.each(self.designer_tool.options.calendar_event,function(index,month_events){
				$.each(month_events,function(date,message){
					table += '<tr current_month='+self.current_calendar_component.options.month+' selected_date='+date+' ><td>'+date+'</td><td>'+message+'</td><td><a class="atk-effect-danger xshop-designer-calendar-event-delete" href="#">Delete</a></td></tr>';
				});
			});

			table +='</tbody></table></div></div>';
			$(table).appendTo(this);
			$('.xshop-designer-calendar-event-delete').click(function(event){

				selected_date = $(this).closest('tr').attr('selected_date');
				current_month = $(this).closest('tr').attr('current_month');
				delete (self.designer_tool.options.calendar_event[current_month][selected_date]);
				$(this).closest('tr').hide();
				self.current_calendar_component.render();
			});

		},
		close:function(){
			$('.xshop-designer-calendar-event-count').empty();
			$('.xshop-designer-calendar-event-count').text(' '+self.getCalendarEvent());
		}
	});

	$(event_btn).click(function(event){
		event_dialog.dialog("open");
	});

	$(this.event_add).click(function(event){
		curr_month = self.current_calendar_component.options.month;

		if(self.designer_tool.options.calendar_event[curr_month]== undefined)
		self.designer_tool.options.calendar_event[curr_month]= new Object;

		self.designer_tool.options.calendar_event[curr_month][self.event_date.val()]=new Object;
		// self.designer_tool.options.calendar_event[curr_month][self.event_date.val()] = self.event_message.val();
		self.designer_tool.options.calendar_event[curr_month][self.event_date.val()] = self.event_message.val();
		self.current_calendar_component.render();
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
	// this.col8 = $('<div class="atk-col-3"></div>').appendTo(this.row2);
	this.calendar_remove = $('<button class="atk-button atk-swatch-red" title="Remove Selected Calendar"><span class="glyphicon glyphicon-trash"></span></button>').appendTo(this.buttonset);
	this.calendar_remove.click(function(){
		dt  = self.current_calendar_component.designer_tool;
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

//added some class for vertical tabs
$( "#xepan-designer-vertical-tab" ).tabs().addClass( "ui-helper-clearfix" ); // for vertical tabs ui-tabs-vertical 
$( "#xepan-designer-vertical-tab li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

//```````````````````````````````````````````````````````````````````````````|
//------------------------------Add Hide Show Button-------------------------
//___________________________________________________________________________| 
	this.hide_show_btn = $('<button class="atk-button btn-warning" title="Hide or show thw options"> <i class="icon-atkcog"></i> </button>').appendTo(this.buttonset);
	hide_show_frame = $('<div id="xshop-designer-calendar-options-dialog" class="xshop-designer-calendar-options-frame"></div>').appendTo(this.element);
	
	// hide_header_text_align
	// hide_header_bg_color
	// hide_header_text_bold
	// hide_header_show_hide_btn
	// hide_header_height
	header_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Header options to show/hide</div><div class="panel-body"></div><ul class="list-group"><li class="list-group-item" data_variable="hide_header_font_size">Font Size<input data_variable="hide_header_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_font_color">Font Color<input data_variable="hide_header_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_text_align">Text Align<input data_variable="hide_header_text_align" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_bg_color">Background Color<input data_variable="hide_header_bg_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_text_bold">Bold Text<input data_variable="hide_header_text_bold" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_show_hide_btn">Text Display<input data_variable="hide_header_show_hide_btn" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_header_height">Text Height<input data_variable="hide_header_height" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	day_date_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Day Date options to show/hide</div><div class="panel-body"></div><ul class="list-group"><li class="list-group-item" data_variable="hide_day_date_font_size">Font Size<input data_variable="hide_day_date_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_date_font_color">Font Color<input data_variable="hide_day_date_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_date_font_height">Cell Height<input data_variable="hide_day_date_font_height" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	day_name_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Day Name options to show/hide</div><div class="panel-body"></div><ul class="list-group"><li class="list-group-item" data_variable="hide_day_name_font_size">Font Size<input data_variable="hide_day_name_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_font_color">Font Color<input data_variable="hide_day_name_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_day_name_font_bg_color">Background Color<input data_variable="hide_day_name_font_bg_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	event_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Event options to show/hide</div><div class="panel-body"></div><ul class="list-group"><li class="list-group-item" data_variable="hide_event_font_size">Font Size	<input data_variable="hide_event_font_size" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_event_font_color">Font Color	<input data_variable="hide_event_font_color" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);
	other_calendar_options = $('<div class="panel panel-default xshop-calendar-editor-options-to-show"><div class="panel-heading">Other Calendar options to show/hide</div><div class="panel-body"></div><ul class="list-group"><li class="list-group-item" data_variable="hide_month">Hide Month/Sequence<input data_variable="hide_month" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_starting_month">Starting Month<input data_variable="hide_starting_month" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li><li class="list-group-item" data_variable="hide_remove_btn">Remove Button<input data_variable="hide_remove_btn" class="xshop-calendar-show-hide-checkbox" type="checkbox" /></li></ul></div>').appendTo(hide_show_frame);

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
			//Show to default/Save Options
			//Header Options
			

			$('input[data_variable="hide_header_font_size"]').prop('checked',self.current_calendar_component.options.hide_header_font_size);
			$('input[data_variable="hide_header_font_color"]').prop('checked',self.current_calendar_component.options.hide_header_font_color);
			$('input[data_variable="hide_header_text_align"]').prop('checked',self.current_calendar_component.options.hide_header_text_align);
			$('input[data_variable="hide_header_bg_color"]').prop('checked',self.current_calendar_component.options.hide_header_bg_color);
			$('input[data_variable="hide_header_text_bold"]').prop('checked',self.current_calendar_component.options.hide_header_text_bold);
			$('input[data_variable="hide_header_show_hide_btn"]').prop('checked',self.current_calendar_component.options.hide_header_show_hide_btn);
			$('input[data_variable="hide_header_height"]').prop('checked',self.current_calendar_component.options.hide_header_height);
			
			//Day Date/Date
			$('input[data_variable="hide_day_date_font_size"]').prop('checked',self.current_calendar_component.options.hide_day_date_font_size);
			$('input[data_variable="hide_day_date_font_color"]').prop('checked',self.current_calendar_component.options.hide_day_date_font_color);
			$('input[data_variable="hide_day_date_font_height"]').prop('checked',self.current_calendar_component.options.hide_day_date_font_height);
			
			//Day Name/Week
			$('input[data_variable="hide_day_name_font_size"]').prop('checked',self.current_calendar_component.options.hide_day_name_font_size);
			$('input[data_variable="hide_day_name_font_color"]').prop('checked',self.current_calendar_component.options.hide_day_name_font_color);
			$('input[data_variable="hide_day_name_font_bg_color"]').prop('checked',self.current_calendar_component.options.hide_day_name_font_bg_color);

			//Event
			$('input[data_variable="hide_event_font_size"]').prop('checked',self.current_calendar_component.options.hide_event_font_size);
			$('input[data_variable="hide_event_font_color"]').prop('checked',self.current_calendar_component.options.hide_event_font_color);

			//Calendar Options
			$('input[data_variable="hide_month"]').prop('checked',self.current_calendar_component.options.hide_month);
			$('input[data_variable="hide_starting_month"]').prop('checked',self.current_calendar_component.options.hide_starting_month);
			$('input[data_variable="hide_remove_btn"]').prop('checked',self.current_calendar_component.options.hide_remove_btn);
		
		}
	});

	$(this.hide_show_btn).click(function(){
		option_display_dialog.dialog("open");
	});

    //Set from Saved Values
	this.setCalendarComponent = function(component){
		// console.log(component);
		this.current_calendar_component  = component;		
		$(this.header_font_size).val(component.options.header_font_size);
		$(this.header_color_picker).colorpicker('setColor',component.options.header_font_color);

		$(this.day_date_color_picker).colorpicker('setColor',component.options.day_date_font_color);
		$(this.day_date_font_size).val(component.options.day_date_font_size);

		$(this.day_name_bg_color_picker).colorpicker('setColor',component.options.day_name_bg_color);

		$(this.day_name_color_picker).colorpicker('setColor',component.options.day_name_font_color);
		$(this.day_name_font_size).val(component.options.day_name_font_size);

		$(this.event_color_picker).colorpicker('setColor',component.options.event_font_color);
		$(this.event_font_size).val(component.options.event_font_size);
		
		$(this.height).val(component.options.height);

		$(this.events).val(component.options.events);
		$(this.cell_height).val(component.options.calendar_cell_heigth);
		// console.log(component.options.calendar_cell_heigth);

		$(this.designer_mode).val(component.options.designer_mode);
		$(this.load_design).val(component.options.load_design);

		$(this.month).val(component.options.month);
		$(this.starting_date).val(component.options.starting_date);

		if(this.designer_tool.options.calendar_starting_month)
			$(this.starting_month_datepicker).datepicker('setDate',new Date(this.designer_tool.options.calendar_starting_year,parseInt(this.designer_tool.options.calendar_starting_month),0));
		else
			$(this.starting_month_datepicker).datepicker('setDate',new Date(component.options.starting_year,parseInt(component.options.starting_month),0));

		$(this.starting_year).val(component.options.starting_year);
		$(this.type).val(component.options.type);
		$(this.width).val(component.options.width);
		$(this.x).val(component.options.x);
		$(this.y).val(component.options.y);

		$(this.event_count).html(self.getCalendarEvent());

		if(component.options.designer_mode == false && 0){
			//Hide Header Font Size
			if(component.options.hide_header_font_size){
				$(this.header_font_size_label).show();
				$(this.header_font_color).show();
			}else{
				$(this.header_font_size_label).hide();
				$(this.header_font_color).hide();

			}

			//Header Font Color
			if(component.options.hide_header_font_color){
				$(this.header_color_label).show();
				$(this.header_color_picker).show();
			}else{
				$(this.header_color_label).hide();
				$(this.header_color_picker).hide();

			}

			// hide_header_text_align
			if(component.options.hide_header_text_align){
				$(this.header_align_label).show();
				$(this.header_align).show();
			}else{
				$(this.header_align_label).hide();
				$(this.header_align).hide();
			}			
			// hide_header_bg_color
			if(component.options.hide_header_bg_color){
				$(this.header_bg_color_label).show();
				$(this.header_bg_color_picker).show();
			}else{
				$(this.header_bg_color_label).hide();
				$(this.header_bg_color_picker).hide();
			}

			// hide_header_text_bold
			if(component.options.hide_header_text_bold){
				$(this.h_btn_set).show();
				$(this.h_bold).show();
			}else{
				$(this.h_btn_set).hide();
				$(this.h_bold).hide();
			}

			//hide_header_show_hide_btn
			if(component.options.hide_header_show_hide_btn){
				$(this.showhide_btn_set).show();
				$(this.showhide_btn).show();
			}else{
				$(this.showhide_btn_set).hide();
				$(this.showhide_btn).hide();
			}

			// // hide_header_height
			// if(component.options.hide_header_show_hide_btn){
			// 	$(this.showhide_btn_set).show();
			// 	$(this.showhide_btn).show();
			// }else{
			// 	$(this.showhide_btn_set).hide();
			// 	$(this.showhide_btn).hide();
			// }


			//Hide Day Date Font Size
			if(component.options.hide_day_date_font_size){
				$(this.day_date_font_size_label).hide();
				$(this.day_date_font_size).hide();
			}else{
				$(this.day_date_font_size_label).show();
				$(this.day_date_font_size).show();
			}

			//Hide Day Date Font color
			if(component.options.hide_day_date_font_color){
				$(this.day_date_color_picker).hide();
				$(this.day_date_color_label).hide();
			}else{
				$(this.day_date_color_picker).show();
				$(this.day_date_color_label).show();
			}

			//Hide Day Date Font Height
			if(component.options.hide_day_date_font_height){
				$(this.height_label).hide();
				$(this.cell_height).hide();
			}else{
				$(this.height_label).show();
				$(this.cell_height).show();
			}

			//Hide Day Name Font Size
			if(component.options.hide_day_name_font_size){
				$(this.day_name_font_size_label).hide();
				$(this.day_name_font_size).hide();
			}else{
				$(this.day_name_font_size_label).show();
				$(this.day_name_font_size).show();
			}

			//Hide Day Name Font Color
			if(component.options.hide_day_name_font_color){
				$(this.day_name_color_label).hide();
				$(this.day_name_color_picker).hide();
			}else{
				$(this.day_name_color_label).show();
				$(this.day_name_color_picker).show();
			}

			//Hide Day Name Font BG Color
			if(component.options.hide_day_name_font_bg_color){
				$(this.day_name_bg_color_label).hide();
				$(this.day_name_bg_color_picker).hide();
			}else{
				$(this.day_name_bg_color_label).show();
				$(this.day_name_bg_color_picker).show();
			}

			//Hide Event Font Size
			if(component.options.hide_event_font_size){
				$(this.event_font_size_label).hide();
				$(this.event_font_size).hide();
			}else{
				$(this.event_font_size_label).show();
				$(this.event_font_size).show();
			}

			//Hide Event Font Color
			if(component.options.hide_event_font_color){
				$(this.event_color_label).hide();
				$(this.event_color_picker).hide();
			}else{
				$(this.event_color_label).show();
				$(this.event_color_picker).show();
			}

			//Hide Month 
			if(component.options.hide_month){
				$(this.month_label).hide();
				$(this.month).hide();
			}else{
				$(this.month_label).show();
				$(this.month).show();
			}

			//Hide Starting Month
			if(component.options.hide_starting_month){
				$(this.starting_month_datepicker).hide();
				$(this.starting_month).hide();
			}else{
				$(this.starting_month_datepicker).show();
				$(this.starting_month).show();
			}
			
			//Hide Remove Button
			if(component.options.hide_remove_btn){
				$(this.calendar_remove).hide();
			}else{
				$(this.calendar_remove).show();
			}

		}

	}
}


Calendar_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;
	this.options = {
		header_font_size:32,
		header_font_color:'#000000',
		header_font_family:'freemono',
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
		event_font_size:10,
		event_font_family:'freemono',
		event_font_size:'#00000',
		day_name_bg_color:'#FFFFFF',
		calendar_cell_heigth:20,
		calendar_cell_bg_color:undefined,
		alignment: "center",
		valignment:'middle',
		border:1,

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
		x:undefined,
		y:undefined,
		zindex:0,
		events:{},
		type: 'Calendar',

		movable:false,

		hide_header_font_size:false,
		hide_header_font_color:false,
		hide_header_text_align:false,
		hide_header_bg_color:false,
		hide_header_text_bold:false,
		hide_header_show_hide_btn:false,
		hide_header_height:false,

		hide_day_date_font_size:true,
		hide_day_date_font_color:true,
		hide_day_date_font_height:true,

		hide_day_name_font_size:true,
		hide_day_name_font_color:true,
		hide_day_name_font_bg_color:true,

		hide_event_font_size:true,
		hide_event_font_color:true,

		hide_month:false,
		hide_starting_month:false,
		hide_remove_btn:true,
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
		tool_btn = $('<div class="btn"><i class="glyphicon glyphicon-calendar"></i><br>Calendar</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));

		this.editor = new xShop_Calendar_Editor(parent.find('.xshop-designer-tool-topbar-options'),self.designer_tool);

		// CREATE NEW Calendar COMPONENT ON CANVAS Default 
		tool_btn.click(function(event){
			// create new CalendarComponent type object
				// $.univ().frameURL('Add Calendar Form','index.php?page=xShop_page_designer_calendar&item_id='+self.designer_tool.options.item_id+'&item_member_design_id='+self.designer_tool.options.item_member_design_id+'&xsnb_design_template='+self.designer_tool.options.designer_mode);
			self.designer_tool.current_selected_component = undefined;
			// create new CalendarComponent type object
			var new_calendar = new Calendar_Component();
			new_calendar.init(self.designer_tool,self.canvas, self.editor);
			self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].components.push(new_calendar);
			new_calendar.render(true);
		});

	}

	this.render = function(place_in_center){
		var self = this;
		
		// console.log("month = "+ self.designer_tool.options.calendar_starting_month + "Year" + self.designer_tool.options.calendar_starting_year);
		
		if(self.options.load_design == true){
			self.designer_tool.options.calendar_event = JSON.parse(self.options.events);
			if(self.designer_tool.options.calendar_starting_month == undefined){
				self.designer_tool.options.calendar_starting_month = self.options.starting_month;
				self.designer_tool.options.calendar_starting_year = self.options.starting_year;
			}
			self.options.load_design = "false";
		}
		// console.log(JSON.stringify(self.designer_tool.options.calendar_event));
		// console.log(self.designer_tool.options.calendar_event);
		self.options.events = JSON.stringify(self.designer_tool.options.calendar_event);
		self.options.starting_date = self.designer_tool.options.calendar_starting_month;

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
					//Displaying x and y position of calender on it's editor tool
					self.editor.calendar_x.val(position.left);
					self.editor.calendar_y.val(position.top);
				}
			}).resizable({
				containment: "parent",
				handles: "e, se, s",
				autoHide: true,
				stop: function(e,ui){
					self.options.width = self.designer_tool.screen2option(ui.size.width);
					self.options.height = self.designer_tool.screen2option(ui.size.height);
					// alert(self.options.height);
					self.editor.calendar_width.val(ui.size.width);
					self.editor.calendar_height.val(ui.size.height);
					self.render();
				}
			});
			$(this.element).data('component',self);

			$(this.element).click(function(event) {
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
	            self.designer_tool.option_panel.css('top',0);

	            designer_currentTarget = $(event.currentTarget);
	            height_diff = parseInt($(self.designer_tool.option_panel).height());
	            top_value = parseInt($(designer_currentTarget).offset().top) - parseInt(height_diff);

	            self.designer_tool.option_panel.css('top',top_value);
	            self.designer_tool.option_panel.css('left',$(designer_currentTarget).offset().left);
	            self.editor.calendar_border.val(self.options.border);

	            self.editor.setCalendarComponent(self);
	            
	            if(self.designer_tool.options.designer_mode){
		            self.designer_tool.freelancer_panel.FreeLancerComponentOptions.element.show();
		            self.designer_tool.freelancer_panel.setComponent($(this).data('component'));
	            }else{

	            	// self.editor.hide_show_btn.hide();
	            }

		        event.stopPropagation();

				//check For the Z-index
            	if(self.options.zindex == 0){
            		$('span.xshop-designer-calendar-down-btn').addClass('xepan-designer-button-disable');
            	}else
            		$('span.xshop-designer-calendar-down-btn').removeClass('xepan-designer-button-disable');
            		
			});
		}else{
			this.element.show();
		}

		this.element.css('top',self.options.y  * self.designer_tool.zoom);
		this.element.css('left',self.options.x * self.designer_tool.zoom);

		if(this.xhr != undefined)
			this.xhr.abort();

		this.xhr = $.ajax({
			url: '?page=xepan_commerce_designer_rendercalendar',
			type: 'GET',
			data: { 
					header_font_size:self.options.header_font_size,
					header_font_color:self.options.header_font_color,
					header_bg_color:self.options.header_bg_color,
					header_bold:self.options.header_bold,
					header_show:self.options.header_show,
					header_align:self.options.header_align,
					header_font_family:self.options.header_font_family,
					day_date_font_size:self.options.day_date_font_size,
					day_date_font_color:self.options.day_date_font_color,
					day_name_font_size:self.options.day_name_font_size,
					day_name_font_color:self.options.day_name_font_color,
					day_name_bold:self.options.day_name_bold,
					day_name_cell_height:self.options.day_name_cell_height,
					event_font_size:self.options.event_font_size,
					event_font_color:self.options.event_font_color,
					day_name_bg_color:self.options.day_name_bg_color,
					calendar_cell_heigth:self.options.calendar_cell_heigth,
					calendar_cell_bg_color:self.options.calendar_cell_bg_color,
					alignment:self.options.alignment,
					valignment:self.options.valignment,
					border:self.options.border,

					zoom: self.designer_tool.zoom,
					zindex:self.options.zindex,
					month:self.options.month,
					width:self.options.width,
					height:self.options.height,

					global_starting_month:self.designer_tool.options.calendar_starting_month,
					global_starting_year:self.designer_tool.options.calendar_starting_year,
					starting_month:self.options.starting_month,
					starting_year:self.options.starting_year,
					resizable:self.options.resizable,
					movable:self.options.movable,
					colorable:self.options.colorable,
					editor:self.options.editor,
					designer_mode:self.options.designer_mode,
					x:self.options.x,
					y:self.options.y,
					events:JSON.stringify(self.designer_tool.options.calendar_event),

					movable:self.options.movable,
					hide_header_font_size:self.options.hide_header_font_size,
					hide_header_font_color:self.options.hide_header_font_color,

					hide_day_date_font_size:self.options.hide_day_date_font_size,
					hide_day_date_font_color:self.options.hide_day_date_font_color,
					hide_day_date_font_height:self.options.hide_day_date_font_height,

					hide_day_name_font_size:self.options.hide_day_name_font_size,
					hide_day_name_font_color:self.options.hide_day_name_font_color,
					hide_day_name_font_bg_color:self.options.hide_day_name_font_bg_color,

					hide_event_font_size:self.options.hide_event_font_size,
					hide_event_font_color:self.options.hide_event_font_color,

					hide_month:self.options.hide_month,
					hide_starting_month:self.options.hide_starting_month,
					hide_remove_btn:self.options.hide_remove_btn
					},
		})
		.done(function(ret) {
			self.element.find('img').attr('src','data:image/png;base64, '+ ret);
			// $(ret).appendTo(self.element.find('span').html(''));			
			self.xhr=undefined;
			if(place_in_center){
				window.setTimeout(function(){
					// self.element.center(self.designer_tool.canvas);
					// self.element.center(self.designer_tool.canvas);
					self.options.x = self.element.css('left').replace('px','') / self.designer_tool.zoom;
					self.options.y = self.element.css('top').replace('px','') / self.designer_tool.zoom;
				},200);
				place_in_center = 0;
			}
		})
		.fail(function(ret) {
			eval(ret);
			console.log("Calendar Error");
		})
		.always(function() {
			console.log("Calendar complete");
		});
	}
}