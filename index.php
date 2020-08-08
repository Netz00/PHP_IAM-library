<?php 


//take care for constatnts
include_once('constants.inc.php');

foreach ($C as $name => $val) {

    define($name, $val);
}



//include all classes
spl_autoload_register(function($class)
{
    $filename = "class/class.".$class.".inc.php";

    if (file_exists($filename)) {

        include_once($filename);
    }
});



//demo params
$username = "username";
$email    = "YOUREMAIL@gmail.com";
$pass    = "password";


$selector="798e51e6fc50e4a5";
$validator="9d4e78f0475de937d36d232a9700a53518b4a5a0a3e86138df8c24aa9d6432fb";





//change user credentials, leave empty for NO change
$username2 = "BoyO2";
$email2    = "";
$pass2     = "";





//test all class functions
$helper = new helper();
$auth = new Sha256();
$pwdReset = new pwdReset();




echo '<body style="background: black;color: yellowgreen;font-family: monospace;">';



        echo "USER EXISTS: ";
        echo ($helper->isLoginExists($username))?"Username taken already":"Username is free";

        echo "<br>";

        echo "EMAIL EXISTS: ";
        echo ($helper->isEmailExists($email))?"Email taken already":"Email is free";

        echo "<br>";

        echo "IP EXISTS: ";
        echo ($helper->isIP_Exists($email))?"IP taken already to many times":"IP is free"; //IP check

echo "<br>";

echo "<br>";

       // echo "REGISTER USER: ";
      //  echo ($auth->register($username,$email,$pass))?"Successful register!":"Register failed!";

echo "<br>";

echo "<br>";

        echo "LOGIN USER: ";
        echo ($auth->checkLogin($username,$pass))?"Successful login!":"Login failed!";

echo "<br>";
echo "<br>";

        echo "USERNAME: ";
        echo $auth->getUsername();

        echo "<br>";

        echo "USER EMAIL: ";
        echo $auth->getUserEmail();

        echo "<br>";

        echo "USER ID: ";
        echo $auth->getUserID();

        echo "<br>";

        echo "HASHED USER PASSWORD: ";
        echo $auth->hash($pass);


echo "<br>";
echo "<br>";

        echo "PASSWORD RESET REQUEST SEND: ";
        echo "OFF";
        //echo ($pwdReset->recoverRequest($email))?"Successful!":"Failed!";

        echo "<br>";

        echo "TOKEN AND VALIDATOR VERIFICATION: ";
        echo ($pwdReset->pwd_reset($selector,$validator))?"Valid!":"Invalid!";

        echo "<br>";

        echo "PASSWORD CHANGED: ";
        echo ($pwdReset->pwd_change($pass2,$auth))?"Successful!":"Failed!";


echo "<br>";
echo "<br>";

        echo "SESSION UNSET: ";
        echo "OFF";
        //echo $auth->clearSesion();

        echo "<br>";

        echo "SESSION ID: ";
        echo $_SESSION["user_id"];

echo "<br>";
echo "<br>";


        echo "ARE COOKIES SET: ";
        echo ($auth->isSetAuthCookie())?"YES":"NO";

        echo "<br>";

        if($auth->isSetAuthCookie()){
            //echo "CLEAR COOKIES...";
            //echo $auth->clearAuthCookie();
        }
        else{
            echo "SETTING COOKIE...: ";
            echo ($auth->createCookie($auth->getUsername()))?"Successful!":"Failed!";
        }


echo "<br>";

        echo "ARE COOKIES SET: ";
        echo ($auth->isSetAuthCookie())?"YES":"NO";

        echo "<br>";

        echo "USER LOGEN IN: ";
        echo ($auth->getLoginStatus())?"YES":"NO";

echo "<br>";

        if($auth->isSetAuthCookie()){
            echo "CHECKING COOKIES... ";
            $auth->verifyCookies();
        }
echo "<br>";

        echo "USER LOGEN IN: ";
        echo ($auth->getLoginStatus())?"YES":"NO";

echo "<br>";
echo "<br>";
     //   echo "USER DATA CHANGED: ";
     //   echo ($auth->updateUserData($username2,$email2,$pass2))?"YES":"NO";



?>