<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/*
 *	@author 	: CI Africa Development Team
 *	date		: 16th March, 2019
 *	Prepartnership Assessment System
 *	https://www.compassionkenya.com
 *	support@compassionkenya.com
 */

class Leads extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> database();
		$this -> load -> library('session');
		$this -> session -> set_userdata('view_type', 'leads');

		$this -> load -> library('grocery_CRUD');

		/*cache control*/
		//
		$this -> output -> set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this -> output -> set_header('Pragma: no-cache');
	}

	function active_leads_information() {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$crud = new grocery_CRUD();
		$crud -> set_theme('tablestrap');
		$crud -> set_table('leads_bio_information');

		$status = 'Active';

		//Required Fields
		$required_array = array('lead_status');

		$crud -> unset_add_fields('lead_status');

		$required_fields = $this -> db -> get_where('lead_bio_fields', array('is_field_null' => 0));

		if ($required_fields -> num_rows() > 0) {
			$columns = array_column($required_fields -> result_array(), 'lead_bio_info_column');
			$required_array = array_merge($required_array, $columns);
		}

		$crud -> required_fields($required_array);

		//Modified by onduso****************************************************************

		//Dropdown Status
		$crud -> field_type('lead_status', 'dropdown', array('Active' => 'Active', 'Closed' => 'Closed'));

		//Get field to display on the bio form
		$fields_to_display_on_bio_form = $this -> db -> get_where('lead_bio_fields', array('show_field' => 1));

		if ($fields_to_display_on_bio_form -> num_rows() > 0) {

			$columns = array_column($fields_to_display_on_bio_form -> result_array(), 'lead_bio_info_column');

			$crud -> add_fields($columns);

			//Add the 'lead_status_field on EDIT form
			array_push($columns, 'lead_status');

			$crud -> edit_fields($columns);
		}

		//End of modification *******************************************************************

		//$crud -> fields('milestone_name', 'milestones_insert_after_id', 'assessment_period_in_days', 'user_customized_review_status', 'assessment_review_status');

		//$crud -> field_type('lead_status','invisible');

		//$crud->edit_fields('lead_status');

		//Relationship
		$crud -> set_relation('assessment_milestones_id', 'assessment_milestones', 'milestone_name');

		//Display in Human Readable
		$crud -> display_as('assessment_milestones_id', get_phrase('assessment'));

		//Hide Assessment Milestone id field on add form
		$crud -> unset_fields(array('assessment_milestones_id'));

		//Unset delete and Edit
		$crud -> unset_delete();

		//Add a leads assessment action button
		$crud -> add_action(get_phrase('assess_lead'), '', 'leads/lead_assessment', 'fa-book');

		//Callback
		$crud -> callback_after_insert(array($this, 'insert_assessment_milestone_id'));
		$crud -> callback_after_insert(array($this,'update_lead_status_to_active'));

		$output = $crud -> render();
		$page_data['page_name'] = 'leads_information';
		$page_data['view_type'] = 'leads';
		$page_data['page_title'] = get_phrase('leads_bio_information') . " : " . get_phrase($status);
		$output = array_merge($page_data, (array)$output);

		$this -> load -> view('backend/index', $output);
	}

	//Added by Onduso
	function update_lead_status_to_active($primary_key) {

		$data['lead_status'] = 1;

		$this -> db -> where(array('leads_bio_information_id' => $primary_key));
		$this -> db -> update('lead_status', $data);

		return true;
	}

	//END

	function insert_assessment_milestone_id($post_array, $primary_key) {

		$first_milestone = $this -> db -> get_where('assessment_milestones', array('milestones_insert_after_id' => '1')) -> row();
		$data['assessment_milestones_id'] = $first_milestone -> assessment_milestones_id;

		$this -> db -> where(array('leads_bio_information_id' => $primary_key));
		$this -> db -> update('leads_bio_information', $data);

		return true;
	}

	function closed_leads_information() {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$crud = new grocery_CRUD();
		$crud -> set_theme('tablestrap');
		$crud -> set_table('leads_bio_information');

		$status = 'Closed';

		//Status filter
		$crud -> where(array('lead_status' => $status));

		//Dropdown Status
		$crud -> field_type('lead_status', 'dropdown', array('Active' => 'Active', 'Closed' => 'Closed'));

		//Relationship
		$crud -> set_relation('assessment_milestones_id', 'assessment_milestones', 'milestone_name');

		//Display in Human Readable
		$crud -> display_as('assessment_id', get_phrase('assessment'));

		//Unset delete and Edit
		$crud -> unset_delete();
		$crud -> unset_add();
		
		//On some admin roll to be able to edit and reopen the closed assessment
		if ($this -> session -> userdata('role_id') != 1) {
			$crud -> unset_edit();
		}

		$output = $crud -> render();
		$page_data['page_name'] = 'leads_information';
		$page_data['view_type'] = 'leads';
		$page_data['page_title'] = get_phrase('leads_bio_information') . " : " . get_phrase($status);
		$output = array_merge($page_data, (array)$output);

		$this -> load -> view('backend/index', $output);
	}

	function lead_assessment($lead_id = '') {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		//get the keys that exists in the array comming from lead_bio_inform
		//Get the lead bio info from table

		$fields_to_show_array = $this -> crud_model -> get_fields_to_display();
		$lead_bio_data = $this -> crud_model -> get_lead_bio_info_as_a_row($lead_id);

		$make_keys_to_value = array_flip($fields_to_show_array);

		//Build an value and key pair
		$build_data_array = array();

		foreach ($make_keys_to_value as $key => $field) {
			if (array_key_exists($key, $lead_bio_data)) {

				$explode_field_name = explode("_", $key);

				$implode_to_human_readable = implode(" ", $explode_field_name);

				$build_data_array[$implode_to_human_readable] = $lead_bio_data -> $key;
			}

		}
		//slice the array to group them in 3 columns on the lead_assessment view

		$human_readable_output = array_chunk($build_data_array, 3, true);

		$page_data['page_name'] = 'lead_assessment';
		$page_data['view_type'] = "leads";
		$page_data['human_readable_fields'] = $human_readable_output;
		$page_data['page_title'] = get_phrase('lead_assessment');
		$this -> load -> view('backend/index', $page_data);

	}

}
