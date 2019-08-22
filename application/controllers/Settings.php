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

class Settings extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> database();
		$this -> load -> library('session');
		$this -> session -> set_userdata('view_type', 'settings');
		$this -> load -> library('utility_forms');
		$this -> load -> library('grocery_CRUD');

		//set_use_datatable

		/*cache control*/
		$this -> output -> set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this -> output -> set_header('Pragma: no-cache');

	}

	function leads_bio_field() {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$crud = new grocery_CRUD();
		$crud -> set_theme('tablestrap');
		$crud -> set_table('lead_bio_fields');

		//Unset Actions
		$crud -> unset_delete();
		$crud -> unset_clone();

		//Unset columns
		$crud -> unset_columns(array('lead_bio_info_column'));
		$crud -> unset_fields(array('lead_bio_info_column'));

		//Display as in human readable fields
		$crud -> display_as('datatype_id', get_phrase('datatype')) -> display_as('lead_bio_fields_name', get_phrase('field_name')) -> display_as('is_field_null', get_phrase('is_field_required?'));

		//Set relationship
		$crud -> set_relation('datatype_id', 'datatype', 'datatype_name');

		//Set yes/no dropdown
		$crud -> field_type('is_field_unique', 'dropdown', array(get_phrase('no'), get_phrase('yes')));
		$crud -> field_type('show_field', 'dropdown', array(get_phrase('no'), get_phrase('yes')));
		$crud -> field_type('is_field_null', 'dropdown', array(get_phrase('no'), get_phrase('yes')));
		$crud -> field_type('is_suspended', 'dropdown', array(get_phrase('no'), get_phrase('yes')));

		//Set Mandatory Fields
		$crud -> required_fields(array('datatype_id'));

		//Hide fields on Edit
		//$crud->unset_edit_fields(array('lead_bio_fields_name'));

		//Callback
		$crud -> callback_after_insert(array($this, 'construct_lead_bio_info_column'));
		// Callback - To be used by Developers (More Development needed to check if the logged user is a developer)
		// Set delete when a developer logs in
		$crud -> callback_before_delete(array($this, 'delete_leads_bio_information_field'));
		$crud -> callback_after_update(array($this, 'modify_column_name'));
		$crud -> callback_edit_field('lead_bio_fields_name', array($this, 'readonly_field_on_edit_form'));

		//Added by Onduso
		$crud -> callback_read_field('show_field', array($this, 'modify_value_to_view'));
		$crud -> callback_read_field('is_field_null', array($this, 'modify_value_to_view'));
		$crud -> callback_read_field('is_suspended', array($this, 'modify_value_to_view'));
		$crud -> callback_read_field('is_field_unique', array($this, 'modify_value_to_view'));
		$crud -> callback_read_field('is_field_unique', array($this, 'modify_value_to_view'));
		$crud -> callback_read_field('default_value', array($this, 'modify_value_to_view'));

		$output = $crud -> render();
		$page_data['page_name'] = 'assessment_settings';
		$page_data['view_type'] = 'settings';
		$page_data['page_title'] = get_phrase('assessment_settings');
		$output = array_merge($page_data, (array)$output);

		$this -> load -> view('backend/index', $output);
	}

	//Onduso added this method
	public function modify_value_to_view($value, $primary_key) {
		//Display Yes or No when viewing and not one
		$value == 1 ? $modfy_html = '<div>Yes</div>' : $modfy_html = '<div>No</div>';

		//If value is not 0 or 1 or any text
		if ($value == '') {$modfy_html = '';
		}

		return $modfy_html;
	}

	function readonly_field_on_edit_form($value, $primary_key) {
		//return '<input class="form-control" readonly="readonly" type="text" value="'.$value.'" style="width:462px">';
		return '<div>' . $value . '</div>';
	}

	function modify_column_name($post_array, $primary_key) {
		$this -> load -> dbforge();

		//Get the lead_bio_fields updated record
		$update_record = $this -> db -> get_where('lead_bio_fields', array('lead_bio_fields_id' => $primary_key)) -> row();

		//Get the datatype record of the update lead_bio_fields updated record
		$datatype = $this -> db -> get_where('datatype', array('datatype_id' => $update_record -> datatype_id)) -> row();

		//Get old column name of the lead_bio_information from the lead_bio_fields lead_bio_info_column value
		$old_colum_name = $update_record -> lead_bio_info_column;

		//Construct the new column name
		$new_column_name = strtolower($this -> remove_special_characters_from_string($update_record -> lead_bio_fields_name));

		$fields = array($old_colum_name => array('name' => $new_column_name, 'type' => $datatype -> sql_type));

		$this -> dbforge -> modify_column('leads_bio_information', $fields);

		//Update the lead_bio_fields lead_bio_info_column field value
		$this -> db -> where(array('lead_bio_fields_id' => $primary_key));
		$this -> db -> update('lead_bio_fields', array('lead_bio_info_column' => $new_column_name));

		return true;
	}

	function delete_leads_bio_information_field($primary_key) {

		$this -> load -> dbforge();

		$field_name = $this -> db -> get_where('lead_bio_fields', array('lead_bio_fields_id' => $primary_key)) -> row() -> lead_bio_info_column;

		$this -> dbforge -> drop_column('leads_bio_information', $field_name);
	}

	function remove_special_characters_from_string($string) {
		$string = preg_replace('/\s+/', '_', $string);
		// Replaces all spaces with hyphens - This is a better solution in the sense that it replaces
		//multiple spaces with a single underscore which is usually the desired output.

		return preg_replace('/[^A-Za-z0-9\_]/', '', $string);
		// Removes special chars.
	}

	function construct_lead_bio_info_column($post_array, $primary_key) {
		//$updates_columns['lead_bio_info_column'] = strtolower(str_replace(" ", "_", $post_array['lead_bio_fields_name']));

		$updates_columns['lead_bio_info_column'] = strtolower($this -> remove_special_characters_from_string($post_array['lead_bio_fields_name']));

		$this -> db -> where(array('lead_bio_fields_id' => $primary_key));
		$this -> db -> update('lead_bio_fields', $updates_columns);

		$this -> alter_leads_bio_information($post_array, $updates_columns);

		return true;
	}

	function alter_leads_bio_information($post_array, $updates_columns) {

		$this -> load -> dbforge();

		$fields = array();

		$field_name = $updates_columns['lead_bio_info_column'];

		$datatype = $this -> db -> get_where('datatype', array('datatype_id' => $post_array['datatype_id'])) -> row();

		$type = $datatype -> sql_type;

		$fields[$field_name]['type'] = $type;
		$fields[$field_name]['constraint'] = '100';

		if ($post_array['is_field_unique'] == '1') {
			$fields[$field_name]['unique'] = true;
		} else {
			$fields[$field_name]['unique'] = false;
		}

		if ($post_array['default_value'] != "") {
			$fields[$field_name]['default'] = $post_array['default_value'];
		}

		if ($post_array['is_field_null'] == '1') {
			$fields[$field_name]['null'] = true;
		} else {
			$fields[$field_name]['null'] = false;
		}

		$this -> dbforge -> add_column('leads_bio_information', $fields);

		return TRUE;
	}

	function assessment_milestones() {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$crud = new grocery_CRUD();
		$crud -> set_theme('tablestrap');
		$crud -> set_table('assessment_milestones');
		//Unset actions
		$crud -> unset_clone();
		$crud -> unset_delete();

		//Dropdown fields conversion
		$crud -> field_type('status', 'dropdown', array(get_phrase('suspended'), get_phrase('active')));

		//Unset Field Add /Edit
		//$crud -> unset_edit_fields(array('assessment_review_status'));
		//$crud -> unset_add_fields(array('assessment_review_status'));
		$crud -> unset_add_fields(array('status'));
		//$crud -> unset_add_fields(array('assessment_review_status'));
		//$crud -> field_type('assessment_review_status','hidden');

		$crud -> fields('milestone_name', 'milestones_insert_after_id', 'assessment_period_in_days', 'user_customized_review_status', 'assessment_review_status');

		$crud -> field_type('assessment_review_status', 'invisible');
		$crud -> edit_fields('milestone_name', 'milestones_insert_after_id', 'assessment_period_in_days', 'user_customized_review_status', 'status');
		//Avoid selection of "New Lead" Milestone
		//$crud -> where('milestone_name!=' , 'New Lead');

		//Order By - Not working
		//$crud -> order_by('assessment_milestones_id');

		//Relation tables
		//Onduso added a where clause so that New Lead is not displayed in the dropdown list
		$crud -> set_relation('milestones_insert_after_id', 'insert_after_milestone', 'insert_after_milestone_name', array('insert_after_milestone_name<>' => 'New Lead'));

		//Display in human readable
		$crud -> display_as('milestones_insert_after_id', get_phrase('insert_after')) -> display_as('user_customized_review_status', get_phrase('customized_review_status'));

		//Set required fields
		$crud -> required_fields(array('milestone_name', 'milestones_insert_after_id', 'assessment_period_in_days'));

		//Callbacks
		$crud -> callback_after_insert(array($this, 'update_insert_after_milestone'));
		$crud -> callback_field('assessment_period_in_days', array($this, 'create_a_range_assessment_period_in_days_field'));
		//$crud -> callback_after_insert(array($this, 'update_assessment_review_status'));
		$crud -> callback_read_field('status', array($this, 'modify_status_on_view_form'));
		$crud -> callback_after_insert(array($this, 'update_assessment_milestone_status_to_active'));
		$crud->callback_before_insert(array($this,'add_hidden_assessment_review_status'));
		
		
		//$crud-> hide_staus_field

		//Callbacks to be run by developers
		// $crud->callback_after_update();

		$output = $crud -> render();
		$page_data['page_name'] = 'assessment_settings';
		$page_data['view_type'] = 'settings';
		$page_data['page_title'] = get_phrase('assessment_milestones');
		$output = array_merge($page_data, (array)$output);

		$this -> load -> view('backend/index', $output);
	}

	//Added by onduso
	public function modify_status_on_view_form($value, $primary_key) {
		$html = '';
		$value == 1 ? $html = '<div>Active</div>' : $html = '<div>Suspended</div>';
		return $html;
	}

	function update_assessment_milestone_status_to_active($primary_key) {

		$data['status'] = 1;

		$this -> db -> where(array('assessment_milestones_id' => $primary_key));
		$this -> db -> update('status', $data);

		return true;
	}

	function add_hidden_assessment_review_status($post_array) {

		$post_array['assessment_review_status'] = $post_array['milestone_name'] . ' In Progress';
		
		return $post_array;
	}

	//End of Onduso added crude form code
	
	
	

	// function update_assessment_review_status($post_array, $primary_key) {
