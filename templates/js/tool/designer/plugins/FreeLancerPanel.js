PageBlock = function(parent,designer,canvas, manager){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.manager = manager;
	this.current_component = undefined;
	this.element =undefined;

	this.add_div=undefined;
	this.input_box=undefined;
	this.add_btn=undefined;
	this.page_list_div = undefined;

	this.init = function(pages_data_array){
		var self = this;
		// make a nice div
		page_col  = $('<div class="col-md-6 col-sm-6 col-lg-6"></div>').appendTo(this.parent);
		this.element = $('<div class="xshop-designer-ft-page text-center">Pages</div>').appendTo(page_col);
		// add input box and add button

		this.add_div = $('<div class="input-group"></div>').appendTo(this.element);
		this.input_box =$('<input type="text" class="form-control" placeholder="New Page"/>').appendTo(this.add_div);
		this.add_btn = $('<span class="input-group-btn" style="font-size:inherit !important;"><button class="btn" type="button">Add</button></span>').appendTo(this.add_div);

		this.add_btn.click(function(event){
			self.addPage(self.input_box.val());
			// self.designer_tool.bottom_bar.renderTool();
		});

		// make a list of current pages with remove buton
		this.page_list_div = $('<div class="list-group xdesigner-page-list"></div>').appendTo(this.element);

		$.each(pages_data_array,function(index,page){
			self.addPageView(page,index);
			// self.addPage(page);
		});

		$(".xdesigner-page-list").sortable({
      		stop : function(event, ui){
      			if(self.managePageSequence())
      				$.univ().successMessage('Page Order Changed');
      			else
      				$.univ().errorMessage('Page Order Unchanged');
      			// self.designer_tool.bottom_bar.renderTool();
        	}
    	});		
	}

	this.managePageSequence = function(){
		self = this;
		new_object = {};
		$('.xdesigner-page-list').children().each(function (i,ui) {
			pagename = $(ui).data('pagename');
			new_object[pagename] =  new Object();
			self.designer_tool.pages_and_layouts[pagename]['sequence_no'] = i + 1;
			new_object[pagename] =  self.designer_tool.pages_and_layouts[pagename];
		});
		
		self.designer_tool.pages_and_layouts = new_object;
		return true;
	}

	this.addPage = function(page_name,duplicate_from_page){
		var self = this;
		page_name = $.trim(page_name);

		// validate page_name
		// existing page name
		if(page_name == ""){
			this.input_box.addClass('atk-effect-danger has-error atk-form-error');
			this.input_box.css('border-color','red');
			return;
		}

		if( !(this.pageExist(page_name)) ){
			if($.trim(duplicate_from_page)){
				this.designer_tool.pages_and_layouts[page_name] =  $.extend({},this.designer_tool.pages_and_layouts[duplicate_from_page]);
				this.designer_tool.layout_finalized[page_name] = "Main Layout";
			}else{

				this.designer_tool.pages_and_layouts[page_name] =  new Object();
				this.designer_tool.pages_and_layouts[page_name]['Main Layout'] =  new Object();
				this.designer_tool.pages_and_layouts[page_name]['Main Layout'].components = [];

				this.designer_tool.pages_and_layouts[page_name]['Main Layout'].background = new BackgroundImage_Component();
				this.designer_tool.pages_and_layouts[page_name]['Main Layout'].background.init(self.designer_tool, self.canvas,null);
				this.designer_tool.layout_finalized[page_name] = "Main Layout";
			}

			
			this.addPageView(page_name);
			this.input_box.val("");
			this.managePageSequence();
		}
		// add page to pagelistdiv and add to designertool pagesnadlayout object

		// console.log("after page add");
		// console.log(self.designer_tool.pages_and_layouts);
	}

	//Return array of all pages of loaded designs
	this.allPage = function(){
		var page_array = [];

		$.each(this.designer_tool.pages_and_layouts,function(index,value){
			page_array.push(index);
		});

		$.each(this.designer_tool.pages_and_layouts,function(index,value){
			// page_array.move(,);
			fromIndex = page_array.indexOf(index);
			toIndex = value['sequence_no'];

			var element = page_array[fromIndex];
		    page_array.splice(fromIndex, 1);
		    page_array.splice(toIndex, 0, element);
		});
		
		return page_array;
	}

	this.addPageView = function(page_name,order){
		var self = this;

		page_row = $('<div class="page_row list-group-item" data-pagename="'+page_name+'"></div>').appendTo(this.page_list_div);
		// div = $('<button class="list-group-item"></button>').appendTo(page_row);
		page_name = $('<span class="xshop-designer-ft-page-name"></span>').appendTo(page_row).html(page_name);
		rm_btn = $('<span class="label label-danger pull-right">x</span>').appendTo(page_row).data('page_name',page_name);
		
		page_row.click(function(event){
			// event.preventDefault();
			$(this).closest('.list-group').find('.list-group-item ').removeClass('active').addClass('activeOff');
			$(this).addClass('active').removeClass('activeOff');
			self.manager.layoutblock.setPage($(this).find('span.xshop-designer-ft-page-name').html());
		});

		rm_btn.click(function(event){
			if(self.removePage($(this).data('page_name')))
				$(this).closest(".page_row").remove();
		});

		duplicate_btn = $('<span class="label label-default pull-right xdesigner-page-btn" title="Page Duplicate "><i class="icon-paste"></i></span>').appendTo(page_row).data('page_name',page_name);
		duplicate_btn.click(function(event){
			if(new_page_name = prompt("New Page Name", page_name[0].firstChild.data+" - copy")){
				self.addPage(new_page_name,page_name[0].firstChild.data);
				// self.designer_tool.bottom_bar.renderTool();
				$.univ().successMessage('Page Duplicate Successfully');
			}else{
				$.univ().errorMessage('Page Duplicate Cancelled');
			}
		});
	}

	this.removePage = function(page_name){
		// TODO Validation :: Do not remove if 'Fron Page'
 		// if(confirm('Are you sure?'))
 		if(page_name[0].firstChild.data == 'Front Page'){
 			$.univ().errorMessage('cannot Delete');
 			return false;
 		}else{
			this.designer_tool.pages_and_layouts[page_name[0].firstChild.data] = null;
			delete this.designer_tool.pages_and_layouts[page_name[0].firstChild.data];
 			return true;
 		}
	}

	this.pageExist = function(page_name){
		page_name = $.trim(page_name);
		return_value = false;
		$.each(this.designer_tool.pages_and_layouts,function(index,value){
			// console.log(index+"::"+page_name);
			if(index === page_name){
				alert('page exist');
				return_value = true;
			}
		});
		
		return return_value;
		
	},

	this.pageCount = function(){
		return $('.xdesigner-page-list').children().length;
	}

}

