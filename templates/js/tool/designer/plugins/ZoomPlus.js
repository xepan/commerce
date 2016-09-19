ZoomPlus_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;

	this.options = {
	};

	this.init = function(designer,canvas){
		this.designer_tool = designer;
		this.canvas = canvas;
	}

	this.initExisting = function(params){

	}

	this.renderTool = function(parent){
		var self=this;
		this.parent = parent;
		this.tool_btn = $('<div class="btn"><i class="glyphicon glyphicon-zoom-in"></i><br>Zoom +</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));

		// CREATE NEW TEXT COMPONENT ON CANVAS
		this.tool_btn.click(function(event){
			// create new TextComponent type object
			var new_width = self.designer_tool.canvas.width() + (self.designer_tool.canvas.width() * 10/100);
			if(new_width > (self.designer_tool.workplace.width() - 40) ){
				new_width = self.designer_tool.workplace.width() - 40;
			}
			if(new_width < (self.designer_tool.workplace.width()-40)){
				self.designer_tool.canvas.width(new_width);
				self.designer_tool.render();
			}
		});

		var idx = $.inArray("ZoomPlus", self.designer_tool.options.ComponentsIncludedToBeShow);
		if (idx == -1) {
			$(this.tool_btn).remove();
		}
	}

	this.render = function(){
		var self = this;
	}
}