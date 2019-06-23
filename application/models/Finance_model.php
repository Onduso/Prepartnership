<?php
class Finance_model extends CI_Model {
	function __construct() {
		parent::__construct();

		$this -> load -> config('dev_config');
		$this -> get_table_prefix();
	}

	//Finance dashboard

	private $table_prefix = '';
	private function get_table_prefix() {

		$this -> table_prefix = $this -> config -> item('table_prefix');

		return $this -> table_prefix;
	}

	private function prod_fcps_with_risk_model() {

		$fcp_array = array();

		$data = $this -> db -> get($this -> table_prefix . 'projectsdetails') -> result_array();

		foreach ($data as $fcp) {

			$fcp_array[$fcp['ID']]['fcp_id'] = $fcp['icpNo'];
			$fcp_array[$fcp['ID']]['risk'] = $fcp['risk'];
		}

		return $fcp_array;
	}

	private function test_fcps_with_risk_model() {

		$fcp_array = array();

		//KE0200 array
		$fcp_array[1]['fcp_id'] = 'KE0200';
		$fcp_array[1]['risk'] = 'High';

		//KE0215 array
		$fcp_array[2]['fcp_id'] = 'KE0215';
		$fcp_array[2]['risk'] = 'Low';

		//KE0300 array
		$fcp_array[3]['fcp_id'] = 'KE0300';
		$fcp_array[3]['risk'] = 'Medium';

		//KE0320 array
		$fcp_array[4]['fcp_id'] = 'KE0320';
		$fcp_array[4]['risk'] = 'High';

		//KE0540 array
		$fcp_array[5]['fcp_id'] = 'KE0540';
		$fcp_array[5]['risk'] = 'Medium';

		return $fcp_array;
	}

	private function prod_parameter_model() {
		$dashboard_params = array();

		$data = $this -> db -> get($this -> table_prefix . 'dashboard_parameter') -> result_array();

		foreach ($data as $parameter) {

			$dashboard_params[$parameter['dashboard_parameter_id']]['dashboard_parameter_name'] = $parameter['dashboard_parameter_name'];
			$dashboard_params[$parameter['dashboard_parameter_id']]['result_method'] = $parameter['result_method'];
			$dashboard_params[$parameter['dashboard_parameter_id']]['is_requested'] = $parameter['is_requested'];
			$dashboard_params[$parameter['dashboard_parameter_id']]['display_on_dashboard'] = $parameter['display_on_dashboard'];
		}

		return $dashboard_params;
	}

	private function test_parameter_model() {
		$dashboard_params = array();

		$dashboard_params[1]['dashboard_parameter_name'] = 'MFR Submitted';
		$dashboard_params[1]['result_method'] = 'has_mfr_submitted';
		$dashboard_params[1]['is_requested'] = 'no';
		$dashboard_params[1]['display_on_dashboard'] = 'yes';

		$dashboard_params[2]['dashboard_parameter_name'] = 'Bank Statement uploaded';
		$dashboard_params[2]['result_method'] = 'has_bank_statement_uploaded';
		$dashboard_params[2]['is_requested'] = 'no';
		$dashboard_params[2]['display_on_dashboard'] = 'yes';

		$dashboard_params[3]['dashboard_parameter_name'] = 'Book Bank Balance';
		$dashboard_params[3]['result_method'] = 'compute_book_bank_balance';
		$dashboard_params[3]['is_requested'] = 'no';
		$dashboard_params[3]['display_on_dashboard'] = 'no';

		$dashboard_params[4]['dashboard_parameter_name'] = 'Statement Bank Balance';
		$dashboard_params[4]['result_method'] = 'compute_statement_bank_balance';
		$dashboard_params[4]['is_requested'] = 'no';
		$dashboard_params[4]['display_on_dashboard'] = 'no';

		$dashboard_params[5]['dashboard_parameter_name'] = 'Oustanding Cheques';
		$dashboard_params[5]['result_method'] = 'compute_outstanding_cheques';
		$dashboard_params[5]['is_requested'] = 'no';
		$dashboard_params[5]['display_on_dashboard'] = 'no';

		$dashboard_params[6]['dashboard_parameter_name'] = 'Deposit in transit';
		$dashboard_params[6]['result_method'] = 'compute_deposit_in_transit';
		$dashboard_params[6]['is_requested'] = 'no';
		$dashboard_params[6]['display_on_dashboard'] = 'no';

		$dashboard_params[7]['dashboard_parameter_name'] = 'Bank Reconciliation';
		$dashboard_params[7]['result_method'] = 'check_bank_reconcile_correct';
		$dashboard_params[7]['is_requested'] = 'no';
		$dashboard_params[7]['display_on_dashboard'] = 'yes';

		$dashboard_params[8]['dashboard_parameter_name'] = 'Confirm Petty Cash';
		$dashboard_params[8]['result_method'] = 'confirm_petty_cash';
		$dashboard_params[8]['is_requested'] = 'yes';
		$dashboard_params[8]['display_on_dashboard'] = 'yes';

		return $dashboard_params;
	}