LayoutBlock = function(parent,designer,canvas, manager){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.manager = manager;
	this.current_component = undefined;
	this.element =undefined;

	this.current_page= undefined;
	this.layout_list_div = undefined;

	this.init = function(){
		var self = this;
		// make a nice div		
		layout_col  = $('<div class="col-md-6 col-sm-6 col-lg-6"></div>').appendTo(this.parent);
		this.element = $('<div class="xshop-designer-ft-layout text-center">Layouts</div>').appendTo(layout_col);
		// add input box and add button
		this.add_div = $('<div class="input-group"></div>').appendTo(this.element);
		this.input_box =$('<input type="text" class="form-control" placeholder="New Layout"/>').appendTo(this.add_div);
		this.add_btn = $('<span class="input-group-btn" style="font-size:inherit !important;"><button class="btn" type="button">Add</button></span>').appendTo(this.add_div);

		this.add_btn.click(function(event){
			self.addLayout(self.input_box.val(),true);
		});

		// make a list of current pages with remove buton
		this.layout_list_div = $('<div class="list-group"></div>').appendTo(this.element);

	}

	this.setPage = function(page_name){
		var self=this;
		page_name = $.trim(page_name);

		this.current_page = page_name;
		// console.log('changed page to ' + page_name);
		//Show Active on Current Page
		$( "div.xshop-designer-ft-page" ).find( 'a.list-group-item:contains('+page_name+')' ).addClass('active');
		//Empty all html:remove repeating layout
		$('div.xshop-designer-ft-layout').find('div.list-group').empty();
		$.each(this.designer_tool.pages_and_layouts[page_name],function(index,layout){			
			if(index === "sequence_no")
				return;
			self.addLayout(index,false);
		});
		// create layout dis with remove button and its event
	}

	this.addLayout = function(layout_name,is_new_layout,duplicate_from_layout){
		var self = this;
		layout_name = $.trim(layout_name);
		duplicate_from_layout = $.trim(duplicate_from_layout);
		// validate page_name
		// existing page name
		if(layout_name == ""){
			this.input_box.addClass('atk-effect-danger has-error atk-form-error');
			this.input_box.css('border-color','red');
			return;
		}
		if(!is_new_layout || !(this.layoutExist(layout_name))){
			
			if(is_new_layout){
				var new_layout= new Object();
				new_layout.components=[];
				new_layout.background = new BackgroundImage_Component();
				new_layout.background.init(self.designer_tool, self.canvas,null);
				this.designer_tool.pages_and_layouts[this.current_page][layout_name] =  new_layout;
			}else if(duplicate_from_layout){
				this.designer_tool.pages_and_layouts[this.current_page][layout_name] =  $.extend({},this.designer_tool.pages_and_layouts[this.current_page][duplicate_from_layout]);
			}


			layout_row = $('<div class="layout_row list-group-item"></div>').appendTo(this.layout_list_div);
			// div = $('<button class="list-group-item"></button>').appendTo(layout_row).html(layout_name);
			layout_name_obj = $('<span class="xshop-designer-ft-layout-name"></span>').appendTo(layout_row).html(layout_name);
			rm_btn = $('<span class="label label-danger pull-right">x</span>').appendTo(layout_row).data('layout_name',layout_name);

			rm_btn.click(function(event){
				if(self.removeLayout($(this).data('layout_name')))
					$(this).closest(".layout_row").remove();			
			});

			duplicate_btn = $('<span class="label label-default pull-right xdesigner-layout-btn" title="Layout Duplicate "><i class="icon-paste"></i></span>').appendTo(layout_row).data('layout_name',layout_name);
			duplicate_btn.click(function(event){

				if(new_layout_name = prompt("New Layout Name", $(this).data('layout_name')+" - copy")){
					self.addLayout(new_layout_name,false,$(this).data('layout_name'));
					// self.designer_tool.bottom_bar.renderTool();
					$.univ().successMessage('Layout Duplicate Successfully');
				}else{
					$.univ().errorMessage('Layout Duplicate Cancelled');
				}
			});

			// console.log(this.designer_tool.pages_and_layouts[this.current_page]);
			this.input_box.val("");
		}			
	}

	this.removeLayout = function(layout_name){
		if(layout_name == "Main Layout"){
			$.univ().errorMessage('Cannot Delete');
			return false;
		}else{
			this.designer_tool.pages_and_layouts[this.current_page][layout_name] = null;
			delete this.designer_tool.pages_and_layouts[this.current_page][layout_name];
			return true;
		}
	}

	this.layoutExist = function(layout_name){
		layout_name = $.trim(layout_name);
		$.each(this.designer_tool.pages_and_layouts[this.current_page],function(index,layout){
			// console.log(index+"::"+layout_name);
			if(index === layout_name){
				return true;
			}
		});
		
		return false;
		
	}

}

