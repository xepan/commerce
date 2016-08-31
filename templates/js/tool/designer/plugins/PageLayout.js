Layout_Tool = function(parent){
	var self = this;
	this.parent=parent;
	this.options={
		base_url:undefined
	},
	// this.text = params.text != undefined?params.text:'Enter Text';
	this.init = function(designer,canvas,page_tool){
		self.designer_tool = designer;
		self.canvas = canvas;
		self.page_tool = page_tool;

		self.options.base_url = designer.options.base_url;

		$('.xshop-designer-layout').remove();
		if(this.parent == undefined)
			this.parent = $('<div class="xshop-designer-layout clearfix"></div>').insertAfter($.find(".xshop-designer-tool-bottombar"));
			//Make Layout Scroller
	}

	this.renderTool = function(page_name){
		var self = this;
		// console.log(page_name);
		// console.log(self);
		$.each(self.designer_tool.pages_and_layouts[page_name],function(index,layout){
			//display the page button
			$('.xshop-designer-show-page').show();
			//hide page button view
			// $('.xshop-designer-pagelayout').hide();
			//add new Layout of current selected page
			img = '<img class="xdesigner-page-layout-thumbnail" src="'+'index.php?page=xepan_commerce_designer_thumbnail&cache='+escape(new Date())+'&xsnb_design_item_id='+self.designer_tool.options.item_id+'&page_name='+page_name+'&layout_name='+index+'&item_member_design_id='+self.designer_tool.options.item_member_design_id+'&thumbnail_width=130'+'" alt="'+index+'"/>';
			layout_btn = $('<div class="xshop-designer-layoutbtn clearfix" xdesigner_item_page_name="'+page_name+'" >'+img+'<p class="xshop-designer-layout-name">'+index+'</p></div>').appendTo($.find('.xshop-designer-layout')).data('layout',index);
			// layout_btn = $('<div class="xshop-designer-layoutbtn clearfix" xdesigner_item_page_name="'+page_name+'" >'+img+'<p class="xshop-designer-layout-name">'+index+'</p><i class="glyphicon glyphicon-ok btn btn-small-xs" > Print</i></div>').appendTo($.find('.xshop-designer-layout')).data('layout',index);
				layout_btn.click(function(){
					self.designer_tool.current_page = page_name;
					self.designer_tool.current_layout = $(this).data('layout');
					// temporary added current layout for loading working layout
					self.designer_tool.temp_current_layout = $(this).data('layout');

					self.designer_tool.render();
					$('.xshop-designer-layoutbtn').removeClass('ui-selected');
					// $('.xshop-designer-layoutbtn').removeClass('btn-success');
					self.page_tool.updateBreadcrumb(self.page_tool.parent);
					$(this).addClass('ui-selected');
				// });

				// layout_btn.find('i').click(function(){
					// mark self as selected layout in designer
					self.designer_tool.layout_finalized[page_name] = index;
					// make all gray
					// $('.xshop-designer-layout').find('i').removeClass('btn-success');
					// make self green
					// $(this).addClass('btn-success');
					print_layout_src = $(this).find('img.xdesigner-page-layout-thumbnail').attr('src');
					page_name_attr = $(this).attr('xdesigner_item_page_name');
					// console.log(print_layout_src);
					// console.log(page_name_attr);
					// print_layout_src = $(this).siblings('img.xdesigner-page-layout-thumbnail').attr('src');
					// page_name_attr = $(this).parent().attr('xdesigner_item_page_name');
					
					// console.log("Hello == "+page_name_attr);
					// console.log($('.xshop-designer-pagelayout > .xshop-designer-pagebtn[xdesigner_item_page_name="'+page_name_attr+'"]'));
					page_thumbnail_view = $('.xshop-designer-pagelayout > .xshop-designer-pagebtn[xdesigner_item_page_name="'+page_name_attr+'"]');
					if(page_thumbnail_view.length > 0)
						$(page_thumbnail_view).find('img.xdesigner-page-layout-thumbnail').attr('src',print_layout_src);
				});

			if(index == self.designer_tool.layout_finalized[page_name]){
				// layout_btn.addClass('btn-success');
				$(layout_btn).addClass('ui-selected');
			}else{
				$(layout_btn).removeClass('ui-selected');
			}

			// if(index == self.designer_tool.current_layout) {
			// 	alert("Layout "+index);
			// 	$(layout_btn).addClass('ui-selected');
			// }else{
			// 	$(layout_btn).removeClass('ui-selected');
			// }
		});
		// console.log(self.designer_tool)
	}	
}

