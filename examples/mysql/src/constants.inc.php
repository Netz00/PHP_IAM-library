<?php
$C = array();

//DATABASE TABLE NAMES
$C['USERS_TABLE'] = "users";
$C['COOKIE_TABLE'] = "tbl_token_auth";
$C['PWD_RESET_TABLE'] = "pwdreset";



$C['MIN_USERNAME_LENGTH'] = 4;


$C['COOKIE_EXPIRATION_TIME'] = (30 * 24 * 60 * 60); //seconds, 600 s = 10 min


//RESET PASSWORD...
$C['RESET_PASS_URL'] = "http://smarthome.localhost/resetPassword";
$C['RESET_RQUEST_EXPIRATION_TIME'] = 600; //seconds, 600 s = 10 min



$C['SUBJECT'] = 'Reset your password';
//simple version:
//$C['MESSAGE']='<p>We recieved a password reset request.'; 
//$C['MESSAGE'].='The link to reset your password is bellow, if you did not make this request, you can ignore this email.</p>';
//$C['MESSAGE'].='<p>Here is your password reset link: <br>';

$C['HEADER'] = "From: wgcraft <wgcraft.official@gmail.com>\r\n";
$C['HEADER'] .= "Reply-To: wgcraft.official@gmail.com\r\n";
$C['HEADER'] .= "Content-type: text/html\r\n";