<?php

namespace xepan\commerce;

class View_CustomerTemplate extends \View {

	public $options=[];
	function init(){
		parent::init();
		$this->js(true)
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/webfont.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fabric.min.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/designer.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jquery.colorpicker.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/cropper.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/pace.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/addtocart.js')
			->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/slick.js')
			;

	
		
		//Check Customer is login or not
		$customer = $this->add('xepan/commerce/Model_Customer');
		if(!$customer->loadLoggedIn("Customer")){
			$this->add('View_Error')->set('Not Authorized');
			return;
		}

		if(!($designer_page = $this->options['designer-page'])){
			$this->add('View_Warning')->set('Specify the designer page');
			return ;
		}
								
		$col = $this->add('Columns')->addClass('atk-box row well well-sm');
		$left = $col->addColumn(6)->addClass('col-md-12 col-sm-12 col-lg-12 col-xs-12');
		// $right = $col->addColumn(6)->addClass('col-md-6');
		$crud = $this->add('xepan\base\CRUD',array('allow_add'=>false,'allow_edit'=>false,'grid_options'=>['paginator_class'=>'Paginator']),null,["view\\tool\\grid\\".$this->options['customer-template-grid-layout']]);
		$paginator = $crud->grid->addPaginator(6);
		$crud->grid->addQuickSearch(['name']);
		$template_model = $this->add('xepan\commerce\Model_Item_Template');
		$template_model->addCondition(
							$template_model
								->dsql()
								->orExpr()
								->where('to_customer_id',$customer->id)
								->where('to_customer_id',null)
								->where('to_customer_id',0)
							);
		$template_model->addCondition('status','Published');
		$item_cat_j = $template_model->leftJoin('category_item_association.item_id','id');
		$item_cat_j->addField('category_id');

		$cat_id = $this->app->stickyGET('commerce_catid');
		if($cat_id){
			$template_model->addCondition('category_id',$cat_id);
		}

		if($this->options['show_duplicate_form'] And $customer['is_designer']){
			$form = $left->add('Form',null,null,['form/empty']);

			$form->add('View')->setElement('label')->set('Template Category');
			$cat_field = $form->addField('xepan\commerce\DropDown','item_category','Select Item Category');
			$cat_model = $this->add('xepan\commerce\Model_Category');
			
			$cat_model->getElement('website_display_item_count')->destroy();
			$cat_model->getElement('min_price')->destroy();
			$cat_model->getElement('max_price')->destroy();

			if(!$this->options['show_empty_category']){

				$cat_model->addExpression('total_template_count')->set(function($m,$q){
					$cat_item_model = $m->add('xepan\commerce\Model_CategoryItemAssociation');
					$cat_item_j = $cat_item_model->leftJoin('item.document_id','item_id');
					$cat_item_j->addField('is_saleable');
					$cat_item_j->addField('is_designable');

					$item_doc_j = $cat_item_j->join('document','document_id');
					$item_doc_j->addField('status');
					$cat_item_model->addCondition('status','Published');
					$cat_item_model->addCondition('is_template',1);
					$cat_item_model->addCondition('is_designable',1);
					$cat_item_model->addCondition('category_id',$m->getElement('id'));

					return $cat_item_model->count();
				})->sortable(true);
				$cat_model->addCondition('total_template_count','>',0);
			}


			$cat_field->setModel($cat_model);
			$cat_field->setEmptyText('please select category');

			$form->add('View')->setElement('label')->set('Template to duplicate');
			$tem_field=$form->addField('xepan\commerce\DropDown','item_template','Select a template to duplicate');
			$tem_field->setModel($template_model);
			$tem_field->setEmptyText('please select a template to duplicate');
			

			$cat_field->js('change',$tem_field->js()->reload(['commerce_catid'=>$cat_field->js()->val()]));

			$form->addSubmit('Duplicate')->addClass('btn btn-primary');
			// $temp_image_model= $right->add('xepan\commerce\Model_Item_Image');
			// $temp_image_model->addCondition('item_id',$_GET['item_image']);

			// if($_GET['item_image'])
			// 	$temp_image_model->addCondition('item_id',$_GET['item_image'])->setLimit(1);
			// // $temp_image_model->tryLoadAny();
			
			// $view=$right->add('View')->addStyle('width','20%');
			// if($temp_image_model->loaded()){
			// 	$view->setElement('img')->setAttr('src',$temp_image_model['file'])->setStyle('width','100%')->addClass('atk-box');
			// }else{
			// 	$view->set('No Image Found');
			// }
			// $tem_field->js('change',$view->js()->reload(['item_image'=>$tem_field->js()->val()]));

			$this->app->stickyGET('item_template_id');
			$this->app->stickyGET('customer_id');
			
			$vp = $this->add('VirtualPage')->set(function($p)use($crud){
				// throw new \Exception($_GET['customer_id'], 1);
				
				$new_item = $p->add('xepan\commerce\Model_Item_Template')->load($_GET['item_template_id']);
				$f = $p->add('Form',null,null,['form/stacked']);
				$c = $f->add('Columns');
				$left_col = $c->addColumn(6)->addClass('col-md-6');
				$right_col = $c->addColumn(6)->addClass('col-md-6');
				$left_col->addField('line','duplicate_item_name')->set($new_item['name']." - ".uniqid());
				$right_col->addField('line','duplicate_item_sku_code')->set($new_item['sku']." - ".uniqid());
				$f->addSubmit('Duplicate')->addClass('btn btn-primary');
				if($f->isSubmitted()){
					$new_item->duplicate(
										$f['duplicate_item_name'],
										$f['duplicate_item_sku_code'],
										$_GET['customer_id'],
										false,
										false,
										$new_item->id,
										$create_default_design_also=true,
										$this->app->auth->model->id
									);
					$f->js(null,$f->js()->closest('.dialog')->dialog('close'))->univ()->successMessage('Design Duplicated')->execute();
				}
			});



			if($form->isSubmitted()){
				$this->js()->univ()->frameURL('Duplicate Template Item',$this->app->url($vp->getURL(),
								[
									'item_template_id'=>$form['item_template'],
									'customer_id'=>$customer->id
								]))
				->execute();
				
				$form->js(null,$crud->js()->reload())->univ()->successMessage('Design Duplicated')->execute();
				
			}
		}

		

		$customer_template_model = $this->add('xepan\commerce\Model_Item');
		$customer_template_model->addCondition(
					$customer_template_model
						->dsql()
						->orExpr()
						->where('to_customer_id',$customer->id)
						->where('designer_id',$customer->id)
				);

		$customer_template_model->setOrder('id','desc');
		$crud->setModel($customer_template_model,array('name','sku','short_description','description','is_party_publish','duplicate_from_item_id'),array('name','sku','designs','is_ordered','is_party_publish'));
		
		if(!$crud->isEditing()){
			$g = $crud->grid;
			$this->count = 1;

			$view_font =  $this->add('xepan\commerce\View_Designer_FontCSS');
			$font_family_config_array = json_encode($view_font->getFontList());

			$g->addHook('formatRow',function($g)use($designer_page ,$paginator,$font_family_config_array){
				// $template_thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',['xsnb_design_item_id'=>$g->model['id'],'width'=>'300']);
				// $g->current_row['template_thumb_url'] = $template_thumb_url;

				$template_edit_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['id'],'xsnb_design_template'=>'true'));
				$g->current_row['template_edit'] = $template_edit_url;

				$template_new_design_url = $this->app->url($designer_page,array('xsnb_design_item_id'=>$g->model['id']));
				$g->current_row['new_design'] = $template_new_design_url;
				$g->current_row['s_no'] = ($this->count++) + $paginator->skip;

				$design = json_decode($g->model['designs'],true);

				if(!$design['design']) return;
				
				$specification = $g->model->getSpecification();

				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['width'],$temp);
				$specification['width']= $temp[1][0];
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['height'],$temp);
				$specification['height']= $temp[1][0];
				$specification['unit']=$temp[2][0];
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['trim'],$temp);
				$specification['trim']= $temp[1][0];
				
				if($specification['width'] > $specification['height'])
					$width = $specification['width'];
				else{
					$ratio = $specification['width'] / $specification['height'];
					$width = (325 *$ratio).'px';
				}

				$g->current_row['width'] = $width;

				$g->js(true)->_selector('#canvas-workspace-'.$g->model->id)->xepan_xshopdesigner(
											array(
													'width'=> $specification['width'],
													'height'=>$specification['height'],
													'trim'=> $specification['trim'],
													'unit'=> $specification['unit'],
													'designer_mode'=> false,
													'design'=>json_encode($design['design']),
													'show_cart'=>'0',
													'selected_layouts_for_print'=>$design['selected_layouts_for_print'],
													'item_id'=>$g->model->id,
													'item_member_design_id' => null,
													'item_name' => $g->model['name'],
													'base_url'=> $this->api->url()->absolute()->getBaseURL(),
													'calendar_starting_month'=> $design['calendar_starting_month'],
													'calendar_starting_year'=> $design['calendar_starting_year'],
													'calendar_event'=> $design['calendar_event'],
													'printing_mode'=>false,
													'show_canvas'=>true,
													'is_start_call'=>1,
													'show_tool_bar'=>0,
													'show_pagelayout_bar'=>0,
													'show_tool_calendar_starting_month'=>0,
													'mode'=>'primary',
													'show_layout_bar'=>0,
													'show_safe_zone'=>0,
													'font_family_list'=>$font_family_config_array
											));

			});
		}

		$this->on('click','button.send_to_approved',function($js,$data){
			// $js->univ()->alert('Hellpo');
			$item = $this->add('xepan\commerce\Model_Item')->load($data['id']);
			$item['is_party_publish'] = 1;
			$item->saveAndUnload();

			$js->univ()->successMessage("Item Design Send To Approval");
		});
		

	}

	function render(){
		parent::render();
	}
}