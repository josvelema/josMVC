<?php

class Pages extends Controller {

	private $account;
	private $admin;

	function init() {
		$this->account = new Account($this);
	}

	function login() {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		$this->session('token', md5(uniqid(rand(), true)));
		$this->view('login.html', [
			'token' => $this->session('token')
		]);
	}

	function authenticate() {
		return $this->account->authenticate($this->post('username'), $this->post('password'), $this->post('rememberme'), $this->post('token'));
	}

	function register() {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		$this->session('token', md5(uniqid(rand(), true)));
		$this->view('register.html', [
			'token' => $this->session('token')
		]);
	}

	function register_process() {
		return $this->account->register($this->post('username'), $this->post('password'), $this->post('cpassword'), $this->post('email'), $this->post('token'), $this->post('captcha'), $this->post('gtoken'));
	}

	function home() {
		if (!$this->account->is_loggedin()) {
			App::redirect('login');
		}
		$this->view('home.html', [
			'role' => $this->session('role'),
			'name' => $this->session('name')
		]);
	}

	function profile() {
		if (!$this->account->is_loggedin()) {
			App::redirect('login');
		}
		$this->view('profile.html', [
			'role' => $this->session('role'),
			'account' => $this->account
		]);
	}

	function profile_edit() {
		if (!$this->account->is_loggedin()) {
			App::redirect('login');
		}
		$msg = '';
		if ($this->post('save')) {
			$msg = $this->account->update_info($this->post('username'), $this->post('password'), $this->post('cpassword'), $this->post('email'));
			if ($msg == 'Success') {
				App::redirect('profile');
			}
		}
		$this->view('edit-profile.html', [
			'role' => $this->session('role'),
			'account' => $this->account,
			'msg' => $msg
		]);
	}

	function activate($email, $code) {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		if (!empty($email) && !empty($code)) {
			$activated = $this->account->activate($email, $code);
			$this->view('activate.html', [
				'activated' => $activated
			]);
		}
	}

	function forgot_password() {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		$msg = '';
		if ($this->post('email')) {
			$msg = $this->account->forgot_password($this->post('email'));
		}
		$this->view('forgot-password.html', [
			'msg' => $msg
		]);
	}

	function reset_password($email, $code) {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		if ($email && $code) {
			$msg = '';
			if ($this->post('npassword') && $this->post('cpassword')) {
				$msg = $this->account->reset_password($email, $code, $this->post('npassword'), $this->post('cpassword'));
			}
			$this->view('reset-password.html', [
				'msg' => $msg,
				'email' => $email,
				'code' => $code
			]);
		}
	}

	function resend_activation() {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		$msg = '';
		if ($this->post('email')) {
			$msg = $this->account->resend_activation($this->post('email'));
		}
		$this->view('resend-activation.html', [
			'msg' => $msg
		]);
	}

	function twofactor() {
		if ($this->account->is_loggedin()) {
			App::redirect('home');
		}
		$msg = '';
		if ($this->session('tfa_code') && $this->session('tfa_email') && $this->session('tfa_id')) {
			if ($this->post('code')) {
				if (!$this->account->verify_twofactor($this->session('tfa_id'), $this->session('tfa_email'), $this->post('code'))) {
					$msg = 'Incorrect code! Please try again!';
				} else {
					App::redirect('home');
				}
			} else {
				if (!$this->account->create_twofactor($this->session('tfa_id'), $this->session('tfa_email'))) {
					$msg = 'Account doesn\'t exist!';
				}
			}
			$this->view('twofactor.html', [
				'msg' => $msg
			]);		
		} else {
			App::redirect('login');
		}
	}

	function captcha() {
		$this->session_start();
		$captcha_code = substr(str_shuffle('01234567890123456789abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'), 0, 6);
		$this->session('captcha', $captcha_code);
		$final_image = imagecreate(150, 50);
		$rgba = [241, 245, 248, 0];
		$image_bg_color = imagecolorallocatealpha($final_image, 241, 245, 248, 0);
		$captcha_code_chars = str_split($captcha_code);
		for($i = 0; $i < count($captcha_code_chars); $i++) {
			$char_small = imagecreate(130, 16);
			$char_large = imagecreate(130, 16);
			$char_bg_color = imagecolorallocate($char_small, 241, 245, 248);
			$char_color = imagecolorallocate($char_small, rand(80,180), rand(80,180), rand(80, 180));
			imagestring($char_small, 1, 1, 0, $captcha_code_chars[$i], $char_color);
			imagecopyresampled($char_large, $char_small, 0, 0, 0, 0, rand(250, 400), 16, 84, 8);
			$char_large = imagerotate($char_large, rand(-6,6), 0);
			imagecopymerge($final_image, $char_large, 20 + (20 * $i), 15, 0, 0, imagesx($char_large), imagesy($char_large), 70);
			imagedestroy($char_small);
			imagedestroy($char_large);
		}
		header('Content-type: image/png');
		imagepng($final_image);
		imagedestroy($final_image);
	}