PageLayout_Component = function (params){
	var self = this;
	this.parent=undefined;
	this.options={
		base_url:undefined,
		page_url:undefined
	},
	// this.text = params.text != undefined?params.text:'Enter Text';
	this.init = function(designer,canvas,parent){

		this.designer_tool = designer;

		//setting base url and page url
		self.options.base_url = designer.options.base_url;
		self.options.page_url = designer.options.page_url;

		this.canvas = canvas;
		if(parent != undefined){
			this.parent = parent;
		}else
			this.parent = this.designer_tool.bottombar;
		// this.updateBreadcrumb(this.parent);
		if(!this.designer_tool.options.multi_page_single_layout)
			this.updateBreadcrumb(this.designer_tool.bottombar);
	}

	this.initExisting = function(params){

	}
	
	this.updateBreadcrumb = function(parent){
		$('.xshop-designer-show-page').remove();
		this.breadcrumb = $('<ol class="xshop-designer-show-page breadcrumb"></ol>').insertAfter(parent);
		this.home_breadcrumb = $('<li><a href="#home">Home</a></li>').appendTo(this.breadcrumb);
		this.home_breadcrumb.click(function(){
			$('.xshop-designer-pagelayout').show();
			$('.xshop-designer-layout').hide();
		});

		this.page_breadcrumb = $('<li>'+self.designer_tool.current_page+'</li>').appendTo(this.breadcrumb);
		this.layout_breadcrumb = $('<li>'+self.designer_tool.current_layout+'</li>').appendTo(this.breadcrumb);
		// $(str).appendTo(this.show_page_btn);
	}

	this.renderTool = function(){
		var self = this;

		$('.xshop-designer-pagelayout').show();
		$('.xshop-designer-pagebtn').remove();
		count = 0;
		self.page_layout_toolbar = page_layout_toolbar = $('<div class="xshop-designer-pagelayout clearfix"></div>').appendTo($.find(".xshop-designer-tool-bottombar"));
			
		$.each(self.designer_tool.pages_and_layouts,function(index,page){
			layout = self.designer_tool.layout_finalized;
			// console.log(layout[index]);
			img = '<img class="xdesigner-page-layout-thumbnail" src="'+'index.php?page=xepan_commerce_designer_thumbnail&cache='+escape(new Date())+'&xsnb_design_item_id='+self.designer_tool.options.item_id+'&page_name='+index+'&layout_name='+layout[index]+'&item_member_design_id='+self.designer_tool.options.item_member_design_id+'&thumbnail_width=130'+'" alt="'+index+'"/>';
			page_btn = $('<div class="xshop-designer-pagebtn" xdesigner_item_page_name="'+index+'" >'+img+'<p class="xshop-designer-page-name" style="color:black;">'+index+'</p></div>').appendTo(page_layout_toolbar).data('page',index);
			page_btn.click(function(event){
				//Trick for solve error like select page one component and then select another page same type component here the option are same as previouse selected page component if edit here some option then previous page current_selected_component display on second page 
				$('.xshop-designer-tool-topbar-options').hide();
				// console.log(self.designer_tool);
				if(!self.designer_tool.options.multi_page_single_layout){
					layout = new Layout_Tool();
					layout.init(self.designer_tool,self.canvas,self);
					layout.renderTool(index);

					self.updateBreadcrumb(self.parent);
				}

				
				self.designer_tool.current_page = index;
				temp_layout = self.designer_tool.layout_finalized[index];
				if(temp_layout === undefined)
					temp_layout = "Main Layout";

				self.designer_tool.current_layout = temp_layout;
				// self.designer_tool.current_layout = 'Main Layout';
				self.designer_tool.render();
				
				$('.xshop-designer-pagebtn').removeClass('ui-selected');
				$(this).addClass('ui-selected');

				// hide page layout
				if(self.designer_tool.options.multi_page_single_layout){
					$(self.page_layout_toolbar).toggle();

				}

			});

			if(index == self.designer_tool.current_page) {
				$(page_btn).addClass('ui-selected');
			}else{
				$(page_btn).removeClass('ui-selected');
			}

		//TODO must shifted into function
		/*default Front  Main Layout Display */	

		if(!self.designer_tool.options.multi_page_single_layout){
			layout = new Layout_Tool();
			layout.init(self.designer_tool,self.canvas,self);
			layout.renderTool(self.designer_tool.current_page);
			self.designer_tool.current_page = self.designer_tool.current_page;

			// loading saved design first time
			temp_layout = self.designer_tool.layout_finalized[self.designer_tool.current_page];
			if(temp_layout === undefined)
				temp_layout = "Main Layout";
			self.designer_tool.current_layout = temp_layout;

			// self.designer_tool.render();
			self.updateBreadcrumb(self.parent);

			if(count > 4){
				$(page_layout_toolbar).slick({
			        dots: false,
			        infinite: false,
			        slidesToShow: 6,
			        slidesToScroll: 3
		      	});
			}
		}



		$('.xshop-designer-pagebtn').removeClass('ui-selected');
		$(this).addClass('ui-selected');
		count = count + 1;
		});
		

	}
}