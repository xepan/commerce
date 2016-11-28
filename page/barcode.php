<?php 
 namespace xepan\commerce;
 class page_barcode extends \xepan\base\Page{

	public $title='Bar Codes';

	function init(){
		parent::init();

		$barcode = $this->add('xepan\commerce\Model_BarCode');
		
		$f = $this->add('Form',null,null,['form/stacked']);
		$c = $f->add('Columns')->addClass('row xepan-push well');
		$prefix = $c->addColumn(2)->addClass('col-md-3');
		$start_c = $c->addColumn(2)->addClass('col-md-3');
		// $number = $c->addColumn(2)->addClass('col-md-3');
		$end_c = $c->addColumn(2)->addClass('col-md-3');
		$postfix = $c->addColumn(2)->addClass('col-md-3');
		$prefix->addField('Line','prefix');
		$start_num = $start_c->addField('Line','start_number');
		$end_num = $end_c->addField('Line','end_number');
		$postfix->addField('Line','postfix');
		
		$crud = $this->add('xepan\hr\CRUD',null,null,['view/barcode/grid']);
		
		$grid = $crud->grid;
		$upl_btn=$grid->addButton('Upload Bar Codes');
		$upl_btn->setIcon('ui-icon-arrowthick-1-n');
		// $item_id = $item->id;

		$upl_btn->js('click')
			->univ()
			->frameURL(
					'Data Upload',
					$this->app->url('xepan_commerce_store_uploadbarcodedata',
									array(
											'cut_page'=>1
										)
									)
					);
		// $upl_btn->js('click')
		// 	->univ()
		// 	->frameURL('Data Upload',);

		$f->addSubmit('Get Barcode')->addClass('btn btn-primary btn-block xepan-push-large');
		
		if($f->isSubmitted()){
			$value="";
			$query= "INSERT INTO `dispatch_barcode` (`name`) VALUES "; 
			
			for ($i=$f['start_number']; $i <= $f['end_number'] ; $i++) {
				$value.= "('".$f['prefix'] . $i . $f['postfix']."'),";
			}

			$query.= trim($value,','). ";";
			$this->app->db->dsql()->expr($query)->execute();
			return $crud->js()->reload()->execute();
		}
		
		$crud->setModel($barcode);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
		$crud->add('xepan\base\Controller_MultiDelete');
	}

}  