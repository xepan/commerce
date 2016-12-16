ZoomMinus_Component = function (params){
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
		tool_btn = $('<div class="btn"><i class="glyphicon glyphicon-zoom-out"></i><br>Zoom -</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));

		// CREATE NEW TEXT COMPONENT ON CANVAS
		tool_btn.click(function(event){
			// create new TextComponent type object
			// if(self.designer_tool.canvas.width() - (self.designer_tool.canvas.width() * 10/100) > self.designer_tool.workplace.width()/2){
				self.designer_tool.canvas.width(self.designer_tool.canvas.width() - (self.designer_tool.canvas.width() * 10/100));
				self.designer_tool.render();
			// }
		});

		var idx = $.inArray("ZoomMinus", self.designer_tool.options.ComponentsIncludedToBeShow);
		if (idx == -1) {
			$(tool_btn).remove();
		}

	}

	this.render = function(){
		var self = this;
	}
}