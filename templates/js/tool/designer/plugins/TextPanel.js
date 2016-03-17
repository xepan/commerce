jQuery.widget("ui.TextPanel",{
	panel: undefined,
	designer_widget_id: undefined,

	_create: function(){
		var self= this;	

		this.designer_widget_id = this.options.designer_widget_id;

		this.setupPanel();
		this.element.html('Text');
		this.element.click(function(event) {
			$('.xshop-designer-component-panel-options').hide();
			self.panel.show();
		});

	},

	setupPanel: function(){
		var self=this;
		this.panel = $('<div class="xshop-designer-component-panel-options" style="display:none"></div>').appendTo('#xshop-designer-component-panel');
		var new_btn = $('<div class="btn btn-block">ADD NEW TEXT</div>').appendTo(this.panel);
		$(new_btn).click(function(event) {
			$("#"+self.designer_widget_id).xepan_xshopdesigner('render',{msg:"GVS"});
		});
	},

	test: function(){
		console.log('test from text');
	}

});