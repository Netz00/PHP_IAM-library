<?php
$_C = array();

// -------------------- IDENTITY SETTINGS --------------------
$_C['MIN_USERNAME_LENGTH'] = 4;

// -------------------- REMEMBER ME --------------------
$_C['REMEMBER_ME_EXPIRATION_TIME'] = (30 * 24 * 60 * 60); // 30 days

// -------------------- PASSWORD RESET URL --------------------
$_C['PWD_RESET_URL'] = "http://localhost/";
$_C['PWD_RESET_REQ_EXPIRATION_TIME'] = (10 * 60); // 10 min

/**
 * 
 * SMTP Settings | For password recovery | Data for SMTP can ask your hosting provider |
 * 
 *  */

$_C['APP_TITLE'] = 'MySQL Example';
$_C['SMTP_HOST'] = 'smtp.gmail.com';                             //SMTP host | Specify main and backup SMTP servers
$_C['SMTP_AUTH'] = true;                                         //SMTP auth (Enable SMTP authentication)
$_C['SMTP_SECURE'] = 'tls';                                      //SMTP secure (Enable TLS encryption, `ssl` also accepted)
$_C['SMTP_PORT'] = 587;                                          //SMTP port (TCP port to connect to)
$_C['SMTP_EMAIL'] = 'xxxxxxxx@xxxxxxxx.xxxxxxxx';                //SMTP email
$_C['SMTP_USERNAME'] = 'xxxxxxxx@xxxxxxxx.xxxxxxxx';             //SMTP username
$_C['SMTP_PASSWORD'] = 'xxxxxxxx';                       //SMTP password