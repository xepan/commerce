jQuery.widget("ui.BackgroundPanel",{
	panel: undefined,

	_create: function(){
		var self= this;
		this.setupPanel();

		this.element.html('Background');
		this.element.click(function(event) {
			$('.xshop-designer-component-panel-options').hide();
			self.panel.show();
		});

	},

	setupPanel: function(){
			this.panel = $('<div class="xshop-designer-component-panel-options" style="display:block">Background</div>').appendTo('#xshop-designer-component-panel');
	}

});