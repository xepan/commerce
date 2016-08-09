<?php
namespace xepan\accounts;

class Model_Ledger extends \xepan\base\Model_Table{
	public $table="ledger";
	public $acl_type='Ledger';	
	
	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\accounts\Group','group_id')->mandatory(true);
		$this->hasOne('xepan\base\Epan','epan_id');
		
		$this->addField('name')->sortable(true);
		$this->addField('related_id'); // user for related like tax/vat
		$this->addField('ledger_type'); //

		$this->addField('LedgerDisplayName')->caption('Ledger Displ. Name');
		$this->addField('is_active')->type('boolean')->defaultValue(true);

		$this->addField('OpeningBalanceDr')->type('money')->defaultValue(0);
		$this->addField('OpeningBalanceCr')->type('money')->defaultValue(0);
		// $this->addField('CurrentBalanceDr')->type('money')->defaultValue(0);
		// $this->addField('CurrentBalanceCr')->type('money')->defaultValue(0);
		
		$this->addField('created_at')->type('date')->defaultValue($this->app->now);
		$this->addField('updated_at')->type('date')->defaultValue($this->app->now);

		$this->addField('affectsBalanceSheet')->type('boolean')->defaultValue(true);

		$this->hasMany('xepan\accounts\TransactionRow','ledger_id',null,'TransactionRows');

		$this->addExpression('balance_sheet_id')->set(function($m,$q){
			return $m->refSQL('group_id')->fieldQuery('balance_sheet_id');
		});

		$this->addExpression('balance_sheet')->set(function($m,$q){
			return $m->refSQL('group_id')->fieldQuery('balance_sheet');
		});


		$this->addExpression('parent_group')->set(function($m,$q){
			return $this->add('xepan\accounts\Model_Group',['table_alias'=>'parent_group'])
					->addCondition('id',$m->refSQL('group_id')->fieldQuery('parent_group_id'))
					->fieldQuery('name');
		});

		$this->addExpression('root_group')->set(function($m,$q){
			return $this->add('xepan\accounts\Model_Group',['table_alias'=>'root_group'])
					->addCondition('id',$m->refSQL('group_id')->fieldQuery('root_group_id'))
					->fieldQuery('name');
		});

		$this->addExpression('root_group_id')->set(function($m,$q){
			return $this->add('xepan\accounts\Model_Group',['table_alias'=>'root_group'])
					->addCondition('id',$m->refSQL('group_id')->fieldQuery('root_group_id'))
					->fieldQuery('id');
		});


		$this->addExpression('CurrentBalanceDr')->set(function($m,$q){
			return $m->refSQL('TransactionRows')->sum('amountDr');
		});
		$this->addExpression('CurrentBalanceCr')->set(function($m,$q){
			return $m->refSQL('TransactionRows')->sum('amountCr');
		});

		$this->addExpression('balance_signed')->set(function($m,$q){
			// return '"123"';
			return $q->expr("((IFNULL([0],0) + IFNULL([1],0))- (IFNULL([2],0)+IFNULL([3],0)))",[$m->getField('OpeningBalanceDr'),$m->getField('CurrentBalanceDr'),$m->getField('OpeningBalanceCr'),$m->getField('CurrentBalanceCr')]);
		});
		
		$this->addExpression('balance_sign')->set(function($m,$q){
			return $q->expr("IF([0]>0,'DR','CR')",[$m->getElement('balance_signed')]);
		});

		$this->addExpression('balance')->set(function($m,$q){
			return $q->expr("Concat(ABS([0]),' ',[1])",[$m->getElement('balance_signed'),$m->getElement('balance_sign')]);
		});




		$this->addHook('beforeDelete',$this);
		
