// xEpan Designer jQuery Widget for extended xShop elements 
var canvas_number = 2;

jQuery.widget("ui.xepan_xshopdesigner",{
		// 'ABeeZee', 'Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Adamina', 'Advent Pro', 'Aguafina Script', 'Akronim', 'Aladin', 'Aldrich', 'Alef', 'Alegreya', 'Alegreya SC', 'Alegreya Sans', 'Alegreya Sans SC', 'Alex Brush', 'Alfa Slab One', 'Alice', 'Alike','Alike Angular', 'Allan', 'Allerta', 'Allerta Stencil', 'Allura', 'Almendra', 'Almendra Display', 'Almendra SC', 'Amarante', 'Amaranth', 'Amatic SC', 'Amethysta', 'Amiri', 'Amita', 'Anaheim', 'Andada', 'Andika', 'Angkor', 'Annie Use Your Telescope', 'Anonymous Pro', 'Antic', 'Antic Didone', 'Antic Slab', 'Anton', 'Arapey', 'Arbutus', 'Arbutus Slab','Architects Daughter','Archivo Black','Archivo Narrow','Arimo','Arizonia','Armata','Artifika','Arvo','Arya','Asap','Asar','Asset','Astloch','Asul','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script','Balthazar','Bangers','Basic','Battambang','Baumans','Bayon','Belgrano','Belleza','BenchNine','Bentham','Berkshire Swash','Bevan','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','Biryani', 'Bitter','Black Ops One','Bokor','Bonbon','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Bubblegum Sans','Bubbler One','Buda','Buenard','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Catamaran','Caudex','Caveat','Caveat Brush','Cedarville Cursive','Ceviche One','Changa One','Chango','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','hivo','Chonburi','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption','Codystar','Combo','Comfortaa','Coming Soon','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Courgette','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Text','Croissant One', 'Crushed', 'Cuprum','Cutive','Cutive Mono','Damion','Dancing Script','Dangrek','Dawning of a New Day','Days One','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Domine','Donegal One','Doppio One','Dorsa','Dosis','Dr Sugiyama','Droid Sans','Droid Sans Mono','Droid Serif','Duru Sans','Dynalight','EB Garamond','Eagle Lake','Eater','Economica','Eczar','Ek Mukta','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Engagement','Englebert','Enriqueta','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Fanwood Text','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Federant','Federo','Felipa','Fenix','Finger Paint','Fira Mono','Fira Sans','Fjalla One','Fjord One','Flamenco','Flavors','Fondamento','Fontdiner Swanky','Forum','Francois One','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','GFS Didot','GFS Neohellenic','Gabriela','Gafata','Galdeano','Galindo','Gentium Basic','Gentium Book Basic','Geo','Geostar','Geostar Fill','Germania One','Gidugu','Gilda Display','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Goblin One','Gochi Hand','Gorditas','Goudy Bookletter 1911','Graduate','Grand Hotel','Gravitas One','Great Vibes','Griffy','Gruppo','Gudea','Gurajada','Habibi','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Headland One','Henny Penny','Herr Von Muellerhoff','Hind','Hind Siliguri','Hind Vadodara','Holtwood One SC','Homemade Apple','Homenaje','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Iceberg','Iceland','Imprima','Inconsolata','Inder','Indie Flower','Inika','Inknut Antiqua','Irish Grover','Istok Web','Italiana','Italianno','Itim','Jacques Francois','Jacques Francois Shadow','Jaldi','Jim Nightshade','Jockey One','Jolly Lodger','Josefin Sans','Josefin Slab','Joti One','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','Kadwa','Kalam','Kameron','Kantumruy','Karla','Karma','Kaushan Script','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kite One','Knewave','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Kurale','La Belle Aurore','Laila','Lakki Reddy','Lancelot','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Libre Baskerville','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','Macondo','Macondo Swash Caps','Magra','Maiden Orange','Mako','Mallanna','Mandali','Marcellus','Marcellus SC','Marck Script','Margarine','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Miniver','Miss Fajardose','Modak','Modern Antiqua','Molengo','Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Muli','Mystery Quest', 'NTR', 'Neucha', 'Neuton', 'New Rocker', 'News Cycle', 'Niconne', 'Nixie One','Nobile','Nokora','Norican','Nosifer','Nothing You Could Do','Noticia Text','Noto Sans','Noto Serif','Nova Cut', 'Nova Flat', 'Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim', 'Nova Square', 'Numans', 'Nunito', 'Odor Mean Chey', 'Offside', 'Old Standard TT', 'Oldenburg', 'Oleo Script', 'Oleo Script Swash Caps', 'Open Sans', 'Open Sans Condensed', 'Oranienbaum', 'Orbitron', 'Oregano', 'Orienta', 'Original Surfer', 'Oswald', 'Over the Rainbow', 'Overlock', 'Overlock SC', 'Ovo', 'Oxygen', 'Oxygen Mono', 'PT Mono', 'PT Sans', 'PT Sans Caption', 'PT Sans Narrow', 'PT Serif', 'PT Serif Caption', 'Pacifico', 'Palanquin', 'Palanquin Dark', 'Paprika', 'Parisienne', 'Passero One', 'Passion One', 'Pathway Gothic One', 'Patrick Hand', 'Patrick Hand SC', 'Patua One', 'Paytone One', 'Peddana', 'Peralta', 'Permanent Marker', 'Petit Formal Script', 'Petrona', 'Philosopher','Piedra','Pinyon Script', 'Pirata One', 'Plaster', 'Play', 'Playball', 'Playfair Display', 'Playfair Display SC', 'Podkova','Poiret One', 'Poller One','Poly','Pompiere','Pontano Sans', 'Poppins','Port Lligat Sans', 'Port Lligat Slab', 'Pragati Narrow', 'Prata', 'Preahvihear','Press Start 2P','Princess Sofia', 'Prociono', 'Prosto One', 'Puritan', 'Purple Purse', 'Quando', 'Quantico', 'Quattrocento', 'Quattrocento Sans', 'Questrial', 'Quicksand','Quintessential','Qwigley', 'Racing Sans One', 'Radley','Rajdhani', 'Raleway', 'Raleway Dots', 'Ramabhadra','Ramaraja', 'Rambla', 'Rammetto One', 'Ranchers', 'Rancho', 'Ranga', 'Rationale','Ravi Prakash', 'Redressed', 'Reenie Beanie', 'Revalia', 'Rhodium Libre', 'Ribeye','Ribeye Marrow', 'Righteous', 'Risque', 'Roboto', 'Roboto Condensed', 'Roboto Mono','Roboto Slab', 'Rochester', 'Rock Salt', 'Rokkitt', 'Romanesco', 'Ropa Sans', 'Rosario','Rosarivo', 'Rouge Script', 'Rozha One', 'Rubik','Rubik Mono One', 'Rubik One', 'Ruda', 'Rufina', 'Ruge Boogie', 'Ruluko', 'Rum Raisin', 'Ruslan Display', 'Russo One', 'Ruthie', 'Rye','Sacramento','Sahitya','Sail', 'Salsa', 'Sanchez', 'Sancreek', 'Sansita One', 'Sarala', 'Sarina', 'Sarpanch', 'Satisfy', 'Scada', 'Scheherazade', 'Schoolbell', 'Seaweed Script', 'Sevillana', 'Seymour One', 'Shadows Into Light', 'Shadows Into Light Two', 'Shanti', 'Share', 'Share Tech', 'Share Tech Mono', 'Shojumaru', 'Short Stack', 'Siemreap', 'Sigmar One', 'Signika', 'Signika Negative', 'Simonetta', 'Sintony', 'Sirin Stencil', 'Six Caps', 'Skranji', 'Slabo 13px', 'Slabo 27px', 'Slackey', 'Smokum','Smythe','Sniglet', 'Snippet', 'Snowburst One', 'Sofadi One', 'Sofia', 'Sonsie One', 'Sorts Mill Goudy', 'Source Code Pro', 'Source Sans Pro', 'Source Serif Pro', 'Special Elite', 'Spicy Rice', 'Spinnaker', 'Spirax', 'Squada One', 'Sree Krushnadevaraya', 'Stalemate', 'Stalinist One','Stardos Stencil', 'Stint Ultra Condensed','Stint Ultra Expanded','Stoke', 'Strait','Sue Ellen Francisco', 'Sumana', 'Sunshiney', 'Supermercado One', 'Sura', 'Suranna', 'Suravaram', 'Suwannaphum', 'Swanky and Moo Moo', 'Syncopate', 'Tangerine', 'Taprom', 'Tauri', 'Teko', 'Telex', 'Tenali Ramakrishna', 'Tenor Sans', 'Text Me One', 'The Girl Next Door', 'Tienne','Tillana', 'Timmana', 'Tinos', 'Titan One', 'Titillium Web', 'Trade Winds', 'Trocchi', 'Trochut', 'Trykker', 'Tulpen One', 'Ubuntu', 'Ubuntu Condensed', 'Ubuntu Mono', 'Ultra','Uncial Antiqua','Underdog', 'Unica One', 'UnifrakturCook','UnifrakturMaguntia', 'Unkempt', 'Unlock', 'Unna', 'VT323', 'Vampiro One', 'Varela', 'Varela Round', 'Vast Shadow', 'Vesper Libre','Vibur', 'Vidaloka','Viga', 'Voces', 'Volkhov', 'Vollkorn', 'Voltaire','Waiting for the Sunrise', 'Wallpoet', 'Walter Turncoat', 'Warnes', 'Wellfleet', 'Wendy One', 'Wire One', 'Work Sans', 'Yanone Kaffeesatz', 'Yantramanav', 'Yellowtail', 'Yeseva One', 'Yesteryear', 'Zeyada'

	editors:[],
	options:{
		// Layout Options
		showTopBar: true,
		// ComponentsIncluded: ['Background','Text','Image','Help'], // Plugins
		IncludeJS: ['FreeLancerPanel','jquery.cookie'], // Plugins
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
		watermark_text:"xepan",
		is_start_call: false,
		start_page:false,
		start_layout:false,

		show_tool_bar:true,
		show_pagelayout_bar:true,
		show_canvas:true,
		printing_mode:false
	},
	_create: function(){
		// console.log('is_start ' +this.options.is_start_call);

		this.current_page='Front Page';
		this.current_layout='Main Layout';

		if(this.options.start_page) this.current_page=this.options.start_page
		if(this.options.start_layout) this.current_layout=this.options.start_layout

		this.pages_and_layouts= {
			"Front Page": {
				"Main Layout": {
					components: [],
					background: undefined
				}
			}
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
		this.pointtopixel = {'6':8,'7':9,'8':11,'9':12,'10':13,'11':15,'12':16,'13':17,'14':19,'15':21,'16':22,'17':23,'18':24,'19':25,'20':26,'21':28,'22':29,'23':31,'24':32,'25':33,'26':35,'27':36,'28':37,'29':38,'30':40,'31':41,'32':42,'33':44,'34':45,'35':47,'36':48};
		this.pixeltopoint = {'6':8,'7':9,'8':11,'9':12,'10':13,'11':15,'12':16,'13':17,'14':19,'15':21,'16':22,'17':23,'18':24,'19':25,'20':26,'21':28,'22':29,'23':31,'24':32,'25':33,'26':35,'27':36,'28':37,'29':38,'30':40,'31':41,'32':42,'33':44,'34':45,'35':47,'36':48};

		this.font_family = ['Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Cabin','Cambay','Cambo','Candal','Petit Formal Script', 'Petrona', 'Philosopher','Piedra', 'Ubuntu'];
		
		this.setupLayout();
	},
		
	setupLayout: function(){
		var self = this;
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
					if(self.options.show_pagelayout_bar)
						self.setupPageLayoutBar();

					self.setupFreelancerPanel();
				}
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
		if(!self.options.is_start_call) return;

		var bottom_bar = $('<div class="xshop-designer-tool-bottombar"></div>');
		bottom_bar.appendTo(this.element);
		self.bottombar_wrapper = bottom_bar;
		$.each(self.pages_and_layouts,function(page_name,layouts){
			pl = $('<div class="xshop-designer-pagethumbnail" data-pagename="'+page_name+'" data-layoutname="'+self.options.selected_layouts_for_print[page_name]+'" >')
				.appendTo(bottom_bar)
				.width(200);

			if(!self.options.printing_mode){
				$(pl).on('click',function(event){
					self.options.start_page = self.current_page = page_name;
					self.options.start_layout =  self.current_layout = self.layout_finalized[page_name];
					self.render();
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
									'item_name':self.options.item_name
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
									// 'calendar_starting_month'=>$saved_design['calendar_starting_month'],
									// 'calendar_starting_year'=>$saved_design['calendar_starting_year'],
									// 'calendar_event'=>$saved_design['calendar_event'],
							});
		});


		if(!self.options.show_pagelayout_bar)
			$(bottombar_wrapper).toggle();
		// var temp = new PageLayout_Component();
		// temp.init(self, self.canvas, bottom_bar);
		// bottom_tool_btn = temp.renderTool();
		// self.bottom_bar = temp;

		// draw first time layout 
		if(!self.options.printing_mode){
			layout_bar = $('<div class="xshop-designer-layout" style="clear:both;"></div>').insertAfter(bottom_bar);
			$.each(self.pages_and_layouts[self.current_page],function(layout_name,design){
				layout_canvas = $('<div class="xshop-designer-layoutthumbnail" data-pagename="'+self.current_page+'" data-layoutname="'+layout_name+'">')
					.appendTo(layout_bar)
					.css('float','left')
					.width(200);

				$(layout_canvas).on('click',function(event){
					var selected_page_name = $(this).attr('data-pagename');
					var selected_layout_name = $(this).attr('data-layoutname');

					self.options.selected_layouts_for_print[selected_page_name] = selected_layout_name; 
					self.layout_finalized[selected_page_name] = selected_layout_name; 

					self.options.start_page = self.current_page = selected_page_name;
					self.options.start_layout =  self.current_layout = selected_layout_name;
					self.render();

					$(this).siblings().removeClass('ui-selected');
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
									'item_name':self.options.item_name
							});

				if(self.current_layout == layout_name)
					$(layout_canvas).addClass('ui-selected');
				else
					$(layout_canvas).removeClass('ui-selected');

			});

			// $(layout).on('click',function(event){
			// 	self.options.start_page = self.current_page = page_name;
			// 	self.options.start_layout =  self.current_layout = self.options.selected_layouts_for_print[page_name];
			// 	self.render();
			// 	// $('.xshop-designer-tool').xepan_xshopdesigner('render');
			// }).css('float','left');
		}

		if(self.options.printing_mode)
			self.setupPdfExport();
	},

	setupPdfExport:function(){
		self = this;

		$('.xshop-designer-tool-bottombar .xshop-designer-pagethumbnail').each(function(){
			generate_pdf_btn = $(this).prepend('<div class="btn btn-primary">Generate PDF</div>');

			$(generate_pdf_btn).click(function(event){
				$(this).find('.lower-canvas').css('border','2px solid red');
				orientation = 'P';
				if(self.options.width > self.options.height)
					orientation = 'L';

				$(this).find('canvas').each(function(index,canvas){
					var pdfObj  = new jsPDF(orientation,self.options.unit,[self.options.width,self.options.height],true);
					img_data = canvas.toDataURL();
					pdfObj.addImage(img_data,'PNG',0,0,self.options.width,self.options.height);
					pdfObj.save(self.options.item_name+"_"+$(this).closest('.xshop-designer-pagethumbnail').attr('data-pagename') +'_'+$(this).closest('.xshop-designer-pagethumbnail').attr('data-layoutname')+".pdf");
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
		this.workplace = $('<div class="xshop-designer-tool-workplace" style="width:100%"></div>').appendTo(this.element);
	},

	setupComponentPanel: function(workplace){
		this.component_panel = $('<div id="xshop-designer-component-panel" class=" col-md-3">Nothing Selecetd</div>').appendTo(workplace);
	},

	setupCanvas: function(){
		var self = this;
		this.canvas = $('<div class="xshop-desiner-tool-canvas atk-move-center" style="position:relative; z-index:0;"><canvas id="xshop-desiner-tool-canvas'+canvas_number+'"></canvas></div>').appendTo(this.workplace);

		// var gl = this.canvasObj.getContext("webgl", {preserveDrawingBuffer: true});



		if(self.options.is_start_call && !self.options.printing_mode)
			this.canvasObj = new fabric.Canvas('xshop-desiner-tool-canvas'+canvas_number,{selection: false});
		else
			this.canvasObj = new fabric.StaticCanvas('xshop-desiner-tool-canvas'+canvas_number);

		if(self.options.is_start_call && !self.options.printing_mode){
			initAligningGuidelines(this.canvasObj);
		}

		this.canvas.css('width',(this.options.width) + this.options.unit); // In given Unit
		this.px_width = this.canvas.width(); // Save in pixel for actual should be width
		// this.canvas.css('max-width',this.px_width+'px');
		

		// console.log('can px_width ' + this.px_width);
		// console.log('can div ' + this.canvas.width());
		// console.log('canOBJ ' + this.canvasObj.width);
		
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
			var el = e.target;

			el.component.options.width = el.width * el.scaleX / self._getZoom();
			el.component.options.height = el.height * el.scaleY / self._getZoom();
		});

		this.canvasObj.on('object:moving',function(e){
			var element= self.canvasObj.item(self.current_selected_component_id);
			var component = element.component;
			component.options.x = element.left / self._getZoom();
			component.options.y = element.top / self._getZoom();


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
	        								left:self.canvasObj._offset.left + element.left + element.width
	        							}
	        						);

		});

		this.canvasObj.on('object:rotating',function(e){
			var element= self.canvasObj.item(self.current_selected_component_id);
			var component = element.component;
			component.options.rotation_angle = parseInt(element.angle);
			component.editor.text_rotate_angle.val(parseInt(element.angle));
		});

		// console.log(this.canvas.width());
		// this.safe_zone = $('<div class="xshop-desiner-tool-safe-zone" style="position:absolute"></div>').appendTo(this.canvas);
		// this.guidex= $('<div class="guidex" style="z-index:100;"></div>').appendTo($('body'));
		// this.guidey= $('<div class="guidey" style="z-index:100;"></div>').appendTo($('body'));
		
		if(!self.options.show_canvas){
			$(self.canvas).toggle();
		}
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

		if(self.options.is_start_call){
			this.safe_zone = new fabric.Rect({
											  left: self._toPixel(this.options.trim),
											  top: self._toPixel(this.options.trim),
											  strokeWidth: 1,
											  stroke: 'rgba(255,0,0,0.6)',
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

		self.canvasObj.renderAll();
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