BackgroundImage_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;
	this.xhr = undefined;

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
		
		editable: true,
		default_url:'templates/images/logo.png',
		url:undefined,
		auto_fit: false,
		// System properties
		type: 'BackgroundImage',
		base_url:undefined,
		page_url:undefined
	};

	this.init = function(designer,canvas, editor){
		var self=this;
		this.designer_tool = designer;
		this.canvas = canvas;

		self.options.base_url = designer.options.base_url;
		self.options.page_url = designer.options.base_url;

		if(editor !== undefined)
			this.editor = editor;
	}

	this.renderTool = function(parent){
		var self=this;
		this.parent = parent;
		
		self.options.base_url = self.designer_tool.options.base_url;
		// self.options.page_url = self.designer_tool.options.base_url+"admin/";
		self.options.page_url = self.designer_tool.options.base_url;

		label = self.designer_tool.options.BackgroundImage_tool_label;
		
		if(label == undefined || label ==  null || !label)
			label = "Background Image";

		bgi_tool_btn = $('<div class="btn xshop-designer-backgroundimage-toolbtn"></div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset')).data('tool',self);
		tool_btn = $('<div><i class="glyphicon glyphicon-picture"></i><br>'+label+'</div>').appendTo(bgi_tool_btn);

		tool_btn.click(function(event){
			design_dirty = true;
			self.designer_tool.current_selected_component = self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].background;

			options ={modal:false,
					width:800,
					// close:function(){
						// self.designer_tool.current_selected_component = undefined;
					// }
				};

			$.univ().frameURL('Add Images From...','?page=xepan_commerce_designer_itemimages',options);
		});

		// console.log("Rakesh designer Mode "+self.designer_tool.options.designer_mode);
		// if(self.designer_tool.options.designer_mode){
			remove_btn = $('<div class="atk-swatch-red icon-trash"></div>').appendTo(bgi_tool_btn);
			remove_btn.click(function(event){

				self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].background.options.url=undefined;
				self.designer_tool.current_selected_component = null;
				var canvas = self.designer_tool.canvasObj;
				canvas.setBackgroundImage(undefined, canvas.renderAll.bind(canvas));

				return;
				// old code .. seems not removing background image
				self.designer_tool.current_selected_component = self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].background;
				$(self.designer_tool.current_selected_component.element).hide();
				$(self.designer_tool.current_selected_component.element).find('img').removeAttr('src');

				self.designer_tool.pages_and_layouts[self.designer_tool.current_page][self.designer_tool.current_layout].background.options.url=undefined;				
				self.designer_tool.current_selected_component = null;
			});
		// }

		var idx = $.inArray("BackgroundImage", self.designer_tool.options.ComponentsIncludedToBeShow);
		if (idx == -1) {
			$(tool_btn).remove();
			$(remove_btn).remove();
			$(bgi_tool_btn).remove();
		}
	}


	this.render = function(designer_tool_obj){

		var self = this;

		if(designer_tool_obj) self.designer_tool = designer_tool_obj;
		if(self.options.base_url == undefined){
			self.options.base_url = self.designer_tool.options.base_url;
			self.options.page_url = self.designer_tool.options.base_url;
		}
		
		var canvas = self.designer_tool.canvasObj;
		canvas.setBackgroundColor('#FFFFFF');
		if(this.options.url == undefined){
			canvas.setBackgroundImage(undefined, canvas.renderAll.bind(canvas));
			return;
		}

		var backScaleX = self.options.crop_width? canvas.width / self.options.crop_width:1;
		var backScaleY = self.options.crop_height? canvas.height / self.options.crop_height:1;
		var backCropX = self.options.crop_x?self.options.crop_x:0;
		var backCropY = self.options.crop_y?self.options.crop_y:0;

		canvas.setBackgroundImage(self.options.url, canvas.renderAll.bind(canvas), {
                    originX: 'left',
                    originY: 'top',
                    left: -1 * backCropX * backScaleX,
                    top:  -1 * backCropY * backScaleY,
                    scaleY: backScaleY,
                    scaleX: backScaleX
                });
        return;

		if(this.element == undefined){
			this.element = $('<div style="position:absolute;z-index:-10;" class="xshop-designer-component xepan-designer-background-image"><span><img></img></span></div>').appendTo(this.canvas);
			// console.log(self.designer_tool.screen2option);
			self.options.width = self.designer_tool.screen2option(self.designer_tool.canvas.width());
			self.options.height = self.designer_tool.screen2option(self.designer_tool.canvas.height());
		}else{
			this.element.show();
		}
		this.element.css('top',self.designer_tool.option2screen(self.options.y));
		this.element.css('left',self.designer_tool.option2screen(self.options.x));
		this.element.css('width',self.designer_tool.option2screen(self.options.width));
		this.element.css('height',self.designer_tool.option2screen(self.options.height));
		// this.element.find('img').width((this.element.find('img').width() * self.designer_tool.delta_zoom /100));
		// this.element.find('img').height((this.element.find('img').height() * self.designer_tool.delta_zoom/100));

		if(this.xhr != undefined)
			this.xhr.abort();
		this.xhr = $.ajax({
			url: '?page=xepan_commerce_designer_renderimage',
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
					width: self.options.width,
					height: self.options.height
					},
		})
		.done(function(ret) {
			self.element.find('img').attr('src','data:image/jpg;base64, '+ ret).width(self.designer_tool.option2screen(self.options.width));
			self.xhr=undefined;
			// console.log(self);
		})
		.fail(function(ret) {
			// evel(ret);
			console.log("error");
		})
		.always(function() {
			// console.log("BackgroundImage complete");
		});
	}

}