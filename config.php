<?php
// Ouput all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Your MySQL database hostname.
define('db_host','localhost');
// Your MySQL database username.
define('db_user','root');
// Your MySQL database password.
define('db_pass','');
// Your MySQL database name.
define('db_name','phplogin_mvc');
// Your MySQL database charset.
define('db_charset','utf8');
/* Registration */
// If enabled, the user will be redirected to the homepage automatically upon registration.
define('auto_login_after_register',false);
/* Account Activation */
// If enabled, the account will require email activation before the user can login.
define('account_activation',false);
// Change "Your Company Name" and "yourdomain.com" - do not remove the < and > characters.
define('mail_from','Your Company Name <noreply@yourdomain.com>');
// The link to the activation page.
define('activation_link','http://yourdomain.com/phplogin/activate');
/* Caching */
// MVC Config below
// Enable/disable caching. Only enable caching in production mode.
define('cache_enabled',false);
// The full path to your cache directory.
define('cache_path','cache/');
/* Add-ons */
// Prevent CSRF attacks with the CSRF add-on.
define('csrf_protection',false);
// Temporarily block visitors that exceed the login attempts threshold.
define('brute_force_protection',false);
// Add a two-factor email authentication to the login page.
define('twofactor_protection',false);
// The native captcha will help prevent bots from registering.
define('captcha_protection',false);
// reCAPTCHA v3 will prevent bots from registering.
define('recaptcha_enabled',false);
// Your reCAPTCHA v3 site key.
define('recaptcha_site_key','YOUR_SITE_KEY');
// Your reCAPTCHA v3 secret key.
define('recaptcha_secret_key','YOUR_SECRET_KEY');
// Google OAuth will enable your users to login with Google.
define('google_oauth_enabled',false);
// The OAuth client ID associated with your API console account.
define('google_oauth_client_id','YOUR_CLIENT_ID');
// The OAuth client secret associated with your API console account.
define('google_oauth_client_secret','YOUR_SECRET_KEY');
// The URL to the Google OAuth file.
define('google_oauth_redirect_uri','http://yourdomain.com/phplogin/googleoauth');
// Facebook OAuth will enable your users to login with Facebook.
define('facebook_oauth_enabled',false);
// The OAuth App ID associated with your Facebook App.
define('facebook_oauth_app_id','YOUR_APP_ID');
// The OAuth App secret associated with your Facebook App.
define('facebook_oauth_app_secret','YOUR_APP_SECRET_ID');
// The URL to the Facebook OAuth file.
define('facebook_oauth_redirect_uri','http://yourdomain.com/phplogin/facebookoauth');
?>