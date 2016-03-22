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
		this.add_btn = $('<span class="input-group-btn"><button class="btn" type="button">Add</button></span>').appendTo(this.add_div);

		this.add_btn.click(function(event){
			self.addPage(self.input_box.val());
			self.designer_tool.bottom_bar.renderTool();
		});

		// make a list of current pages with remove buton
		this.page_list_div = $('<div class="list-group xdesigner-page-list"></div>').appendTo(this.element);

		$.each(pages_data_array,function(index,page){
			self.addPageView(page,index);
			// self.addPage(page);
		});

		$(".xdesigner-page-list").sortable({
      		stop : function(event, ui){
      			new_object = {};
      			$('.xdesigner-page-list').children().each(function (i,ui) {
      				pagename = $(ui).data('pagename');
      				new_object[pagename] =  new Object();
					new_object[pagename] =  self.designer_tool.pages_and_layouts[pagename];
      				
      			});
      			// console.log(self.designer_tool.pages_and_layouts);
      			self.designer_tool.pages_and_layouts = new_object;
      			self.designer_tool.bottom_bar.renderTool();	
      			$.univ().successMessage('Page Order Changed');
      			// console.log(new_object);
        	}
    	});		
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
				this.designer_tool.pages_and_layouts[page_name] =  new Object();
				this.designer_tool.pages_and_layouts[page_name] =  this.designer_tool.pages_and_layouts[duplicate_from_page];
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

		}
		// add page to pagelistdiv and add to designertool pagesnadlayout object

	}

	//Return array of all pages of loaded designs
	this.allPage = function(){
		var page_array = [];
		$.each(this.designer_tool.pages_and_layouts,function(index,value){
			page_array.push(index);
		});
		return page_array;
	}

	this.addPageView = function(page_name,order){
		var self = this;

		page_row = $('<div class="page_row" data-pagename="'+page_name+'"></div>').appendTo(this.page_list_div);
		div = $('<button class="list-group-item"></button>').appendTo(page_row);
		page_name = $('<span class="xshop-designer-ft-page-name"></span>').appendTo(div).html(page_name);
		rm_btn = $('<span class="label label-danger pull-right">x</span>').appendTo(div).data('page_name',page_name);
		
		div.click(function(event){
			// event.preventDefault();
			$(this).parent().siblings().find('a').removeClass('active').addClass('activeOff');
			$(this).addClass('active').removeClass('activeOff');
			self.manager.layoutblock.setPage($(this).find('span.xshop-designer-ft-page-name').html());
		});

		rm_btn.click(function(event){
			if(self.removePage($(this).data('page_name')))
				$(this).closest(".page_row").remove();
		});

		duplicate_btn = $('<span class="label label-default pull-right xdesigner-page-btn" title="Page Duplicate "><i class="icon-paste"></i></span>').appendTo(div).data('page_name',page_name);
		duplicate_btn.click(function(event){
			if(new_page_name = prompt("New Page Name", page_name[0].firstChild.data+" - copy")){
				self.addPage(new_page_name,page_name[0].firstChild.data);
				self.designer_tool.bottom_bar.renderTool();
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
		this.add_btn = $('<span class="input-group-btn"><button class="btn" type="button">Add</button></span>').appendTo(this.add_div);

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
				this.designer_tool.pages_and_layouts[this.current_page][layout_name] =  this.designer_tool.pages_and_layouts[this.current_page][duplicate_from_layout];
			}



			layout_row = $('<div class="layout_row"></div>').appendTo(this.layout_list_div);
			div = $('<button class="list-group-item"></button>').appendTo(layout_row).html(layout_name);
			rm_btn = $('<span class="label label-danger pull-right">x</span>').appendTo(div).data('layout_name',layout_name);

			rm_btn.click(function(event){
				if(self.removeLayout($(this).data('layout_name')))
					$(this).closest(".layout_row").remove();			
			});

			duplicate_btn = $('<span class="label label-default pull-right xdesigner-layout-btn" title="Layout Duplicate "><i class="icon-paste"></i></span>').appendTo(div).data('layout_name',layout_name);
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
				alert('Layout exist');
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

FreeLancerComponentOptions = function(parent, designer, canvas){
	this.parent = parent;
	this.designer_tool = designer;
	this.canvas = canvas;
	this.current_component = undefined;
	this.element =undefined;


	this.init =  function(){
		var self =this;
		ft_btn_set = $('<div class="btn-group" style="display:none;"></div>');
		$('<a title="" data-toggle="dropdown" class="btn dropdown-toggle " data-original-title="Font Size">FT&nbsp;<b class="caret"></b></a>').appendTo(ft_btn_set);
        ft_btn_list = $('<ul class="dropdown-menu"></ul>').appendTo(ft_btn_set);

        this.btn_movable = $('<li class=""><span class="glyphicon glyphicon-ok" style="display:none"></span> Movable</li>').appendTo(ft_btn_list);
        this.btn_colorable = $('<li><span class="glyphicon glyphicon-ok" style="display:none"></span> Colorable</li>').appendTo(ft_btn_list);
        this.btn_editable = $('<li><span class="glyphicon glyphicon-ok" style="display:none"></span> Editable</li>').appendTo(ft_btn_list);
        // this.btn_frontside = $('<li><span class="glyphicon glyphicon-ok" style="display:true"></span> Front side</li>').appendTo(ft_btn_list);
        // this.btn_backside = $('<li><span class="glyphicon glyphicon-remove"></span> Back side</li>').appendTo(ft_btn_list);
        this.btn_resizable = $('<li><a><font size="3">Resizable</font></a></li>').appendTo(ft_btn_list);
        // this.btn_autofit = $('<li><a><font size="3">Autofit</font></a></li>').appendTo(ft_btn_list);
        // this.btn_multiline = $('<li><a><font size="3">Multiline Text</font></a></li>').appendTo(ft_btn_list);
			
		this.element = $(ft_btn_set).appendTo(this.parent);

		// add Flyout
		// add movable button
		// 		its onclick event
		// 	add dsfdf
		
		this.btn_movable.click(function(event){
			self.current_component.options.movable = !self.current_component.options.movable;
			$(this).find('span').toggle();
			if(self.current_component.options.movable){
				self.current_component.element.draggable('enable');
			}else{
				self.current_component.element.draggable('disable');
			}
			
		});

		this.btn_colorable.click(function(event){
			self.current_component.options.colorable = !self.current_component.options.colorable;
			if(self.current_component.options.colorable){
				self.current_component.editor.text_color_picker.next('button').show();
			}else{
				self.current_component.editor.text_color_picker.next('button').hide();
			}
			$(this).find('span').toggle();
		});

		this.btn_editable.click(function(event){
			self.current_component.options.editable = !self.current_component.options.editable;
			if(self.current_component.options.editable){
				self.current_component.editor.text_input.show();
			}else{
				self.current_component.editor.text_input.hide();
			}
			$(this).find('span').toggle();
		});

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
		// console.log(this.current_component);

		if(this.current_component.options.movable){
			$(this.btn_movable).find('span').show();
		}
		else{
			$(this.btn_movable).find('span').hide();
		}

		if(this.current_component.options.colorable){
			$(this.btn_colorable).find('span').show();
		}
		else{
			$(this.btn_colorable).find('span').hide();
		}

		if(this.current_component.options.editable){
			$(this.btn_editable).find('span').show();
		}
		else{
			$(this.btn_editable).find('span').hide();
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
	}

	this.setComponent = function(component){
		this.FreeLancerComponentOptions.setComponent(component);
	}
	// create page_layout_manager
	// create tools option manager

}