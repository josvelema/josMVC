<?php class_exists('View') or exit; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>Roles</title>
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
        <i class="fas fa-list"></i>
        <div class="txt">
            <h2>Roles</h2>
            <p>View, edit, and create accounts.</p>
        </div>
    </div>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>Role</td>
                    <td>Total Accounts</td>
                    <td>Active Accounts</td>
                    <td>Inactive Accounts</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$roles['roles']): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no roles</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($roles['roles'] as $k => $v): ?>
                <tr>
                    <td><?php echo $k ?></td>
                    <td><a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts" ?>/////<?php echo $k ?>/ASC/id/1" class="link1"><?php echo number_format($v) ?></a></td>
                    <td><a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts" ?>///active//<?php echo $k ?>/ASC/id/1" class="link1"><?php echo number_format(isset($roles['active'][$k]) ? $roles['active'][$k] : 0) ?></a></td>
                    <td><a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin/accounts" ?>///inactive//<?php echo $k ?>/ASC/id/1" class="link1"><?php echo number_format(isset($roles['inactive'][$k]) ? $roles['inactive'][$k] : 0) ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

        </main>
        <script src="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/admin.js" ?>"></script>
    </body>
</html>



