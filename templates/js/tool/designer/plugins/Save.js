Save_Component = function (params){
	var self = this;
	this.parent=undefined;
	this.options= {
		base_url:undefined
	},
	// this.text = params.text != undefined?params.text:'Enter Text';
	this.init = function(designer,canvas){
		this.designer_tool = designer;
		this.canvas = canvas;
		self.options.base_url = designer.options.base_url;
	}

	this.initExisting = function(params){

	}

	this.getUrlParameter = function(params) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === params) {
	            return sParameterName[1] === undefined ? true : sParameterName[1];
	        }
    	}
	}

	this.renderTool = function(parent){
		var self = this;
		this.page = undefined;
		this.layout = undefined;
		this.parent = parent;
		tool_btn = $('<div class="btn xshop-render-tool-save-btn "><i class="glyphicon glyphicon-floppy-saved"></i><br>Save</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));
		
		tool_btn.click(function(event){
			console.log(self.designer_tool.options);
			// console.log(self);
			self.layout_array = {};
			$.each(self.designer_tool.pages_and_layouts,function(index,pages){
				self.page = index;
				self.layout_array[index]= new Object;
				$.each(self.designer_tool.pages_and_layouts[index],function(index,layout){
					self.layout = index;
					self.layout_array[self.page][index]=new Object;
					self.layout_array[self.page][self.layout]['components']=[];
					$.each(layout.components,function(index,component){
						//Setup Image Path Relative
						if(component.options.type=="Image"){
							// console.log("Rakesh");
							url = component.options.url;
							component.options.url = url.substr(url.indexOf("/upload"));
							// console.log(component.options.url);
						}
						self.layout_array[self.page][self.layout]['components'].push(JSON.stringify(component.options));
					});


					background_options = self.designer_tool.pages_and_layouts[self.page][self.layout]['background'].options;
					//Setup Image Path Relative
					if(background_options.url){
						background_options.url = background_options.url.substr(background_options.url.indexOf("/upload"));
						// console.log(background_options.url);
					}				
					self.layout_array[self.page][self.layout]['background'] = JSON.stringify(background_options);
				});	
			});
			
			// console.log(self);
			$.ajax({
					url: 'index.php?page=xepan_commerce_designer_save',
					type: 'POST',
					datatype: "json",
					data: { xshop_item_design:JSON.stringify(self.layout_array),//json object
							item_id:self.designer_tool.options.item_id,//designed item id
							designer_mode:self.designer_tool.options.designer_mode,
							item_member_design_id:self.designer_tool.options.item_member_design_id,
							px_width : self.designer_tool.px_width,
							selected_layouts_for_print : JSON.stringify(self.designer_tool.layout_finalized)
						},
				})
				.done(function(ret) {
					if($.isNumeric(ret)){
						self.designer_tool.options.item_member_design_id = ret;
						page = self.getUrlParameter('page');
						if(!self.getUrlParameter('item_member_design')){
							old_url = window.location.href;
							new_url = old_url.split( '&' )[0];
							$.univ().successMessage('loading your saved design');
							$.univ.location(old_url+'&item_member_design='+ret);
						}else
							$.univ().successMessage('Saved Successfully');
					}
					else if(ret.indexOf('false')===0){
						// eval(ret);
						// $.univ().errorMessage('Not Saved, some thing wrong');
					}else{
						// eval(ret);
						//todo delete because cart tool is depricated in xepan2
						//cart tool moved to separate tool
						if(!isNaN(+ret)){
							self.designer_tool.options.item_member_design_id = ret;
							// if(self.designer_tool.cart != undefined || self.designer_tool.cart != '0'){
							// 	self.designer_tool.cart.xepan_xshop_addtocart('option','item_member_design_id',ret);
							// 	// console.log(self.designer_tool.cart.options);
							// }
							// // window.history.pushState('page', 'saved_page', 'replace url');
							// $.univ().successMessage('Saved Successfully');
						}else{
							eval(ret);
						}
					}
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});
		});
	}
}