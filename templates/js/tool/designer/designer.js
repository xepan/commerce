// xEpan Designer jQuery Widget for extended xShop elements 
jQuery.widget("ui.xepan_xshopdesigner",{
	pages_and_layouts: {
		"Front Page": {
			"Main Layout": {
				components: [],
				background: undefined
			}
		}
	},

	layout_finalized : {"Front Page" : "Main Layout"},

	current_selected_component : undefined,
	// components:[],
	current_page:'Front Page',
	current_layout: 'Main Layout',
	// item_id:undefined, // used from options
	item_member_design_id:undefined, // used from options
	workplace:undefined,
	canvas:undefined,
	safe_zone: undefined,
	cart: undefined,
	zoom: 1,
	delta_zoom: 0,
	px_width:undefined,
	option_panel: undefined,
	freelancer_panel: undefined,
	editors : [],
	top_bar: undefined,
	pointtopixel:{
					'4':5,'5':7,'6':8,'7':9,'8':11,'9':12,'10':13,'11':15,'12':16,'13':17,'14':19,'15':21,'16':22,'17':23,'18':24,'19':25,'20':26,'21':28,'22':29,'23':31,'24':32,'25':33,'26':35,'27':36,'28':37,'29':38,'30':40,'31':41,'32':42,'33':44,'34':45,'35':47,'36':48
				},
	options:{
		// Layout Options
		showTopBar: true,
		// ComponentsIncluded: ['Background','Text','Image','Help'], // Plugins
		IncludeJS: ['FreeLancerPanel'], // Plugins
		ComponentsIncluded: ['BackgroundImage','Text','Image','PDF','ZoomPlus','ZoomMinus','Save','Calendar'], // Plugins
		design: [],
		show_cart: false,
		cart_options: [],
		designer_mode: false,
		width: undefined,
		height: undefined,
		selected_layouts_for_print:{},
		calendar_starting_month:undefined,
		calendar_starting_year:undefined,
		calendar_event:{},
		base_url:undefined,
		watermark_text:"xepan"
	},
	_create: function(){
		this.setupLayout();
	},
		
	setupLayout: function(){
		var self = this;
		// Load Plugin Files
		// 
		$.each(this.options.IncludeJS, function(index, js_file) {
			$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/designer/plugins/"+js_file+".js");
		});

		$.each(this.options.ComponentsIncluded, function(index, component) {
			$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/designer/plugins/"+component+".js");
		});

		// // Page Layout Load js
		$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/designer/plugins/PageLayout.js");

		$.atk4(function(){
			self.setupWorkplace();
			window.setTimeout(function(){
				self.setupCanvas();
				if(self.options.showTopBar){
					self.setupToolBar();
				}
				self.loadDesign();
				self.setupPageLayoutBar();
				self.setupFreelancerPanel();
				// self.setupCart();
				self.render();
			},200);
		});

		// this.setupComponentPanel(workplace);
	},

	loadDesign: function(){
		var self = this;
		if(self.options.design == "" || !self.options.design || self.options.design=='null'){
			var temp = new BackgroundImage_Component();
				temp.init(self, self.canvas,null);
				self.pages_and_layouts[self.current_page][self.current_layout]['background'] = temp;
				return;
		} 
		saved_design = JSON.parse(self.options.design);
		
		$.each(saved_design,function(page_name,page_object){
			self.pages_and_layouts[page_name]={};
			self.layout_finalized[page_name]='Main Layout';

			$.each(page_object,function(layout_name,layout_object){
				self.pages_and_layouts[page_name][layout_name]={};
				self.pages_and_layouts[page_name][layout_name]['components']=[];
				
				if(layout_object.components != undefined && layout_object.components.length != 0){
					$.each(layout_object.components,function(key,value){
						value = JSON.parse(value);
						var temp = new window[value.type + "_Component"]();
						temp.init(self, self.canvas, self.editors[value.type]);
						temp.options = value;
						temp.options.load_design = true;
						self.pages_and_layouts[page_name][layout_name]['components'][key] = temp;
					});
				}
				

				var temp = new BackgroundImage_Component();
				temp.init(self, self.canvas,null);
				
				if(layout_object.background != undefined){
					temp.options = JSON.parse(layout_object.background);
				}
				self.pages_and_layouts[page_name][layout_name]['background'] = temp;

			});

		});
		
		if(self.options.selected_layouts_for_print=="" || !self.options.selected_layouts_for_print || self.options.selected_layouts_for_print ==null || self.options.selected_layouts_for_print ==undefined){

		}else{
			// console.log('check me');
				// console.log(self.options.selected_layouts_for_print);
				// console.log(self.layout_finalized);

			$.each(self.options.selected_layouts_for_print,function(page,layout){
				self.layout_finalized[page] = layout;
			});
		}
	},

	setupPageLayoutBar : function(){
		//Page and Layout Setup
		var self = this;
		var bottom_bar = $('<div class="xshop-designer-tool-bottombar"></div>');
		bottom_bar.appendTo(this.element);
		self.bottombar_wrapper = bottom_bar;
		var temp = new PageLayout_Component();
		temp.init(self, self.canvas, bottom_bar);
		bottom_tool_btn = temp.renderTool();
		self.bottom_bar = temp;
	},

	setupToolBar: function(){
		var self=this;

		this.top_bar = $('<div class="xshop-designer-tool-topbar row"></div>');
		this.top_bar.prependTo(this.element);

		//Add Designer Item Name 
		var item_name = $('<h1 class="xshop-designer-item-name">'+self.options.item_name+'</h1>');
		item_name.prependTo(this.top_bar.parent());

		var buttons_set = $('<div class="xshop-designer-tool-topbar-buttonset"></div>').appendTo(this.top_bar);
		this.option_panel = $('<div class="xshop-designer-tool-topbar-options pull-right" style="display:none;"></div>').appendTo(this.top_bar);

		// this.remove_btn = $('<div class="xshop-designer-remove-toolbtn"><i class="glyphicon glyphicon-remove"></i><br>Remove</div>').appendTo(this.option_panel);

		// this.remove_btn.click(function(event){
		// 	$.each(self.pages_and_layouts[self.current_page][self.current_layout].components, function(index,cmp){
		// 		if(cmp === self.current_selected_component){
		// 			// console.log(self.pages_and_layouts);
		// 			$(self.current_selected_component.element).remove();
		// 			self.pages_and_layouts[self.current_page][self.current_layout].components.splice(index,1);
		// 			self.current_selected_component = null;
		// 			self.option_panel.hide();
		// 			// console.log(self.pages_and_layouts);
		// 			// self.render();
		// 		}
		// 	});
		// });
		$.each(this.options.ComponentsIncluded, function(index, component) {
			var temp = new window[component+"_Component"]();
			temp.init(self, self.canvas);
			tool_btn = temp.renderTool(self.top_bar) ;
			self.editors[component] = temp.editor;
		});
		
		
		// Hide options if not clicked on any component
		$(this.canvas).click(function(event){
			$('.ui-selected').removeClass('ui-selected');
			self.option_panel.hide();
			self.current_selected_component = undefined;
			if(self.options.designer_mode){
				self.freelancer_panel.FreeLancerComponentOptions.element.hide();
			}
			$('div.guidex').css('display','none');
			$('div.guidey').css('display','none');
			event.stopPropagation();
		});
	},

	setupFreelancerPanel: function(){
		var self=this;
		if(this.options.designer_mode){
			// console.log(this);
			this.freelancer_panel = new FreeLancerPanel(this.top_bar,self, self.canvas);
			this.freelancer_panel.init();
		}
	},

	setupWorkplace: function(){
		this.workplace = $('<div class="xshop-designer-tool-workplace" style="width:100%"></div>').appendTo(this.element);
	},

	setupComponentPanel: function(workplace){
		this.component_panel = $('<div id="xshop-designer-component-panel" class=" col-md-3">Nothing Selecetd</div>').appendTo(workplace);
	},

	setupCanvas: function(){
		var self = this;
		this.canvas = $('<div class="xshop-desiner-tool-canvas atk-move-center" style="position:relative; z-index:0;"></div>').appendTo(this.workplace);
		
		this.canvas.css('width',this.options.width + this.options.unit); // In given Unit
		this.px_width = this.canvas.width(); // Save in pixel for actual should be width
		// this.canvas.css('max-width',this.px_width+'px');
		this.canvas.css('overflow','hidden');
		if(this.canvas.width() > this.workplace.width()){
			this.canvas.css('width', this.workplace.width() - 20 + 'px');
		}

		if(this.canvas.width() < (this.workplace.width()/2)){
			this.canvas.width((this.workplace.width()/2));
		}
		// console.log(this.canvas.width());
		
		this.safe_zone = $('<div class="xshop-desiner-tool-safe-zone" style="position:absolute"></div>').appendTo(this.canvas);
		this.guidex= $('<div class="guidex" style="z-index:100;"></div>').appendTo($('body'));
		this.guidey= $('<div class="guidey" style="z-index:100;"></div>').appendTo($('body'));
	},

	setupCart: function(){
		var self=this;
		if(!self.options.show_cart) return;
		if(self.options.designer_mode) return;

		self.options.cart_options['show_cart_btn']=true;
		self.options.cart_options['base_url']= self.options.base_url;

		cart_container = $('<div class="xepan-xshop-designer-cart-container"></div>').appendTo(self.element);
		price_div = $('<div class="xshop-item-price"></div>').appendTo(cart_container);
		original_rate = $('<div class="xshop-item-old-price">'+self.options.currency_symbole+" "+self.options.item_original_price+'</div>').appendTo(price_div);
		price_rate = $('<div class="xshop-item-new-price">'+self.options.currency_symbole+" "+self.options.item_sale_price+'</div>').appendTo(price_div);
		this.cart = $('<div class="xshop-designer-item-custom-field-container"></div>').appendTo(cart_container);
		this.cart.xepan_xshop_addtocart(self.options.cart_options);

		cart_container.hide();
		
		// //Adding Next and Previous Button
		// next_btn = $('<div class="atk-swatch-ink btn btn-info atk-padding-small pull-right">Next</div>').insertAfter($.find('.xshop-designer-tool-workplace'));
		// $(next_btn).click(function(event){
		// 	if($(this).text()=="Next")
		// 		$(this).text('Previous');
		// 	else
		// 		$(this).text('Next');

		// 	$(cart_container).toggle('slow');
		// 	$('html,body').animate({
  //           	scrollTop: $(cart_container).offset().top - 200},
  //           'slow');

		// });

	},

	render: function(param){
		var self = this;
		this.canvas.css('height',this.options.height + this.options.unit); // In Given Unit
		this.canvas.height(this.canvas.height() * this._getZoom()); // get in pixel .height() and multiply by zoom 

		this.safe_zone.css('width',(this.options.width - (this.options.trim * 2)) + this.options.unit); // In given unit
		this.safe_zone.css('height',(this.options.height - (this.options.trim * 2)) + this.options.unit); // In given UNit

		this.safe_zone.width(this.safe_zone.width() * this._getZoom()); // get width in pixels and multiply by our zoom
		this.safe_zone.height(this.safe_zone.height() * this._getZoom()); // get height in pixels and multiply by our zoom

		var trim_in_px= (this.canvas.width() - this.safe_zone.width()) / 2;
		this.safe_zone.css('margin-left',trim_in_px);
		this.safe_zone.css('margin-right',trim_in_px);
		this.safe_zone.css('margin-top',trim_in_px);
		this.safe_zone.css('margin-bottom',trim_in_px);
		
		this.canvas.find('.xshop-designer-component').hide();
		// console.log('Components in '+ self.pages_and_layouts[self.current_page][self.current_layout].components.length);
		if(self.pages_and_layouts[self.current_page][self.current_layout].components != undefined && self.pages_and_layouts[self.current_page][self.current_layout].components.length != 0){
			$.each(self.pages_and_layouts[self.current_page][self.current_layout].components, function(index, component) {
				component.render();
			});
		}
			
		if(self.pages_and_layouts[self.current_page][self.current_layout].background != undefined && self.pages_and_layouts[self.current_page][self.current_layout].background.length != 0){
			self.pages_and_layouts[self.current_page][self.current_layout].background.render();
		}
	},

	_getZoom:function(){
		var zoom = (this.canvas.width())/ this.px_width;
		if(zoom != this.zoom){
			this.delta_zoom = this.zoom + zoom;
			this.zoom = zoom;
		}
		// console.log(this.zoom);
		return this.zoom;
	},

	_isDesignerMode:function(){
		return this.options.designer_mode;
	},

	get_widget: function(){
		return this;
	},

	check: function(){
		// console.log(this.components);
	},

	screen2option: function(val){
		return val / this._getZoom();
	},

	option2screen: function(val){
		return val * this._getZoom();
	}


});