	function googleoauth() {
		if (!google_oauth_enabled) {
			return;
		}
		if ($this->get('code')) {
			$response = $this->account->google_oauth_authenticate($this->get('code'));
			if ($response == 'success') {
				App::redirect('home');
			} else {
				return $response;
			}
		} else {
			$params = [
				'response_type' => 'code',
				'client_id' => google_oauth_client_id,
				'redirect_uri' => google_oauth_redirect_uri,
				'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
				'access_type' => 'offline',
				'prompt' => 'consent'
			];
			header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
			exit;
		}
	}

	function facebookoauth() {
		if (!facebook_oauth_enabled) {
			return;
		}
		if ($this->get('code')) {
			$response = $this->account->facebook_oauth_authenticate($this->get('code'));
			if ($response == 'success') {
				App::redirect('home');
			} else {
				return $response;
			}
		} else {
			$params = [
				'client_id' => facebook_oauth_app_id,
				'redirect_uri' => facebook_oauth_redirect_uri,
				'response_type' => 'code',
				'scope' => 'email'
			];
			header('Location: https://www.facebook.com/dialog/oauth?' . http_build_query($params));
			exit;
		}
	}

	/* ADMIN METHODS */

	function admin_init() {
		if (!$this->account->is_loggedin()) {
			App::redirect('login');
		}
		if ($this->session('role') != 'Admin') {
			App::redirect('home');
		}
		$this->admin = new Admin($this);
	}

	function admin_dashboard() {
		$this->admin_init();
		$this->view('admin/dashboard.html', [
			'summary' => $this->admin->get_accounts_summary(),
			'selected' => 'dashboard',
			'selected_child' => ''
		]);		
	}

	function admin_accounts($msg = '', $search = '', $status = '', $activation = '', $role = '', $order = 'ASC', $order_by = 'id', $page = 1) {
		$this->admin_init();
		$results_per_page = 20;
		$accounts = $this->admin->get_accounts($search, $status, $activation, $role, $order, $order_by, $page, $results_per_page);	
		$url = App::root_url() . '/admin/accounts//' . $search . '/' . $status . '/' . $activation . '/' . $role;
		if ($msg == 'msg1') {
			$msg = 'Account created successfully!';
		} else if ($msg == 'msg2') {
			$msg = 'Account updated successfully!';
		} else if ($msg == 'msg3') {
			$msg = 'Account deleted successfully!';
		} else {
			$msg = '';
		}
		$this->view('admin/accounts.html', [
			'accounts' => $accounts['results'],
			'accounts_total' => $accounts['total'],
			'search' => $search,
			'status' => $status,
			'activation' => $activation,
			'role' => $role,
			'order' => $order,
			'order_by' => $order_by,
			'page' => $page,
			'results_per_page' => $results_per_page,
			'url' => $url,
			'msg' => $msg,
			'selected' => 'accounts',
			'selected_child' => 'view'
		]);
	}

	function admin_delete_account($id) {
		$this->admin_init();
		$this->admin->delete_account($id);
		App::redirect('admin/accounts/msg3');
	}