// 
		// $data['assessment_review_status'] = $post_array['milestone_name'] . ' In Progress';
// 
		// $this -> db -> where(array('assessment_milestones_id' => $primary_key));
		// $this -> db -> update('assessment_milestones', $data);
// 
		// return true;
	// }


	function create_a_range_assessment_period_in_days_field($value, $primary_key = null) {
		return '<input class="form-control" name="assessment_period_in_days"  type="number" min = "1" max = "180" value="' . $value . '" style="width:462px">';
	}

	function update_insert_after_milestone($post_array, $primary_key) {
		$data['insert_after_milestone_id'] = $primary_key;
		$data['insert_after_milestone_name'] = $post_array['milestone_name'];

		$this -> db -> insert('insert_after_milestone', $data);

		return true;
	}

	function compassion_stage() {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$crud = new grocery_CRUD();
		$crud -> set_theme('tablestrap');
		$crud -> set_table('compassion_connect_mapping');

		//Where condition
		//$crud -> where(array('lead_score_parameter<>' => 'No Match'));

		//Dropdowns
		$crud -> set_relation('lead_score_stage', 'connect_stage', 'connect_stage_name');

		//Unset delete and Edit
		$crud -> unset_delete();
		$crud -> unset_clone();

		$output = $crud -> render();
		$page_data['page_name'] = 'assessment_settings';
		$page_data['view_type'] = 'settings';
		$page_data['page_title'] = get_phrase('compassion_stages');
		$output = array_merge($page_data, (array)$output);

		$this -> load -> view('backend/index', $output);
	}

	function assessment_progress_measures() {
		if ($this -> session -> userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$crud = new grocery_CRUD();
		$crud -> set_theme('tablestrap');
		$crud -> set_table('assessment_progress_measure');

		//Defined range of weights
		$crud -> field_type('weight', 'dropdown', range(0, 10));
		$crud -> field_type('status', 'dropdown', array(get_phrase('suspended'), get_phrase('active')));

		//Set relationship to CC mapping
		$crud -> set_relation('compassion_connect_mapping', 'compassion_connect_mapping', 'lead_score_parameter');

		//Callback function added by Onduso
		$crud -> callback_read_field('status', array($this, 'modify_status_on_view_form'));

		//Prevented actions
		$crud -> unset_delete();

		$output = $crud -> render();
		$page_data['page_name'] = 'assessment_settings';
		$page_data['view_type'] = 'settings';
		$page_data['page_title'] = get_phrase('assessment_progress_measure');
		$output = array_merge($page_data, (array)$output);

		$this -> load -> view('backend/index', $output);

	}

}
