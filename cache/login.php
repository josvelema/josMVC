<?php class_exists('View') or exit; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Login</title>
		<link href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/style.css" ?>" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body>
		
<div class="login">

    <h1>Login</h1>

    <div class="links">
        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/login" ?>" class="active">Login</a>
        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/register" ?>">Register</a>
    </div>

    <form action="<?php echo "http://localhost/projects/phplogin/advanced_mvc/authenticate" ?>" method="post">

        <input type="hidden" name="token" value="<?php echo $token ?>">

        <label for="username">
            <i class="fas fa-user"></i>
        </label>
        <input type="text" name="username" placeholder="Username" id="username" required>

        <label for="password">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>

        <label id="rememberme">
            <input type="checkbox" name="rememberme">Remember me
        </label>

        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/forgotpassword" ?>">Forgot Password?</a>

        <?php if (google_oauth_enabled): ?>
        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/googleoauth" ?>" class="gl-btn"><i class="fa-brands fa-google"></i>Login with Google</a>
        <?php endif; ?>

        <?php if (facebook_oauth_enabled): ?>
        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/facebookoauth" ?>" class="fb-btn"><i class="fa-brands fa-google"></i>Login with Facebook</a>
        <?php endif; ?>

        <div class="msg"></div>

        <input type="submit" value="Login">

    </form>

</div>

		
<script>
let loginForm = document.querySelector('.login form');
loginForm.onsubmit = event => {
    event.preventDefault();
    fetch(loginForm.action, { method: 'POST', body: new FormData(loginForm) }).then(response => response.text()).then(result => {
        if (result.toLowerCase().includes('success')) {
            window.location.href = 'home';
        } else if (result.includes('tfa:')) {
            window.location.href = result.replace('tfa: ', '');
        } else {
            document.querySelector('.msg').innerHTML = result;
        }
    });
};
</script>

	</body>
</html>





