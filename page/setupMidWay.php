<?php

namespace xepan\commerce;


class page_setupMidWay extends \xepan\base\Page {
	
	public $title = "Setup in between Mid Session/Year";

	function init(){
		parent::init();

		$form = $this->add('Form');
		$form->addField('DatePicker','year_start_date');
		$form->addSubmit('Execute');


		if($form->isSubmitted()){
			set_time_limit(720);
			// due and paid invoice transaction created

			// truncate all tables first
			$tables = ['account_balance_sheet','account_group','account_transaction','account_transaction_row','account_transaction_types','ledger','custom_account_entries_templates','custom_account_entries_templates_transactions','custom_account_entries_templates_transaction_row','lodgement'];

			foreach ($tables as $table) {
				$this->app->db->dsql()->table($table)->truncate()->execute();
			}


			$this->add('xepan\accounts\Model_BalanceSheet')->loadDefaults();
	        $this->add('xepan\accounts\Model_Group')->loadDefaults();
	        $this->add('xepan\accounts\Model_Ledger')->loadDefaults();
			
			// Import json transaction templates
			$path=realpath(getcwd().'/vendor/xepan/accounts/defaultAccount');
			// throw new \Exception($path, 1);
			
			if(file_exists($path)){
	       		foreach (new \DirectoryIterator($path) as $file) {
	       			 if($file->isDot()) continue;
	       			// echo $path."/".$file;
	       			 $json= file_get_contents($path."/".$file);
	       			 $import_model = $this->add('xepan\accounts\Model_EntryTemplate');
	       			 $import_model->importJson($json);
	       		}
	       	}	
				
			$invoices = $this->add('xepan\commerce\Model_SalesInvoice');
			$invoices->addCondition('status',['Due','Paid']);
			$invoices->addCondition('created_at','>=',$form['year_start_date']);

			$ledger = $this->add('xepan\accounts\Model_Ledger')->load("Sales Account");

			$t = $this->app->db->dsql();
			$t->sql_templates['update']="update [table_noalias] [join] set [set] [where]";
			
			$t->table('qsp_master')->join('document','document_id')
					->set('nominal_id',$ledger->id)
					->where('type','SalesInvoice')->update()->execute();
			
			$t = $this->app->db->dsql();
			$t->table('document')
					->set('status','Due')
					->where('type','SalesInvoice')->update()->execute();

			foreach ($invoices as $inv) {
				$inv->updateTransaction();
			}

			// foreach ($this->openingBalances as $ledger_name => $balances_array) {
			// $this->add('xepan\accounts\Model_Ledger')->load($ledger_name)
			// 	->set('OpeningBalanceDr',$balances_array[0])
			// 	->set('OpeningBalanceCr',$balances_array[1])
			// 	->save();
			// }

			// foreach ($this->new_ledgers as $ledger_new) {
			// 	$ledgers = $this->add('xepan\accounts\Model_Ledger');
			// 	$ledgers['name'] = $ledger_new['name'];
			// 	$ledgers['group_id'] = $this->add('xepan\accounts\Model_Group')->load($ledger_new['group'])->get('id');
			// 	$ledgers['ledger_type'] = $ledger_new['ledger_type'];
			// 	$ledgers['OpeningBalanceCr'] = $ledger_new['OpeningBalanceCr'];
			// 	$ledgers['OpeningBalanceDr'] = $ledger_new['OpeningBalanceDr'];
			// 	$ledgers->save();
			// }


			$form->js()->reload()->univ()->successMessage('Done')->execute();
		}

	}

	// 'Ledger_name' =>[DR,CR]
	
	// public $openingBalances=[
	// 	'Cash Account' =>[24978.37,0],
	// 	'Profit & Loss (Opening)' =>[0,97009.01],
	// 	'Capital Account' =>[0,100000],
	// 	'Service Tax 14' =>[2432.47,0]
	// ];

	// public $new_ledgers = [
	// 	['name'=>'Bank OD (A/c)','group'=>'Bank OD','ledger_type'=>'Expenses','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>2014.42],
	// 	['name'=>'Gowarav Vishwakarma (Unsecured Loans)','group'=>'Loans And Advances From Related Parties (Long Term)','ledger_type'=>'Unsecured Loans','OpeningBalanceCr'=>213676,'OpeningBalanceDr'=>0],
		
	// 	['name'=>'TDS Deduction','group'=>'Tax Payable','ledger_type'=>'TDS Deduction','OpeningBalanceCr'=>4000,'OpeningBalanceDr'=>0],
		
	// 	['name'=>'Deepak Kanojia','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>210000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Nilam Joshi','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>8000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Rahul Potter Accountant','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>15000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Rakesh Sinha','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>49000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Vaibhav Sharma','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>198000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Vijay Mali','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>129000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Gowarav Vishwakarma','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>20000,'OpeningBalanceDr'=>0],
	// 	['name'=>'Priti Bhardwaj','group'=>'Sundry Creditor','ledger_type'=>'Employees','OpeningBalanceCr'=>25000,'OpeningBalanceDr'=>0],
		
	// 	['name'=>'Computer','group'=>'Computers & Printers','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>66647],
	// 	['name'=>'Printer','group'=>'Computers & Printers','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>2920],
	// 	['name'=>'Laptop Adapter','group'=>'Computers & Printers','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>303],
	// 	['name'=>'Mobile Instruments','group'=>'Computers & Printers','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>42743],
	// 	['name'=>'UPS','group'=>'Computers & Printers','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>22253],
	// 	['name'=>'USB Mouse','group'=>'Computers & Printers','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>212],
		
	// 	['name'=>'Furniture','group'=>'Furniture & Fixtures','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>13772],
	// 	['name'=>'Office Furniture','group'=>'Furniture & Fixtures','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>79316],

	// 	['name'=>'Tools & Equipments','group'=>'Office Equipment','ledger_type'=>'FixedAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>4082],

	// 	['name'=>'Accrued Interest On FDR','group'=>'Current Investments','ledger_type'=>'CurrentAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>49326],
	// 	['name'=>'FDR - ICICI Bank','group'=>'Current Investments','ledger_type'=>'CurrentAssets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>350000],

	// 	['name'=>'SIA “Clusterpoint”','group'=>'Sundry Debtor','ledger_type'=>'Customer','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>169957.39],
	// 	['name'=>'Prime Scan','group'=>'Sundry Debtor','ledger_type'=>'Customer','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>125000],
	// 	['name'=>'Where is my money','group'=>'Sundry Debtor','ledger_type'=>'Customer','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>60168.36],
		
	// 	['name'=>'MAT CREDIT FY 2014-15','group'=>'Other Current Assets','ledger_type'=>'Other Current Assets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>2200],
	// 	['name'=>'TDS - Deducted - FY 2015-16','group'=>'Other Current Assets','ledger_type'=>'Current Assets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>40360],
		
	// 	['name'=>'Premilinary Expenses','group'=>'Other Non Current Assets','ledger_type'=>'Assets','OpeningBalanceCr'=>0,'OpeningBalanceDr'=>10000]
		
	// ]; 
}