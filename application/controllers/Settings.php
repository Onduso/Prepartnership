<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    /*
     *	@author 	: Nicodemus Karisa Mwambire
     *	date		: 16th June, 2018
     *	Techsys School Management System
     *	https://www.techsysolutions.com
     *	support@techsysolutions.com
     */

class Settings extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
		$this->session->set_userdata('view_type','settings');

       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');

    }
		
	  function assessment_settings($param1="",$param2=""){
        if ($this->session->userdata('user_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
		
		
		$page_data['lead_bio_fields_output'] = $this->list_lead_bio();
		
        $page_data['page_name']                 = 'assessment_settings';
		$page_data['view_type'] = "settings";
        $page_data['page_title']                = get_phrase('assessment_settings');
        $this->load->view('backend/index', $page_data);	
	}

	 /*****SITE/SYSTEM SETTINGS*********/
    function system_settings($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('user_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        
        if ($param1 == 'do_update') {
			 
            $data['description'] = $this->input->post('system_name');
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_title');
            $this->db->where('type' , 'system_title');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('address');
            $this->db->where('type' , 'address');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('phone');
            $this->db->where('type' , 'phone');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('paypal_email');
            $this->db->where('type' , 'paypal_email');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('currency');
            $this->db->where('type' , 'currency');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_email');
            $this->db->where('type' , 'system_email');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_name');
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('language');
            $this->db->where('type' , 'language');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('text_align');
            $this->db->where('type' , 'text_align');
            $this->db->update('settings' , $data);
			
			$data['description'] = $this->input->post('system_start_date');
            $this->db->where('type' , 'system_start_date');
            $this->db->update('settings' , $data);
			
			$data['description'] = $this->input->post('sidebar-collapsed');
            $this->db->where('type' , 'sidebar-collapsed');
            $this->db->update('settings' , $data);
			
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated')); 
            redirect(base_url() . 'index.php?settings/system_settings/', 'refresh');
        }
        if ($param1 == 'upload_logo') {
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/logo.png');
            $this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
            redirect(base_url() . 'index.php?settings/system_settings/', 'refresh');
        }
        if ($param1 == 'change_skin') {
            $data['description'] = $param2;
            $this->db->where('type' , 'skin_colour');
            $this->db->update('settings' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('theme_selected')); 
            redirect(base_url() . 'index.php?settings/system_settings/', 'refresh'); 
        }

        $page_data['page_name']  = 'system_settings';
		$page_data['view_type']  = 'settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }

/*****SMS SETTINGS*********/
    function sms_settings($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('user_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        if ($param1 == 'clickatell') {

            $data['description'] = $this->input->post('clickatell_user');
            $this->db->where('type' , 'clickatell_user');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('clickatell_password');
            $this->db->where('type' , 'clickatell_password');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('clickatell_api_id');
            $this->db->where('type' , 'clickatell_api_id');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?settings/sms_settings/', 'refresh');
        }

        if ($param1 == 'twilio') {

            $data['description'] = $this->input->post('twilio_account_sid');
            $this->db->where('type' , 'twilio_account_sid');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('twilio_auth_token');
            $this->db->where('type' , 'twilio_auth_token');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('twilio_sender_phone_number');
            $this->db->where('type' , 'twilio_sender_phone_number');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?settings/sms_settings/', 'refresh');
        }

        if ($param1 == 'active_service') {

            $data['description'] = $this->input->post('active_sms_service');
            $this->db->where('type' , 'active_sms_service');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?settings/sms_settings/', 'refresh');
        }

        $page_data['page_name']  = 'sms_settings';
		$page_data['view_type']  = 'settings';
        $page_data['page_title'] = get_phrase('sms_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }
    
    /*****LANGUAGE SETTINGS*********/
    function manage_language($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('user_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');
		
		if ($param1 == 'edit_phrase') {
			$page_data['edit_profile'] 	= $param2;	
		}
		if ($param1 == 'update_phrase') {
			$language	=	$param2;
			$total_phrase	=	$this->input->post('total_phrase');
			for($i = 1 ; $i < $total_phrase ; $i++)
			{
				//$data[$language]	=	$this->input->post('phrase').$i;
				$this->db->where('phrase_id' , $i);
				$this->db->update('language' , array($language => $this->input->post('phrase'.$i)));
			}
			redirect(base_url() . 'index.php?settings/manage_language/edit_phrase/'.$language, 'refresh');
		}
		if ($param1 == 'do_update') {
			$language        = $this->input->post('language');
			$data[$language] = $this->input->post('phrase');
			$this->db->where('phrase_id', $param2);
			$this->db->update('language', $data);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'index.php?settings/manage_language/', 'refresh');
		}
		if ($param1 == 'add_phrase') {
			$data['phrase'] = $this->input->post('phrase');
			$this->db->insert('language', $data);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'index.php?settings/manage_language/', 'refresh');
		}
		if ($param1 == 'add_language') {
			$language = $this->input->post('language');
			$this->load->dbforge();
			$fields = array(
				$language => array(
					'type' => 'LONGTEXT'
				)
			);
			$this->dbforge->add_column('language', $fields);
			
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'index.php?settings/manage_language/', 'refresh');
		}
		if ($param1 == 'delete_language') {
			$language = $param2;
			$this->load->dbforge();
			$this->dbforge->drop_column('language', $language);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			
			redirect(base_url() . 'index.php?settings/manage_language/', 'refresh');
		}
		$page_data['page_name']        = 'manage_language';
		$page_data['view_type']        = 'settings';
		$page_data['page_title']       = get_phrase('manage_language');
		//$page_data['language_phrases'] = $this->db->get('language')->result_array();
		$this->load->view('backend/index', $page_data);	
    }

 function expense_category($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('user_login') != 1)
            redirect('login', 'refresh');
        if ($param1 == 'create') {
            $data['name']   =   $this->input->post('name');
			$data['income_category_id']   =   $this->input->post('income_category_id');
            $this->db->insert('expense_category' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?settings/school_settings');
        }
        if ($param1 == 'edit') {
            $data['name']   =   $this->input->post('name');
			$data['income_category_id'] = $this->input->post('income_category_id');
            $this->db->where('expense_category_id' , $param2);
            $this->db->update('expense_category' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?settings/school_settings');
        }
        if ($param1 == 'delete') {
            $this->db->where('expense_category_id' , $param2);
            $this->db->delete('expense_category');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?settings/school_settings');
        }

        $page_data['page_name']  = 'school_settings';
		$page_data['view_type']  = 'settings';
        $page_data['page_title'] = get_phrase('school_settings');
        $this->load->view('backend/index', $page_data);
    }
	function income_category($param1 = '' , $param2 = ''){
        if ($this->session->userdata('user_login') != 1)
            redirect('login', 'refresh');
		
        if ($param1 == 'create') {
            $data['name']   =   $this->input->post('name');
			$data['opening_balance']   =   $this->input->post('opening_balance');
            $this->db->insert('income_categories' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?settings/income_category');
        }
        if ($param1 == 'edit') {
            $data['name']   =   $this->input->post('name');
			$data['opening_balance']   =   $this->input->post('opening_balance');
			//$data['income_category_id'] = $this->input->post('income_category_id');
            $this->db->where('income_category_id' , $param2);
            $this->db->update('income_categories' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?settings/income_category');
        }
        if ($param1 == 'delete') {
            $this->db->where('income_category_id' , $param2);
            $this->db->delete('income_categories');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?settings/income_category');
        }

        $page_data['page_name']  = 'school_settings';
		$page_data['view_type']  = 'settings';
        $page_data['page_title'] = get_phrase('income_category');
        $this->load->view('backend/index', $page_data);			
	}

	function update_income_category_opening_balance($income_category_id =""){
		$this->db->where(array('income_category_id'=>$income_category_id));
		$data['opening_balance'] = $this->input->post('opening_balance');
		$this->db->update('income_categories',$data);
	}

	function opening_balances($param1="",$param2=""){
			
			$this->db->where(array('name'=>'cash'));
			$data['opening_balance'] = $this->input->post('cash');
			$this->db->update('accounts' , $data);
			
			$this->db->where(array('name'=>'bank'));
			$data1['opening_balance'] = $this->input->post('bank');
            $this->db->update('accounts' , $data1);
			
			$this->db->where(array('type'=>'system_start_date'));
			$data2['description'] = $this->input->post('system_start_date');
            $this->db->update('settings' , $data2);

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?settings/school_settings/', 'refresh');				
	}

	function user_profiles($param1 = '' , $param2 = ''){
        if ($this->session->userdata('user_login') != 1)
            redirect('login', 'refresh');
		
		if($param1=="create"){
			
			$msg = get_phrase('failure');
				
			$data['name'] = $this->input->post('name');
			$data['description'] = $this->input->post('description');
			//$data['visibility'] = $this->input->post('visibility');
			
			$this->db->insert("profile",$data);
			
			if($this->db->affected_rows() > 0){
				$msg = get_phrase('success');
			}
			
			$this->session->set_flashdata('flash_message' , $msg);
            redirect(base_url() . 'settings/user_profiles/', 'refresh');
		}
		
        $page_data['page_name']  = 'user_profiles';
		$page_data['view_type']  = 'settings';
        $page_data['page_title'] = get_phrase('user_profiles');
        $this->load->view('backend/index', $page_data);			
	}
	
	function entitlement($param1="",$param2=""){
		if ($this->session->userdata('user_login') != 1)
            redirect('login', 'refresh');
		
        $page_data['page_name']  = 'entitlement';
		$page_data['profile_id']  = $param1;
		$page_data['view_type']  = 'settings';
        $page_data['page_title'] = get_phrase('entitlement');
        $this->load->view('backend/index', $page_data);
	}
	
	function update_entitlement($param1="",$param2="",$param3=""){
			
		if($param3 === 'true'){
			$data['entitlement_id'] = $param1;
			$data['profile_id'] = $param2;
			$this->db->insert("access",$data);
		}else{
			$this->db->where(array("entitlement_id"=>$param1,"profile_id"=>$param2));
			$this->db->delete("access");
		}	
			
		//echo "Update Successful";
	}

	function promote_to_user($param1="",$param2=""){
		if($param1=="teacher"){
			$teacher = $this->db->get_where("teacher",array("teacher_id"=>$param2))->result_array();
			extract($teacher[0]);
			
			$name_array = explode(" ", $name);
			
			$data['firstname'] = array_shift($name_array);
			$data['lastname'] = implode(" ", $name_array);
			$data['email'] = $email;
			$data['password'] = "default";
			$data['phone'] = $phone;
			$data['login_type_id'] = $this->db->get_where("login_type",array("name"=>"teacher"))->row()->login_type_id;
			$data['profile_id'] = 0;
			$data['type_user_id'] = $teacher_id;
			$data['auth'] = 1;
			
			$msg = get_phrase("failed");
			
			/**Check if exists**/
			$exists = $this->db->get_where("user",array("email"=>$email))->num_rows();
			if($exists == 0) {
				$this->db->insert("user",$data);
				$msg = get_phrase("success");
			}
			
			$this->session->set_flashdata('flash_message' , $msg);
            redirect(base_url() . 'index.php?teacher/teacher/', 'refresh');
		}
			
		if($param1=="admin"){
			$admin = $this->db->get_where("admin",array("admin_id"=>$param2))->result_array();
			extract($admin[0]);
			
			$name_array = explode(" ", $name);
			
			$data['firstname'] = array_shift($name_array);
			$data['lastname'] = implode(" ", $name_array);
			$data['email'] = $email;
			$data['password'] = "default";
			$data['phone'] = $phone;
			$data['login_type_id'] = $this->db->get_where("login_type",array("name"=>"admin"))->row()->login_type_id;
			$data['profile_id'] = 0;
			$data['type_user_id'] = $admin_id;
			$data['auth'] = 1;
			
			$msg = get_phrase("failed");
			
			/**Check if exists**/
			$exists = $this->db->get_where("user",array("email"=>$email))->num_rows();
			if($exists == 0) {
				$this->db->insert("user",$data);
				$msg = get_phrase("success");
			}
			
			$this->session->set_flashdata('flash_message' , $msg);
            redirect(base_url() . 'index.php?admin/admin/', 'refresh');
		}	
			
	
	}
private function load_library(){
		$this->load->library('utility_forms');
		return new Utility_forms();
	}
	
private function list_lead_bio(){
		
		$build_list = $this->load_library();
		
		$selected_columns = array("Field Name"=>"lead_bio_fields_name",
		'Data Type'=>"datatype_name","Is Field Unique?"=>"is_field_unique","Is Field Null?"=>"is_field_null",
		'default_value');
	
		$build_list->set_selected_fields($selected_columns,'lead_bio_fields_id');		
	
				
		$build_list->set_panel_title("Lead Bio Fields");
		
		$action = array(
			'add' 	=> array('href'=>'admin/add_lead_bio_fields'),
			'view' 	=> array('href'=>'admin/view_single_lead_bio'),
			'edit' 	=> array('href'=>'admin/edit_lead_bio_fields'),
			'delete'=> array('href'=>'admin/delete_lead_bio_fields')
		);
		
		$build_list->set_list_action($action);
		
		$join_array = array('datatype'=>array('lead_bio_fields.datatype_id','datatype.datatype_id'));
		
		$build_list->set_table_join($join_array);
		
		$build_list->set_db_table("lead_bio_fields");
		
		$build_list->set_add_form();
		
		
		return $build_list->render_item_list();
		// $page_data['view_type']	= "settings";
		// $page_data['page_name']	= "assessment_settings";
		// $page_data['page_title']	= "assessment_settings";
		// $this->load->view('backend/index',$page_data);
	}
	
}	