FreeLancerPageLayoutManager = function(parent,designer, canvas){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.current_component = undefined;
	this.element =undefined;
	this.page=undefined;
	this.pageblock=undefined;
	this.layoutblock = undefined;


	this.init = function(){
		var self = this;
		this.element = $('<div class="btn xshop-designer-freelancer-tool" title="Pages and Layout" ><i class="glyphicon glyphicon-list-alt"></i><br>P&L</div>').appendTo(this.parent);
		this.page = $('<div></div>').appendTo(this.element);

		this.pageblock = new PageBlock(this.page,this.designer_tool,this.canvas,this);
		// console.log(this.pageblock.allPage());
		this.pageblock.init(this.pageblock.allPage());

		this.layoutblock = new LayoutBlock(this.page,this.designer_tool,this.canvas,this);
		this.layoutblock.init();
		// this.layoutblock.setPage('page1');

		this.page.dialog({autoOpen: false, modal: true, width:600});
		this.element.click(function(event){
			// Update recent pages and layouts 
			self.page.dialog('open');
		});
	}
}

FreeLancerDesignerOptions = function(parent, designer, canvas){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.element = undefined;

	this.init =  function(){
		var self =this;
		
		this.element = $('<div class="btn xshop-designer-freelancer-designer-mode-options" title="Pages and Layout" ><i class="glyphicon glyphicon-list-alt"></i><br>Designer Mode</div>').appendTo(this.parent);
		this.designeroption = $('<div></div>').appendTo(this.element);
		this.designeroption.dialog({autoOpen: false, modal: true, width:600});
		
        tool_settings = $('<ul class="list-group xshop-designer-setting-options"></ul>').appendTo(this.designeroption);

        model_label = $('<li class="list-group-item" data_variable="Mode">Designer Mode: </li> ').appendTo(tool_settings);
		var setting_button_set = $('<select class="list-group xshop-designer-tool-mode-setting">Designer Mode</select>').appendTo(model_label);

		$('<option value="Primary" class="atk-move-left">Primary</option><option value="multi-page-single-layout" class="atk-move-left">Multi Page Single Layout</option>').appendTo(setting_button_set);


        this.primary = $('<li class="list-group-item" data-variable="mode" data_value="Primary"><input data_variable="Primary" type="checkbox" class="xshop-designer-setting-option"/> Primary </li>').appendTo(setting_button_set);
        this.multi_page_single_layout = $('<li class="list-group-item" data-variable="mode" data_value="multi-page-single-layout"><input data_value="multi-page-single-layout" data-variable="mode" type="checkbox" class="xshop-designer-setting-option"/> Multi Page Single Layout </li>').appendTo(setting_button_set);

        this.btn_show_BackgroundImage = $('<li class="list-group-item" data_variable="BackgroundImage"><input data_variable="BackgroundImage" type="checkbox" class="xshop-designer-toolbtn"/> Hide BackGround Image Tool</li>').appendTo(tool_settings);
        this.btn_show_Text = $('<li class="list-group-item" data_variable="Text"><input data_variable="Text" type="checkbox" class="xshop-designer-toolbtn"/> Hide Text Tool </li>').appendTo(tool_settings);
        this.btn_show_Image = $('<li class="list-group-item" data_variable="Image"><input data_variable="Image" type="checkbox" class="xshop-designer-toolbtn"/> Hide Image Tool </li>').appendTo(tool_settings);
        this.btn_show_Calendar = $('<li class="list-group-item" data_variable="Calendar"><input data_variable="Calendar" type="checkbox" class="xshop-designer-toolbtn"/> Hide Calendar Tool </li>').appendTo(tool_settings);
        this.btn_show_CalendarStartingMonth = $('<li class="list-group-item" data_variable="show_tool_calendar_starting_month"><input data_variable="show_tool_calendar_starting_month" type="checkbox" class="xshop-designer-tool-display-option"/> Hide Calendar Starting Month Tool</li>').appendTo(tool_settings);
        this.btn_show_ZoomPlus = $('<li class="list-group-item" data_variable="ZoomPlus"><input data_variable="ZoomPlus" type="checkbox" class="xshop-designer-toolbtn"/> Hide Zoom Plus Tool </li>').appendTo(tool_settings);
        this.btn_show_ZoomMinus = $('<li class="list-group-item" data_variable="ZoomMinus"><input data_variable="ZoomMinus" type="checkbox" class="xshop-designer-toolbtn"/> Hide Zoom Minus Tool </li>').appendTo(tool_settings);

		$('.xshop-designer-toolbtn').click(function(event){
			// checked = $(this).is(':checked');
			option = $(this).attr('data_variable');
			
			if(self.designer_tool.options.ComponentsIncludedToBeShow == null || self.designer_tool.options.ComponentsIncludedToBeShow == undefined){
				  self.designer_tool.options.ComponentsIncludedToBeShow.push(option);
			}else{
				var idx = $.inArray(option, self.designer_tool.options.ComponentsIncludedToBeShow);
				if (idx == -1) {
				  self.designer_tool.options.ComponentsIncludedToBeShow.push(option);
				}else{
				  self.designer_tool.options.ComponentsIncludedToBeShow.splice(idx, 1);
				}
			}			
		});

        this.btn_show_canvas = $('<li class="list-group-item" data_variable="show_canvas"><input data_variable="show_canvas" type="checkbox" class="xshop-designer-tool-display-option"/> Show Canvas </li>').appendTo(tool_settings);
        this.btn_show_page_and_layout = $('<li class="list-group-item" data_variable="show_pagelayout_bar"><input data_variable="show_pagelayout_bar" type="checkbox" class="xshop-designer-tool-display-option"/> Show Page And Layout Bar </li>').appendTo(tool_settings);
        this.btn_show_layout_bar = $('<li class="list-group-item" data_variable="show_layout_bar"><input data_variable="show_layout_bar" type="checkbox" class="xshop-designer-tool-display-option"/> Show Layout Bar </li>').appendTo(tool_settings);

        $('.xshop-designer-tool-display-option').click(function(event){
			checked = $(this).is(':checked');
			option = $(this).attr('data_variable');
			if(checked){
				eval("self.designer_tool.options."+option+"= true;");
			}else{
				eval("self.designer_tool.options."+option+"= false;");
			}
		});

        if(self.designer_tool.options.show_canvas){
        	$('input[data_variable="show_canvas"]').prop('checked',true);
        }

        if(self.designer_tool.options.show_pagelayout_bar){
        	$('input[data_variable="show_pagelayout_bar"]').prop('checked',true);
        }

        if(self.designer_tool.options.show_layout_bar){
        	$('input[data_variable="show_layout_bar"]').prop('checked',true);
        }
        if(self.designer_tool.options.show_tool_calendar_starting_month == true || self.designer_tool.options.show_tool_calendar_starting_month == "true" || self.designer_tool.options.show_tool_calendar_starting_month == 1){
        	$('input[data_variable="show_tool_calendar_starting_month"]').prop('checked',true);
        }

        // hide tool display item 
        if(self.designer_tool.options.mode === "multi-page-single-layout"){
        	$(this.btn_show_canvas).hide();
        	$(this.btn_show_page_and_layout).hide();
        	$(this.btn_show_layout_bar).hide();
        }

        // Designer Mode Select field changed
		$(setting_button_set).change(function(event){
			self.designer_tool.options.mode = $(this).val();
			if($(this).val() === "multi-page-single-layout"){
				(self.btn_show_canvas).hide();
				(self.btn_show_page_and_layout).hide();
				(self.btn_show_layout_bar).hide();
			}else{
				(self.btn_show_canvas).show();
				(self.btn_show_page_and_layout).show();
				(self.btn_show_layout_bar).hide();
			}
		});

		label = $('<li class="list-group-item" ><label>BackGround Tool Label &nbsp;</label></li>').appendTo(tool_settings);
		this.bg_label = $('<input name="BackGround Tool Label" type="text" id="xshop-designer-bg-label" />').appendTo(label);
		$(this.bg_label).change(function(){
			self.designer_tool.options.BackgroundImage_tool_label = $(this).val();
		});

		this.element.click(function(event){
			self.designeroption.dialog('open');
		});

        //set pre-saved value
		if(self.designer_tool.options.mode){
			$(setting_button_set).val(self.designer_tool.options.mode);
		}

		$.each(self.designer_tool.options.ComponentsIncluded,function(index,name){
			var idx = $.inArray(name, self.designer_tool.options.ComponentsIncludedToBeShow);
			if (idx == -1) {
				$('input[data_variable="'+name+'"]').prop('checked',true);
			}
		});

		$(this.bg_label).val(self.designer_tool.options.BackgroundImage_tool_label);

	}
}

