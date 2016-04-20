<?php

namespace xepan\commerce;

class View_CustomerDesign extends \View {

	public $options=[];

	function init(){
		parent::init();

		//Check Customer is login or not
		$customer = $this->add('xepan/commerce/Model_Customer');
		if(!$customer->loadLoggedIn()){
			$this->add('View_Error')->set('Not Authorized');
			return;
		}

		if(!($designer_page = $this->options['designer-page'])){
			$this->add('View_Warning')->set('Specify the designer page');
			return ;
		}
		
		$col = $this->add('Columns')->addClass('atk-box');
		$left = $col->addColumn(7)->addClass('col-md-12');
		$right = $col->addColumn(5)->addClass('col-md-12');
		$form = $left->add('Form');
		$crud = $this->add('CRUD',array('allow_add'=>false,'allow_edit'=>'false'));

		$template_model = $this->add('xepan\commerce\Model_Item_Template');
		$template_model->addCondition(
							$template_model
								->dsql()
								->orExpr()
								->where('to_customer_id',$customer->id)
								->where('to_customer_id',null)
							);

		$tem_field=$form->addField('xepan\commerce\DropDown','item_template');
		$tem_field->setModel($template_model);
		$tem_field->setEmptyText('Please Select');

		$form->addSubmit('Duplicate');
		if($form->isSubmitted()){

			$new_item = $template_model
						->load($form['item_template'])
						->duplicate(
								$template_model['name']." - new",
								$template_model['sku']." - new",
								$template_model['designer_id'],
								false,
								false,
								$template_model->id,
								$create_default_design_also=true
							);
			
			$form->js(null,$crud->js()->reload())->univ()->successMessage('Design Duplicated')->execute();
		}

		$customer_designs_model = $this->add('xepan\commerce/Model_Item_Template_Design');
		$customer_designs_model->addCondition('contact_id',$customer->id);

		$customer_designs_model->setOrder('id','desc');
		$crud->setModel($customer_designs_model,array('name','sku','short_description','description','is_party_publish','duplicate_from_item_id'),array('name','sku','designs','is_ordered','is_party_publish'));

	// /* Right Column Js Reload Item Image*/
	// 	$temp_image_model= $right->add('xShop/Model_ItemImages');
	// 	$temp_image_model->addCondition('item_id',$_GET['image_item_id']);

	// 	$temp_image_model->tryLoadAny();
	// 	if($temp_image_model->loaded()){
	// 		$img=$right->add('View')->setElement('img')->setAttr('src',$temp_image_model['item_image'])->setStyle('width','100%')->addClass('atk-box');
	// 		// $img->js('click')->univ()->loacation($this->api->url(null,$temp_image_model['item_image']));
	// 	}else{
	// 		$right->add('View_Box')->set('No Image Found');
	// 	}

	// 	$tem_field->js('change',$right->js()->reload(array('image_item_id'=>$tem_field->js()->val())));
	// /* End of  Right Column Js Reload Item Image*/
		
	// 	if(!$crud->isEditing()){
	// 		$g = $crud->grid;
	// 		$g->add_sno();
	// 		$g->addPaginator(10);
	// 		$g->addQuickSearch(array('name'));
	// 		//Image
	// 		// $g->addMethod('format_designs',function($g,$f)use($customer_designs_model){
	// 		// 	$g->current_row_html[$f] = '<img style="box-shadow:0 3px 5px rgba(0, 0, 0, 0.2);" src="index.php?page=xShop_page_designer_thumbnail&item_member_design_id='.$customer_designs_model['id'].'" width="100px;"/>';
	// 		// });
	// 		// 	$g->addFormatter('designs','designs');
			
	// 		$g->addMethod('format_name',function($g,$f){
	// 			$is_ordered = "<small style='color:red'>No</small>";
	// 			if($g->model['is_ordered']) $is_ordered = "<small style='color:green'>Yes</small>";
	// 			$g->current_row_html[$f]=$g->model['name']."<br> <small style='color:gray'> SKU - ".$g->model['sku']."</small><br><small style='color:gray'> Is Ordered - ".$is_ordered.'</small>';
	// 		});
	// 		$g->addFormatter('name','name');
	// 		$g->removeColumn('sku');
	// 		$g->removeColumn('is_ordered');

	// 		//Edit Template
	// 		if(!$member['is_organization'] ){				
	// 			$g->addColumn('edit_template');
	// 			$g->addMethod('format_edit_template',function($g,$f)use($designer,$designer_page){
	// 				// echo $this->api->url();
	// 				if($g->model->ref('item_id')->get('designer_id') == $designer->id){
	// 					$img = '<img style="box-shadow:0 3px 5px rgba(0, 0, 0, 0.2);" class="atk-align-center" src="index.php?page=xShop_page_designer_thumbnail&xsnb_design_item_id='.$g->model['item_id'].'" width="100px;"/>';
	// 					$g->current_row_html[$f]=$img.'</br>'.'<a class="icon-pencil atk-margin-small" target="_blank" href='.$g->api->url('index',array('subpage'=>$designer_page,'xsnb_design_item_id'=>$g->model['item_id'],'xsnb_design_template'=>'true')).'>Edit Template</a>';
	// 				}
	// 				else
	// 					$g->current_row_html[$f]='';	
	// 			});

	// 			$g->addFormatter('edit_template','edit_template');
	// 		}


	// 		//Edit Design
	// 		$g->addColumn('design');
	// 		// $subpage = $_GET['designer_page'];//$this->html_attributes['xsnb-desinger-page'];
	// 		$g->addMethod('format_design',function($g,$f)use($designer,$designer_page,$customer_designs_model){
	// 			if(!$g->model['is_dummy']){
	// 				$img = '<img style="box-shadow:0 3px 5px rgba(0, 0, 0, 0.2);" src="index.php?page=xShop_page_designer_thumbnail&item_member_design_id='.$customer_designs_model['id'].'" width="100px;"/>';
	// 				$g->current_row_html[$f]=$img.'<br/>'.'<a target="_blank" class="icon-pencil atk-margin-small" href='.$g->api->url('index',array('subpage'=>$designer_page,'xsnb_design_item_id'=>'not-available','xsnb_design_template'=>'false','item_member_design_id'=>$g->model->id)).'>Design</a>';
	// 			}
	// 			else
	// 				$g->current_row_html[$f] ='';
	// 		});
	// 		$g->addFormatter('design','design');

			
	// 		if($g->hasColumn('is_party_publish'))$g->removeColumn('is_party_publish');

	// 		$g->addMethod('format_edit',function($g,$f){
	// 			if($g->model['is_ordered'])
	// 				$g->current_row_html[$f]="";
	// 			else
	// 				$g->current_row_html[$f]= $g->current_row_html[$f];
	// 		});

	// 		$g->addFormatter('edit','edit');
			
	// 		$g->addMethod('format_delete1',function($g,$f){
	// 			if($g->model['is_ordered'])
	// 				$g->current_row_html[$f]="";
	// 			else
	// 				$g->current_row_html[$f]= $g->current_row_html[$f];
	// 		});		
	// 		$g->addFormatter('delete','delete1');
	// 		// $g->addColumn('Expander','Images');

	// 		$g->add('VirtualPage')->addColumn('Images','Images',array('icon'=>'file-image','descr'=>'image'),$g)->set(function($p)use($g){
	// 			$image_design_id = $p->id;
	// 			$item_member_design = $p->add('xShop/Model_ItemMemberDesign')->load($image_design_id);
	// 			$item_model = $item_member_design->ref('item_id');

	// 			// $p->add('View_Info')->set("Images".$_GET['xshop_item_member_designs_id']);
	// 			$item_id = $item_model->id;
	// 			if(!$item_id){
	// 				$this->add('View_Warning')->set('Item Model Not Loaded');
	// 				return;
	// 			}

	// 			$item_images_model = $p->add('xShop/Model_ItemImages')->addCondition('item_id',$item_id)->addCondition('customefieldvalue_id',null);
	// 			$item_images_model->setOrder('id','desc');
	// 			$crud = $p->add('CRUD');
	// 			$crud->setModel($item_images_model,array('item_image_id','alt_text','title'),array('item_image','alt_text','title'));
	// 			if(!$crud->isEditing()){
	// 				$g = $crud->grid;
	// 				$g->addMethod('format_image_thumbnail',function($g,$f){
	// 					$g->current_row_html[$f] = '<img style="height:40px;max-height:40px;" src="'.$g->current_row[$f].'"/>';
	// 				});
	// 				$g->addFormatter('item_image','image_thumbnail');
	// 				$g->addFormatter('alt_text','Wrap');
	// 				$g->addFormatter('title','Wrap');
	// 				$g->addPaginator($ipp=50);
	// 			}

	// 		});

	// 		$g->removeColumn('designs');
	// 	}
		

	}
}