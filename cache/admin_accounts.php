<?php class_exists('View') or exit; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>Accounts</title>
        <link href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/admin.css" ?>" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    </head>
    <body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>Admin</h1>
            <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin" ?>"<?php echo $selected == 'dashboard' ? ' class="selected"' : '' ?>><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts" ?>"<?php echo $selected == 'accounts' ? ' class="selected"' : '' ?>><i class="fas fa-users"></i>Accounts</a>
            <div class="sub">
                <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts" ?>"<?php echo $selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '' ?>><span>&#9724;</span>View Accounts</a>
                <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/account" ?>"<?php echo $selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '' ?>><span>&#9724;</span>Create Account</a>
            </div>
            <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/roles" ?>"<?php echo $selected == 'roles' ? ' class="selected"' : '' ?>><i class="fas fa-list"></i>Roles</a>
            <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/emailtemplates" ?>"<?php echo $selected == 'emailtemplate' ? ' class="selected"' : '' ?>><i class="fas fa-envelope"></i>Email Templates</a>
            <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/settings" ?>"<?php echo $selected == 'settings' ? ' class="selected"' : '' ?>><i class="fas fa-tools"></i>Settings</a>
            <div class="footer">
                <a href="https://codeshack.io/package/php/advanced-secure-login-registration-system/" target="_blank">Advanced Login & Registration</a>
                Version 2.0.0
            </div>
        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="space-between"></div>
                <div class="dropdown right">
                    <i class="fas fa-user-circle"></i>
                    <div class="list">
                        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/account" ?>/<?php echo $_SESSION['id'] ?>">Edit Profile</a>
                        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/logout" ?>">Logout</a>
                    </div>
                </div>
            </header>
            

<div class="content-title">
    <div class="title">
        <i class="fa-solid fa-users"></i>
        <div class="txt">
            <h2>Accounts</h2>
            <p>View, edit, and create accounts.</p>
        </div>
    </div>
</div>

<?php if ($msg): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?php echo $msg ?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/account" ?>" class="btn">Create Account</a>
    <form action="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts" ?>" method="get" class="filters-form">
        <div class="filters">
            <a href="#"><i class="fas fa-filter"></i> Filters</a>
            <div class="list">
                <label><input id="status_active" type="checkbox" name="status" value="active"<?php echo $status=='active'?' checked':'' ?>>Active</label>
                <label><input id="status_inactive" type="checkbox" name="status" value="inactive"<?php echo $status=='inactive'?' checked':'' ?>>Inactive</label>
                <label><input id="activation_pending" type="checkbox" name="activation" value="pending"<?php echo $activation=='pending'?' checked':'' ?>>Pending Activation</label>
                <?php if ($role): ?>
                <label><input id="role" type="checkbox" name="role" value="<?php echo $role ?>" checked><?php echo $role ?></label>
                <?php endif; ?>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search username or email..." value="<?php echo $search ?>" class="responsive-width-100">
                <i class="fas fa-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/id/1">#<?php if ($order_by=='id'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/username/1">Username<?php if ($order_by=='username'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/email/1">Email<?php if ($order_by=='email'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/activation_code/1">Activation Code<?php if ($order_by=='activation_code'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/role/1">Role<?php if ($order_by=='role'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/registered/1">Registered Date<?php if ($order_by=='registered'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?php echo $url ?>/<?php echo $order=='ASC'?'DESC':'ASC' ?>/last_seen/1">Last Seen<?php if ($order_by=='last_seen'): ?><i class="fas fa-level-<?php echo str_replace(['ASC', 'DESC'], ['up','down'], $order) ?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$accounts): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?php echo $account['id'] ?></td>
                    <td><?php echo $account['username'] ?></td>
                    <td class="responsive-hidden"><?php echo $account['email'] ?></td>
                    <td class="responsive-hidden"><?php echo $account['activation_code'] ? $account['activation_code'] : '--' ?></td>
                    <td class="responsive-hidden"><?php echo $account['role'] ?></td>
                    <td class="responsive-hidden"><?php echo $account['registered'] ?></td>
                    <td class="responsive-hidden" title="<?php echo $account['last_seen'] ?>"><?php echo App::time_elapsed_string($account['last_seen']) ?></td>
                    <td>
                        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/account" ?>/<?php echo $account['id'] ?>" class="link1">Edit</a>
                        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts/delete" ?>/<?php echo $account['id'] ?>" class="link1" onclick="return confirm('Are you sure you want to delete this account?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    <?php if ($page > 1): ?>
    <a href="<?php echo $url ?>/<?php echo $order ?>/<?php echo $order_by ?>/<?php echo $page-1 ?>">Prev</a>
    <?php endif; ?>
    <span>Page <?php echo $page ?> of <?php echo ceil($accounts_total / $results_per_page) == 0 ? 1 : ceil($accounts_total / $results_per_page) ?></span>
    <?php if ($page * $results_per_page < $accounts_total): ?>
    <a href="<?php echo $url ?>/<?php echo $order ?>/<?php echo $order_by ?>/<?php echo $page+1 ?>">Next</a>
    <?php endif; ?>
</div>


        </main>
        <script src="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/admin.js" ?>"></script>
    </body>
</html>



