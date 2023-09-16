<?php

class Account {

    private $controller;
    private $app;
    private $db;
    private $info;

    function __construct($controller) {
        $this->controller = $controller;
        $this->app = $controller->app;
        $this->db = $controller->db;
    }

	function authenticate($username, $password, $remember, $token) {
        if (brute_force_protection) {
            $login_attempts = $this->login_attempts(FALSE);
            if ($login_attempts && $login_attempts['attempts_left'] <= 0) {
            	return 'You cannot login right now! Please try again later!';
            }
        }
        if (csrf_protection && (!isset($token) || $token != $this->controller->session('token'))) {
        	return 'Incorrect token provided!';
        }
        if (empty($username) || empty($password)) {
        	return 'Please fill both the username and password fields!';
        }
        $account = $this->db->query('SELECT * FROM accounts WHERE username = ?', $username)->fetchArray();
        if ($account) {
        	if (password_verify($password, $account['password'])) {
                $this->controller->session_start();
        		if (account_activation && $account['activation_code'] != 'activated') {
        			return 'Please activate your account to login! Click <a href="' . App::root_url()  . '/resendactivation">here</a> to resend the activation email.';
                } else if (twofactor_protection && $_SERVER['REMOTE_ADDR'] != $account['ip']) {
                    $this->controller->session('tfa_code', uniqid());
                    $this->controller->session('tfa_email', $account['email']);
                    $this->controller->session('tfa_id', $account['id']);
                    return 'tfa: twofactor';
        		} else {
                    $this->controller->session_regenerate_id();
        			$this->controller->session('loggedin', TRUE);
        			$this->controller->session('name', $account['username']);
        			$this->controller->session('id', $account['id']);
        			$this->controller->session('role', $account['role']);
                    if ($remember) {
        				$cookiehash = !empty($account['rememberme']) ? $account['rememberme'] : password_hash($account['id'] . $account['username'] . 'yoursecretkey', PASSWORD_DEFAULT);
        				$days = 30;
        				setcookie('rememberme', $cookiehash, (int)(time()+60*60*24*$days));
        				$this->db->query('UPDATE accounts SET rememberme = ? WHERE id = ?', $cookiehash, $account['id']);
        			}
                    $date = date('Y-m-d\TH:i:s');
                    $this->db->query('UPDATE accounts SET last_seen = ? WHERE id = ?', $date, $account['id']);
                    if (brute_force_protection) {
                        $this->db->query('DELETE FROM login_attempts WHERE ip_address = ?', $_SERVER['REMOTE_ADDR']);
                    }
        			return 'Success';
        		}
        	} else {
                if (brute_force_protection) {
                    $login_attempts = $this->login_attempts(TRUE);
                    return 'Incorrect username and/or password! You have ' . $login_attempts['attempts_left'] . ' attempts remaining!';
                }
        		return 'Incorrect username and/or password!';
        	}
        }
        if (brute_force_protection) {
            $login_attempts = $this->login_attempts(TRUE);
            return 'Incorrect username and/or password! You have ' . $login_attempts['attempts_left'] . ' attempts remaining!';
        }
        return 'Incorrect username and/or password!';
	}