(function($) {
    /**
     * KeyUp with delay event setup
     * 
     * @link http://stackoverflow.com/questions/1909441/jquery-keyup-delay#answer-12581187
     * @param function callback
     * @param int ms
     */
    $.fn.delayKeyup = function(callback, ms){
            $(this).keyup(function( event ){
                var srcEl = event.currentTarget;
                if( srcEl.delayTimer )
                    clearTimeout (srcEl.delayTimer );
                srcEl.delayTimer = setTimeout(function(){ callback( $(srcEl) ); }, ms);
            });

        return $(this);
    };
})(jQuery);


$.ui.plugin.add("draggable", "smartguides", {
	start: function(event, ui) {
		var i = $(this).data("ui-draggable");
		o = i.options;
		i.elements = [];
		$(o.smartguides.constructor != String ? ( o.smartguides.items || ':data(ui-draggable)' ) : o.smartguides).each(function() {
			var $t = $(this); var $o = $t.offset();
			if(this != i.element[0]) i.elements.push({
				item: this,
				width: $t.outerWidth(), height: $t.outerHeight(),
				top: $o.top, left: $o.left
			});
		});
	},
	drag: function(event, ui) {
		var inst = $(this).data("ui-draggable"), o = inst.options;
		var d = o.tolerance;
        $(".guidex").css({"display":"none"});
        $(".guidey").css({"display":"none"});
            var x1 = ui.offset.left, x2 = x1 + inst.helperProportions.width,
                y1 = ui.offset.top, y2 = y1 + inst.helperProportions.height;
            	xc = (x1 + x2) /2, yc = (y1 + y2) / 2;
            for (var i = inst.elements.length - 1; i >= 0; i--){
                var l = inst.elements[i].left, r = l + inst.elements[i].width,
                    t = inst.elements[i].top, b = t + inst.elements[i].height;
                    
                    hc = (l + r) / 2, vc = (t + b) / 2;

                    var ls = Math.abs(l - x2) <= d;
                    var lss = Math.abs(l - x1) <= d;
                    var rs = Math.abs(r - x1) <= d;
                    var ts = Math.abs(t - y2) <= d;
                    var bs = Math.abs(b - y1) <= d;
                	var hs = Math.abs(hc - xc) <= d;
                    var vs = Math.abs(vc - yc) <= d;
                    var rr = Math.abs(r - x2) <=d;

                if(lss){
                    ui.position.left = inst._convertPositionTo("relative", { top: 0, left: l }).left - inst.margins.left;
                    $(".guidex").css({"left":l-d+4,"display":"block"});
                }
                if(ls) {
                    ui.position.left = inst._convertPositionTo("relative", { top: 0, left: l - inst.helperProportions.width }).left - inst.margins.left;
                    $(".guidex").css({"left":l-d+4,"display":"block"});
                }
                if(rs) {
                    ui.position.left = inst._convertPositionTo("relative", { top: 0, left: r }).left - inst.margins.left;
                     $(".guidex").css({"left":r-d+4,"display":"block"});
                }
                
                if(ts) {
                    ui.position.top = inst._convertPositionTo("relative", { top: t - inst.helperProportions.height, left: 0 }).top - inst.margins.top;
                    $(".guidey").css({"top":t-d+4,"display":"block"});
                }
                if(bs) {
                    ui.position.top = inst._convertPositionTo("relative", { top: b, left: 0 }).top - inst.margins.top;
                    $(".guidey").css({"top":b-d+4,"display":"block"});
                }
                if(rr){
	                ui.position.left = inst._convertPositionTo("relative", { top: 0, left: r - inst.helperProportions.width}).left - inst.margins.left;
                    $(".guidex").css({"left":r-d+4,"display":"block"});
                }
                if(hs) {
                    ui.position.left = inst._convertPositionTo("relative", { top: 0, left: hc - inst.helperProportions.width/2 }).left - inst.margins.left;
                     $(".guidex").css({"left":hc-d+4,"display":"block"});
                }
                if(vs) {
                    ui.position.top = inst._convertPositionTo("relative", { top: vc - inst.helperProportions.height/2, left: 0 }).top - inst.margins.top;
                    $(".guidey").css({"top":vc-d+8,"display":"block"});
                }
            };
        },

        stop: function(event, ui){
        	$(".guidex").hide();
        	$(".guidey").hide();
        }
});


// Sticky the Designer Tool Top Bar
function sticky_relocate() {
    var window_top = $(window).scrollTop();
    if($('.xshop-designer-tool-topbar').length > 0){
	    var div_top = $('.xshop-designer-tool-topbar').offset().top;
	    if (window_top > 5) {
	        $('.xshop-designer-tool-topbar').addClass('xshop-designer-top-bar-stick');
	    } else {
	        $('.xshop-designer-tool-topbar').removeClass('xshop-designer-top-bar-stick');
	    }
    }
}

// $(function () {
//     $(window).scroll(sticky_relocate);
//     sticky_relocate();
// });