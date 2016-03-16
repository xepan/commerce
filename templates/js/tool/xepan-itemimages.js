jQuery.widget("ui.xepan_itemimages",{
	options:{
	},
	_create: function(){
		$(this.element).elevateZoom({
			// gallery:this.element, 
			cursor: 'pointer'
		});
	}
});