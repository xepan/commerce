// <?php  

// /**
// * description: ATK Page
// * 
// * @author : Gowrav Vishwakarma
// * @email : gowravvishwakarma@gmail.com, info@xavoc.com
// * @website : http://xepan.org
// * 
// */ 

// namespace xepan\commerce;

// class page_additem extends \Page {
// 	public $title='Add Item';

// 	function init(){
// 		parent::init();

// 		$item = $this->add('xepan\commerce\Model_Item')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
// 		$basic = $this->add('xepan\base\View_Document',
// 				[
// 					'action'=>$this->api->stickyGET('action')?:'add', // add/edit
// 					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
// 					'allow_many_on_add' => false, // Only visible if editinng,
// 					'view_template' => ['page/additem','basic']
// 				],
// 				'basic'
// 			);
// 		$basic->setModel($item,['x'],['name'
// 								]);

// 		// $seo_item = $this->add('xepan\base\View_Document',
// 		// 		[
// 		// 			'action'=>$this->api->stickyGET('action')?:'view', // add/edit
// 		// 			'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
// 		// 			'allow_many_on_add' => false, // Only visible if editinng,
// 		// 			'view_template' => ['page/itemdetail','seo']
// 		// 		],
// 		// 		'seo'
// 		// 	);

// 		// $seo_item->setModel($item,['y'],['meta_title','meta_description','tags']);

// 		// $cat_item = $this->add('xepan\base\View_Document',
// 		// 		[
// 		// 			'action'=>$this->api->stickyGET('action')?:'view', // add/edit
// 		// 			'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
// 		// 			'allow_many_on_add' => false, // Only visible if editinng,
// 		// 			'view_template' => ['page/itemdetail','catg']
// 		// 		],
// 		// 		'catg'
// 		// 	);
// 		// $cat_item->setModel($item,['z'],['category_name']);


// 	}

// 	function defaultTemplate(){
// 		return ['page/additem'];

// 	}
// }