    function register($username, $password, $cpassword, $email, $token, $captcha = NULL, $gtoken = NULL) {
        if (csrf_protection && (!isset($token) || $token != $this->controller->session('token'))) {
        	return 'Incorrect token provided!';
        }
        if (!isset($username, $password, $cpassword, $email)) {
        	return 'Please complete the registration form!';
        }
        if (captcha_protection && (!$captcha || !$this->controller->session('captcha'))) {
            return 'Please complete the registration form!';
        }
        if (empty($username) || empty($password) || empty($email)) {
        	return 'Please complete the registration form!';
        }
        if (captcha_protection && $this->controller->session('captcha') !== $captcha) {
            return 'Incorrect captcha code!';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        	return 'Please enter a valid email address!';
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            return 'Please enter a valid username!';
        }
        if (strlen($password) > 20 || strlen($password) < 5) {
        	return 'Password must be between 5 and 20 characters long!';
        }
        if ($cpassword != $password) {
        	return 'Passwords do not match!';
        }
        if (recaptcha_enabled) {
            if (!isset($gtoken)) {
                exit('Captcha verification failed! Please try again!');
            }
            if (empty($gtoken)) {
                exit('Captcha verification failed! Please try again!');
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'secret' => recaptcha_secret_key,
                'response' => $gtoken
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response, true);
            if (!$result['success'] || $result['score'] < 0.5) {
                exit('Captcha verification failed! Please try again!');
            }
        }
        $account = $this->db->query('SELECT * FROM accounts WHERE username = ? OR email = ?', $username, $email)->fetchArray();
        if ($account) {
        	return 'Username and/or email exists!';
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        	$uniqid = account_activation ? uniqid() : 'activated';
            $role = 'Member';
            $date = date('Y-m-d\TH:i:s');
            $ip = $_SERVER['REMOTE_ADDR'];
        	$this->db->query('INSERT INTO accounts (username, password, email, activation_code, role, registered, last_seen, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $username, $password, $email, $uniqid, $role, $date, $date, $ip);
        	if (account_activation) {
        		$this->send_activation_email($email, $uniqid);
        		return 'Please check your email to activate your account!';
        	} else {
                if (auto_login_after_register) {
                    $this->controller->session_start();
        			$this->controller->session_regenerate_id();	
                    $this->controller->session('loggedin', TRUE);
        			$this->controller->session('name', $username);
        			$this->controller->session('id', $this->db->lastInsertID());
        			$this->controller->session('role', $role);	
                    return 'autologin';
                } else {
        		    return 'You have successfully registered! You can now login!';
                }
        	}
        }
	}

    function is_loggedin() {
        if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme']) && !$this->controller->session('loggedin')) {
        	$account = $this->db->query('SELECT * FROM accounts WHERE rememberme = ?', $_COOKIE['rememberme'])->fetchArray();
        	if ($account) {
                $this->controller->session_start();
                $this->controller->session_regenerate_id();
                $this->controller->session('loggedin', TRUE);
                $this->controller->session('name', $account['username']);
                $this->controller->session('id', $account['id']);
                $this->controller->session('role', $account['role']);
                $date = date('Y-m-d\TH:i:s');
                $this->db->query('UPDATE accounts SET last_seen = ? WHERE id = ?', $date, $account['id']);
        	} else {
        		return FALSE;
        	}
        } else if (!$this->controller->session('loggedin')) {
        	return FALSE;
        }
        return TRUE;
    }

    function send_activation_email($email, $code) {
        $subject = 'Account Activation Required';
    	$headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    	$activate_link = App::root_url() . '/activate/' . $email . '/' . $code;
    	$email_template = str_replace('%link%', $activate_link, file_get_contents('app/views/activation-email-template.html'));
    	mail($email, $subject, $email_template, $headers);
    }

    function info($column) {
        if (!$this->info) {
            $this->info = $this->db->query('SELECT * FROM accounts WHERE id = ?', $this->controller->session('id'))->fetchArray();
        }
        return $this->info[$column];
    }

    function update_info($username, $password, $cpassword, $email) {
        if (empty($username) || empty($email)) {
            return 'The input fields must not be empty!';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please provide a valid email address!';
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            return 'Username must contain only letters and numbers!';
        }
        if (!empty($password) && (strlen($password) > 20 || strlen($password) < 5)) {
            return 'Password must be between 5 and 20 characters long!';
        }
        if ($cpassword != $password) {
            return 'Passwords do not match!';
        }
        $account = $this->db->query('SELECT * FROM accounts WHERE (username = ? OR email = ?) AND username != ? AND email != ?', $username, $email, $this->info('username'), $this->info('email'))->fetchArray();
        if ($account) {
            return 'Account already exists with that username and/or email!';
        }
        $uniqid = account_activation && $this->info('email') != $email ? uniqid() : $this->info('activation_code');
        $password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $this->info('password');
        $this->db->query('UPDATE accounts SET username = ?, password = ?, email = ?, activation_code = ? WHERE id = ?', $username, $password, $email, $uniqid, $this->info('id'));
        $this->controller->session('name', $username);
        if (account_activation && $this->info('email') != $email) {
            $this->send_activation_email($email, $uniqid);
            unset($_SESSION['loggedin']);
            return 'You have changed your email address! You need to re-activate your account!';
        }
        return 'Success';
    }

    function activate($email, $code) {
        $account = $this->db->query('SELECT * FROM accounts WHERE email = ? AND activation_code = ?', $email, $code)->fetchArray();
    	if ($account) {
    		$this->db->query('UPDATE accounts SET activation_code = "activated" WHERE email = ? AND activation_code = ?', $email, $code);
    		return TRUE;
    	}
    	return FALSE;
    }

