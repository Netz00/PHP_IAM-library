<?php
$_C = array();

// -------------------- IDENTITY SETTINGS --------------------
$_C['MIN_USERNAME_LENGTH'] = 4;

// -------------------- REMEMBER ME --------------------
$_C['REMEMBER_ME_EXPIRATION_TIME'] = (30 * 24 * 60 * 60); // 30 days

// -------------------- PASSWORD RESET --------------------
$_C['PWD_RESET_URL'] = "http://localhost/reset";
$_C['PWD_RESET_REQ_EXPIRATION_TIME'] = (10 * 60); // 10 min

// -------------------- API THROTTLING --------------------
$_C['MAX_ACC_PER_IP'] = 5; //max number of accounts that can be made from same IP address
$_C['API_throttling'] = 100; //API throttling (max number of requests per minute, if reached, IP will be banned for next 24 hrs)
