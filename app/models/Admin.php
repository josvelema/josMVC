<?php

class Admin {

    private $controller;
    private $app;
    private $db;

    function __construct($controller) {
        $this->controller = $controller;
        $this->app = $controller->app;
        $this->db = $controller->db;
    }

    function get_accounts($search = '', $status = '', $activation = '', $role = '', $order = 'ASC', $order_by = 'id', $page = 1, $results_per_page = 20) {
		$order = $order == 'DESC' ? 'DESC' : 'ASC';
		$order_by_whitelist = ['id','username','email','activation_code','role','registered','last_seen'];
		$order_by = $order_by && in_array($order_by, $order_by_whitelist) ? $order_by : 'id';
        $param1 = ($page - 1) * $results_per_page;
        $param2 = $results_per_page;
        $param3 = '%' . $search . '%';
        $where = '';
        $where .= $search ? 'WHERE (username LIKE ? OR email LIKE ?) ' : '';
        if ($status == 'active') {
            $where .= $where ? 'AND last_seen > date_sub(now(), interval 1 month) ' : 'WHERE last_seen > date_sub(now(), interval 1 month) ';
        }
        if ($status == 'inactive') {
            $where .= $where ? 'AND last_seen < date_sub(now(), interval 1 month) ' : 'WHERE last_seen < date_sub(now(), interval 1 month) ';
        }
        if ($activation == 'pending') {
            $where .= $where ? 'AND activation_code != "activated" ' : 'WHERE activation_code != "activated" ';
        }
        if ($role) {
            $where .= $where ? 'AND role = ? ' : 'WHERE role = ? ';
        }
        $params = [];
        if ($search) {
            $params[] = $param3;
            $params[] = $param3;
        }
        if ($role) {
            $params[] = $role;
        }
        if ($params) {
            $total = $this->db->query('SELECT COUNT(*) AS total FROM accounts ' . $where, $params)->fetchArray();
        } else {
            $total = $this->db->query('SELECT COUNT(*) AS total FROM accounts ' . $where)->fetchArray();
        }
        $params[] = $param1;
        $params[] = $param2;      
        $accounts = $this->db->query('SELECT * FROM accounts ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT ?,?', $params)->fetchAll();
        return [
            'results' => $accounts,
            'total' => $total['total']
        ];
    }

