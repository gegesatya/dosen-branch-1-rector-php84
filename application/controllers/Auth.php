<?php defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
		$this->session->set_userdata('tahun_ajaran', $this->db->get('siakad_setting')->row()->tahun_ajaran_aktif);
		$this->session->set_userdata('smt', $this->db->where('a_periode_aktif', 1)->order_by('id_smt', 'DESC')->get('semester')->result());
		date_default_timezone_set("Asia/Jakarta");
	}

	public function index()
	{

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		} else {
			if (!$this->ion_auth->is_admin()) {
				redirect(site_url('perkuliahan'), 'refresh');
			} else {
				$this->data['message'] = validation_errors() ?: $this->session->flashdata('message');
				$this->data['users'] = $this->ion_auth->users()->result();
				foreach ($this->data['users'] as $k => $user) {
					$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
				}

				$data['judul_besar'] = 'Manage User';
				$data['judul_kecil'] = 'List';
				$data['m_auth_index'] = TRUE;
				$data['output'] = $this->data;
				$data['view_file'] = 'auth/index';
				$this->load->view('auth/main', $data, FALSE);
			}
		}
	}

	// log the user in
	public function login()
	{
		$this->data['title'] = "Login";

		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true) {
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
				//$this->log_library->log_history('login');

				$id_ptk = $this->ion_auth->user()->row()->id_ptk;
				$id_sms = $this->ion_auth->user()->row()->id_sms;

				$nm_ptk = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row()->nm_ptk;

				$siakad_dosen = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row();
				$photo = !empty($siakad_dosen->photo) ? $siakad_dosen->photo : 'no_photo.jpg';
				// $src_image = 'http://' . $_SERVER['HTTP_HOST'] . '/siakad_file_dosen/' . $photo;
				$setting = $this->db->get('siakad_setting')->row();
				$id_smt = $setting->tahun_ajaran_aktif;
				$nm_smt = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;


				$user_id = $this->ion_auth->user()->row()->id;
				$this->session->set_userdata(
					[
						'id_ptk' 		=> $id_ptk,
						'nm_ptk'			=> $nm_ptk,
						'photo'			=> $photo,
						'nm_smt'			=> $nm_smt,
						'siakad_dosen'	=> $siakad_dosen,
						'prodi'			=> $id_sms,
						'setting'		=> $setting,
						'id_smt'			=> $id_smt,
						'user_id'		=> $user_id
					]
				);

				$this->session->set_flashdata('message', $this->ion_auth->messages());

				if ($this->ion_auth->in_group([15])) {
					redirect(site_url('C_jadwal'), 'refresh');
				} else {
					redirect(site_url('C_wall'), 'refresh');
				}
			} else {

				$this->session->set_flashdata('message', $this->ion_auth->errors());
				$this->session->set_flashdata('message_type', 'error');
				redirect('auth/login', 'refresh');
			}
		} else {
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$this->data['message'] = validation_errors() ?: $this->session->flashdata('message');
			$this->data['message_type'] = (validation_errors()) ? 'error' : $this->session->flashdata('message_type');

			$this->data['identity'] = [
				'name' => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('identity'),
			];
			$this->data['password'] = [
				'name' => 'password',
				'id'   => 'password',
				'type' => 'password',
			];

			$this->_render_page('auth/login', $this->data);
		}
	}




	// log the user out
	public function logout()
	{

		//$this->log_library->log_history('logout');									   
		$this->data['title'] = "Logout";

		$session_a = ['id_ptk', 'nm_ptk', 'photo', 'nm_smt', 'siakad_dosen', 'prodi', 'setting', 'id_smt', 'user_id'];

		$this->session->unset_userdata($session_a);

		// log the user out
		$logout = $this->ion_auth->logout();

		// redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		$this->session->set_flashdata('message_type', 'success');
		redirect('auth/login', 'refresh');
	}

	// change password
	public function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false) {
			// display the form
			// set the flash data error message if there is one
			$this->data['message'] = validation_errors() ?: $this->session->flashdata('message');

			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = [
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
			];
			$this->data['new_password'] = [
				'name'    => 'new',
				'id'      => 'new',
				'type'    => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			];
			$this->data['new_password_confirm'] = [
				'name'    => 'new_confirm',
				'id'      => 'new_confirm',
				'type'    => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			];
			$this->data['user_id'] = [
				'name'  => 'user_id',
				'id'    => 'user_id',
				'type'  => 'hidden',
				'value' => $user->id,
			];

			////////Config Halaman////////
			/* $data['judul_besar'] = 'Manage User';
            $data['judul_kecil'] = 'Change Password';
			$data['m_change_password'] = TRUE;
			$data['output'] = $this->data;
			$data['view_file'] = 'auth/change_password';

			$this->load->view('auth/main', $data, FALSE); */
			$data['judul_besar'] = 'Manage User';
			$data['judul_kecil'] = 'Change Password';
			$data['menu_aktif']	= 'm_change_password';
			$data['after_page'] = 'change_password/change_password_after_page';

			$data['output'] = $this->data;
			$data['view_file'] = 'auth/change_password';

			$this->load->view('auth/main', $data, false);
		} else {
			$identity = $this->session->userdata('identity');

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change) {
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	// forgot password
	public function forgot_password()
	{
		// setting validation rules by checking wheather identity is username or email
		if ($this->config->item('identity', 'ion_auth') != 'email') {
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
		} else {
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		}


		if ($this->form_validation->run() == false) {
			$this->data['type'] = $this->config->item('identity', 'ion_auth');
			// setup the input
			$this->data['identity'] = [
				'name' => 'identity',
				'id' => 'identity',
			];

			if ($this->config->item('identity', 'ion_auth') != 'email') {
				$this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
			} else {
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}

			// set any errors and display the form
			$this->data['message'] = validation_errors() ?: $this->session->flashdata('message');
			$this->_render_page('auth/forgot_password', $this->data);
		} else {
			$identity_column = $this->config->item('identity', 'ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

			if (empty($identity)) {

				if ($this->config->item('identity', 'ion_auth') != 'email') {
					$this->ion_auth->set_error('forgot_password_identity_not_found');
				} else {
					$this->ion_auth->set_error('forgot_password_email_not_found');
				}

				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten) {
				// if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	// reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code) {
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user) {
			// if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false) {
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = validation_errors() ?: $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = [
					'name' => 'new',
					'id'   => 'new',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				];
				$this->data['new_password_confirm'] = [
					'name'    => 'new_confirm',
					'id'      => 'new_confirm',
					'type'    => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				];
				$this->data['user_id'] = [
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
				];
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				// render
				$this->_render_page('auth/reset_password', $this->data);
			} else {
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

					// something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));
				} else {
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change) {
						// if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect("auth/login", 'refresh');
					} else {
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		} else {
			// if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}


	// activate the user
	public function activate($id, $code = false)
	{
		if ($code !== false) {
			$activation = $this->ion_auth->activate($id, $code);
		} else if ($this->ion_auth->is_admin()) {
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation) {
			// redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		} else {
			// redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	// deactivate the user
	public function deactivate($id = NULL)
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page.');
		}

		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE) {
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();

			$this->_render_page('auth/deactivate_user', $this->data);
		} else {
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes') {
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
					$this->ion_auth->deactivate($id);
				}
			}

			// redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	// create a new user
	public function create_user()
	{
		$this->data['title'] = "Create User";

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}

		$tables = $this->config->item('tables', 'ion_auth');
		$identity_column = $this->config->item('identity', 'ion_auth');
		$this->data['identity_column'] = $identity_column;

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
		if ($identity_column !== 'email') {
			$this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
		} else {
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
		}
		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true) {
			$email    = strtolower($this->input->post('email'));
			$identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
			$password = $this->input->post('password');

			$additional_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name'  => $this->input->post('last_name'),
				'company'    => $this->input->post('company'),
				'phone'      => $this->input->post('phone'),
			];
		}
		if ($this->form_validation->run() == true && $this->ion_auth->register($identity, $password, $email, $additional_data)) {
			// check to see if we are creating the user
			// redirect them back to the admin page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		} else {
			// display the create user form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ?: (($this->ion_auth->errors() ?: $this->session->flashdata('message'))));

			$this->data['first_name'] = [
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('first_name'),
			];
			$this->data['last_name'] = [
				'name'  => 'last_name',
				'id'    => 'last_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('last_name'),
			];
			$this->data['identity'] = [
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('identity'),
			];
			$this->data['email'] = [
				'name'  => 'email',
				'id'    => 'email',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('email'),
			];
			$this->data['company'] = [
				'name'  => 'company',
				'id'    => 'company',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('company'),
			];
			$this->data['phone'] = [
				'name'  => 'phone',
				'id'    => 'phone',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('phone'),
			];
			$this->data['password'] = [
				'name'  => 'password',
				'id'    => 'password',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password'),
			];
			$this->data['password_confirm'] = [
				'name'  => 'password_confirm',
				'id'    => 'password_confirm',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			];

			////////Config Halaman////////
			$data['judul_besar'] = 'Manage User';
			$data['judul_kecil'] = 'Add User';
			$data['output'] = $this->data;
			$data['view_file'] = 'auth/create_user';
			$this->load->view('auth/main', $data, FALSE);
		}
	}

	// edit a user
	public function edit_user($id)
	{
		$this->data['title'] = "Edit User";

		if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		$groups = $this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');
		$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required');

		if (isset($_POST) && !empty($_POST)) {
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
				show_error($this->lang->line('error_csrf'));
			}

			// update the password if it was posted
			if ($this->input->post('password')) {
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE) {
				$data = [
					'first_name' => $this->input->post('first_name'),
					'last_name'  => $this->input->post('last_name'),
					'company'    => $this->input->post('company'),
					'phone'      => $this->input->post('phone'),
				];

				// update the password if it was posted
				if ($this->input->post('password')) {
					$data['password'] = $this->input->post('password');
				}



				// Only allow updating groups if user is admin
				if ($this->ion_auth->is_admin()) {
					//Update the groups user belongs to
					$groupData = $this->input->post('groups');

					if (isset($groupData) && !empty($groupData)) {

						$this->ion_auth->remove_from_group('', $id);

						foreach ($groupData as $grp) {
							$this->ion_auth->add_to_group($grp, $id);
						}
					}
				}

				// check to see if we are updating the user
				if ($this->ion_auth->update($user->id, $data)) {
					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					if ($this->ion_auth->is_admin()) {
						redirect('auth', 'refresh');
					} else {
						redirect('/', 'refresh');
					}
				} else {
					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('message', $this->ion_auth->errors());
					if ($this->ion_auth->is_admin()) {
						redirect('auth', 'refresh');
					} else {
						redirect('/', 'refresh');
					}
				}
			}
		}

		// display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ?: (($this->ion_auth->errors() ?: $this->session->flashdata('message'))));

		// pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;

		$this->data['first_name'] = [
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
		];
		$this->data['last_name'] = [
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
		];
		$this->data['company'] = [
			'name'  => 'company',
			'id'    => 'company',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('company', $user->company),
		];
		$this->data['phone'] = [
			'name'  => 'phone',
			'id'    => 'phone',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone', $user->phone),
		];
		$this->data['password'] = [
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password'
		];
		$this->data['password_confirm'] = [
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'type' => 'password'
		];

		////////Config Halaman////////
		$data['judul_besar'] = 'Edit User';
		$data['judul_kecil'] = 'List';
		$data['m_auth_index'] = TRUE;
		$data['output'] = $this->data;
		$data['view_file'] = 'auth/edit_user';
		$this->load->view('auth/main', $data, FALSE);
	}

	// create a new group
	public function create_group()
	{
		$this->data['title'] = $this->lang->line('create_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash');

		if ($this->form_validation->run() == TRUE) {
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if ($new_group_id) {
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth", 'refresh');
			}
		} else {
			// display the create group form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ?: (($this->ion_auth->errors() ?: $this->session->flashdata('message'))));

			$this->data['group_name'] = [
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('group_name'),
			];
			$this->data['description'] = [
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('description'),
			];

			////////Config Halaman////////
			$data['judul_besar'] = 'Edit Group';
			$data['judul_kecil'] = 'List';
			$data['m_auth_index'] = TRUE;
			$data['output'] = $this->data;
			$data['view_file'] = 'auth/create_group';
			$this->load->view('auth/main', $data, FALSE);
		}
	}

	// edit a group
	public function edit_group($id)
	{
		// bail if no group id given
		if (!$id || empty($id)) {
			redirect('auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash');

		if (isset($_POST) && !empty($_POST)) {
			if ($this->form_validation->run() === TRUE) {
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if ($group_update) {
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				} else {
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("auth", 'refresh');
			}
		}

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ?: (($this->ion_auth->errors() ?: $this->session->flashdata('message'))));

		// pass the user to the view
		$this->data['group'] = $group;

		$readonly = $this->config->item('admin_group', 'ion_auth') === $group->name ? 'readonly' : '';

		$this->data['group_name'] = [
			'name'    => 'group_name',
			'id'      => 'group_name',
			'type'    => 'text',
			'value'   => $this->form_validation->set_value('group_name', $group->name),
			$readonly => $readonly,
		];
		$this->data['group_description'] = [
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		];

		////////Config Halaman////////
		$data['judul_besar'] = 'Edit Group';
		$data['judul_kecil'] = 'List';
		$data['m_auth_index'] = TRUE;
		$data['output'] = $this->data;
		$data['view_file'] = 'auth/edit_group';
		$this->load->view('auth/main', $data, FALSE);
	}


	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return [$key => $value];
	}

	public function _valid_csrf_nonce()
	{
		if (
			$this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function _render_page($view, $data = null, $returnhtml = false) //I think this makes more sense
	{

		$this->viewdata = (empty($data)) ? $this->data : $data;

		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);

		if ($returnhtml) return $view_html; //This will return html on 3rd argument being true
	}

	public function connect_with_google()
	{
		require_once APPPATH . "third_party/google/vendor/autoload.php";

		$provider = new League\OAuth2\Client\Provider\Google([
			'clientId' => '441983676746-9sqn1b7tmcm6lf5afgtpk59r79rrv4ke.apps.googleusercontent.com',
			'clientSecret' => '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',
			'redirectUri' => 'https://dosen.wbi.ac.id/auth/connect_with_google',
		]);

		if (!empty($_GET['error'])) {
			// Got an error, probably user denied access
			exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
		} elseif (empty($_GET['code'])) {

			// If we don't have an authorization code then get one
			$authUrl = $provider->getAuthorizationUrl();
			$_SESSION['oauth2state'] = $provider->getState();
			$this->session->set_userdata('g_login_referrer', $this->agent->referrer());
			header('Location: ' . $authUrl);
			exit();
		} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
			// State is invalid, possible CSRF attack in progress
			unset($_SESSION['oauth2state']);
			exit('Invalid state');
		} else {
			// Try to get an access token (using the authorization code grant)
			$token = $provider->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);
			// Optional: Now you have a token you can look up a users profile data
			try {
				// We got an access token, let's now get the owner details
				$user = $provider->getResourceOwner($token);

				$g_user = new stdClass();
				$g_user->id = $user->getId();
				$g_user->email = $user->getEmail();
				$g_user->name = $user->getName();
				$g_user->avatar = $user->getAvatar();

				// $this->auth_model->login_with_google($g_user);

				$ions = $this->db->where('username', $user->getEmail())->get('users')->row();
				if ($this->ion_auth->login_2($ions->username, $ions->password, $remember)) {
					//$this->log_library->log_history('login');

					$id_ptk = $this->ion_auth->user()->row()->id_ptk;
					$id_sms = $this->ion_auth->user()->row()->id_sms;

					$nm_ptk = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row()->nm_ptk;

					$siakad_dosen = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row();
					$photo = !empty($siakad_dosen->photo) ? $siakad_dosen->photo : 'no_photo.jpg';
					$setting = $this->db->get('siakad_setting')->row();

					$id_smt = $setting->tahun_ajaran_aktif;
					$nm_smt = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;


					$user_id = $this->ion_auth->user()->row()->id;
					$this->session->set_userdata(
						[
							'id_ptk' 		=> $id_ptk,
							'nm_ptk'		=> $nm_ptk,
							'photo'			=> $photo,
							'nm_smt'		=> $nm_smt,
							'siakad_dosen'	=> $siakad_dosen,
							'prodi'			=> $id_sms,
							'setting'		=> $setting,
							'id_smt'		=> $id_smt,
							'user_id'		=> $user_id
						]
					);

					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect(site_url('C_jadwal'), 'refresh');
				} else {

					$this->session->set_flashdata('message', $this->ion_auth->errors());
					redirect('auth/login', 'refresh');
				}

				// if (!empty($this->session->userdata('g_login_referrer'))) {
				// 	redirect($this->session->userdata('g_login_referrer'));
				// } else {
				//	redirect(base_url());
				// }
			} catch (Exception $e) {
				// Failed to get user details
				exit('Something went wrong: ' . $e->getMessage());
			}
		}
	}

	// public function test()
	// {
	// 	$ions = $this->db->where('username', 'gege.satya@wbi.ac.id')->get('users')->row();
	// 	if ($this->ion_auth->login_2($ions->username, $ions->password)) {

	// 		$id_ptk = $this->ion_auth->user()->row()->id_ptk;
	// 		$id_sms = $this->ion_auth->user()->row()->id_sms;

	// 		$nm_ptk = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row()->nm_ptk;

	// 		$siakad_dosen = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row();
	// 		$photo = !empty($siakad_dosen->photo) ? $siakad_dosen->photo : 'no_photo.jpg';
	// 		$setting = $this->db->get('siakad_setting')->row();

	// 		$id_smt = $setting->tahun_ajaran_aktif;
	// 		$nm_smt = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;

	// 		$user_id = $this->ion_auth->user()->row()->id;
	// 		$this->session->set_userdata(
	// 			array(
	// 				'id_ptk' 		=> $id_ptk,
	// 				'nm_ptk'		=> $nm_ptk,
	// 				'photo'			=> $photo,
	// 				'nm_smt'		=> $nm_smt,
	// 				'siakad_dosen'	=> $siakad_dosen,
	// 				'prodi'			=> $id_sms,
	// 				'setting'		=> $setting,
	// 				'id_smt'		=> $id_smt,
	// 				'user_id'		=> $user_id
	// 			)
	// 		);

	// 		echo "sukses";
	// 	} else {
	// 		echo $ions->password;
	// 		echo "gagal";
	// 	}
	// }
}
