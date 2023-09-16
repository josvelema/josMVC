<?php class_exists('View') or exit; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Profile</title>
		<link href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/style.css" ?>" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<header class="header">

			<div class="wrapper">

				<h1>Website Title</h1>

				<input type="checkbox" id="menu">
				<label for="menu">
					<i class="fa-solid fa-bars"></i>
				</label>
				
				<nav class="menu">
					<a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/home" ?>"><i class="fas fa-home"></i>Home</a>
					<a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/profile" ?>"><i class="fas fa-user-circle"></i>Profile</a>
					<?php if ($role == 'Admin'): ?>
					<a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/admin" ?>" target="_blank"><i class="fas fa-user-cog"></i>Admin</a>
					<?php endif ?>
					<a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/logout" ?>"><i class="fas fa-sign-out-alt"></i>Logout</a>
				</nav>

			</div>

		</header>
        
<div class="content profile">

    <h2>Profile Page</h2>

    <div class="block">

        <p>Your account details are below.</p>

        <div class="profile-detail">
            <strong>Username</strong>
            <?php echo $account->info('username') ?>
        </div>

        <div class="profile-detail">
            <strong>Email</strong>
            <?php echo $account->info('email') ?>
        </div>

        <div class="profile-detail">
            <strong>Role</strong>
            <?php echo $account->info('role') ?>
        </div>

        <div class="profile-detail">
            <strong>Registered</strong>
            <?php echo $account->info('registered') ?>
        </div>

        <a class="profile-btn" href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/profile/edit" ?>">Edit Details</a>

    </div>

</div>

	</body>
</html>



