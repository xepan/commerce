<?php

namespace xepan\commerce;
class Tool_Item_Designer extends \View{
	public $options = [];
	public $item=null;
	public $target=null;
	public $render_designer=true;
	public $designer_mode=false;
	public $load_designer_tool = true;
	public $specification=array('width'=>false,'height'=>false,'trim'=>false,'unit'=>false);
	public $item_member_design_id;
	public $item_id;

	public $printing_mode = false;
	public $show_canvas = true;
	public $is_start_call = 1;
	public $show_tool_bar = true;
	public $show_pagelayout_bar = true;
	public $show_layout_bar = true;
	public $show_paginator = true;


	function init(){
		parent::init();

		//Load Associate Designer Item
		if(!$this->item_member_design_id){
			$this->item_member_design_id = $this->api->stickyGET('item_member_design');
		}

		$item_member_design_id = $this->item_member_design_id;

		if(!$this->item_id){
			$this->item_id = $this->api->stickyGET('xsnb_design_item_id');			
		}

		$item_id = $this->item_id;
		
		$want_to_edit_template_item = $this->api->stickyGET('xsnb_design_template');
				
		$this->addClass('xshop-designer-tool xshop-item');

		if(isset($this->api->xepan_xshopdesigner_included)){
			// throw $this->exception('Designer Tool Cannot be included twise on same page','StopRender');
		}else{
			$this->api->xepan_xshopdesigner_included = true;
		}


		$designer = $this->add('xepan\base\Model_Contact');
		$designer_loaded = $designer->loadLoggedIn(); // return true of false
		
		// 3. Design own in-complete design again
		if($item_member_design_id and $designer_loaded){
			
			$target = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($item_member_design_id);
			if(!$target->loaded()) return;
				
			if($target['contact_id'] != $designer->id){
				$target->unload();
				unset($target);	
			}else{
				$this->item = $item = $target->ref('item_id');
			}
		}
			

		// 1. Designer wants edit template
		if($item_id and $want_to_edit_template_item=='true'  and $designer_loaded){
			$target = $this->item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			
			if(!$target->loaded()){
				return;	
			} 
			$item = $target;

			if($target['designer_id'] != $designer->id){
				return;
			}
			$this->designer_mode = true;
		}
		
		// 2. New personalized item
		if($item_id and is_numeric($item_id) and $want_to_edit_template_item !='true' and !isset($target)){

			$this->item = $item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			if(!$item->loaded()) {
				return;
			}

			$target = $this->add('xepan\commerce\Model_Item_Template_Design')->addCondition('item_id',$item->id);
			// $target = $item->ref('xepan\commerce\Item_Template_Design');
			$target['designs'] = $item['designs'];
		}


		
		if(!isset($target)){
			$this->render_designer = false;
			$this->add('View_Warning')->set('Insufficient Values, Item unknown or Not Authorised');
			$this->load_designer_tool = false;			
			return;
		}
		
		$this->target = $target;
		
		// check for required specifications like width / height
		if(!($this->specification['width'] = $item->specification('width')) OR !($this->specification['height'] = $item->specification('height')) OR !($this->specification['trim'] = $item->specification('trim'))){
			$this->add('View_Error')->set('Item Does not have \'width\' and/or \'height\' and/or \'trim\' specification(s) set');
			$this->load_designer_tool = false;
			return;
		}else{
			// width and hirght might be like '51mm' and '91 mm' so get digit and unit sperated
			// print_r($this->specification);
			preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $this->specification['width'],$temp);
			$this->specification['width']= $temp[1][0];
			preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $this->specification['height'],$temp);
			$this->specification['height']= $temp[1][0];
			$this->specification['unit']=$temp[2][0];

			preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $this->specification['trim'],$temp);
			$this->specification['trim']= $temp[1][0];
		}
	}

	function render(){
		if($this->load_designer_tool){
		
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/designer.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/flat_top_orange.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/jquery.colorpicker.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/designer/cropper.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/addtocart.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/slick.css" />');
		$this->api->template->appendHTML('js_include','<link rel="stylesheet" type="text/css" href="'.$this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/css/tool/slick-theme.css" />');
		$this->js(true)->_css('fontello');
		$this->js(true)->_css('jquery-ui');
		$this->js(true)->_css('tool/designer/jquery.colorpicker');

		$this->app->jquery->addStaticInclude('tool/designer/fabric.min');
		// $this->app->jquery->addStaticInclude('tool/designer/customiseControls.min');

		$this->js(true)
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/webfont.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fabric.min.js')
				// ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/aligning_guidelines.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/designer.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jquery.colorpicker.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/cropper.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/pace.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/addtocart.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/slick.js')
				;
		
		// RE DEFINED ALSO AT page_designer_exportpdf
		$this->js(true)
				->_library('WebFont')->load(['google'=>['families'=>[ 'Abel:bold,bolditalic,italic,regular', 'Abril Fatface:bold,bolditalic,italic,regular', 'Aclonica:bold,bolditalic,italic,regular', 'Acme:bold,bolditalic,italic,regular', 'Actor:bold,bolditalic,italic,regular', 'Cabin:bold,bolditalic,italic,regular','Cambay:bold,bolditalic,italic,regular','Cambo:bold,bolditalic,italic,regular','Candal:bold,bolditalic,italic,regular','Petit Formal Script:bold,bolditalic,italic,regular', 'Petrona:bold,bolditalic,italic,regular', 'Philosopher:bold,bolditalic,italic,regular','Piedra:bold,bolditalic,italic,regular', 'Ubuntu:bold,bolditalic,italic,regular']]]);
				// ->_library('WebFont')->load(['google'=>['families'=>[ 'ABeeZee', 'Abel', 'Abril Fatface', 'Aclonica', 'Acme', 'Actor', 'Adamina', 'Advent Pro', 'Aguafina Script', 'Akronim', 'Aladin', 'Aldrich', 'Alef', 'Alegreya', 'Alegreya SC', 'Alegreya Sans', 'Alegreya Sans SC', 'Alex Brush', 'Alfa Slab One', 'Alice', 'Alike','Alike Angular', 'Allan', 'Allerta', 'Allerta Stencil', 'Allura', 'Almendra', 'Almendra Display', 'Almendra SC', 'Amarante', 'Amaranth', 'Amatic SC', 'Amethysta', 'Amiri', 'Amita', 'Anaheim', 'Andada', 'Andika', 'Angkor', 'Annie Use Your Telescope', 'Anonymous Pro', 'Antic', 'Antic Didone', 'Antic Slab', 'Anton', 'Arapey', 'Arbutus', 'Arbutus Slab','Architects Daughter','Archivo Black','Archivo Narrow','Arimo','Arizonia','Armata','Artifika','Arvo','Arya','Asap','Asar','Asset','Astloch','Asul','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script','Balthazar','Bangers','Basic','Battambang','Baumans','Bayon','Belgrano','Belleza','BenchNine','Bentham','Berkshire Swash','Bevan','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','Biryani', 'Bitter','Black Ops One','Bokor','Bonbon','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Bubblegum Sans','Bubbler One','Buda','Buenard','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Catamaran','Caudex','Caveat','Caveat Brush','Cedarville Cursive','Ceviche One','Changa One','Chango','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','hivo','Chonburi','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption','Codystar','Combo','Comfortaa','Coming Soon','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Courgette','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Text','Croissant One', 'Crushed', 'Cuprum','Cutive','Cutive Mono','Damion','Dancing Script','Dangrek','Dawning of a New Day','Days One','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Domine','Donegal One','Doppio One','Dorsa','Dosis','Dr Sugiyama','Droid Sans','Droid Sans Mono','Droid Serif','Duru Sans','Dynalight','EB Garamond','Eagle Lake','Eater','Economica','Eczar','Ek Mukta','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Engagement','Englebert','Enriqueta','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Fanwood Text','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Federant','Federo','Felipa','Fenix','Finger Paint','Fira Mono','Fira Sans','Fjalla One','Fjord One','Flamenco','Flavors','Fondamento','Fontdiner Swanky','Forum','Francois One','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','GFS Didot','GFS Neohellenic','Gabriela','Gafata','Galdeano','Galindo','Gentium Basic','Gentium Book Basic','Geo','Geostar','Geostar Fill','Germania One','Gidugu','Gilda Display','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Goblin One','Gochi Hand','Gorditas','Goudy Bookletter 1911','Graduate','Grand Hotel','Gravitas One','Great Vibes','Griffy','Gruppo','Gudea','Gurajada','Habibi','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Headland One','Henny Penny','Herr Von Muellerhoff','Hind','Hind Siliguri','Hind Vadodara','Holtwood One SC','Homemade Apple','Homenaje','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Iceberg','Iceland','Imprima','Inconsolata','Inder','Indie Flower','Inika','Inknut Antiqua','Irish Grover','Istok Web','Italiana','Italianno','Itim','Jacques Francois','Jacques Francois Shadow','Jaldi','Jim Nightshade','Jockey One','Jolly Lodger','Josefin Sans','Josefin Slab','Joti One','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','Kadwa','Kalam','Kameron','Kantumruy','Karla','Karma','Kaushan Script','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kite One','Knewave','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Kurale','La Belle Aurore','Laila','Lakki Reddy','Lancelot','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Libre Baskerville','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','Macondo','Macondo Swash Caps','Magra','Maiden Orange','Mako','Mallanna','Mandali','Marcellus','Marcellus SC','Marck Script','Margarine','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Miniver','Miss Fajardose','Modak','Modern Antiqua','Molengo','Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Muli','Mystery Quest', 'NTR', 'Neucha', 'Neuton', 'New Rocker', 'News Cycle', 'Niconne', 'Nixie One','Nobile','Nokora','Norican','Nosifer','Nothing You Could Do','Noticia Text','Noto Sans','Noto Serif','Nova Cut', 'Nova Flat', 'Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim', 'Nova Square', 'Numans', 'Nunito', 'Odor Mean Chey', 'Offside', 'Old Standard TT', 'Oldenburg', 'Oleo Script', 'Oleo Script Swash Caps', 'Open Sans', 'Open Sans Condensed', 'Oranienbaum', 'Orbitron', 'Oregano', 'Orienta', 'Original Surfer', 'Oswald', 'Over the Rainbow', 'Overlock', 'Overlock SC', 'Ovo', 'Oxygen', 'Oxygen Mono', 'PT Mono', 'PT Sans', 'PT Sans Caption', 'PT Sans Narrow', 'PT Serif', 'PT Serif Caption', 'Pacifico', 'Palanquin', 'Palanquin Dark', 'Paprika', 'Parisienne', 'Passero One', 'Passion One', 'Pathway Gothic One', 'Patrick Hand', 'Patrick Hand SC', 'Patua One', 'Paytone One', 'Peddana', 'Peralta', 'Permanent Marker', 'Petit Formal Script', 'Petrona', 'Philosopher','Piedra','Pinyon Script', 'Pirata One', 'Plaster', 'Play', 'Playball', 'Playfair Display', 'Playfair Display SC', 'Podkova','Poiret One', 'Poller One','Poly','Pompiere','Pontano Sans', 'Poppins','Port Lligat Sans', 'Port Lligat Slab', 'Pragati Narrow', 'Prata', 'Preahvihear','Press Start 2P','Princess Sofia', 'Prociono', 'Prosto One', 'Puritan', 'Purple Purse', 'Quando', 'Quantico', 'Quattrocento', 'Quattrocento Sans', 'Questrial', 'Quicksand','Quintessential','Qwigley', 'Racing Sans One', 'Radley','Rajdhani', 'Raleway', 'Raleway Dots', 'Ramabhadra','Ramaraja', 'Rambla', 'Rammetto One', 'Ranchers', 'Rancho', 'Ranga', 'Rationale','Ravi Prakash', 'Redressed', 'Reenie Beanie', 'Revalia', 'Rhodium Libre', 'Ribeye','Ribeye Marrow', 'Righteous', 'Risque', 'Roboto', 'Roboto Condensed', 'Roboto Mono','Roboto Slab', 'Rochester', 'Rock Salt', 'Rokkitt', 'Romanesco', 'Ropa Sans', 'Rosario','Rosarivo', 'Rouge Script', 'Rozha One', 'Rubik','Rubik Mono One', 'Rubik One', 'Ruda', 'Rufina', 'Ruge Boogie', 'Ruluko', 'Rum Raisin', 'Ruslan Display', 'Russo One', 'Ruthie', 'Rye','Sacramento','Sahitya','Sail', 'Salsa', 'Sanchez', 'Sancreek', 'Sansita One', 'Sarala', 'Sarina', 'Sarpanch', 'Satisfy', 'Scada', 'Scheherazade', 'Schoolbell', 'Seaweed Script', 'Sevillana', 'Seymour One', 'Shadows Into Light', 'Shadows Into Light Two', 'Shanti', 'Share', 'Share Tech', 'Share Tech Mono', 'Shojumaru', 'Short Stack', 'Siemreap', 'Sigmar One', 'Signika', 'Signika Negative', 'Simonetta', 'Sintony', 'Sirin Stencil', 'Six Caps', 'Skranji', 'Slabo 13px', 'Slabo 27px', 'Slackey', 'Smokum','Smythe','Sniglet', 'Snippet', 'Snowburst One', 'Sofadi One', 'Sofia', 'Sonsie One', 'Sorts Mill Goudy', 'Source Code Pro', 'Source Sans Pro', 'Source Serif Pro', 'Special Elite', 'Spicy Rice', 'Spinnaker', 'Spirax', 'Squada One', 'Sree Krushnadevaraya', 'Stalemate', 'Stalinist One','Stardos Stencil', 'Stint Ultra Condensed','Stint Ultra Expanded','Stoke', 'Strait','Sue Ellen Francisco', 'Sumana', 'Sunshiney', 'Supermercado One', 'Sura', 'Suranna', 'Suravaram', 'Suwannaphum', 'Swanky and Moo Moo', 'Syncopate', 'Tangerine', 'Taprom', 'Tauri', 'Teko', 'Telex', 'Tenali Ramakrishna', 'Tenor Sans', 'Text Me One', 'The Girl Next Door', 'Tienne','Tillana', 'Timmana', 'Tinos', 'Titan One', 'Titillium Web', 'Trade Winds', 'Trocchi', 'Trochut', 'Trykker', 'Tulpen One', 'Ubuntu', 'Ubuntu Condensed', 'Ubuntu Mono', 'Ultra','Uncial Antiqua','Underdog', 'Unica One', 'UnifrakturCook','UnifrakturMaguntia', 'Unkempt', 'Unlock', 'Unna', 'VT323', 'Vampiro One', 'Varela', 'Varela Round', 'Vast Shadow', 'Vesper Libre','Vibur', 'Vidaloka','Viga', 'Voces', 'Volkhov', 'Vollkorn', 'Voltaire','Waiting for the Sunrise', 'Wallpoet', 'Walter Turncoat', 'Warnes', 'Wellfleet', 'Wendy One', 'Wire One', 'Work Sans', 'Yanone Kaffeesatz', 'Yantramanav', 'Yellowtail', 'Yeseva One', 'Yesteryear', 'Zeyada']]]);

		// $this->js(true)->_load('item/addtocart');
		$saved_design = $design = json_decode($this->target['designs'],true);
		$selected_layouts_for_print = $design['selected_layouts_for_print']; // trimming other array values like px_width etc
		$design = $design['design']; // trimming other array values like px_width etc
		$design = json_encode($design);
		$cart_options = "{}";
		// $selected_layouts_for_print ="front_layout";
		$currency ="INR";
		
		// $cart_options = $this->item->getBasicCartOptions();
		// $cart_options['item_member_design'] = $_GET['item_member_design']?:'0';
		// $cart_options['show_qty'] = '1'; // ?????????????  from options
		// $cart_options['show_price'] = '1'; //$this->show_price;
		// $cart_options['show_custom_fields'] = '1'; //$this->show_custom_fields;
		// $cart_options['is_designable'] = $this->item['is_designable']; //$this->show_custom_fields;
				
		// echo "<pre>";
		// print_r ($saved_design);
		// echo "</pre>";
		// exit;
		// var_dump($this->specification);
		// exit;
			$this->js(true)->xepan_xshopdesigner(array('width'=>$this->specification['width'],
														'height'=>$this->specification['height'],
														'trim'=>$this->specification['trim'],
														'unit'=>'mm',
														'designer_mode'=> $this->designer_mode,
														'design'=>$design,
														'show_cart'=>'1',
														// 'cart_options' => $cart_options,
														'selected_layouts_for_print' => $selected_layouts_for_print,
														'item_id'=>$this->item_id,
														'item_member_design_id' => $this->item_member_design_id,
														'item_name' => $this->item['name'] ." ( ".$this->item['sku']." ) ",
														'item_sale_price'=>$this->item['sale_price'],
														'item_original_price'=>$this->item['original_price'],
														'currency_symbole'=>$currency,
														'base_url'=>$this->api->url()->absolute()->getBaseURL(),
														'watermark_text'=>$this->options['watermark_text'],
														'calendar_starting_month'=>$saved_design['calendar_starting_month'],
														'calendar_starting_year'=>$saved_design['calendar_starting_year'],
														'calendar_event'=>$saved_design['calendar_event'],

														'is_start_call'=>'1',
														'show_tool_bar'=>$this->show_tool_bar,
														'show_pagelayout_bar'=>$saved_design['show_pagelayout_bar']?$saved_design['show_pagelayout_bar']:$this->show_pagelayout_bar,
														'show_canvas'=>$saved_design['show_canvas']?$saved_design['show_canvas']:$this->show_canvas,
														'printing_mode'=>$this->printing_mode,
														'show_layout_bar'=>$this->show_canvas,
														'show_paginator'=>$this->show_paginator,
														'mode'=>$saved_design['mode'],
														'ComponentsIncludedToBeShow'=>$saved_design['ComponentsIncludedToBeShow'],
														'BackgroundImage_tool_label'=>$saved_design['BackgroundImage_tool_label']
												));
			// ->slick(array("dots"=>false,"slidesToShow"=>3,"slidesToScroll"=>2));
		}
		parent::render();
	}

}