		$this->is([
				'name|required|unique_in_epan'
			]);
	}

	function beforeDelete(){
		if($this->ref('TransactionRows')->count()->getOne())
			throw new \Exception("This Account Cannot be Deleted, its has content Many. Please delete Transaction Row first", 1);
	}


	//creating Employee ledger
	function createEmployeeLedger($app,$employee_for){
		if(!($employee_for instanceof \xepan\hr\Model_Employee))
			throw new \Exception("must pass Employee model", 1);	
		
		if(!$employee_for->loaded())
			throw new \Exception("must pass Employee loaded model", 1);	

		$creditor = $app->add('xepan\accounts\Model_Group')->loadSundryCreditor();
		
		return $app->add('xepan\accounts\Model_Ledger')->createNewLedger($employee_for,$creditor,"Employee");
	}

	//creating customer ledger
	function createCustomerLedger($app,$customer_for){
		if(!($customer_for instanceof \xepan\commerce\Model_Customer))
			throw new \Exception("must pass customer model", 1);	
		
		if(!$customer_for->loaded())
			throw new \Exception("must pass customer loaded model", 1);	

		$debtor = $app->add('xepan\accounts\Model_Group')->loadSundryDebtor();
		
		return $app->add('xepan\accounts\Model_Ledger')->createNewLedger($customer_for,$debtor,"Customer");
	}

	//creating supplier ledger
	function createSupplierLedger($app,$supplier_for){	
		
		if(!($supplier_for instanceof \xepan\commerce\Model_Supplier))
			throw new \Exception("must pass supplier model", 1);	

		if(!$supplier_for->loaded())
			throw new \Exception("must pass loaded supplier", 1);	

		$creditor = $app->add('xepan\accounts\Model_Group')->loadSundryCreditor();

		return $app->add('xepan\accounts\Model_Ledger')->createNewLedger($supplier_for,$creditor,"Supplier");

	}

	function createOutsourcePartyLedger($app,$outsource_party_for){	
		
		if(!($outsource_party_for instanceof \xepan\production\Model_OutsourceParty))
			throw new \Exception("must pass outsourceparty model", 1);	

		if(!$outsource_party_for->loaded())
			throw new \Exception("must pass loaded outsourceparty", 1);	

		$outsource = $app->add('xepan\accounts\Model_Group')->loadSundryCreditor();

		return $app->add('xepan\accounts\Model_Ledger')->createNewLedger($outsource_party_for,$outsource,"OutsourceParty");

	}



	function createNewLedger($contact_for,$group,$ledger_type=null){

		$ledger = $this->add('xepan\accounts\Model_Ledger');
		$ledger->addCondition('contact_id',$contact_for->id);
		$ledger->addCondition('group_id',$group->id);
		$ledger->addCondition('ledger_type',$ledger_type);

		$ledger->tryLoadAny();

		$ledger['name'] = $contact_for['name'];
		$ledger['LedgerDisplayName'] = $contact_for['name'];
		$ledger['updated_at'] =  $this->api->now;
		$ledger['related_id'] =  $contact_for->id;
		return $ledger->save();
	}

	function createTaxLedger($tax_obj){
		
		if(!($tax_obj instanceof \xepan\commerce\Model_Taxation))
			throw new \Exception("must pass taxation model", 1);	

		if(!$tax_obj->loaded())
			throw new \Exception("must loaded taxation", 1);

		$ledger = $this->add('xepan\accounts\Model_Ledger');
		$ledger->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadDutiesAndTaxes()->get('id'));
		$ledger->addCondition('ledger_type',$tax_obj['name']);

		$ledger->tryLoadAny();

		$ledger['name'] = $tax_obj['name'];
		$ledger['LedgerDisplayName'] = $tax_obj['name'];
		$ledger['related_id'] = $tax_obj['id'];
		$ledger['updated_at'] =  $this->api->now;
		return $ledger->save();
	}

	function LoadTaxLedger($tax_obj){
		if(!($tax_obj instanceof \xepan\commerce\Model_Taxation))
			throw new \Exception("must pass taxation model", 1);	

		if(!$tax_obj->loaded())
			throw new \Exception("must loaded taxation", 1);

		$ledger = $this->add('xepan\accounts\Model_Ledger');
		$ledger->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadDutiesAndTaxes()->get('id'));
		$ledger->addCondition('ledger_type',$tax_obj['name']);
		$ledger->addCondition('related_id',$tax_obj->id);
		return $ledger->tryLoadAny();
	}


	function debitWithTransaction($amount,$transaction_id,$currency_id,$exchange_rate){

		$transaction_row=$this->add('xepan\accounts\Model_TransactionRow');
		$transaction_row['_amountDr']=$amount;
		$transaction_row['side']='DR';
		$transaction_row['transaction_id']=$transaction_id;
		$transaction_row['ledger_id']=$this->id;
		$transaction_row['currency_id']=$currency_id;
		$transaction_row['exchange_rate']=$exchange_rate;
		// $transaction_row['accounts_in_side']=$no_of_accounts_in_side;
		$transaction_row->save();

		$this->debitOnly($amount);
	}

	function creditWithTransaction($amount,$transaction_id,$currency_id,$exchange_rate){

		$transaction_row=$this->add('xepan\accounts\Model_TransactionRow');
		$transaction_row['_amountCr']=$amount;
		$transaction_row['side']='CR';
		$transaction_row['transaction_id']=$transaction_id;
		$transaction_row['ledger_id']=$this->id;
		$transaction_row['currency_id']=$currency_id;
		$transaction_row['exchange_rate']=$exchange_rate;
		// $transaction_row['accounts_in_side']=$no_of_accounts_in_side;
		$transaction_row->save();

		// if($only_transaction) return;
		
		$this->creditOnly($amount);
	}

	function debitOnly($amount){ 
		$this->hook('beforeLedgerDebited',array($amount));
		$this['CurrentBalanceDr']=$this['CurrentBalanceDr']+$amount;
		$this->save();
		$this->hook('afterLedgerDebited',array($amount));
	}

	function creditOnly($amount){
		$this->hook('beforeLedgerCredited',array($amount));
		$this['CurrentBalanceCr']=$this['CurrentBalanceCr']+$amount;
		$this->save();
		$this->hook('afterLedgerCredited',array($amount));
	}

	function getOpeningBalance($on_date=null,$side='both',$forPandL=false) {
		if(!$on_date) $on_date = '1970-01-02';
		if(!$this->loaded()) throw $this->exception('Model Must be loaded to get opening Balance','Logic');
		

		$transaction_row=$this->add('xepan\accounts\Model_TransactionRow');
		$transaction_join=$transaction_row->join('account_transaction.id','transaction_id');
		$transaction_join->addField('transaction_date','created_at');
		$transaction_row->addCondition('transaction_date','<',$on_date);
		$transaction_row->addCondition('ledger_id',$this->id);

		if($forPandL){
			$financial_start_date = $this->api->getFinancialYear($on_date,'start');
			$transaction_row->addCondition('created_at','>=',$financial_start_date);
		}

		$transaction_row->addExpression('sdr')->set(function($m,$q){
			return $q->expr('sum([0])',[$m->getField('amountDr')]);
		});

		$transaction_row->addExpression('scr')->set(function($m,$q){
			return $q->expr('sum([0])',[$m->getField('amountCr')]);
		});

		// $transaction_row->_dsql()->del('fields')->field('SUM(amountDr) sdr')->field('SUM(amountCr) scr');
		$result = $transaction_row->getRows();
		$result=$result[0];
		// if($this['OpeningBalanceCr'] ==null){
		// 	$temp_account = $this->add('xepan\accounts\Model_Ledger')->load($this->id);
		// 	$this['OpeningBalanceCr'] = $temp_account['OpeningBalanceCr'];
		// 	$this['OpeningBalanceDr'] = $temp_account['OpeningBalanceDr'];
		// }


		$cr = $result['scr'];
		if(!$forPandL) $cr = $cr + $this['OpeningBalanceCr'];
		if(strtolower($side) =='cr') return $cr;

		$dr = $result['sdr'];		
		if(!$forPandL) $dr = $dr + $this['OpeningBalanceDr'];
		if(strtolower($side) =='dr') return $dr;

		return array('CR'=>$cr,'DR'=>$dr,'cr'=>$cr,'dr'=>$dr,'Cr'=>$cr,'Dr'=>$dr);
	}

	function quickSearch($app,$search_string,&$result_array,$relevency_mode){

		$this->addExpression('Relevance')->set('MATCH(name, ledger_type, LedgerDisplayName) AGAINST ("'.$search_string.'" '.$relevency_mode.')');
		$this->addCondition('Relevance','>',0);
 		$this->setOrder('Relevance','Desc');
 			
 		if($this->count()->getOne()){
 			foreach ($this->getRows() as $data) {	 				 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_accounts_accounts',['status'=>$data['status']])->getURL(),
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 				];
 			}
		}

		$groups = $this->add('xepan\accounts\Model_Group');
		$groups->addExpression('Relevance')->set('MATCH(name) AGAINST ("'.$search_string.'" '.$relevency_mode.')');
		$groups->addCondition('Relevance','>',0);
 		$groups->setOrder('Relevance','Desc');
 		
 		if($groups->count()->getOne()){
 			foreach ($groups->getRows() as $data) {	 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_accounts_group')->getURL(),
 				];
 			}
		}

		$currency = $this->add('xepan\accounts\Model_Currency');
		$currency->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$search_string.'" '.$relevency_mode.')');
		$currency->addCondition('Relevance','>',0);
 		$currency->setOrder('Relevance','Desc');
 		
 		if($currency->count()->getOne()){
 			foreach ($currency->getRows() as $data) {	 				
 				$result_array[] = [
 					'image'=>null,
 					'title'=>$data['name'],
 					'relevency'=>$data['Relevance'],
 					'url'=>$this->app->url('xepan_accounts_currency')->getURL(),
 					'type_status'=>$data['type'].' '.'['.$data['status'].']',
 				];
 			}
		}
	}

	function loadDefaultLedgersReceivable(){
		$this->addCondition('name','Accounts Receivable');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadDirectIncome()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function loadDefaultSalesLedger(){
		$this->addCondition('name','Sales Account');
		$this->addCondition('ledger_type','SalesAccount');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadRootSalesGroup()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function filterSalesLedger(){
		$this->addCondition('ledger_type','SalesAccount');
		$this->addCondition('root_group_id',$this->add('xepan\accounts\Model_Group')->loadRootSalesGroup()->fieldQuery('id'));
		$this->tryLoadAny();

		return $this;
	}

	function loadDefaultPurchaseLedger(){
		$this->addCondition('name','Purchase Account');
		$this->addCondition('ledger_type','PurchaseAccount');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadRootPurchaseGroup()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;	
	}

	function filterPurchaseLedger(){
		$this->addCondition('ledger_type','PurchaseAccount');
		$this->addCondition('root_group_id',$this->add('xepan\accounts\Model_Group')->loadRootPurchaseGroup()->fieldQuery('id'));
		$this->tryLoadAny();

		return $this;
	}

	function loadDefaultRoundLedger(){
		$this->addCondition('name','Round Account');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectIncome()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function loadDefaultTaxLedger(){
		$this->addCondition('name','Tax Account');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadDutiesAndTaxes()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}


	function loadDefaultDiscountGivenLedger(){
		$this->addCondition('name','Discount Given');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadDirectExpenses()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function loadDefaultDiscountRecieveLedger(){
		$this->addCondition('name','Discount Recieve');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectIncome()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function loadDefaultShippingLedger(){
		$this->addCondition('name','Shipping Account');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectExpenses()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;	
	}

	function loadDefaultExchangeLoss(){
		$this->addCondition('name','Exchange Loss');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectExpenses()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;	
	}

	function loadDefaultExchangeGain(){
		$this->addCondition('name','Exchange Gain');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectIncome()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;	
	}

	function loadDefaultCashLedger(){
		$this->addCondition('name','Cash Account');
		$this->addCondition('ledger_type','CashAccount');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadRootCashGroup()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function filterCashLedgers(){
		$this->addCondition('ledger_type','CashAccount');
		$this->addCondition('root_group_id',$this->add('xepan\accounts\Model_Group')->loadRootCashGroup()->fieldQuery('id'));

		return $this;
	}


	function loadDefaultBankLedger(){
		// $this->addCondition('name','Your Default Bank Account');
		$this->addCondition('ledger_type','BankAccount');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadRootBankGroup()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this['name']='Your Default Bank Account';
			$this->save();
		}

		return $this;
	}

	function filterBankLedgers(){
		$this->addCondition('ledger_type','BankAccount');
		$this->addCondition('root_group_id',$this->add('xepan\accounts\Model_Group')->loadRootBankGroup()->fieldQuery('id'));

		return $this;
	}

	function loadDefaultBankChargesLedger(){
		$this->addCondition('name','Bank Charges');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectExpenses()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}

		return $this;
	}

	function filterBankCharges(){
		$this->addCondition('ledger_type','BankCharges');
		$this->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadIndirectExpenses()->fieldQuery('id'));
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this['name'] = 'Bank Charges';
			$this->save();
		}

		return $this;
	}

	function contact(){
		if($this['contact_id'])
			return $this->ref('contact_id');

		return false;
	}

	function group(){
		return $this->ref('group_id');
	}

	function isSundryDebtor(){
		return $this->group()->isSundryDebtor();
	}

	function isSundryCreditor(){
		return $this->group()->isSundryCreditor();
	}

	
}
