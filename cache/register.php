<?php class_exists('View') or exit; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Register</title>
		<link href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/app/static/style.css" ?>" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body>
		
<div class="register">

    <h1>Register</h1>

    <div class="links">
        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/login" ?>">Login</a>
        <a href="<?php echo "http://localhost/projects/phplogin/advanced_mvc/register" ?>" class="active">Register</a>
    </div>

    <form action="<?php echo "http://localhost/projects/phplogin/advanced_mvc/register_process" ?>" method="post" autocomplete="off">

        <input type="hidden" name="token" value="<?php echo $token ?>">

        <label for="username">
            <i class="fas fa-user"></i>
        </label>
        <input type="text" name="username" placeholder="Username" id="username" required>

        <label for="password">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>

        <label for="cpassword">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="cpassword" placeholder="Confirm Password" id="cpassword" required>

        <label for="email">
            <i class="fas fa-envelope"></i>
        </label>
        <input type="email" name="email" placeholder="Email" id="email" required>

        <?php if (captcha_protection): ?>
        <div class="captcha">
            <img src="<?php echo "http://localhost/projects/phplogin/advanced_mvc/captcha" ?>" width="150" height="50">
            <input type="text" id="captcha" name="captcha" placeholder="Enter captcha code" title="Please enter the captcha code!" required>
        </div>
        <?php endif; ?>

        <div class="msg"></div>

        <input type="submit" value="Register">

    </form>

</div>

		
<?php if (recaptcha_enabled): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo recaptcha_site_key ?>"></script>
<script>
let registrationForm = document.querySelector('.register form');
registrationForm.onsubmit = event => {
	event.preventDefault();
	grecaptcha.ready(() => {
		grecaptcha.execute('<?php echo recaptcha_site_key ?>', {action: 'submit'}).then(token => {
			registrationForm.querySelector('.msg').insertAdjacentHTML('beforebegin', '<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
			let formData = new FormData(registrationForm);
			formData.append('gtoken', token);
			fetch(registrationForm.action, { method: 'POST', body: formData }).then(response => response.text()).then(result => {
				if (result.toLowerCase().includes('autologin')) {
					window.location.href = 'home';
				} else {
					document.querySelector('.msg').innerHTML = result;
				}
			});  
		});
	});
};
</script>
<?php else: ?>
<script>
let registrationForm = document.querySelector('.register form');
registrationForm.onsubmit = event => {
    event.preventDefault();
    fetch(registrationForm.action, { method: 'POST', body: new FormData(registrationForm) }).then(response => response.text()).then(result => {
        if (result.toLowerCase().includes('autologin')) {
            window.location.href = 'home';
        } else {
            document.querySelector('.msg').innerHTML = result;
        }
    });
};
</script>
<?php endif; ?>

	</body>
</html>