    function get_accounts_summary() {
        $new_accounts = $this->db->query('SELECT * FROM accounts WHERE cast(registered as DATE) = cast(now() as DATE) ORDER BY registered DESC')->fetchAll();
        $total_accounts = $this->db->query('SELECT COUNT(*) AS total FROM accounts')->fetchArray();
        $inactive_accounts = $this->db->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month)')->fetchArray();
        $active_accounts_day = $this->db->query('SELECT * FROM accounts WHERE last_seen > date_sub(now(), interval 1 day) ORDER BY last_seen DESC')->fetchAll();
        $active_accounts_month = $this->db->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month)')->fetchArray();
        return [
            'new_accounts' => $new_accounts,
            'total_accounts' => $total_accounts['total'],
            'inactive_accounts' => $inactive_accounts['total'],
            'active_accounts_day' => $active_accounts_day,
            'active_accounts_month' => $active_accounts_month['total']
        ];
    }

    function get_account($id = NULL) {
        $account = [
            'username' => '',
            'password' => '',
            'email' => '',
            'activation_code' => '',
            'rememberme' => '',
            'role' => 'Member',
            'registered' => date('Y-m-d\TH:i:s'),
            'last_seen' => date('Y-m-d\TH:i:s')
        ];
        if ($id) {
            $account = $this->db->query('SELECT * FROM accounts WHERE id = ?', $id)->fetchArray();
        }
        return $account;
    }

    function update_account($id, $username, $password, $email, $activation_code, $rememberme, $role, $registered, $last_seen, $acc) {
        $result = $this->db->query('SELECT id FROM accounts WHERE username = ? AND username != ?', $username, $acc['username'])->fetchArray();
        if ($result) {
            return 'Username already exists!';
        }
        $result = $this->db->query('SELECT id FROM accounts WHERE email = ? AND email != ?', $email, $acc['email'])->fetchArray();
        if ($result) {
            return 'Email already exists!';
        }        
        $account = $this->db->query('SELECT password FROM accounts WHERE id = ?', $id)->fetchArray();
        $password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $account['password'];
        $this->db->query('UPDATE accounts SET username = ?, password = ?, email = ?, activation_code = ?, rememberme = ?, role = ?, registered = ?, last_seen = ? WHERE id = ?', $username, $password, $email, $activation_code, $rememberme, $role, $registered, $last_seen, $id);
        return '';
    }

    function create_account($username, $password, $email, $activation_code, $rememberme, $role, $registered, $last_seen) {
        $result = $this->db->query('SELECT id FROM accounts WHERE username = ?', $username)->fetchArray();
        if ($result) {
            return 'Username already exists!';
        }
        $result = $this->db->query('SELECT id FROM accounts WHERE email = ?', $email)->fetchArray();
        if ($result) {
            return 'Email already exists!';
        } 
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $this->db->query('INSERT INTO accounts (username,password,email,activation_code,rememberme,role,registered,last_seen) VALUES (?,?,?,?,?,?,?,?)', $username, $password, $email, $activation_code, $rememberme, $role, $registered, $last_seen);
    }

    function delete_account($id) {
        $this->db->query('DELETE FROM accounts WHERE id = ?', $id);
    }

    function get_roles() {
        $roles_list = ['Member', 'Admin'];
        $roles = $this->db->query('SELECT role, COUNT(*) as total FROM accounts GROUP BY role')->fetchAll();
        $roles = array_column($roles, 'total', 'role');
        foreach ($roles_list as $r) {
            if (!isset($roles[$r])) $roles[$r] = 0;
        }
        $active = $this->db->query('SELECT role, COUNT(*) as total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month) GROUP BY role')->fetchAll();
        $inactive = $this->db->query('SELECT role, COUNT(*) as total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month) GROUP BY role')->fetchAll();
        return [
            'roles' => $roles,
            'active' => array_column($active, 'total', 'role'),
            'inactive' => array_column($inactive, 'total', 'role')
        ];
    }

    static function format_key($key) {
        $key = str_replace(['_', 'url', 'db ', ' pass', ' user', 'csrf', ' uri', 'oauth', 'recaptcha', ' id', 'twofactor '], [' ', 'URL', 'Database ', ' Password', ' Username', 'CSRF', 'URI', 'OAuth', 'reCAPTCHA', ' ID', 'Two-Factor '], strtolower($key));
        return ucwords($key);
    }

    static function format_var_html($key, $value, $comment) {
        $html = '';
        $type = 'text';
        $value = htmlspecialchars(trim($value, '\''), ENT_QUOTES);
        $type = strpos($key, 'pass') !== false ? 'password' : $type;
        $type = in_array(strtolower($value), ['true', 'false']) ? 'checkbox' : $type;
        $checked = strtolower($value) == 'true' ? ' checked' : '';
        $html .= '<label for="' . $key . '">' . self::format_key($key) . '</label>';
        if (substr($comment, 0, 2) === '//') {
            $html .= '<p class="comment">' . ltrim($comment, '//') . '</p>';
        }
        if ($type == 'checkbox') {
            $html .= '<input type="hidden" name="' . $key . '" value="false">';
        }
        $html .= '<input type="' . $type . '" name="' . $key . '" id="' . $key . '" value="' . $value . '" placeholder="' . self::format_key($key) . '"' . $checked . '>';
        return $html;
    }

    static function format_tabs($contents) {
        $rows = explode("\n", $contents);
        echo '<div class="tabs">';
        echo '<a href="#" class="active">General</a>';
        for ($i = 0; $i < count($rows); $i++) {
            preg_match('/\/\*(.*?)\*\//', $rows[$i], $match);
            if ($match) {
                echo '<a href="#">' . $match[1] . '</a>';
            }
        }
        echo '</div>';
    }

    static function format_form($contents) {
        $rows = explode("\n", $contents);
        echo '<div class="tab-content active">';
        for ($i = 0; $i < count($rows); $i++) {
            preg_match('/\/\*(.*?)\*\//', $rows[$i], $match);
            if ($match) {
                echo '</div><div class="tab-content">';
            }
            preg_match('/define\(\'(.*?)\', ?(.*?)\)/', $rows[$i], $match);
            if ($match) {
                echo self::format_var_html($match[1], $match[2], $rows[$i-1]);
            }
        }  
        echo '</div>';
    }

}

?>