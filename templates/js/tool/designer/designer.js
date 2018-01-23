// xEpan Designer jQuery Widget for extended xShop elements 
var canvas_number = 2;
design_dirty = false;
jQuery.widget("ui.xepan_xshopdesigner",{
		// 'ABeeZee', 'Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Adamina', 'Advent Pro', 'Aguafina Script', 'Akronim', 'Aladin', 'Aldrich', 'Alef', 'Alegreya', 'Alegreya SC', 'Alegreya Sans', 'Alegreya Sans SC', 'Alex Brush', 'Alfa Slab One', 'Alice', 'Alike','Alike Angular', 'Allan', 'Allerta', 'Allerta Stencil', 'Allura', 'Almendra', 'Almendra Display', 'Almendra SC', 'Amarante', 'Amaranth', 'Amatic SC', 'Amethysta', 'Amiri', 'Amita', 'Anaheim', 'Andada', 'Andika', 'Angkor', 'Annie Use Your Telescope', 'Anonymous Pro', 'Antic', 'Antic Didone', 'Antic Slab', 'Anton', 'Arapey', 'Arbutus', 'Arbutus Slab','Architects Daughter','Archivo Black','Archivo Narrow','Arimo','Arizonia','Armata','Artifika','Arvo','Arya','Asap','Asar','Asset','Astloch','Asul','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script','Balthazar','Bangers','Basic','Battambang','Baumans','Bayon','Belgrano','Belleza','BenchNine','Bentham','Berkshire Swash','Bevan','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','Biryani', 'Bitter','Black Ops One','Bokor','Bonbon','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Bubblegum Sans','Bubbler One','Buda','Buenard','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Catamaran','Caudex','Caveat','Caveat Brush','Cedarville Cursive','Ceviche One','Changa One','Chango','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','hivo','Chonburi','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption','Codystar','Combo','Comfortaa','Coming Soon','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Courgette','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Text','Croissant One', 'Crushed', 'Cuprum','Cutive','Cutive Mono','Damion','Dancing Script','Dangrek','Dawning of a New Day','Days One','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Domine','Donegal One','Doppio One','Dorsa','Dosis','Dr Sugiyama','Droid Sans','Droid Sans Mono','Droid Serif','Duru Sans','Dynalight','EB Garamond','Eagle Lake','Eater','Economica','Eczar','Ek Mukta','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Engagement','Englebert','Enriqueta','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Fanwood Text','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Federant','Federo','Felipa','Fenix','Finger Paint','Fira Mono','Fira Sans','Fjalla One','Fjord One','Flamenco','Flavors','Fondamento','Fontdiner Swanky','Forum','Francois One','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','GFS Didot','GFS Neohellenic','Gabriela','Gafata','Galdeano','Galindo','Gentium Basic','Gentium Book Basic','Geo','Geostar','Geostar Fill','Germania One','Gidugu','Gilda Display','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Goblin One','Gochi Hand','Gorditas','Goudy Bookletter 1911','Graduate','Grand Hotel','Gravitas One','Great Vibes','Griffy','Gruppo','Gudea','Gurajada','Habibi','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Headland One','Henny Penny','Herr Von Muellerhoff','Hind','Hind Siliguri','Hind Vadodara','Holtwood One SC','Homemade Apple','Homenaje','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Iceberg','Iceland','Imprima','Inconsolata','Inder','Indie Flower','Inika','Inknut Antiqua','Irish Grover','Istok Web','Italiana','Italianno','Itim','Jacques Francois','Jacques Francois Shadow','Jaldi','Jim Nightshade','Jockey One','Jolly Lodger','Josefin Sans','Josefin Slab','Joti One','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','Kadwa','Kalam','Kameron','Kantumruy','Karla','Karma','Kaushan Script','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kite One','Knewave','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Kurale','La Belle Aurore','Laila','Lakki Reddy','Lancelot','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Libre Baskerville','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','Macondo','Macondo Swash Caps','Magra','Maiden Orange','Mako','Mallanna','Mandali','Marcellus','Marcellus SC','Marck Script','Margarine','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Miniver','Miss Fajardose','Modak','Modern Antiqua','Molengo','Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Muli','Mystery Quest', 'NTR', 'Neucha', 'Neuton', 'New Rocker', 'News Cycle', 'Niconne', 'Nixie One','Nobile','Nokora','Norican','Nosifer','Nothing You Could Do','Noticia Text','Noto Sans','Noto Serif','Nova Cut', 'Nova Flat', 'Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim', 'Nova Square', 'Numans', 'Nunito', 'Odor Mean Chey', 'Offside', 'Old Standard TT', 'Oldenburg', 'Oleo Script', 'Oleo Script Swash Caps', 'Open Sans', 'Open Sans Condensed', 'Oranienbaum', 'Orbitron', 'Oregano', 'Orienta', 'Original Surfer', 'Oswald', 'Over the Rainbow', 'Overlock', 'Overlock SC', 'Ovo', 'Oxygen', 'Oxygen Mono', 'PT Mono', 'PT Sans', 'PT Sans Caption', 'PT Sans Narrow', 'PT Serif', 'PT Serif Caption', 'Pacifico', 'Palanquin', 'Palanquin Dark', 'Paprika', 'Parisienne', 'Passero One', 'Passion One', 'Pathway Gothic One', 'Patrick Hand', 'Patrick Hand SC', 'Patua One', 'Paytone One', 'Peddana', 'Peralta', 'Permanent Marker', 'Petit Formal Script', 'Petrona', 'Philosopher','Piedra','Pinyon Script', 'Pirata One', 'Plaster', 'Play', 'Playball', 'Playfair Display', 'Playfair Display SC', 'Podkova','Poiret One', 'Poller One','Poly','Pompiere','Pontano Sans', 'Poppins','Port Lligat Sans', 'Port Lligat Slab', 'Pragati Narrow', 'Prata', 'Preahvihear','Press Start 2P','Princess Sofia', 'Prociono', 'Prosto One', 'Puritan', 'Purple Purse', 'Quando', 'Quantico', 'Quattrocento', 'Quattrocento Sans', 'Questrial', 'Quicksand','Quintessential','Qwigley', 'Racing Sans One', 'Radley','Rajdhani', 'Raleway', 'Raleway Dots', 'Ramabhadra','Ramaraja', 'Rambla', 'Rammetto One', 'Ranchers', 'Rancho', 'Ranga', 'Rationale','Ravi Prakash', 'Redressed', 'Reenie Beanie', 'Revalia', 'Rhodium Libre', 'Ribeye','Ribeye Marrow', 'Righteous', 'Risque', 'Roboto', 'Roboto Condensed', 'Roboto Mono','Roboto Slab', 'Rochester', 'Rock Salt', 'Rokkitt', 'Romanesco', 'Ropa Sans', 'Rosario','Rosarivo', 'Rouge Script', 'Rozha One', 'Rubik','Rubik Mono One', 'Rubik One', 'Ruda', 'Rufina', 'Ruge Boogie', 'Ruluko', 'Rum Raisin', 'Ruslan Display', 'Russo One', 'Ruthie', 'Rye','Sacramento','Sahitya','Sail', 'Salsa', 'Sanchez', 'Sancreek', 'Sansita One', 'Sarala', 'Sarina', 'Sarpanch', 'Satisfy', 'Scada', 'Scheherazade', 'Schoolbell', 'Seaweed Script', 'Sevillana', 'Seymour One', 'Shadows Into Light', 'Shadows Into Light Two', 'Shanti', 'Share', 'Share Tech', 'Share Tech Mono', 'Shojumaru', 'Short Stack', 'Siemreap', 'Sigmar One', 'Signika', 'Signika Negative', 'Simonetta', 'Sintony', 'Sirin Stencil', 'Six Caps', 'Skranji', 'Slabo 13px', 'Slabo 27px', 'Slackey', 'Smokum','Smythe','Sniglet', 'Snippet', 'Snowburst One', 'Sofadi One', 'Sofia', 'Sonsie One', 'Sorts Mill Goudy', 'Source Code Pro', 'Source Sans Pro', 'Source Serif Pro', 'Special Elite', 'Spicy Rice', 'Spinnaker', 'Spirax', 'Squada One', 'Sree Krushnadevaraya', 'Stalemate', 'Stalinist One','Stardos Stencil', 'Stint Ultra Condensed','Stint Ultra Expanded','Stoke', 'Strait','Sue Ellen Francisco', 'Sumana', 'Sunshiney', 'Supermercado One', 'Sura', 'Suranna', 'Suravaram', 'Suwannaphum', 'Swanky and Moo Moo', 'Syncopate', 'Tangerine', 'Taprom', 'Tauri', 'Teko', 'Telex', 'Tenali Ramakrishna', 'Tenor Sans', 'Text Me One', 'The Girl Next Door', 'Tienne','Tillana', 'Timmana', 'Tinos', 'Titan One', 'Titillium Web', 'Trade Winds', 'Trocchi', 'Trochut', 'Trykker', 'Tulpen One', 'Ubuntu', 'Ubuntu Condensed', 'Ubuntu Mono', 'Ultra','Uncial Antiqua','Underdog', 'Unica One', 'UnifrakturCook','UnifrakturMaguntia', 'Unkempt', 'Unlock', 'Unna', 'VT323', 'Vampiro One', 'Varela', 'Varela Round', 'Vast Shadow', 'Vesper Libre','Vibur', 'Vidaloka','Viga', 'Voces', 'Volkhov', 'Vollkorn', 'Voltaire','Waiting for the Sunrise', 'Wallpoet', 'Walter Turncoat', 'Warnes', 'Wellfleet', 'Wendy One', 'Wire One', 'Work Sans', 'Yanone Kaffeesatz', 'Yantramanav', 'Yellowtail', 'Yeseva One', 'Yesteryear', 'Zeyada'

	editors:[],
	options:{
		// Layout Options
		showTopBar: true,
		// ComponentsIncluded: ['Background','Text','Image','Help'], // Plugins
		IncludeJS: ['FreeLancerPanel','jquery.cookie'], // Plugins
		ComponentsIncluded: ['BackgroundImage','Text','Image','ZoomPlus','ZoomMinus','Save','Calendar'], // Plugins
		ComponentsIncludedToBeShow: ['BackgroundImage','Text','Image','ZoomPlus','ZoomMinus','Save','Calendar'], // Plugins
		// ComponentsIncluded: ['BackgroundImage','Text','Image','PDF','ZoomPlus','ZoomMinus','Save','Calendar'], // Plugins
		BackgroundImage_tool_label:"Background Image",
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
		watermark_text:"xepan",
		is_start_call: false,
		start_page:false,
		start_layout:false,

		show_tool_bar:true,
		show_pagelayout_bar:true,
		show_canvas:true,
		printing_mode:false,
		show_layout_bar:true,
		show_paginator:true,
		show_navigation:false,
		// mode:"multi-page-single-layout",
		mode:"primary",
		file_name:undefined,
		show_tool_calendar_starting_month:true,
		canvas_render_callback:undefined,
		make_static:false,
		generating_image:false,
		preview_thumbnail_max_width:'96',
		show_safe_zone:1,

		// undo redo 
		ur_state:[],
		ur_index:-1
	},
	_create: function(){
		// console.log('is_start ' +this.options.is_start_call);

		this.current_page='Front Page';
		this.current_layout='Main Layout';

		if(this.options.start_page) this.current_page=this.options.start_page
		if(this.options.start_layout) this.current_layout=this.options.start_layout

		this.pages_and_layouts = {};
		this.pages_and_layouts["Front Page"]={
				"Main Layout": {
					components: [],
					background: undefined
				},
				"sequence_no":1
			};

		this.layout_finalized = {"Front Page" : "Main Layout"};

		this.current_selected_component = undefined;
		this.current_selected_component_id = undefined;
			// components:[],
			// item_id:undefined, // used from options
		this.item_member_design_id = undefined; // used from options
		this.workplace = undefined;
		this.canvas = undefined;
		this.canvasObj = undefined;
		this.safe_zone = undefined;
		this.cart = undefined;
		this.zoom = 1;
		this.delta_zoom = 0;
		this.px_width = undefined;
		this.option_panel = undefined;
		this.freelancer_panel = undefined;
		this.top_bar = undefined;
		this.pointtopixel = {'6':8,'7':9,'8':11,'9':12,'10':13,'11':15,'12':16,'13':17,'14':19,'15':21,'16':22,'17':23,'18':24,'19':25,'20':26,'21':28,'22':29,'23':31,'24':32,'25':33,'26':35,'27':36,'28':37,'29':38,'30':40,'31':41,'32':42,'33':44,'34':45,'35':47,'36':48,'37':49,'38':51,'39':52,'40':53,'41':54,'42':56,'43':57,'44':59,'45':60,'46':61,'47':63,'48':64,'49':65,'50':67,'51':68,'52':69,'53':71,'54':72,'55':73,'56':75,'57':76,'58':77,'59':79,'60':80,'61':81,'62':83,'63':84,'64':85,'65':87,'66':88,'67':89,'68':91,'69':92,'70':93,'71':95,'72':96,'73':97,'74':99,'75':100,'76':101,'77':103,'78':104,'79':105,'80':106,'81':108,'82':109,'83':111,'84':112,'85':113,'86':115,'87':116,'88':117,'89':119,'90':120,'91':121,'92':122,'93':124,'94':125,'95':127,'96':128,'97':129,'98':131,'99':132,'100':133};
		this.pixeltopoint = {'6':8,'7':9,'8':11,'9':12,'10':13,'11':15,'12':16,'13':17,'14':19,'15':21,'16':22,'17':23,'18':24,'19':25,'20':26,'21':28,'22':29,'23':31,'24':32,'25':33,'26':35,'27':36,'28':37,'29':38,'30':40,'31':41,'32':42,'33':44,'34':45,'35':47,'36':48};

		this.font_family = ['Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Cabin','Cambay','Cambo','Candal','Petit Formal Script', 'Petrona', 'Philosopher','Piedra', 'Ubuntu'];
		if(this.options.font_family_list != undefined)
			this.font_family = JSON.parse(this.options.font_family_list);

		if(this.options.ComponentsIncludedToBeShow == undefined || this.options.ComponentsIncludedToBeShow == null)
			this.options.ComponentsIncludedToBeShow = ['BackgroundImage','Text','Image','ZoomPlus','ZoomMinus','Save','Calendar'];

		this.setupLayout();
	},
		
	setupLayout: function(){
		var self = this;
		// setting
		if(self.options.mode == "multi-page-single-layout"){
			self.options.show_canvas = false;
			self.options.show_pagelayout_bar = true;
			self.options.show_layout_bar = false;
			self.options.show_paginator = false;
			self.options.show_navigation = false;
		}
		
		// Load Plugin Files
		if(self.options.is_start_call){
			$.each(this.options.IncludeJS, function(index, js_file) {
				$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/designer/plugins/"+js_file+".js");
			});

			$.each(this.options.ComponentsIncluded, function(index, component) {
				$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/designer/plugins/"+component+".js");
			});

			// // Page Layout Load js
			$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/designer/plugins/PageLayout.js");
		}


		$.atk4(function(){
			self.setupWorkplace();
			window.setTimeout(function(){

				self.setupCanvas();

				if(self.options.is_start_call){
					if(self.options.showTopBar){
						self.setupToolBar();
					}
				}
								
				self.loadDesign();
				if(self.options.is_start_call){
					if(self.options.show_pagelayout_bar != "false" || self.options.show_pagelayout_bar == false)
						self.setupPageLayoutBar();

					self.setupFreelancerPanel();
				}
					// self.setupCart();
				self.render();
				
				if(self.options.mode === "multi-page-single-layout")
					self.setupNextPreviousNavigation();
			},200);
		});
		// this.setupComponentPanel(workplace);
	},

	loadDesign: function(load_design){
		var self = this;
		if(self.options.design == "" || !self.options.design || self.options.design=='null'){
			var temp = new BackgroundImage_Component();
				temp.init(self, self.canvas,null);
				self.pages_and_layouts[self.current_page][self.current_layout]['background'] = temp;
				return;
		}
		if(load_design == null)
			saved_design = JSON.parse(self.options.design);
		else
			saved_design = load_design;

		var reset_sequence_no = 1;
		$.each(saved_design,function(page_name,page_object){

			self.pages_and_layouts[page_name]={};
			self.layout_finalized[page_name] = 'Main Layout';

			self.pages_and_layouts[page_name]['sequence_no'] = page_object['sequence_no']?page_object['sequence_no']:reset_sequence_no;
			reset_sequence_no ++;
			
			$.each(page_object,function(layout_name,layout_object){
				if(layout_name === "sequence_no"){
					return;
				}

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

	setupNextPreviousNavigation:function(){
		var self = this;
		$('.xshop-designer-tool-workplace-previous-wrapper').remove();
		$('.xshop-designer-tool-workplace-next-wrapper').remove();
		$('.designer-show-all-page-btn').remove();

		if(!self.options.is_start_call) return;

		show_all_page = $('<div class="btn designer-show-all-page-btn"><i class="glyphicon glyphicon-th"></i><br>Show All</div>').appendTo($('.xshop-designer-tool-topbar-buttonset'));
		$(show_all_page).click(function(){
			$(".xshop-designer-tool-workplace").hide();
			$(".xshop-designer-tool-workplace-previous-wrapper").hide();
			$(".xshop-designer-tool-workplace-next-wrapper").hide();
			
			self.options.show_layout_bar = false;
			self.options.show_paginator = false;
			self.show_canvas = true;
			self.show_navigation = false;
			
			$('.xshop-designer-tool-bottombar .xshop-designer-tool-workplace').show();
			$('.xshop-designer-tool-bottombar').show();
			// self.setupPageLayoutBar();
		});

		// var navigation = $('<div class="xshop-designer-tool-next-previous-navigation"></div>');
		// navigation.appendTo(this.element);
		workplace_next_wrapper = $('<div class="xshop-designer-tool-workplace-next-wrapper" style="float:right;"></div>').insertBefore(self.workplace);
		workplace_previous_wrapper = $('<div class="xshop-designer-tool-workplace-previous-wrapper" style="float:right;"></div>').insertBefore(self.workplace);
		// workplace_next_wrapper = $('<div class="xshop-designer-tool-workplace-next-wrapper" style="width:10%;float:left;text-align:right;"></div>').insertAfter(self.workplace);

		$(self.workplace).css('clear','both');
		// $(workplace_next_wrapper).height($('.xshop-designer-tool-workplace').height());
		// $(workplace_previous_wrapper).height($('.xshop-designer-tool-workplace').height());

		previous_button = $('<div title="Previous Page" class="btn btn-default previous-button"> << Previous Page </div>').appendTo(workplace_previous_wrapper);
		next_button = $('<div title="Next Page"  class="btn btn-default next-button"> Next Page >> </div>').appendTo(workplace_next_wrapper);

		// $(previous_button).css('margin-top',($('.xshop-designer-tool-workplace').height()/2)+'px');
		// $(next_button).css('margin-top',($('.xshop-designer-tool-workplace').height()/2)+'px');

		$(next_button).click(function(){
			next_page = self.nextPage(self.current_page,self);
			if(next_page != self.current_page){
				$(previous_button).removeAttr("disabled");
			}
			else{
				$(this).attr('disabled','disabled');
			}
			self.options.start_page = self.current_page = next_page;
			self.options.start_layout = self.current_layout = "Main Layout";
			self.render();
		});

		$(previous_button).click(function(){
			previous_page = self.previousPage(self.current_page,self);

			if(previous_page != self.current_page)
				$(next_button).removeAttr("disabled");
			else{
				$(this).attr("disabled",'disabled');
			}

			self.options.start_page = self.current_page = previous_page;
			self.options.start_layout = self.current_layout = "Main Layout";
			self.render();
		});

		if(!self.options.show_navigation){
			$(workplace_next_wrapper).hide();
			$(workplace_previous_wrapper).hide();
			$('.xshop-designer-tool > .xshop-designer-tool-workplace').hide();
			// $('.xshop-designer-tool > .xshop-designer-tool-workplace').show();
		}

	},

	getPageName: function(sequence_no,designer_tool){
		var sequence_page_name;
		$.each(designer_tool.pages_and_layouts,function(page_name, obj){
			if(obj['sequence_no'] === sequence_no){
				sequence_page_name = page_name;
				return false;
			}
		});

		return sequence_page_name;
	},

	nextPage: function(current_page,designer_tool){
		var pages = undefined;
		if(this.pages_and_layouts !=undefined)
			pages = designer_tool.pages_and_layouts;

		if(pages === undefined)
			return current_page;

		pages_array = [];
		// $.each(pages,function(page_name){
		// 	pages_array.push(page_name);
		// });

		$.each(pages,function(page_name,value){
			// console.log("Page Name= "+page_name+" sequence ="+value['sequence_no']);
			pages_array.splice((value['sequence_no'] - 1), 0, page_name);
		});
		// $.each(pages,function(page_name,value){
		// 	toIndex = value['sequence_no'];
		// 	var element = pages_array[fromIndex];
	 //    	pages_array.splice(fromIndex, 1);
	 //    	pages_array.splice(toIndex, 0, element);

			// console.log(pages_array);
		// });

		// console.log(pages);

		count = pages_array.length;
		current_page_index = pages_array.indexOf(current_page);
		required_index = current_page_index + 1;

		// console.log("current index"+current_page_index+" = required index = "+required_index);
		if((required_index +1) > count)
			return current_page;

		// console.log(pages_array[required_index]);
		return pages_array[required_index];

	},

	previousPage:function(current_page,designer_tool){
		self = designer_tool;
		var  pages = undefined;
		if(self.pages_and_layouts != undefined)
			pages = self.pages_and_layouts;
		
		if(self.designer_tool != undefined)
			pages = self.designer_tool.pages_and_layouts;

		if(pages === undefined)
			return current_page;

		pages_array = [];
		$.each(pages,function(page_name,value){
			// console.log("Page Name= "+page_name+" sequence ="+value['sequence_no']);
			pages_array.splice((value['sequence_no'] - 1), 0, page_name);
		});
		count = pages_array.length;
		current_page_index = pages_array.indexOf(current_page);
		required_index = current_page_index - 1;

		if(required_index < 0)
			return current_page;

		return pages_array[required_index];
	},

	setupPageLayoutBar : function(){
		//Page and Layout Setup
		var self = this;
		if(!self.options.is_start_call) return;

		$.atk4.includeJS(self.options.base_url+"vendor/xepan/commerce/templates/js/tool/jquery.fancybox.js");

		var bottom_bar = $('<div class="xshop-designer-tool-bottombar"></div>');
		bottom_bar.appendTo(this.element);
		self.bottombar_wrapper = bottom_bar;
		count = 0;

		self.page_count = 0;
		$.each(self.pages_and_layouts,function(page_name,layouts){
			self.page_count =  self.page_count + 1;
		});

		for (var i = 1; i <= self.page_count; i++) {
			var page_name = self.getPageName(i, self);
			layouts = self.pages_and_layouts[page_name];

		// $.each(self.pages_and_layouts,function(page_name,layouts){
			layout_name = "Main Layout";
			if(self.options.selected_layouts_for_print && self.options.selected_layouts_for_print['page_name'])
				layout_name = self.options.selected_layouts_for_print[page_name];

			pl = $('<div title="click to zoom" style="cursor:pointer;" class="xshop-designer-pagethumbnail" data-pagename="'+page_name+'" data-layoutname="'+layout_name+'" >')
				.appendTo(bottom_bar)
				.width(200)
				;

			// set width option on preview mode
			if(self.options.is_preview_mode){
				var preview_width = self.options.width;
				if(preview_width > parseInt(self.options.preview_thumbnail_max_width))
					preview_width = parseInt(self.options.preview_thumbnail_max_width);

				pl.width(preview_width + self.options.unit)
					.css('margin','15px 3px 0px 0px')
					.css('border','1px solid #F3F3F3')
					;
			}


			if(!self.options.printing_mode){

				$(pl).on('click',function(event){
					if(self.options.is_preview_mode)
						return;
					temp_page_name = $(this).attr('data-pagename');

					self.options.start_page = self.current_page = temp_page_name;
					self.options.start_layout =  self.current_layout = self.layout_finalized[temp_page_name];
					
					if(self.options.mode == "multi-page-single-layout"){
						$(".xshop-designer-tool-workplace").show();

						// $(".xshop-designer-tool-workplace-next-wrapper").show();
						// $(".xshop-designer-tool-workplace-previous-wrapper").show();

						self.options.show_navigation = true;
						self.options.show_pagelayout_bar = false;
						self.options.show_canvas = true;
						
						$(self.canvas).show();
						self.setupNextPreviousNavigation();
						$('.xshop-designer-tool-bottombar').hide();
					}

					self.render();
					
					$(this).siblings().removeClass('ui-selected');
					$(this).addClass('ui-selected');
					if(self.options.show_layout_bar){
						self.layoutBar(bottom_bar);
					}
					// $('.xshop-designer-tool').xepan_xshopdesigner('render');
				}).css('float','left');

			}
				
				// .height(100)
			pl.xepan_xshopdesigner({
									'width':self.options.width,
									'height':self.options.height,
									'trim':0,
									'unit':self.options.unit,
									'designer_mode': false,
									'design':self.options.design,
									'show_cart':'0',
									'start_page': page_name,
									'start_layout':self.layout_finalized[page_name],
									'printing_mode':self.options.printing_mode,
									'item_name':self.options.item_name,
									// 'cart_options' => $cart_options,
									// 'selected_layouts_for_print' => $selected_layouts_for_print,
									// 'item_id'=>$this->item_id,
									// 'item_member_design_id' => $this->item_member_design_id,
									// 'item_name' => $this->item['name'] ." ( ".$this->item['sku']." ) ",
									// 'item_sale_price'=>$this->item['sale_price'],
									// 'item_original_price'=>$this->item['original_price'],
									// 'currency_symbole'=>$currency,
									// 'base_url':$this->api->url()->absolute()->getBaseURL(),
									// 'watermark_text'=>$this->options['watermark_text'],
									'calendar_starting_month':self.options.calendar_starting_month,
									'calendar_starting_year':self.options.calendar_starting_year,
									'calendar_event':self.options.calendar_event,
									'show_canvas':true,
									"mode":"Primary",
									'generating_image':self.options.generating_image,
									'show_safe_zone':0
							});

			$('<div class="pagelayoutname text-center">'+page_name+'</div>').appendTo(pl);
			if(self.current_page == page_name)
				$(pl).addClass('ui-selected');
			else
				$(pl).removeClass('ui-selected');

			count = count + 1;
		}

		if(count > 4 && self.options.show_paginator){
			$(bottom_bar).slick({
		        dots: false,
		        infinite: false,
		        slidesToShow: 6,
		        slidesToScroll: 3
	      	});
		}

		if(!self.options.show_pagelayout_bar)
			$(self.bottombar_wrapper).toggle();
		// var temp = new PageLayout_Component();
		// temp.init(self, self.canvas, bottom_bar);
		// bottom_tool_btn = temp.renderTool();
		// self.bottom_bar = temp;

		// draw first time layout
		if(!self.options.printing_mode && self.options.show_layout_bar){
			self.layoutBar(bottom_bar);
		}

		if(self.options.printing_mode)
			self.setupPdfExport();

		// img preview using fancy box
		if(self.options.is_preview_mode){
			$('.xshop-designer-pagethumbnail').click(function(){
				canvasObj = $(this).xepan_xshopdesigner('getCanvasObj');
				if( parseInt(canvasObj.width) > 600)
					var multiplier_factor = 1;
				else{
					var multiplier_factor = 3;
				}

				img_data = canvasObj.toDataURL({
											    multiplier: multiplier_factor
											});
	         	$.fancybox.open('<img src="'+img_data+'">');
			});
		}
	},

	layoutBar:function(bottom_bar){
		self = this;
		$('.xshop-designer-layout').remove();

		layout_bar = $('<div class="xshop-designer-layout" style="clear:both;"></div>').insertAfter(bottom_bar);
		$.each(self.pages_and_layouts[self.current_page],function(layout_name,design){

			if(layout_name == "sequence_no"){
				return;
			}

			layout_canvas = $('<div class="xshop-designer-layoutthumbnail" data-pagename="'+self.current_page+'" data-layoutname="'+layout_name+'">')
				.appendTo(layout_bar)
				.css('float','left')
				.width(200);

			$(layout_canvas).on('click',function(event){
				var selected_page_name = $(this).attr('data-pagename');
				var selected_layout_name = $(this).attr('data-layoutname');

				if(self.options.selected_layouts_for_print == undefined)
					self = self.designer_tool;

				self.options.selected_layouts_for_print[selected_page_name] = selected_layout_name; 
				self.layout_finalized[selected_page_name] = selected_layout_name; 

				self.options.start_page = self.current_page = selected_page_name;
				self.options.start_layout =  self.current_layout = selected_layout_name;
				self.render();

				$(this).closest('.xshop-designer-layout').children('.xshop-designer-layoutthumbnail').removeClass('ui-selected');
				$(this).addClass('ui-selected');
				// $('.xshop-designer-tool').xepan_xshopdesigner('render');
			});

			layout_canvas.xepan_xshopdesigner({
								'width':self.options.width,
								'height':self.options.height,
								'trim':0,
								'unit':self.options.unit,
								'designer_mode': false,
								'design':self.options.design,
								'show_cart':'0',
								'start_page': self.current_page,
								'start_layout':layout_name,
								'printing_mode':self.options.printing_mode,
								'item_name':self.options.item_name,
								'mode':"Primary",
								'calendar_starting_month':self.options.calendar_starting_month,
								'calendar_starting_year':self.options.calendar_starting_year,
								'calendar_event':self.options.calendar_event,
								'show_canvas':true,
								'show_safe_zone':false
						});


			$('<div class="pagelayoutname text-center">'+layout_name+'</div>').appendTo(layout_canvas);

			if(self.current_layout == layout_name)
				$(layout_canvas).addClass('ui-selected');
			else
				$(layout_canvas).removeClass('ui-selected');

		});
	},

	setupPdfExport:function(){
		self = this;

		$('.xshop-designer-tool-bottombar .xshop-designer-pagethumbnail').each(function(){
			generate_pdf_btn = $(this).prepend('<div class="btn btn-primary">Generate PDF</div>');

			$(generate_pdf_btn).click(function(event){
				$(this).find('.lower-canvas').css('border','2px solid red');
				orientation = 'P';

				var width = self.options.width;
				var height = self.options.height;
				var unit = self.options.unit;
				// if(self.designer_tool){
				// 	width = self.designer_tool.options.width;
				// 	height = self.designer_tool.options.height;
				// 	unit = self.designer_tool.options.unit;
				// }

				// console.log("width ="+width+" height="+height+" unit="+unit);

				if(self.options.width > self.options.height)
					orientation = 'L';

				$(this).find('canvas').each(function(index,canvas){
					// var pdfObj  = new jsPDF(orientation,self.options.unit,[self.options.width,self.options.height],true);
					var pdfObj  = new jsPDF(orientation,self.options.unit,[self.options.width,self.options.height]);
					img_data = canvas.toDataURL('image/jpeg',1.0);
					pdfObj.addImage(img_data,'JPEG',0,0,self.options.width,self.options.height);
					pdfObj.save(self.options.file_name+"_"+$(this).closest('.xshop-designer-pagethumbnail').attr('data-pagename') +'_'+$(this).closest('.xshop-designer-pagethumbnail').attr('data-layoutname')+".pdf");
				});

			});
		});


		// $('.xshop-designer-tool-bottombar')
		// if(self.options.printing_mode){
		// 	self.pdfObj = new jsPDF('p','mm','a4');
		// 	self.pdfObj.save('a.pdf');
		// 	// $('div').html(self.canvasObj.toDataURL());
		// 	// self.pdfObj.addImage(self.canvasObj.toDataURL(),'png',15,40);
		// }
	},

	setupToolBar: function(){
		var self=this;

		this.top_bar = $('<div class="xshop-designer-tool-topbar row"></div>');
		this.top_bar.prependTo(this.element);

		//Add Designer Item Name 
		if(self.options.show_tool_bar){
			var item_name = $('<h1 class="xshop-designer-item-name">'+self.options.item_name+'</h1>');
			item_name.prependTo(this.top_bar.parent());
		}

		var buttons_set = $('<div class="xshop-designer-tool-topbar-buttonset"></div>').appendTo(this.top_bar);
		this.option_panel = $('<div class="xshop-designer-tool-topbar-options" style="display:none; position:absolute;"></div>').appendTo(this.top_bar);

		$undo_btn = $('<div id="xdesigner-undo" class="btn btn-deault"><i class="glyphicon glyphicon-repeat" style="-moz-transform: scaleX(-1);-o-transform: scaleX(-1);-webkit-transform: scaleX(-1);transform: scaleX(-1);filter: FlipH;-ms-filter: "FlipH";"></i><br>Undo</div>').appendTo(self.top_bar.find('.xshop-designer-tool-topbar-buttonset'));
		$redo_btn = $('<div id="xdesigner-redo" class="btn btn-deault"><i class="glyphicon glyphicon-repeat"></i><br>Redo</div>').appendTo(self.top_bar.find('.xshop-designer-tool-topbar-buttonset'));
		$undo_btn.click(function(event){
			self.undo();
		});
		$redo_btn.click(function(event) {
			self.redo();
		});
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
		
		if(!self.options.show_tool_bar){
			$(self.top_bar).toggle();
		}
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
		this.workplace = $('<div class="xshop-designer-tool-workplace" style="width:100%; height:100%; top:0; left:0; bottom:0; right:0;"></div>').appendTo(this.element);
	},

	setupComponentPanel: function(workplace){
		this.component_panel = $('<div id="xshop-designer-component-panel" class=" col-md-3">Nothing Selecetd</div>').appendTo(workplace);
	},

	setupCanvas: function(){
		var self = this;
		this.canvas = $('<div class="xshop-desiner-tool-canvas atk-move-center" style="position:relative; z-index:0;"><canvas id="xshop-desiner-tool-canvas'+canvas_number+'"></canvas></div>').appendTo(this.workplace);

		// var gl = this.canvasObj.getContext("webgl", {preserveDrawingBuffer: true});

		fabric.Canvas.prototype.customiseControls({
		    // tl: {
		    //     action: undefined,
		    //     cursor: 'pointer',
		    // },
		    // tr: {
		    //     action: 'rotate'
		    // },
		    // bl: {
		    //     action: 'remove',
		    //     cursor: 'pointer'
		    // },
		    // br: {
		    //     action: 'moveUp',
		    //     cursor: 'pointer'
		    // },
		    mb: {
		        action: 'rotate',
		        cursor: 'pointer'
		    },
		    mt: {
		        cursor: 'pointer',
		        action: function(e,target) {
		           $(target.component.editor.element).show();
		        }
		    },
		    // mr: {
		    //     action: function( e, target ) {
		    //         target.set( {
		    //             left: 200
		    //         } );
		    //         canvas.renderAll();
		    //     }
		    //  }
		 });

		fabric.Object.prototype.centeredRotation = true;

		fabric.Image.prototype.setControlsVisibility({
		    mt: true, // middle top disable
		    mb: true, // midle bottom
		    ml: false, // middle left
		    mr: false, // I think you get it
		    mtr: false
		});

		fabric.Image.prototype.customiseCornerIcons({
		    settings: {
		        borderColor: 'black',
		        cornerSize: 20,
		        cornerShape: 'rect',
		        cornerBackgroundColor: 'black',
		        cornerPadding: 10,
		        lockUniScaling : true,
		    },
		    tr: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_resize.png'
		    },
		    mb: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_rotate.svg'
		    },
		    mt: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_settings.svg'
		    }
		});

		fabric.Group.prototype.setControlsVisibility({
		    mt: true, // middle top disable
		    mb: true, // midle bottom
		    ml: true, // middle left
		    mr: true, // I think you get it
		    mtr: false
		});

		fabric.Group.prototype.customiseCornerIcons({
		    settings: {
		        borderColor: 'black',
		        cornerSize: 20,
		        cornerShape: 'rect',
		        cornerBackgroundColor: 'black',
		        cornerPadding: 10,
		        lockUniScaling : true,
		    },
		    tr: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_resize.png'
		    },
		    mb: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_rotate.svg'
		    },
		    mt: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_settings.svg'
		    }
		});

		fabric.Text.prototype.setControlsVisibility({
		    mt: true, // middle top disable
		    mb: true, // midle bottom
		    ml: false, // middle left
		    mr: false, // I think you get it
		    mtr: false,
		    tl:false,
		    tr:false,
		    bl:false,
		    br: false
		});

		fabric.Text.prototype.customiseCornerIcons({
		    settings: {
		        borderColor: 'black',
		        cornerSize: 20,
		        cornerShape: 'rect',
		        cornerBackgroundColor: 'black',
		        cornerPadding: 10,
		        lockUniScaling : true,
		    },
		    tr: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_resize.png'
		    },
		    mb: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_rotate.svg'
		    },
		    mt: {
		        icon: 'vendor/xepan/commerce/templates/js/tool/designer/icons_settings.svg'
		    }
		});

		// set origin to center
		// fabric.Object.prototype.setOriginToCenter = function () {
		//     this._originalOriginX = this.originX;
		//     this._originalOriginY = this.originY;

		//     var center = this.getCenterPoint();
		//     this.set({
		//         originX: 'center',
		//         originY: 'center',
		//         left: center.x,
		//         top: center.y
		//     });
		// };
		// fabric.Object.prototype.setCenterToOrigin = function () {
		//     var originPoint = this.translateToOriginPoint(
		//     this.getCenterPoint(),
		//     this._originalOriginX,
		//     this._originalOriginY);

		//     this.set({
		//         originX: this._originalOriginX,
		//         originY: this._originalOriginY,
		//         left: originPoint.x,
		//         top: originPoint.y
		//     });
		// };

		if((self.options.is_start_call && !self.options.printing_mode) && !self.options.make_static ){
			this.canvasObj = new fabric.Canvas('xshop-desiner-tool-canvas'+canvas_number,{selection: false,stateful: false});
			initAligningGuidelines(this.canvasObj);			
		}else{
			this.canvasObj = new fabric.StaticCanvas('xshop-desiner-tool-canvas'+canvas_number,{stateful: false,renderOnAddRemove: false});
		}


		if(this.options.canvas_render_callback !==undefined){
			this.canvasObj.on('after:render',this.options.canvas_render_callback);
		}


		this.canvas.css('width',(this.options.width) + this.options.unit); // In given Unit
		this.px_width = this.canvas.width(); // Save in pixel for actual should be width
		// this.canvas.css('max-width',this.px_width+'px');
		

		canvas_number++;
		
		// JUST SCALE HERE FOR BETTER QUALITY IMAGE PRODUCTION
		if(self.options.printing_mode){
			this.canvas.css('width',(3.125*this.options.width) + this.options.unit)

		}else{			
			// designer website mode
			this.canvas.css('overflow','hidden');
			if(this.canvas.width() > this.workplace.width()){
				this.canvas.css('width', this.workplace.width() - 20 + 'px');
			}

			if(this.canvas.width() < (this.workplace.width()/2)){
				this.canvas.width((this.workplace.width()/2));
			}

			// Requires Here not above becose, zoom sets width only and height needs to be in render as per new zoom ratio
			this.canvas.css('height',(this.options.height) + this.options.unit); // In Given Unit
			this.canvas.height(this.canvas.height() * this._getZoom()); // get in pixel .height() and multiply by zoom `	
			
			if(this.canvas.height() > this.canvas.width() && this.canvas.height() > 550){
				// require reconsider some ratio by withc width will be reduced more
					this.canvas.width(550/this.canvas.height()*this.canvas.width());
					this.canvas.height(550);
			}

		}

		this.canvasObj.on('selection:cleared',function(){
				$('.ui-selected').removeClass('ui-selected');
				self.option_panel.hide();
				self.current_selected_component = undefined;
				self.current_selected_component_id = undefined;
				if(self.options.designer_mode){
					self.freelancer_panel.FreeLancerComponentOptions.element.hide();
				}
				// $('div.guidex').css('display','none');
				// $('div.guidey').css('display','none');
				// event.stopPropagation();
		});

		this.canvasObj.on('object:selected',function(e){
			self.current_selected_component_id = self.canvasObj.getObjects().indexOf(e.target);
			self.current_selected_component = self.canvasObj.getActiveObject().component;
		});

		this.canvasObj.on('object:scaling',function(e){
			design_dirty = true;
			var el = e.target;
			el.component.options.width = el.width * el.scaleX / self._getZoom();
			el.component.options.height = el.height * el.scaleY / self._getZoom();

			// also save X,Y in case scaling from left side
			var element= self.canvasObj.item(self.current_selected_component_id);
			var component = element.component;
			component.options.x = element.left / self._getZoom();
			component.options.y = element.top / self._getZoom();
			// console.log(el.component.options.width);
			// console.log(el.component.options.height);
			// maintain between boundry
			// not working btw now, so why to calculate if not working ... comment it ;)
			
			// var obj = e.target;
	  //        // if object is too big ignore
	  //       if(obj.currentHeight > obj.canvas.height || obj.currentWidth > obj.canvas.width){
	  //           return;
	  //       }        
	  //       obj.setCoords();        
	  //       // top-left  corner
	  //       if(obj.getBoundingRect().top < 0 || obj.getBoundingRect().left < 0){
	  //           obj.top = Math.max(obj.top, obj.top-obj.getBoundingRect().top);
	  //           obj.left = Math.max(obj.left, obj.left-obj.getBoundingRect().left);
	  //       }
	  //       // bot-right corner
	  //       if(obj.getBoundingRect().top+obj.getBoundingRect().height  > obj.canvas.height || obj.getBoundingRect().left+obj.getBoundingRect().width  > obj.canvas.width){
	  //           obj.top = Math.min(obj.top, obj.canvas.height-obj.getBoundingRect().height+obj.top-obj.getBoundingRect().top);
	  //           obj.left = Math.min(obj.left, obj.canvas.width-obj.getBoundingRect().width+obj.left-obj.getBoundingRect().left);
	  //       }

			// self.option_panel.offset(
	  //       							{
	  //       								top:self.canvasObj._offset.top + element.top - self.option_panel.height(),
	  //       								left:self.canvasObj._offset.left + element.left
	  //       							}
	  //       						);
		});

		this.canvasObj.on('object:moving',function(e){
			design_dirty = true;
			self.current_selected_component.editor.element.hide();
			var element= self.canvasObj.item(self.current_selected_component_id);
			var component = element.component;
			component.options.x = element.left / self._getZoom();
			component.options.y = element.top / self._getZoom();

			// maintain between boundry

	        var obj = e.target;
	         // if object is too big ignore
	        if(obj.currentHeight > obj.canvas.height || obj.currentWidth > obj.canvas.width){
	            return;
	        }        
	        obj.setCoords();        
	        // top-left  corner
	        if(obj.getBoundingRect().top < 0 || obj.getBoundingRect().left < 0){
	            obj.top = Math.max(obj.top, obj.top-obj.getBoundingRect().top);
	            obj.left = Math.max(obj.left, obj.left-obj.getBoundingRect().left);
	        }
	        // bot-right corner
	        if(obj.getBoundingRect().top+obj.getBoundingRect().height  > obj.canvas.height || obj.getBoundingRect().left+obj.getBoundingRect().width  > obj.canvas.width){
	            obj.top = Math.min(obj.top, obj.canvas.height-obj.getBoundingRect().height+obj.top-obj.getBoundingRect().top);
	            obj.left = Math.min(obj.left, obj.canvas.width-obj.getBoundingRect().width+obj.left-obj.getBoundingRect().left);
	        }

			self.option_panel.offset(
	        							{
	        								top:self.canvasObj._offset.top + element.top - self.option_panel.height(),
	        								left:self.canvasObj._offset.left + element.left
	        							}
	        						);

		});

		this.canvasObj.on('object:rotating',function(e){
			design_dirty = true;
			
			var element= self.canvasObj.item(self.current_selected_component_id);
			var component = element.component;

			// do for reset cordinates when it rotate
		    element.setCoords();
		    
			// console.log("originX "+element.originX+ " originY "+element.originY);
			component.options.rotation_angle = parseInt(element.angle);
			component.editor.text_rotate_angle.val(parseInt(element.angle));

			component.options.x = element.oCoords.tl.x / self._getZoom();
			component.options.y = element.oCoords.tl.y / self._getZoom();

		});

		// console.log(this.canvas.width());
		// this.safe_zone = $('<div class="xshop-desiner-tool-safe-zone" style="position:absolute"></div>').appendTo(this.canvas);
		// this.guidex= $('<div class="guidex" style="z-index:100;"></div>').appendTo($('body'));
		// this.guidey= $('<div class="guidey" style="z-index:100;"></div>').appendTo($('body'));
		
		if(!self.options.show_canvas){
			$(self.canvas).hide();
		}


		// undo redo 
		this.canvasObj.on(
		    'object:modified', function () {
		    	self.updateModifications();
			},

		    'object:added', function () {
		    	self.updateModifications();
			}
		);

	},

	updateModifications: function(){
		var self = this;
		
		var opt = $.extend({}, self.createSaveDesignArray());
		// console.log(self.current_page);

		if(self.options.ur_state[self.current_page] == undefined)
			self.options.ur_state[self.current_page] = [];

		if(self.options.ur_state[self.current_page][self.current_layout] == undefined)
			self.options.ur_state[self.current_page][self.current_layout] = [];

        self.options.ur_index = self.options.ur_index + 1;
        self.options.ur_state[self.current_page][self.current_layout][self.options.ur_index] = opt;
				
		self.options.ur_state[self.current_page][self.current_layout] = self.options.ur_state[self.current_page][self.current_layout].slice(0,self.options.ur_index+1);
		
		$('#xdesigner-undo').removeClass('disabled');
		$('#xdesigner-redo').removeClass('disabled');
	},

	undo: function() {
		var self = this;
		if (self.options.ur_index >= 0){
			self.options.ur_index = self.options.ur_index - 1;
			var undo_options = self.options.ur_state[self.current_page][self.current_layout][self.options.ur_index];
			self.loadDesign(undo_options);
			self.render();
			$('#xdesigner-redo').removeClass('disabled');
	    }else{
	    	$('#xdesigner-undo').addClass('disabled');
	    	console.log('no more undo found');
	    }
	},

	redo: function(){
		var self = this;
		var length = self.options.ur_state[self.current_page][self.current_layout].length;

		if (self.options.ur_index < (length-1)){
			self.options.ur_index = self.options.ur_index + 1;
			var redo_options = self.options.ur_state[self.current_page][self.current_layout][self.options.ur_index];
			self.loadDesign(redo_options);
			self.render();
			$('#xdesigner-undo').removeClass('disabled');
	    }else{
	    	$('#xdesigner-redo').addClass('disabled');
	    	console.log('no more redo found');
	    }
	},

	createSaveDesignArray: function(){

			self.layout_array = {};
			image_array = {};
			var generate_image = false;
			var current_working_page = self.current_page;
			var current_working_layout = self.current_layout;
			var current_designer_tool = self;

			

			var temp_page_and_layout = self.pages_and_layouts;
			
			var layouts_count = 0;
			var canvas_drawn = 0;
			var ajax_saved_run = 0;

			$.each(temp_page_and_layout,function(page_name,layouts){

				self.layout_array[page_name] = new Object;
				image_array[page_name] = new Object();
				$.each(layouts,function(layout_name,layout){
					if(layout_name == "sequence_no"){
						// console.log("saved insde"+layout_name);
						return;
					}

					layouts_count++;

					self.layout_array[page_name][layout_name]=new Object;
					self.layout_array[page_name][layout_name]['components']=[];
					image_array[page_name][layout_name] = new Object();

					// var array = [{id:'12', name:'Smith', value:1},{id:'13', name:'Jones', value:2}];
					// var array = layout.components;
					layout.components.sort(function(a, b){

						// console.log("sort");
						// console.log(a.options.zindex);
						// console.log(a.options.url);
						// console.log("b");
						// console.log(b.options.zindex);
						// console.log(b.options.url);
						
					    var a1= a.options.zindex, b1= b.options.zindex;
					    if(a1 == b1) return 0;
					    return a1> b1? 1: -1;
					});

					// console.log(array);
					self.zindex_count = 0;
					$.each(layout.components,function(index,component){
						//Setup Image Path Relative
						if(component.options.type == "Image"){
							url = component.options.url;
							component.options.url = url.substr(url.indexOf("websites/"));
						}
						// console.log(self.zindex_count);
						// console.log(component.options.url);
						options_to_save = component.options;
						options_to_save.zindex = self.zindex_count;
						self.zindex_count += 1;

						self.layout_array[page_name][layout_name]['components'].push(JSON.stringify(options_to_save));
					});

					background_options = layouts[layout_name]['background'].options;
					// background_options = self.designer_tool.pages_and_layouts[page_name][layout_name]['background'].options;
					//Setup Image Path Relative
					if(background_options.url){
						background_options.url = background_options.url.substr(background_options.url.indexOf("websites/"));
						// console.log(background_options.url);
					}				
					self.layout_array[page_name][layout_name]['background'] = JSON.stringify(background_options);
					self.layout_array[page_name]['sequence_no'] = layouts['sequence_no'];


				});
			});

			return self.layout_array;
			// console.log("save inside");
			// console.log(self.layout_array);
		
	},

	setupCart: function(){
		return;
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

	render: function(){

		var self = this;

		select_object_id = self.current_selected_component_id;	

		// Requires Here not above becose, zoom sets width only and height needs to be in render as per new zoom ratio
		this.canvas.css('height',(this.options.height) + this.options.unit); // In Given Unit

		this.canvas.height(this.canvas.height() * this._getZoom()); // get in pixel .height() and multiply by zoom `	
		
		this.canvasObj.setWidth(this.canvas.width());
		this.canvasObj.setHeight(this.canvas.height());
		this.canvasObj.calcOffset();
		
		this.canvas.find('.xshop-designer-component').hide();
		this.canvasObj.clear();
		this.canvasObj.setBackgroundImage('', this.canvasObj.renderAll.bind(this.canvasObj));

		if(self.options.is_start_call){
			self.current_layout = self.layout_finalized[self.current_page];
		}
		
		$.each(self.pages_and_layouts[self.current_page][self.current_layout].components, function(index, component) {
			component.element = undefined;
		});



		// console.log('Components in '+ self.pages_and_layouts[self.current_page][self.current_layout].components.length);
		if(self.pages_and_layouts[self.current_page][self.current_layout].components != undefined && self.pages_and_layouts[self.current_page][self.current_layout].components.length != 0){
			$.each(self.pages_and_layouts[self.current_page][self.current_layout].components, function(index, component) {
				component.render(self);
			});
		}

		if(self.pages_and_layouts[self.current_page][self.current_layout].background != undefined && self.pages_and_layouts[self.current_page][self.current_layout].background.length != 0){
			self.pages_and_layouts[self.current_page][self.current_layout].background.render(self);
		}

		if(select_object_id){
			self.current_selected_component_id = select_object_id;
			self.canvasObj.setActiveObject(self.canvasObj.item(select_object_id));
		}

		if(self.options.show_safe_zone == 1){
		// if(self.options.is_start_call && self.options.show_safe_zone == 1){
			this.safe_zone = new fabric.Rect({
											  left: self._toPixel(this.options.trim),
											  top: self._toPixel(this.options.trim),
											  strokeWidth: 1,
											  stroke: 'rgba(0, 175, 239, 1)',
											  strokeDashArray: [5,5],
											  fill: 'transparent',
											  hasBorders: true,
											  hasControls: false,
											  selectable: false,
											  evented: false,
											  width: self._toPixel((this.options.width - (this.options.trim * 2))),
											  height: self._toPixel((this.options.height - (this.options.trim * 2))),
											  angle: 0
											});

			this.canvasObj.add(this.safe_zone);
		}
		
		self.canvasObj.renderAll();

		// console.log("display all object");
		// console.log(self.canvasObj.getObjects());
		// // self.canvasObj.getObjects().map(function(o) {
		// //   console.log(o);
		// // });
		// console.log("----------------------------------------------------");
		// $.each(self.canvasObj.getObjects(),function(index,object){
		// 	console.log(object);
		// 	// object.bringToFront();
		// });
		// // var zin = self.current_text_component.designer_tool.canvasObj.getObjects().indexOf(current_text);
		// // console.log();
		// console.log("------Object Only----------------------------------------------");

		return;
	},

	_toPixel:function(value){
		return this.canvas.width() /  this.options.width * value;
	},

	_toUnit:function(value){
		return  this.options.width / this.canvas.width() * value;
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
	},

	isSavedDesign: function(){
		return this.options.item_member_design_id?true:false;
	},

	getCanvasObj: function(){
		return this.canvasObj;
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


/**
 * Should objects be aligned by a bounding box?
 * [Bug] Scaled objects sometimes can not be aligned by edges
 *
 */
function initAligningGuidelines(canvas) {

  var ctx = canvas.getSelectionContext(),
      aligningLineOffset = 5,
      aligningLineMargin = 4,
      aligningLineWidth = 1,
      aligningLineColor = 'rgb(0,255,0)',
      viewportTransform,
      zoom = 1;

  function drawVerticalLine(coords) {
    drawLine(
      coords.x + 0.5,
      coords.y1 > coords.y2 ? coords.y2 : coords.y1,
      coords.x + 0.5,
      coords.y2 > coords.y1 ? coords.y2 : coords.y1);
  }

  function drawHorizontalLine(coords) {
    drawLine(
      coords.x1 > coords.x2 ? coords.x2 : coords.x1,
      coords.y + 0.5,
      coords.x2 > coords.x1 ? coords.x2 : coords.x1,
      coords.y + 0.5);
  }

  function drawLine(x1, y1, x2, y2) {
    ctx.save();
    ctx.lineWidth = aligningLineWidth;
    ctx.strokeStyle = aligningLineColor;
    ctx.beginPath();
    ctx.moveTo(((x1+viewportTransform[4])*zoom), ((y1+viewportTransform[5])*zoom));
    ctx.lineTo(((x2+viewportTransform[4])*zoom), ((y2+viewportTransform[5])*zoom));
    ctx.stroke();
    ctx.restore();
  }

  function isInRange(value1, value2) {
    value1 = Math.round(value1);
    value2 = Math.round(value2);
    for (var i = value1 - aligningLineMargin, len = value1 + aligningLineMargin; i <= len; i++) {
      if (i === value2) {
        return true;
      }
    }
    return false;
  }

  var verticalLines = [ ],
      horizontalLines = [ ];

  canvas.on('mouse:down', function () {
    viewportTransform = canvas.viewportTransform;
    zoom = canvas.getZoom();
  });

  canvas.on('object:moving', function(e) {
  	design_dirty = true;
    var activeObject = e.target,
        canvasObjects = canvas.getObjects(),
        activeObjectCenter = activeObject.getCenterPoint(),
        activeObjectLeft = activeObjectCenter.x,
        activeObjectTop = activeObjectCenter.y,
        activeObjectHeight = activeObject.getBoundingRectHeight() / viewportTransform[3],
        activeObjectWidth = activeObject.getBoundingRectWidth() / viewportTransform[0],
        horizontalInTheRange = false,
        verticalInTheRange = false,
        transform = canvas._currentTransform;

    if (!transform) return;

    // It should be trivial to DRY this up by encapsulating (repeating) creation of x1, x2, y1, and y2 into functions,
    // but we're not doing it here for perf. reasons -- as this a function that's invoked on every mouse move

    for (var i = canvasObjects.length; i--; ) {

      if (canvasObjects[i] === activeObject) continue;

      var objectCenter = canvasObjects[i].getCenterPoint(),
          objectLeft = objectCenter.x,
          objectTop = objectCenter.y,
          objectHeight = canvasObjects[i].getBoundingRectHeight() / viewportTransform[3],
          objectWidth = canvasObjects[i].getBoundingRectWidth() / viewportTransform[0];

      // snap by the horizontal center line
      if (isInRange(objectLeft, activeObjectLeft)) {
        verticalInTheRange = true;
        verticalLines.push({
          x: objectLeft,
          y1: (objectTop < activeObjectTop)
            ? (objectTop - objectHeight / 2 - aligningLineOffset)
            : (objectTop + objectHeight / 2 + aligningLineOffset),
          y2: (activeObjectTop > objectTop)
            ? (activeObjectTop + activeObjectHeight / 2 + aligningLineOffset)
            : (activeObjectTop - activeObjectHeight / 2 - aligningLineOffset)
        });
        activeObject.setPositionByOrigin(new fabric.Point(objectLeft, activeObjectTop), 'center', 'center');
      }

      // snap by the left edge
      if (isInRange(objectLeft - objectWidth / 2, activeObjectLeft - activeObjectWidth / 2)) {
        verticalInTheRange = true;
        verticalLines.push({
          x: objectLeft - objectWidth / 2,
          y1: (objectTop < activeObjectTop)
            ? (objectTop - objectHeight / 2 - aligningLineOffset)
            : (objectTop + objectHeight / 2 + aligningLineOffset),
          y2: (activeObjectTop > objectTop)
            ? (activeObjectTop + activeObjectHeight / 2 + aligningLineOffset)
            : (activeObjectTop - activeObjectHeight / 2 - aligningLineOffset)
        });
        activeObject.setPositionByOrigin(new fabric.Point(objectLeft - objectWidth / 2 + activeObjectWidth / 2, activeObjectTop), 'center', 'center');
      }

      // snap by the right edge
      if (isInRange(objectLeft + objectWidth / 2, activeObjectLeft + activeObjectWidth / 2)) {
        verticalInTheRange = true;
        verticalLines.push({
          x: objectLeft + objectWidth / 2,
          y1: (objectTop < activeObjectTop)
            ? (objectTop - objectHeight / 2 - aligningLineOffset)
            : (objectTop + objectHeight / 2 + aligningLineOffset),
          y2: (activeObjectTop > objectTop)
            ? (activeObjectTop + activeObjectHeight / 2 + aligningLineOffset)
            : (activeObjectTop - activeObjectHeight / 2 - aligningLineOffset)
        });
        activeObject.setPositionByOrigin(new fabric.Point(objectLeft + objectWidth / 2 - activeObjectWidth / 2, activeObjectTop), 'center', 'center');
      }

      // snap by the vertical center line
      if (isInRange(objectTop, activeObjectTop)) {
        horizontalInTheRange = true;
        horizontalLines.push({
          y: objectTop,
          x1: (objectLeft < activeObjectLeft)
            ? (objectLeft - objectWidth / 2 - aligningLineOffset)
            : (objectLeft + objectWidth / 2 + aligningLineOffset),
          x2: (activeObjectLeft > objectLeft)
            ? (activeObjectLeft + activeObjectWidth / 2 + aligningLineOffset)
            : (activeObjectLeft - activeObjectWidth / 2 - aligningLineOffset)
        });
        activeObject.setPositionByOrigin(new fabric.Point(activeObjectLeft, objectTop), 'center', 'center');
      }

      // snap by the top edge
      if (isInRange(objectTop - objectHeight / 2, activeObjectTop - activeObjectHeight / 2)) {
        horizontalInTheRange = true;
        horizontalLines.push({
          y: objectTop - objectHeight / 2,
          x1: (objectLeft < activeObjectLeft)
            ? (objectLeft - objectWidth / 2 - aligningLineOffset)
            : (objectLeft + objectWidth / 2 + aligningLineOffset),
          x2: (activeObjectLeft > objectLeft)
            ? (activeObjectLeft + activeObjectWidth / 2 + aligningLineOffset)
            : (activeObjectLeft - activeObjectWidth / 2 - aligningLineOffset)
        });
        activeObject.setPositionByOrigin(new fabric.Point(activeObjectLeft, objectTop - objectHeight / 2 + activeObjectHeight / 2), 'center', 'center');
      }

      // snap by the bottom edge
      if (isInRange(objectTop + objectHeight / 2, activeObjectTop + activeObjectHeight / 2)) {
        horizontalInTheRange = true;
        horizontalLines.push({
          y: objectTop + objectHeight / 2,
          x1: (objectLeft < activeObjectLeft)
            ? (objectLeft - objectWidth / 2 - aligningLineOffset)
            : (objectLeft + objectWidth / 2 + aligningLineOffset),
          x2: (activeObjectLeft > objectLeft)
            ? (activeObjectLeft + activeObjectWidth / 2 + aligningLineOffset)
            : (activeObjectLeft - activeObjectWidth / 2 - aligningLineOffset)
        });
        activeObject.setPositionByOrigin(new fabric.Point(activeObjectLeft, objectTop + objectHeight / 2 - activeObjectHeight / 2), 'center', 'center');
      }
    }

    if (!horizontalInTheRange) {
      horizontalLines.length = 0;
    }

    if (!verticalInTheRange) {
      verticalLines.length = 0;
    }
  });

  canvas.on('before:render', function() {
    canvas.clearContext(canvas.contextTop);
  });

  canvas.on('after:render', function() {
    for (var i = verticalLines.length; i--; ) {
      drawVerticalLine(verticalLines[i]);
    }
    for (var i = horizontalLines.length; i--; ) {
      drawHorizontalLine(horizontalLines[i]);
    }

    verticalLines.length = horizontalLines.length = 0;
  });

  canvas.on('mouse:up', function() {
    verticalLines.length = horizontalLines.length = 0;
    canvas.renderAll();
  });
}


// $(function () {
//     $(window).scroll(sticky_relocate);
//     sticky_relocate();
// });