<?php
 
namespace xepan\commerce;

class page_customer extends \xepan\base\Page {
	public $title='Customers';

	function page_index(){
		// parent::init();

		$customer_model = $this->add('xepan\commerce\Model_Customer');
		$customer_model->add('xepan\base\Controller_TopBarStatusFilter');
		

		//Total Orders
		$customer_model->addExpression('orders')->set(" 'Todo 10' ");

		$customer_model->addExpression('organization_name_with_name')
					->set($customer_model->dsql()
						->expr('CONCAT(IFNULL([0],"")," ::[ ",IFNULL([1],"")," ]")',
							[$customer_model->getElement('name'),
								$customer_model->getElement('organization_name')]))
					->sortable(true);

		$crud = $this->add('xepan\hr\CRUD',
							[
							'action_page'=>'xepan_commerce_newcustomer',
							'edit_page'=>'xepan_commerce_customerdetail'
							],
							null,
							['view/customer/grid']
						);
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'name']);
		$crud->setModel($customer_model)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);

		$frm=$crud->grid->addQuickSearch(['name','organization_name']);

		$crud->add('xepan\base\Controller_MultiDelete');
		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-customer-detail')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-customer-id]')->data('id')]);
			
			// $newcustomer_btn=$c->grid->addButton('new')->addClass('btn btn-primary');

			// $p=$this->add('VirtualPage');
			// $p->set(function($p){
			// 	$f=$p->add('Form');
			// 	$f->addField('text','json');
			// 	$f->addSubmit('Go');
				
			// 	if($f->isSubmitted()){
			// 		$import_m=$this->add('xepan\base\Model_GraphicalReport');

			// 		$import_m->importJson($f['json']);	
					
			// 		$f->js()->reload()->univ()->successMessage('Done')->execute();
			// 	}
			// });
			// if($import_btn->isClicked()){
			// 	$this->js()->univ()->frameURL('Import',$p->getUrl())->execute();
			// }
		}

		/**			
		CSV Importer
		*/
		$grid = $crud->grid;
		$import_btn=$grid->addButton('Import CSV')->addClass('btn btn-primary');
		$import_btn->setIcon('ui-icon-arrowthick-1-n');

		$import_btn->js('click')
			->univ()
			->frameURL(
					'Import CSV',
					$this->app->url('./import')
					);
	}

	function page_import(){
		
		$form = $this->add('Form');
		$form->addSubmit('Download Sample File');
		
		if($_GET['download_sample_csv_file']){
			$output = ['first_name','last_name','address','city','state','country','pin_code','organization','post','website','source','remark','personal_email_1','personal_email_2','official_email_1','official_email_2','personal_contact_1','personal_contact_2','official_contact_1','official_contact_2','billing_name','billing_address','billing_city','billing_pincode','shipping_name','shipping_address','shipping_city','shipping_pincode','tin_no','pan_no'];

			$output = implode(",", $output);
	    	header("Content-type: text/csv");
	        header("Content-disposition: attachment; filename=\"sample_xepan_customer_import.csv\"");
	        header("Content-Length: " . strlen($output));
	        header("Content-Transfer-Encoding: binary");
	        print $output;
	        exit;
		}

		if($form->isSubmitted()){
			$form->js()->univ()->newWindow($form->app->url('xepan_commerce_customer_import',['download_sample_csv_file'=>true]))->execute();
		}

		$this->add('View')->setElement('iframe')->setAttr('src',$this->api->url('./execute',array('cut_page'=>1)))->setAttr('width','100%');
	}
	
	function downloadsamplefile(){

	}

	function page_import_execute(){

		ini_set('max_execution_time', 0);

		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='".$this->api->url(null,array('cut_page'=>1))."' enctype='multipart/form-data'>
			<input type='file' name='csv_customer_file'/>
			<input type='submit' value='Upload'/>
			</form>"
			);

		if($_FILES['csv_customer_file']){
			if ( $_FILES["csv_customer_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["csv_customer_file"]["error"] );
			}else{
				$mimes = ['text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext'];
				if(!in_array($_FILES['csv_customer_file']['type'],$mimes)){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new \xepan\base\CSVImporter($_FILES['csv_customer_file']['tmp_name'],true,',');
				$data = $importer->get();

				$customer = $this->add('xepan\commerce\Model_Customer');
				$customer->addCustomerFromCSV($data);
				$this->add('View_Info')->set('Total Records : '.count($data));
			}
		}
	}
}



























// <?php
//  namespace xepan\commerce;
//  class page_customerprofile extends \Page{
//  	public $title='Customer';

// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/customerprofile'];
// 	}
// }