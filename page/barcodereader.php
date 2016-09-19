<?php 
 namespace xepan\commerce;
 class page_barcodereader extends \xepan\base\Page{

	public $title='Bar Codes';

	function init(){
		parent::init();

		$barcodereader = $this->add('xepan\commerce\Model_BarCodeReader');
		
		$f = $this->add('Form',null,null,['form/stacked']);
		$c = $f->add('Columns')->addClass('row xepan-push');
		$prefix = $c->addColumn(4)->addClass('col-md-4');
		$number = $c->addColumn(4)->addClass('col-md-4');
		$postfix = $c->addColumn(4)->addClass('col-md-4');
		$prefix->addField('Line','prefix');
		$number->addField('Line','number');
		$postfix->addField('Line','postfix');
		
		$crud = $this->add('xepan\hr\CRUD',null,null,['view/barcodereader/grid']);
		
		$grid = $crud->grid;
		$upl_btn=$grid->addButton('Upload Bar Codes');
		$upl_btn->setIcon('ui-icon-arrowthick-1-n');
		// $upl_btn->js('click')
		// 	->univ()
		// 	->frameURL('Data Upload',);

		$f->addSubmit('Get Barcode')->addClass('btn btn-primary btn-block');
		if($f->isSubmitted()){
			$barcode_name = $f['prefix'] . $f['number'] . $f['postfix'];
			$barcodereader['name'] = $barcode_name;
			$barcodereader->save();

			return $crud->js()->reload()->execute();
		}
		
		$crud->setModel($barcodereader);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
	}

}  