    function forgot_password($email) {
        $account = $this->db->query('SELECT * FROM accounts WHERE email = ?', $email)->fetchArray();
        if ($account) {
            $uniqid = uniqid();
            $this->db->query('UPDATE accounts SET reset = ? WHERE email = ?', $uniqid, $email);
            $subject = 'Password Reset';
            $headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            $reset_link = App::root_url() . '/resetpassword/' . $email . '/' . $uniqid;
            $email_template = str_replace(['%link%', '%username%'], [$reset_link, $account['username']], file_get_contents('app/views/resetpass-email-template.html'));
            mail($email, $subject, $email_template, $headers);
            return 'Reset password link has been sent to your email!';
        }
        return 'Account does not exist with that email!';
    }

    function reset_password($email, $code, $npass, $cpass) {
        $account = $this->db->query('SELECT * FROM accounts WHERE email = ? AND reset = ?', $email, $code)->fetchArray();
        if ($account) {
            if (isset($npass, $cpass)) {
                if (strlen($npass) > 20 || strlen($npass) < 5) {
                	return 'Password must be between 5 and 20 characters long!';
                } else if ($npass != $cpass) {
                    return 'Passwords must match!';
                } else {
                    $password = password_hash($npass, PASSWORD_DEFAULT);
                    $this->db->query('UPDATE accounts SET password = ?, reset = "" WHERE email = ?', $password, $email);
                    return 'Password has been reset! You can now <a href="' . App::root_url() . '/">login</a>!';
                }
            }
        }
        return 'Incorrect email and/or code!';
    }

    function resend_activation($email) {
        $account = $this->db->query('SELECT * FROM accounts WHERE email = ? AND activation_code != "" AND activation_code != "activated"', $email)->fetchArray();
        if ($account) {
            $this->send_activation_email($email, $account['activation_code']);
            return 'Activaton link has been sent to your email!';
        } else {
            return 'We do not have an account with that email!';
        }
    }