	function admin_account($id = NULL) {
		$this->admin_init();
		$page = 'Create';
		$error_msg = '';
		if ($id) {
			$account = $this->admin->get_account($id);
			$page = 'Edit';
			if ($this->post('submit')) {
				$result = $this->admin->update_account($id, $this->post('username'), $this->post('password'), $this->post('email'), $this->post('activation_code'), $this->post('rememberme'), $this->post('role'), $this->post('registered'), $this->post('last_seen'), $account);
				if ($result) {
					$error_msg = $result;
					$account = [
						'username' => $this->post('username'),
						'password' => $this->post('password'),
						'email' => $this->post('email'),
						'activation_code' => $this->post('activation_code'),
						'rememberme' => $this->post('rememberme'),
						'role' => $this->post('role'),
						'registered' => $this->post('registered'),
						'last_seen' => $this->post('last_seen')
					];
				} else {
					App::redirect('admin/accounts/msg2');
				}
			}
			if ($this->post('delete')) {
				App::redirect('admin/accounts/delete/' . $id);
			}
		} else {
			$account = $this->admin->get_account();
			if ($this->post('submit')) {
				$result = $this->admin->create_account($this->post('username'), $this->post('password'), $this->post('email'), $this->post('activation_code'), $this->post('rememberme'), $this->post('role'), $this->post('registered'), $this->post('last_seen'));
				if ($result) {
					$error_msg = $result;
					$account = [
						'username' => $this->post('username'),
						'password' => $this->post('password'),
						'email' => $this->post('email'),
						'activation_code' => $this->post('activation_code'),
						'rememberme' => $this->post('rememberme'),
						'role' => $this->post('role'),
						'registered' => $this->post('registered'),
						'last_seen' => $this->post('last_seen')
					];
				} else {
					App::redirect('admin/accounts/msg1');
				}
			}
		}
		$this->view('admin/account.html', [
			'account' => $account,
			'page' => $page,
			'roles' => ['Member', 'Admin'],
			'error_msg' => $error_msg,
			'selected' => 'accounts',
			'selected_child' => 'manage'
		]);
	}

	function admin_roles() {
		$this->admin_init();
		$this->view('admin/roles.html', [
			'roles' => $this->admin->get_roles(),
			'selected' => 'roles',
			'selected_child' => ''
		]);		
	}

	function admin_emailtemplates($msg = '') {
		$this->admin_init();
		if ($this->post('activation_email_template')) {
			file_put_contents('app/views/activation-email-template.html', $this->post('activation_email_template'));
			App::redirect('admin/emailtemplates/msg1');
		}
		if ($this->post('twofactor_email_template')) {
			file_put_contents('app/views/twofactor-email-template.html', $this->post('twofactor_email_template'));
			App::redirect('admin/emailtemplates/msg1');
		}
		if ($this->post('resetpass_email_template')) {
			file_put_contents('app/views/resetpass-email-template.html', $this->post('resetpass_email_template'));
			App::redirect('admin/emailtemplates/msg1');
		}
		if (file_exists('app/views/activation-email-template.html')) {
			$activation_email_template = file_get_contents('app/views/activation-email-template.html');
		}
		if (file_exists('app/views/twofactor-email-template.html')) {
			$twofactor_email_template = file_get_contents('app/views/twofactor-email-template.html');
		}
		if (file_exists('app/views/resetpass-email-template.html')) {
			$resetpass_email_template = file_get_contents('app/views/resetpass-email-template.html');
		}
		$msg = $msg == 'msg1' ? 'Email template updated successfully!' : '';
		$this->view('admin/emailtemplates.html', [
			'activation_email_template' => isset($activation_email_template) ? $activation_email_template : '',
			'twofactor_email_template' => isset($twofactor_email_template) ? $twofactor_email_template : '',
			'resetpass_email_template' => isset($resetpass_email_template) ? $resetpass_email_template : '',
			'msg' => $msg,
			'selected' => 'emailtemplate',
			'selected_child' => ''
		]);
	}

	function admin_settings($msg = '') {
		$this->admin_init();
		$contents = file_get_contents('config.php');
		if (!empty($this->post())) {
			foreach ($this->post() as $k => $v) {
				$v = in_array(strtolower($v), ['true', 'false']) ? strtolower($v) : '\'' . $v . '\'';
				$contents = preg_replace('/define\(\'' . $k . '\'\, ?(.*?)\)/s', 'define(\'' . $k . '\',' . $v . ')', $contents);
			}
			file_put_contents('config.php', $contents);
			App::redirect('admin/settings/msg1');
		}
		$msg = $msg == 'msg1' ? 'Settings updated successfully!' : '';
		$this->view('admin/settings.html', [
			'contents' => $contents,
			'msg' => $msg,
			'selected' => 'settings',
			'selected_child' => ''
		]);
	}

	function logout() {
		session_start();
		session_destroy();
		if (isset($_COOKIE['rememberme'])) {
		    unset($_COOKIE['rememberme']);
		    setcookie('rememberme', '', time() - 3600);
		}
		App::redirect('login');
	}

}

?>