FreeLancerComponentOptions = function(parent, designer, canvas){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.current_component = undefined;
	this.element =undefined;


	this.init =  function(){
		var self =this;

		this.designer_setting = $('<div class="btn" style="display:none;" title="Tools Settings" ><i class="glyphicon glyphicon-cog"></i><br>Setting</div>').appendTo(this.parent);
		this.setting_page = $('<div></div>').appendTo(this.designer_setting);

		this.setting_page.dialog({autoOpen: false, modal: true, width:600});
		this.designer_setting.click(function(event){
			self.setting_page.dialog('open');
		});

		// ft_btn_set = $('<div class="btn-group" style="display:none;"></div>');
		// $('<a title="" data-toggle="dropdown" class="btn dropdown-toggle " data-original-title="Font Size">FT&nbsp;<b class="caret"></b></a>').appendTo(ft_btn_set);
        // this.btn_frontside = $('<li><span class="glyphicon glyphicon-ok" style="display:true"></span> Front side</li>').appendTo(setting_button_set);
        // this.btn_backside = $('<li><span class="glyphicon glyphicon-remove"></span> Back side</li>').appendTo(setting_button_set);
        // this.btn_autofit = $('<li><a><font size="3">Autofit</font></a></li>').appendTo(setting_button_set);
        // this.btn_multiline = $('<li><a><font size="3">Multiline Text</font></a></li>').appendTo(setting_button_set);
        
        setting_button_set = $('<ul class="list-group xshop-designer-setting-options"></ul>').appendTo(this.setting_page);
        this.btn_movable = $('<li class="list-group-item" data_variable="movable"><input data_variable="movable" type="checkbox" class="xshop-designer-setting-option"/> Movable </li>').appendTo(setting_button_set);
        // this.btn_colorable = $('<li class="list-group-item" data_variable="colorable"><input data_variable="colorable" type="checkbox" class="xshop-designer-setting-option"/> Colorable </li>').appendTo(setting_button_set);
        this.btn_editable = $('<li class="list-group-item" data_variable="editable"><input data_variable="editable" type="checkbox" class="xshop-designer-setting-option"/> Editable </li>').appendTo(setting_button_set);
        this.btn_resizable = $('<li class="list-group-item" data_variable="resizable"><input data_variable="resizable" type="checkbox" class="xshop-designer-setting-option"/> Resizable </li>').appendTo(setting_button_set);
        this.img_replace_option_wrapper = $('<li class="list-group-item" data_variable="replace_mode">Image Replace Mode </li>').appendTo(setting_button_set);
		this.btn_img_replace = $('<select><option value="crop_in_ratio">Crop In Ratio</option><option value="fit_to_scale">Fit To Scale</option><option value="free_crop">Free Crop</option></select>').appendTo(this.img_replace_option_wrapper);

		this.element = this.designer_setting;
		
		$('.xshop-designer-setting-option').click(function(event){
			// console.log(this);
			checked = $(this).is(':checked');
			option = $(this).attr('data_variable');
			eval('self.current_component.options.'+option+' = '+checked+';');
		});

		$(this.btn_img_replace).change(function(event){
			self.designer_tool.current_selected_component.options.replace_mode = $(this).val();
		});
// -------------------------
		// this.btn_frontside.click(function(){
		// 	self.current_component.options.frontside = !self.current_component.options.frontside;
		// 	self.current_component.options.z_index = 5;
		// 	//Todo Add Z-index value 5 on div of image
		// 	// console.log(self.current_component.options);
		// 	$(this).find('span').toggle();

		// });

		// this.btn_backside.click(function(){
		// 	self.current_component.options.backside = !self.current_component.options.backside;
		// 	self.current_component.options.z_index = 0;
		// 	//Todo Add Z-index value 0 on div of image
		// 	// console.log(self.current_component.options);
		// 	$(this).find('span').toggle();
		// });

	}

	this.setComponent = function(component){
		this.current_component = component;
		$('input[data_variable="movable"]').prop('checked',this.current_component.options.movable);
		$('input[data_variable="colorable"]').prop('checked',this.current_component.options.colorable);
		$('input[data_variable="editable"]').prop('checked',this.current_component.options.editable);
		$('input[data_variable="resizable"]').prop('checked',this.current_component.options.resizable);
		
		if(this.current_component.options.type == "Image"){
			$('li[data_variable="replace_mode"] select').val(this.current_component.options.replace_mode?this.current_component.options.replace_mode:"crop_in_ratio");
			$('input[data_variable="colorable"]').closest('li').hide();
			$('li[data_variable="replace_mode"]').show();
		}else{

			$('input[data_variable="colorable"]').closest('li').show();
			$('li[data_variable="replace_mode"]').hide();
		}


	}

}


FreeLancerPanel = function(parent, designer, canvas){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.current_component = undefined;
	this.element = undefined;
	this.FreeLancerComponentOptions=undefined;
	this.FreeLancerPageLayoutManager=undefined;

	this.init =  function(){
		this.element = $('<div class="row pull-left"></div>').appendTo(this.parent);
		this.FreeLancerPageLayoutManager = new FreeLancerPageLayoutManager(this.element,this.designer_tool, this.canvas);
		this.FreeLancerPageLayoutManager.init();
		this.FreeLancerComponentOptions = new FreeLancerComponentOptions(this.element,this.designer_tool, this.canvas);
		this.FreeLancerComponentOptions.init();
		
		// Designer Mode
		this.DesignerOption = new FreeLancerDesignerOptions(this.element,this.designer_tool, this.canvas);
		this.DesignerOption.init();
		
	}

	this.setComponent = function(component){
		this.FreeLancerComponentOptions.setComponent(component);
	}
	// create page_layout_manager
	// create tools option manager

}