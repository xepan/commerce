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
			set_time_limit(120);
			// due and paid invoice transaction created

			// truncate all tables first
			$tables = ['account_balance_sheet','account_group','account_transaction','account_transaction_row','account_transaction_types','ledger','custom_account_entries_templates','custom_account_entries_templates_transactions','custom_account_entries_templates_transaction_row'];

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
					->set('status','Due')
					->where('type','SalesInvoice')->update()->execute();

			foreach ($invoices as $inv) {
				$inv->updateTransaction();
			}

			$form->js()->reload()->univ()->successMessage('Done')->execute();
		}

	}
}