	private function dashboard_parameters() {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_parameter_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_parameter_model();
		}

	}

	//We will have to pass month aurgumet in prod models
	private function prod_mfr_submission_data_model($month) {
		$mfr_submission_data = array();

		$data = $this -> db -> get_where($this -> table_prefix . 'opfundsbalheader', array('closureDate' => $month)) -> result_array();

		foreach ($data as $mfr_submission) {

			$mfr_submission_data[$mfr_submission['balHdID']]['fcp_id'] = $mfr_submission['icpNo'];
			$mfr_submission_data[$mfr_submission['balHdID']]['closure_date'] = $mfr_submission['closureDate'];
			$mfr_submission_data[$mfr_submission['balHdID']]['submitted'] = $mfr_submission['submitted'];
			$mfr_submission_data[$mfr_submission['balHdID']]['submission_date'] = $mfr_submission['stmp'];
		}

		return $mfr_submission_data;
	}

	private function test_mfr_submission_data_model() {

		$mfr_submission_data = array();

		//KE0200 array
		$mfr_submission_data[1]['fcp_id'] = 'KE0200';
		$mfr_submission_data[1]['closure_date'] = '2019-03-31';
		$mfr_submission_data[1]['submitted'] = 1;
		$mfr_submission_data[1]['submission_date'] = '2019-04-05';

		//KE0215 array
		$mfr_submission_data[2]['fcp_id'] = 'KE0215';
		$mfr_submission_data[2]['closure_date'] = '2019-03-31';
		$mfr_submission_data[2]['submitted'] = 0;
		$mfr_submission_data[2]['submission_date'] = '2019-04-10';

		//KE0300 array
		$mfr_submission_data[3]['fcp_id'] = 'KE0300';
		$mfr_submission_data[3]['closure_date'] = '2019-03-31';
		$mfr_submission_data[3]['submitted'] = 1;
		$mfr_submission_data[3]['submission_date'] = '2019-04-02';

		//KE0320 array
		$mfr_submission_data[4]['fcp_id'] = 'KE0320';
		$mfr_submission_data[4]['closure_date'] = '2019-03-31';
		$mfr_submission_data[4]['submitted'] = 1;
		$mfr_submission_data[4]['submission_date'] = '2019-04-03';

		//KE0540 array
		$mfr_submission_data[5]['fcp_id'] = 'KE0540';
		$mfr_submission_data[5]['closure_date'] = '2019-03-31';
		$mfr_submission_data[5]['submitted'] = 0;
		$mfr_submission_data[5]['submission_date'] = '2019-07-04';

		return $mfr_submission_data;
	}

	private function switch_environ_mfr_submission_data($month) {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_mfr_submission_data_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_mfr_submission_data_model($month);
		}

	}

	private function prod_bank_statement_uploaded_data_model($month_bank_statement_uploaded) {

		$files = array();
		try {
			$dir_path = 'uploads/bank_statements';
			$dir = new DirectoryIterator($dir_path);

			$counter = 1;

			foreach ($dir as $fileinfo) {
				if (!$fileinfo -> isDot()) {

					$file_path = $dir_path . '/' . $fileinfo -> getFilename() . '/' . date('Y-m', strtotime($month_bank_statement_uploaded));

					$yes_no_flag = false;

					if (file_exists($file_path)) {

						if ($this -> checkFolderIsEmptyOrNot($file_path)) {
							$yes_no_flag = true;
						}
					}

					$files[$counter]['fcp_id'] = $fileinfo -> getFilename();
					$files[$counter]['file_exists'] = $yes_no_flag;
					$files[$counter]['closure_date'] = $month_bank_statement_uploaded;

					$counter++;

				}
			}
		} catch(Exception $e) {

		}

		return $files;

	}

	private function test_bank_statement_uploaded_data_model() {

		$bank_statement_uploaded_data = array();

		//KE0200 array
		$bank_statement_uploaded_data[1]['fcp_id'] = 'KE0200';
		$bank_statement_uploaded_data[1]['file_exists'] = true;
		$bank_statement_uploaded_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$bank_statement_uploaded_data[2]['fcp_id'] = 'KE0215';
		$bank_statement_uploaded_data[2]['file_exists'] = false;
		$bank_statement_uploaded_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$bank_statement_uploaded_data[3]['fcp_id'] = 'KE0300';
		$bank_statement_uploaded_data[3]['file_exists'] = false;
		$bank_statement_uploaded_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$bank_statement_uploaded_data[4]['fcp_id'] = 'KE0320';
		$bank_statement_uploaded_data[4]['file_exists'] = true;
		$bank_statement_uploaded_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$bank_statement_uploaded_data[5]['fcp_id'] = 'KE0540';
		$bank_statement_uploaded_data[5]['file_exists'] = true;
		$bank_statement_uploaded_data[5]['closure_date'] = '2019-03-31';

		return $bank_statement_uploaded_data;

	}

	private function switch_environ_deposit_in_transit_data($month) {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_deposit_in_transit_data_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_deposit_in_transit_data_model($month);
		}
	}

	private function switch_environ_outstanding_cheques_data($month) {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_outstanding_cheques_data_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_outstanding_cheques_data_model($month);
		}
	}

	private function transactions_raised_in_past_not_cleared($vtype, $month, $amount_key = "outstanding_cheque_amount") {
		$transaction_array = array();

		$first_day_of_month = date('Y-m-01', strtotime($month));
		$last_day_of_month = date('Y-m-t', strtotime($month));

		$this -> db -> select_sum('totals');
		$this -> db -> select(array('hID', 'icpNo', 'TDate', 'ChqState', 'clrMonth', 'VType'));
		$this -> db -> group_by(array('VType', 'icpNo'));
		$this -> db -> where(array('TDate<' => $first_day_of_month));
		//In Between condition
		$data = $this -> db -> get_where($this -> table_prefix . 'voucher_header', array('VType' => $vtype, 'ChqState' => 0, 'clrMonth' => '0000-00-00')) -> result_array();

		foreach ($data as $transaction) {

			$transaction_array[$transaction['hID']]['fcp_id'] = $transaction['icpNo'];
			$transaction_array[$transaction['hID']]['closure_date'] = $transaction['TDate'];
			$transaction_array[$transaction['hID']][$amount_key] = $transaction['totals'];

		}

		return $transaction_array;
	}

	private function transactions_raised_in_month_not_cleared($vtype, $month, $amount_key = "outstanding_cheque_amount") {
		$transaction_array = array();

		$first_day_of_month = date('Y-m-01', strtotime($month));
		$last_day_of_month = date('Y-m-t', strtotime($month));

		$this -> db -> select_sum('totals');
		$this -> db -> select(array('hID', 'icpNo', 'TDate', 'ChqState', 'clrMonth', 'VType'));
		$this -> db -> group_by(array('VType', 'icpNo'));
		$this -> db -> where(array('TDate>=' => $first_day_of_month, 'TDate<=' => $last_day_of_month));
		//In Between condition
		$data = $this -> db -> get_where($this -> table_prefix . 'voucher_header', array('VType' => $vtype, 'ChqState' => 0, 'clrMonth' => '0000-00-00')) -> result_array();

		foreach ($data as $transaction) {

			$transaction_array[$transaction['hID']]['fcp_id'] = $transaction['icpNo'];
			$transaction_array[$transaction['hID']]['closure_date'] = $transaction['TDate'];
			$transaction_array[$transaction['hID']][$amount_key] = $transaction['totals'];

		}

		return $transaction_array;
	}

	private function transactions_raised_in_month_cleared_in_future($vtype, $month, $amount_key = "outstanding_cheque_amount") {
		$transaction_array = array();

		$first_day_of_month = date('Y-m-01', strtotime($month));
		$last_day_of_month = date('Y-m-t', strtotime($month));

		$this -> db -> select_sum('totals');
		$this -> db -> select(array('hID', 'icpNo', 'TDate', 'ChqState', 'clrMonth', 'VType'));
		$this -> db -> group_by(array('VType', 'icpNo'));
		$this -> db -> where(array('TDate>=' => $first_day_of_month, 'TDate<=' => $last_day_of_month));
		//In Between condition
		$data = $this -> db -> get_where($this -> table_prefix . 'voucher_header', array('VType' => $vtype, 'ChqState' => 1, 'clrMonth >' => $last_day_of_month)) -> result_array();

		foreach ($data as $transaction) {

			$transaction_array[$transaction['hID']]['fcp_id'] = $transaction['icpNo'];
			$transaction_array[$transaction['hID']]['closure_date'] = $transaction['TDate'];
			$transaction_array[$transaction['hID']][$amount_key] = $transaction['totals'];

		}

		return $transaction_array;
	}

	private function transactions_raised_in_past_cleared_in_future($vtype, $month, $amount_key = "outstanding_cheque_amount") {
		$transaction_array = array();

		$first_day_of_month = date('Y-m-01', strtotime($month));
		$last_day_of_month = date('Y-m-t', strtotime($month));

		$this -> db -> select_sum('totals');
		$this -> db -> select(array('hID', 'icpNo', 'TDate', 'ChqState', 'clrMonth', 'VType'));
		$this -> db -> group_by(array('VType', 'icpNo'));
		$this -> db -> where(array('TDate<' => $first_day_of_month));
		//In Between condition
		$data = $this -> db -> get_where($this -> table_prefix . 'voucher_header', array('VType' => $vtype, 'ChqState' => 1, 'clrMonth >' => $last_day_of_month)) -> result_array();

		foreach ($data as $transaction) {

			$transaction_array[$transaction['hID']]['fcp_id'] = $transaction['icpNo'];
			$transaction_array[$transaction['hID']]['closure_date'] = $transaction['TDate'];
			$transaction_array[$transaction['hID']][$amount_key] = $transaction['totals'];

		}

		return $transaction_array;
	}

	private function prod_outstanding_cheques_data_model($month) {
		
		$transaction_arrays = array();

		$transactions_raised_in_month_not_cleared = $this -> transactions_raised_in_month_not_cleared('CHQ', $month, 'outstanding_cheque_amount');
		$transactions_raised_in_past_not_cleared = $this -> transactions_raised_in_past_not_cleared('CHQ', $month, 'outstanding_cheque_amount');
		$transactions_raised_in_month_cleared_in_future = $this -> transactions_raised_in_month_cleared_in_future('CHQ', $month, 'outstanding_cheque_amount');
		$transactions_raised_in_past_cleared_in_future = $this -> transactions_raised_in_past_cleared_in_future('CHQ', $month, 'outstanding_cheque_amount');

		$fcps_array = array_column($this -> prod_fcps_with_risk_model(), 'fcp_id');

		$merge_array = array();

		$merge_array = array_merge($transactions_raised_in_month_not_cleared, $transactions_raised_in_past_not_cleared, $transactions_raised_in_month_cleared_in_future, $transactions_raised_in_past_cleared_in_future);
		$cnt = 0;

		foreach ($fcps_array as $fcp) {
			$sum_fcp_deposit_in_transit = 0;
			foreach ($merge_array as $transaction) {
				if ($fcp == $transaction['fcp_id']) {
					$sum_fcp_deposit_in_transit += $transaction['outstanding_cheque_amount'];
					$transaction_arrays[$cnt]['fcp_id'] = $transaction['fcp_id'];
					$transaction_arrays[$cnt]['closure_date'] = $month;
					$transaction_arrays[$cnt]['outstanding_cheque_amount'] = $sum_fcp_deposit_in_transit;
				}
			}
			$cnt++;
		}

		return $transaction_arrays;
	}

	private function test_outstanding_cheques_data_model() {

		$outstanding_cheques_data = array();

		//KE0200 array
		$outstanding_cheques_data[1]['fcp_id'] = 'KE0200';
		$outstanding_cheques_data[1]['outstanding_cheque_amount'] = 300000.89;
		$outstanding_cheques_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$outstanding_cheques_data[2]['fcp_id'] = 'KE0215';
		$outstanding_cheques_data[2]['outstanding_cheque_amount'] = 17789.34;
		$outstanding_cheques_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$outstanding_cheques_data[3]['fcp_id'] = 'KE0300';
		$outstanding_cheques_data[3]['outstanding_cheque_amount'] = 889750.23;
		$outstanding_cheques_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$outstanding_cheques_data[4]['fcp_id'] = 'KE0320';
		$outstanding_cheques_data[4]['outstanding_cheque_amount'] = 435678.00;
		$outstanding_cheques_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$outstanding_cheques_data[5]['fcp_id'] = 'KE0540';
		$outstanding_cheques_data[5]['outstanding_cheque_amount'] = 29879.70;
		$outstanding_cheques_data[5]['closure_date'] = '2019-03-31';

		return $outstanding_cheques_data;

	}

	private function switch_environ_statement_bank_balance_data($month) {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_statement_bank_balance_data_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_statement_bank_balance_data_model($month);
		}
	}

	private function prod_statement_bank_balance_data_model($month) {

		$statement_bank_balance_data = array();

		$data = $this -> db -> get_where($this -> table_prefix . 'statementbal', array('month' => $month)) -> result_array();

		foreach ($data as $statement_balance) {

			$statement_bank_balance_data[$statement_balance['balID']]['fcp_id'] = $statement_balance['icpNo'];
			$statement_bank_balance_data[$statement_balance['balID']]['closure_date'] = $statement_balance['month'];
			$statement_bank_balance_data[$statement_balance['balID']]['statement_amount'] = $statement_balance['amount'];

		}
		return $statement_bank_balance_data;
	}

	private function test_statement_bank_balance_data_model() {

		$statement_bank_balance_data = array();

		//KE0200 array
		$statement_bank_balance_data[1]['fcp_id'] = 'KE0200';
		$statement_bank_balance_data[1]['statement_amount'] = 23998.90;
		$statement_bank_balance_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$statement_bank_balance_data[2]['fcp_id'] = 'KE0215';
		$statement_bank_balance_data[2]['statement_amount'] = 100298.60;
		$statement_bank_balance_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$statement_bank_balance_data[3]['fcp_id'] = 'KE0300';
		$statement_bank_balance_data[3]['statement_amount'] = 1619643.16;
		$statement_bank_balance_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$statement_bank_balance_data[4]['fcp_id'] = 'KE0320';
		$statement_bank_balance_data[4]['statement_amount'] = 238989.71;
		$statement_bank_balance_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$statement_bank_balance_data[5]['fcp_id'] = 'KE0540';
		$statement_bank_balance_data[5]['statement_amount'] = 97600.81;
		$statement_bank_balance_data[5]['closure_date'] = '2019-03-31';

		return $statement_bank_balance_data;

	}

	private function prod_deposit_in_transit_data_model($month) {

		$transaction_arrays = array();

		$transactions_raised_in_month_not_cleared = $this -> transactions_raised_in_month_not_cleared('CR', $month, 'deposit_in_transit_amount');
		$transactions_raised_in_past_not_cleared = $this -> transactions_raised_in_past_not_cleared('CR', $month, 'deposit_in_transit_amount');
		$transactions_raised_in_month_cleared_in_future = $this -> transactions_raised_in_month_cleared_in_future('CR', $month, 'deposit_in_transit_amount');
		$transactions_raised_in_past_cleared_in_future = $this -> transactions_raised_in_past_cleared_in_future('CR', $month, 'deposit_in_transit_amount');

		$fcps_array = array_column($this -> prod_fcps_with_risk_model(), 'fcp_id');

		$merge_array = array();

		$merge_array = array_merge($transactions_raised_in_month_not_cleared, $transactions_raised_in_past_not_cleared, $transactions_raised_in_month_cleared_in_future, $transactions_raised_in_past_cleared_in_future);
		$cnt = 0;

		foreach ($fcps_array as $fcp) {

			$sum_fcp_deposit_in_transit = 0;
			foreach ($merge_array as $transaction) {
				if ($fcp == $transaction['fcp_id']) {
					$sum_fcp_deposit_in_transit += $transaction['deposit_in_transit_amount'];
					$transaction_arrays[$cnt]['fcp_id'] = $transaction['fcp_id'];
					$transaction_arrays[$cnt]['closure_date'] = $month;
					$transaction_arrays[$cnt]['deposit_in_transit_amount'] = $sum_fcp_deposit_in_transit;
				}
			}
			$cnt++;
		}

		return $transaction_arrays;
	}

	private function test_deposit_in_transit_data_model() {

		$deposit_in_transit_data = array();

		//KE0200 array
		$deposit_in_transit_data[1]['fcp_id'] = 'KE0200';
		$deposit_in_transit_data[1]['deposit_in_transit_amount'] = 3330.49;
		$deposit_in_transit_data[1]['closure_date'] = '2019-03-31';

		//KE0215 array
		$deposit_in_transit_data[2]['fcp_id'] = 'KE0215';
		$deposit_in_transit_data[2]['deposit_in_transit_amount'] = 8987.29;
		$deposit_in_transit_data[2]['closure_date'] = '2019-03-31';

		//KE0300 array
		$deposit_in_transit_data[3]['fcp_id'] = 'KE0300';
		$deposit_in_transit_data[3]['deposit_in_transit_amount'] = 27987.19;
		$deposit_in_transit_data[3]['closure_date'] = '2019-03-31';

		//KE0320 array
		$deposit_in_transit_data[4]['fcp_id'] = 'KE0320';
		$deposit_in_transit_data[4]['deposit_in_transit_amount'] = 4098.89;
		$deposit_in_transit_data[4]['closure_date'] = '2019-03-31';

		//KE0540 array
		$deposit_in_transit_data[5]['fcp_id'] = 'KE0540';
		$deposit_in_transit_data[5]['deposit_in_transit_amount'] = 40456.89;
		$deposit_in_transit_data[5]['closure_date'] = '2019-03-31';

		return $deposit_in_transit_data;

	}

	private function switch_environ_book_bank_cash_balance_data($month) {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_book_bank_cash_balance_data_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_book_bank_cash_balance_data_model($month);
		}
	}

	private function prod_book_bank_cash_balance_data_model($month) {

		$bank_cash_balance_data = array();

		$data = $this -> db -> get_where($this -> table_prefix . 'cashbal', array('month' => $month)) -> result_array();

		foreach ($data as $cash_balance) {
			//Populate Cash Balances
			if ($cash_balance['accNo'] == 'BC') {
				$bank_cash_balance_data[$cash_balance['balID']]['fcp_id'] = $cash_balance['icpNo'];
				$bank_cash_balance_data[$cash_balance['balID']]['closure_date'] = $cash_balance['month'];
				$bank_cash_balance_data[$cash_balance['balID']]['account_type'] = $cash_balance['accNo'];
				$bank_cash_balance_data[$cash_balance['balID']]['balance_amount'] = $cash_balance['amount'];
			}

		}

		return $bank_cash_balance_data;
	}

	private function test_book_bank_cash_balance_data_model() {

		$bank_cash_balance_data = array();

		//KE0200 array
		$bank_cash_balance_data[1]['fcp_id'] = 'KE0200';
		$bank_cash_balance_data[1]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[1]['account_type'] = 'BC';
		$bank_cash_balance_data[1]['balance_amount'] = 12509.60;

		//KE0215 array
		$bank_cash_balance_data[2]['fcp_id'] = 'KE0215';
		$bank_cash_balance_data[2]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[2]['account_type'] = 'BC';
		$bank_cash_balance_data[2]['balance_amount'] = 10000300.52;

		//KE0300 array
		$bank_cash_balance_data[3]['fcp_id'] = 'KE0300';
		$bank_cash_balance_data[3]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[3]['account_type'] = 'BC';
		$bank_cash_balance_data[3]['balance_amount'] = 757880.12;

		//KE0320 array
		$bank_cash_balance_data[4]['fcp_id'] = 'KE0320';
		$bank_cash_balance_data[4]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[4]['account_type'] = 'BC';
		$bank_cash_balance_data[4]['balance_amount'] = 376898.02;

		//KE0540 array
		$bank_cash_balance_data[5]['fcp_id'] = 'KE0540';
		$bank_cash_balance_data[5]['closure_date'] = '2019-03-31';
		$bank_cash_balance_data[5]['account_type'] = 'BC';
		$bank_cash_balance_data[5]['balance_amount'] = 476987.00;

		return $bank_cash_balance_data;

	}

	private function group_data_by_fcp_id($database_results) {

		$group_by_fcp_id_array = array();

		foreach ($database_results as $row) {

			if (isset($row['fcp_id'])) {
				$group_by_fcp_id_array[$row['fcp_id']] = $row;
			}

		}

		return $group_by_fcp_id_array;
	}

	private function has_mfr_submitted($fcp, $month_submitted) {

		$mfr_submitted_data = $this -> switch_environ_mfr_submission_data($month_submitted);

		$group = $this -> group_data_by_fcp_id($mfr_submitted_data);

		$yes_no_flag = 'No';

		//Check if the fcp has an Mfr submitted in the $month_submitted
		if (isset($group[$fcp])) {
			if ($group[$fcp]['closure_date'] == $month_submitted && $group[$fcp]['submitted'] == 1) {
				$yes_no_flag = 'Yes';
			}

		}
		return $yes_no_flag;
	}

	private function switch_environ_bank_statement_uploaded($month) {

		if ($this -> config -> item('environment') == 'test') {
			return $this -> test_bank_statement_uploaded_data_model();
		} elseif ($this -> config -> item('environment') == 'prod') {

			return $this -> prod_bank_statement_uploaded_data_model($month);
		}
	}

	private function has_bank_statement_uploaded($fcp, $month_uploaded) {

		$bank_statement_submitted = $this -> switch_environ_bank_statement_uploaded($month_uploaded);

		//$bank_statement_submitted = $this -> test_bank_statement_uploaded_data_model();

		$group = $this -> group_data_by_fcp_id($bank_statement_submitted);

		$yes_no_flag = 'No';

		//Check if the fcp has an Mfr submitted in the $month_submitted
		if (isset($group[$fcp]['closure_date'])) {
			if ($group[$fcp]['closure_date'] == $month_uploaded) {

				$yes_no_flag = $group[$fcp]['file_exists'] ? 'Yes' : 'No';
			}
		}

		return $yes_no_flag;
	}

	private function compute_book_bank_balance($fcp, $month_computed) {

		$bank_cash_balance_data = $this -> switch_environ_book_bank_cash_balance_data($month_computed);

		$group = $this -> group_data_by_fcp_id($bank_cash_balance_data);

		$balance_amount = 0.00;

		//Check if the fcp has an Mfr submitted in the $month_submitted
		if (isset($group[$fcp])) {
			if ($group[$fcp]['closure_date'] == $month_computed && $group[$fcp]['account_type'] == 'BC') {

				$balance_amount = $group[$fcp]['balance_amount'];
			}
		}

		return number_format($balance_amount, 2);
	}

	private function compute_statement_bank_balance($fcp, $month_computed) {

		$statement_bank_balance_data = $this -> switch_environ_statement_bank_balance_data($month_computed);

		$statement_bank_balance_amount = 0.00;

		$group = $this -> group_data_by_fcp_id($statement_bank_balance_data);

		//Check if the fcp has an Mfr submitted in the $month_submitted
		if (isset($group[$fcp])) {
			if ($group[$fcp]['closure_date'] == $month_computed) {

				$statement_bank_balance_amount = $group[$fcp]['statement_amount'];
			}
		}

		return number_format($statement_bank_balance_amount, 2);
	}

	private function compute_outstanding_cheques($fcp, $month) {

		$outstanding_cheques_data = $this -> switch_environ_outstanding_cheques_data($month);

		$outstanding_cheques_amount = 0.00;

		$group = $this -> group_data_by_fcp_id($outstanding_cheques_data);

		//Check if the fcp has an Mfr submitted in the $month_submitted
		if (isset($group[$fcp])) {
			if ($group[$fcp]['closure_date'] == $month) {

				$outstanding_cheques_amount = $group[$fcp]['outstanding_cheque_amount'];
			}
		}

		return number_format($outstanding_cheques_amount, 2);
	}

	private function compute_deposit_in_transit($fcp, $month) {

		$deposit_in_transit_data = $this -> switch_environ_deposit_in_transit_data($month);

		$deposit_in_transit_amount = 0.00;

		$group = $this -> group_data_by_fcp_id($deposit_in_transit_data);

		//Check if the fcp has an Mfr submitted in the $month_submitted
		if (isset($group[$fcp])) {
			if ($group[$fcp]['closure_date'] == $month) {

				$deposit_in_transit_amount = $group[$fcp]['deposit_in_transit_amount'];
			}
		}

		return number_format($deposit_in_transit_amount, 2);
	}

	private function check_bank_reconcile_correct($fcp, $month) {

		$book_bank_balance = str_replace(',', '', $this -> compute_book_bank_balance($fcp, $month));

		$statement_balance = str_replace(',', '', $this -> compute_statement_bank_balance($fcp, $month));

		$outstanding_cheques = str_replace(',', '', $this -> compute_outstanding_cheques($fcp, $month));

		$deposit_in_transit = str_replace(',', '', $this -> compute_deposit_in_transit($fcp, $month));

		$compute_bank_reconcile = ($book_bank_balance + $outstanding_cheques) - $deposit_in_transit;

		$yes_no_flag = 'No';

		if (round($compute_bank_reconcile, 2) == round($statement_balance, 2)) {

			$yes_no_flag = 'Yes';
		}

		return $yes_no_flag;
	}

	private function confirm_petty_cash($fcp, $month) {
		return 'Yes';
	}

	public function build_dashboard_array($dashboard_month) {

		$fcps_array_with_risk = '';

		if ($this -> config -> item('environment') == 'test') {
			$fcps_array_with_risk = $this -> test_fcps_with_risk_model();
		} elseif ($this -> config -> item('environment') == 'prod') {
			$fcps_array_with_risk = $this -> prod_fcps_with_risk_model();
		}

		$parameters_array = $this -> dashboard_parameters();

		$final_grid_array = array();

		$final_grid_array['fcps_with_risks'] = array();

		$final_grid_array['parameters'] = array();

		foreach ($fcps_array_with_risk as $fcp_with_risk) {

			$final_grid_array['fcps_with_risks'][$fcp_with_risk['fcp_id']]['risk'] = $fcp_with_risk['risk'];

			foreach ($parameters_array as $key => $value) {

				if ($value['display_on_dashboard'] == 'yes') {

					$final_grid_array['fcps_with_risks'][$fcp_with_risk['fcp_id']]['params'][$key] = call_user_func(array($this, $value['result_method']), $fcp_with_risk['fcp_id'], $dashboard_month);
				}
			}

		}

		foreach ($parameters_array as $key => $value) {
			if ($value['display_on_dashboard'] == 'yes') {
				$final_grid_array['parameters'][$value['is_requested']][$key] = $value['dashboard_parameter_name'];
			}

		}

		return $final_grid_array;
	}

	private function checkFolderIsEmptyOrNot($folderName) {
		$files = array();
		if ($handle = opendir($folderName)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..")
					$files[] = $file;
				if (count($files) >= 1)
					break;
			}
			closedir($handle);
		}
		return (count($files) > 0) ? TRUE : FALSE;
	}

}


