<?php
namespace xepan\commerce;

class Tool_Invoice extends \xepan\cms\View_Tool{
	public $options = [];

	function init(){
		parent::init();
		
		$filter = $this->app->stickyGET('filter');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$status = $this->app->stickyGET('invoice_status');

		if($this->owner instanceof \AbstractController) return;
		
		if(!$this->app->auth->isLoggedIn()){
			$this->add('View_Error')->set('Login first to view records.');
			return;
		}

		$this->customer = $customer = $this->add('xepan\commerce\Model_Customer');
		$customer->loadLoggedIn();

		$inv_model = $this->add('xepan\commerce\Model_SalesInvoice');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->makePanelsCoppalsible(true)
			->layout([
					'from_date'=>'Filter Invoice~c1~2~closed',
					'to_date'=>'c2~2',
					'invoice_status'=>'c3~2',
					'FormButtons~&nbsp;'=>'c4~2'
				]);
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('DropDown','invoice_status')->setValueList(array_combine($inv_model->status, $inv_model->status))->setEmptyText('All');

		$form->addSubmit('Filter')->addClass('btn btn-primary');
		
		$inv_model->addExpression('invoice_no')->set(function($m,$q){
			return $q->expr('CONCAT(IFNULL([0],"-")," ",[1])',[
						$m->getElement('serial'),
						$m->getElement('document_no')
					]);
		});
		$inv_model->addCondition('contact_id',$customer->id);

		if($filter){
			if($from_date){
				$inv_model->addCondition('created_at','>=',$from_date);
			}
			if($to_date){
				$inv_model->addCondition('created_at','<',$this->app->nextDate($to_date));
			}
			if($status){
				$inv_model->addCondition('status',$status);
			}
		}

		$grid = $this->add('xepan\base\Grid');

		if($form->isSubmitted()){
			$grid->js()->reload([
					'filter'=>1,
					'from_date'=>$form['from_date']?:0,
					'to_date'=>$form['to_date']?:0,
					'invoice_status'=>$form['invoice_status']?:0
				])->execute();
		}

		$grid->addHook('formatRow',function($g){
			$g->current_row_html['download'] = '<button class="xepan-customer-documentprint" data-id="'.$g->model['id'].'"><i class="fa fa-download"></i> Download</button>';
		});

		$grid->template->tryDel('Pannel');
		$grid->setModel($inv_model,['invoice_no','created_at','status','net_amount']);
		$grid->addColumn('download');
		$inv_model->setOrder('created_at','desc');
		$grid->addPaginator(10);

		$print_url = $this->api->url('xepan_commerce_orderdetailprint');
        $grid->on('click','.xepan-customer-documentprint',function($js,$data)use($print_url){
            return $js->univ()->newWindow($print_url."&document_id=".$data['id']);
        });
	}
}