PDF_Component = function (params){
	this.parent=undefined;
	this.designer_tool= undefined;
	this.canvas= undefined;
	this.element = undefined;
	this.editor = undefined;
	this.base_url = undefined;
	this.options = {
	};

	this.init = function(designer,canvas){
		var self = this;
		self.designer_tool = designer;
		self.canvas = canvas;
		self.options.base_url = designer.options.base_url;
	}

	this.initExisting = function(params){

	}

	this.renderTool = function(parent){
		var self=this;
		this.parent = parent;
		tool_btn = $('<div class="btn"><i class="glyphicon glyphicon-file"></i><br>Preview</div>').appendTo(parent.find('.xshop-designer-tool-topbar-buttonset'));

		// CREATE NEW TEXT COMPONENT ON CANVAS
		tool_btn.click(function(event){
			// create new PDFComponent type object
			// $.univ().newWindow('index.php?page=xepan_commerce_designer_pdf&item_id='+self.designer_tool.options.item_id+'&item_member_design_id='+self.designer_tool.options.item_member_design_id+'&xsnb_design_template='+self.designer_tool.options.designer_mode);
			if( self.designer_tool.options.item_id == undefined && (self.designer_tool.options.item_member_design_id == undefined || self.designer_tool.options.item_member_design_id == null )){
				$.univ().errorMessage('Please Save Your Design First');
			}else{
				$.univ().newWindow(self.designer_tool.options.base_url+'index.php?page=xepan_commerce_designer_pdf&item_id='+self.designer_tool.options.item_id+'&item_member_design_id='+self.designer_tool.options.item_member_design_id+'&xsnb_design_template='+self.designer_tool.options.designer_mode+'&watermark_text='+self.designer_tool.options.watermark_text);
			}
		});

	}

	this.render = function(){
		var self = this;
	}
}