<?php 

abstract class auth extends cookie {

    
protected abstract function hash($password);

protected abstract function isValidPassword($password, $hash);


 function register($username,$email,$password){

    $username = $this -> quote($username);
    $email = $this -> quote($email);
    $password = $this -> quote($password);
    $password=$this->hash($password);

    $query="INSERT INTO " . USERS_TABLE . " (username,email,password,ip_address) VALUES (?, ?, ?, ?);";
    $result = $this -> insert($query,'ssss',array($username,$email,$password,$_SERVER['REMOTE_ADDR']));

  

if($result === false) {
    $error = $this -> error();
    return $error;
    // Send the error to an administrator, log to a file, etc.
    return false;
} else {
    return true;
}
}

 function checkLogin($username,$password){

    $username = $this -> quote($username);
    $password = $this -> quote($password);

    $query="SELECT * FROM " . USERS_TABLE . " WHERE username = ? LIMIT 1;";
    $rows = $this -> runQuery($query,'s',array($username));

if($rows === false) {
    $error = $this -> error();
    // Handle error - inform administrator, log to file, show error page, etc.
    
    return $error;
}


    if($this->isValidPassword($password, $rows[0]["password"]))
    {
        self::$userID = $rows[0]["id"];
        self::$username = $rows[0]["username"];
        self::$userEmail = $rows[0]["email"];

        $this -> setSesion();

     return true;
    }
     

return false;
}



private function  setSesion(){
    session_start();
    self::$isLoggedIn = true;
    $_SESSION["user_id"] = self::$userID ;
}

public function  clearSesion(){
    self::$isLoggedIn = false;
    $_SESSION["user_id"] = "";
    session_destroy();
}




}