	function login_attempts($update = TRUE) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$now = date('Y-m-d H:i:s');
		if ($update) {
			$this->db->query('INSERT INTO login_attempts (ip_address, `date`) VALUES (?,?) ON DUPLICATE KEY UPDATE attempts_left = attempts_left - 1, `date` = VALUES(`date`)', $ip, $now);
		}
		$login_attempts = $this->db->query('SELECT * FROM login_attempts WHERE ip_address = ?', $ip)->fetchArray();
		if ($login_attempts) {
			$expire = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($login_attempts['date'])));
			if ($now > $expire) {
				$this->db->query('DELETE FROM login_attempts WHERE ip_address = ?', $ip);
				$login_attempts = [];
			}
		}
		return $login_attempts;
	}

    function create_twofactor($id, $email) {
        $account = $this->db->query('SELECT * FROM accounts WHERE id = ? AND email = ?', $id, $email)->fetchArray();
        if ($account) {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $this->db->query('UPDATE accounts SET tfa_code = ? WHERE id = ?', $code, $id);
            $subject = 'Your Access Code';
            $headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            $email_template = str_replace('%code%', $code, file_get_contents('app/views/twofactor-email-template.html'));
            mail($account['email'], $subject, $email_template, $headers, mail_from);
            return $code;
        }
        return FALSE;
    }

    function verify_twofactor($id, $email, $code) {
        $account = $this->db->query('SELECT * FROM accounts WHERE id = ? AND email = ?', $id, $email)->fetchArray();
        if ($account && $code == $account['tfa_code']) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $this->db->query('UPDATE accounts SET ip = ? WHERE id = ?', $ip, $id);
            unset($_SESSION['tfa_code']);
            unset($_SESSION['tfa_email']);
            unset($_SESSION['tfa_id']);
            $this->controller->session_start();
            $this->controller->session_regenerate_id();
            $this->controller->session('loggedin', TRUE);
            $this->controller->session('name', $account['username']);
            $this->controller->session('id', $account['id']);
            $this->controller->session('role', $account['role']);
            $date = date('Y-m-d\TH:i:s');
            $this->db->query('UPDATE accounts SET last_seen = ? WHERE id = ?', $date, $account['id']);
            return TRUE;
        }
        return FALSE;
    }

    function google_oauth_authenticate($code) {
        $params = [
            'code' => $code,
            'client_id' => google_oauth_client_id,
            'client_secret' => google_oauth_client_secret,
            'redirect_uri' => google_oauth_redirect_uri,
            'grant_type' => 'authorization_code'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['access_token']) && !empty($response['access_token'])) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v3/userinfo');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
            $response = curl_exec($ch);
            curl_close($ch);
            $profile = json_decode($response, true);
            if (isset($profile['email'])) {
                $account = $this->db->query('SELECT id, role, username FROM accounts WHERE email = ?', $profile['email'])->fetchArray();
                $date = date('Y-m-d\TH:i:s');
                if (!$account) {
                    $username = '';
                    $google_name = '';
                    $google_name .= isset($profile['given_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['given_name']) : '';
                    $google_name .= isset($profile['family_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['family_name']) : '';
                    while (true) {
                        $username = !empty($google_name) ? $google_name . rand(0, 999) : explode('@', $profile['email'])[0] . rand(0, 999);
                        $result = $this->db->query('SELECT id FROM accounts WHERE username = ?', $username)->fetchArray();
                        if (!$result) {
                            break;
                        }
                    }
                    $role = 'Member';
                    $password = password_hash(uniqid() . $date, PASSWORD_DEFAULT);
                    $this->db->query('INSERT INTO accounts (username, password, email, activation_code, role, registered, last_seen) VALUES (?, ?, ?, "activated", ?, ?, ?)', $username, $password, $profile['email'], $role, $date, $date);
                    $id = $this->db->lastInsertId();
                } else {
                    $username = $account['username'];
                    $id = $account['id'];
                    $role = $account['role'];
                }
                $this->controller->session_start();
                $this->controller->session_regenerate_id();
                $this->controller->session('loggedin', TRUE);
                $this->controller->session('name', $username);
                $this->controller->session('id', $id);
                $this->controller->session('role', $role);
                $this->db->query('UPDATE accounts SET last_seen = ? WHERE id = ?', $date, $id);
                return 'success';
            } else {
                return 'Could not retrieve profile information! Please try again later!';
            }
        } else {
            return 'Invalid access token! Please try again later!';
        }
    }

    function facebook_oauth_authenticate($code) {
        $params = [
            'client_id' => facebook_oauth_app_id,
            'client_secret' => facebook_oauth_app_secret,
            'redirect_uri' => facebook_oauth_redirect_uri,
            'code' => $code
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/oauth/access_token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['access_token']) && !empty($response['access_token'])) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v6.0/me?fields=name,email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
            $response = curl_exec($ch);
            curl_close($ch);
            $profile = json_decode($response, true);
            if (isset($profile['email'])) {
                $account = $this->db->query('SELECT id, role, username FROM accounts WHERE email = ?', $profile['email'])->fetchArray();
                $date = date('Y-m-d\TH:i:s');
                if (!$account) {
                    $username = '';
                    $facebook_name = isset($profile['name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['name']) : '';
                    while (true) {
                        $username = !empty($facebook_name) ? $facebook_name . rand(0, 999) : explode('@', $profile['email'])[0] . rand(0, 999);
                        $result = $this->db->query('SELECT id FROM accounts WHERE username = ?', $username)->fetchArray();
                        if (!$result) {
                            break;
                        }
                    }
                    $role = 'Member';
                    $password = password_hash(uniqid() . $date, PASSWORD_DEFAULT);
                    $this->db->query('INSERT INTO accounts (username, password, email, activation_code, role, registered, last_seen) VALUES (?, ?, ?, "activated", ?, ?, ?)', $username, $password, $profile['email'], $role, $date, $date);
                    $id = $this->db->lastInsertId();
                } else {
                    $username = $account['username'];
                    $id = $account['id'];
                    $role = $account['role'];
                }
                $this->controller->session_start();
                $this->controller->session_regenerate_id();
                $this->controller->session('loggedin', TRUE);
                $this->controller->session('name', $username);
                $this->controller->session('id', $id);
                $this->controller->session('role', $role);
                $this->db->query('UPDATE accounts SET last_seen = ? WHERE id = ?', $date, $id);
                return 'success';
            } else {
                return 'Could not retrieve profile information! Please try again later!';
            }
        } else {
            return 'Invalid access token! Please try again later!';
        }
    }

}

?>