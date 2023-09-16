<?php class_exists('View') or exit; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>Dashboard</title>
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
        <i class="fa-solid fa-gauge-high"></i>
        <div class="txt">
            <h2>Dashboard</h2>
            <p>View statistics, new accounts, and more.</p>
        </div>
    </div>
</div>

<div class="dashboard">
    <div class="content-block stat">
        <div class="data">
            <h3>New Accounts (&lt;1 day)</h3>
            <p><?=number_format(count($summary['new_accounts']))?></p>
        </div>
        <i class="fas fa-user-plus"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total accounts created today
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Total Accounts</h3>
            <p><?=number_format($summary['total_accounts'])?></p>
        </div>
        <i class="fas fa-users"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total accounts
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Active Accounts (&lt;30 days)</h3>
            <p><?=number_format($summary['active_accounts_month'])?></p>
        </div>
        <i class="fas fa-user-clock"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total active accounts
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Inactive Accounts (&gt;30 days)</h3>
            <p><?=number_format($summary['inactive_accounts'])?></p>
        </div>
        <i class="fas fa-user-clock"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total inactive accounts
        </div>
    </div>
</div>

<div class="content-title">
    <div class="title">
        <i class="fas fa-user-plus alt"></i>
        <div class="txt">
            <h2>New Accounts</h2>
            <p>Accounts created in the last &lt;1 day.</p>
        </div>
    </div>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Activation Code</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="responsive-hidden">Last Seen</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$summary['new_accounts']): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no newly registered accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($summary['new_accounts'] as $account): ?>
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

<div class="content-title" style="margin-top:40px">
    <div class="title">
        <i class="fas fa-user-clock alt"></i>
        <div class="txt">
            <h2>Active Accounts</h2>
            <p>Accounts active in the last &lt;1 day.</p>
        </div>
    </div>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Activation Code</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="responsive-hidden">Last Seen</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$summary['active_accounts_day']): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no active accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($summary['active_accounts_day'] as $account): ?>
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


        </main>
        <script src="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/admin.js" ?>"></script>
    </body>
</html>



