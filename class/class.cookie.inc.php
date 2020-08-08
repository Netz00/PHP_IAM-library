<?php 
//handle session and cookies
abstract class cookie extends user {

    


    private function generateRandomSelector(){
        return random_bytes(32);
    }

    private function generateRandomPassword(){
        return random_bytes(16);
    }

    private function getExpieryDate(){
        return  time()+COOKIE_EXPIRATION_TIME;
    }    





    public function  createCookie($username){

        $expTime=$this->getExpieryDate();
        $random_password = random_bytes(16);
        $random_selector = random_bytes(32);
        $expiry_date = date("Y-m-d H:i:s", $expTime);

        // Insert new token
        $result=$this->insertToken($username, $random_password, $random_selector, $expiry_date);

        if($result){
            setcookie("member_login", $username, $expTime);
            setcookie("random_password", $random_password, $expTime);
            setcookie("random_selector", $random_selector, $expTime);
            return true;
        }

        return false;
    }



    private function insertToken($username, $random_password, $random_selector, $expiry_date){
        
        $random_password_hash = password_hash($random_password,PASSWORD_DEFAULT);
        $random_selector_hash = password_hash($random_selector,PASSWORD_DEFAULT);

        $query="INSERT INTO " . COOKIE_TABLE . " (username, password_hash, selector_hash, expiry_date) VALUES (?, ?, ?, ?);";
        $result = $this -> insert($query,'ssss',array($username, $random_password_hash, $random_selector_hash, $expiry_date));

        if($result === false) {
            $error = $this -> error();
            // Send the error to an administrator, log to a file, etc.
            return false;
        } 

        return true;
    }



    public function isSetAuthCookie(){
        if (isset($_COOKIE['member_login'])) 
            if (isset($_COOKIE['random_password'])) 
                if (isset($_COOKIE['random_selector'])) 
                    if (! empty($_COOKIE["member_login"]) && ! empty($_COOKIE["random_password"]) && ! empty($_COOKIE["random_selector"]))
                        return true;

        return false;
    }

    public function clearAuthCookie(){

        $username=$_COOKIE['member_login'];
        $this->markExistingToken($username);

        if (isset($_COOKIE['member_login'])) {
            unset($_COOKIE['member_login']); 
            setcookie('member_login', null, -1, '/'); 
        }
        if (isset($_COOKIE['random_password'])) {
            unset($_COOKIE['random_password']); 
            setcookie('random_password', null, -1, '/'); 
        }
        if (isset($_COOKIE['random_selector'])) {
            unset($_COOKIE['random_selector']); 
            setcookie('random_selector', null, -1, '/'); 
        }

        

    }


    //get active cookie ID and turn it off
    private function markExistingToken($username){

        $query="SELECT id FROM " . COOKIE_TABLE . " WHERE (is_expired = 0 AND username = ?);";
        $rows = $this -> runQuery($query,'s',array($username));

        if($rows === false) {
            $error = $this -> error();
            // Send the error to an administrator, log to a file, etc.
            return false;
        } 


        $idsString=implode(',', array_map('intval',$this->array_2d_to_1d($rows,"id")));

        $result = $this -> runBaseQuery('UPDATE ' . COOKIE_TABLE . ' SET is_expired = 1 WHERE id IN(' .  $idsString . ');');

        if($result === false) {
            $error = $this -> error();
            // Send the error to an administrator, log to a file, etc.
            return false;
        } 

        return true;
    }
    //input array and name of 2nd parameter,"id","name"...
    public function array_2d_to_1d ($input_array,$param) {
        $output_array = array();

        for ($i = 0; $i < count($input_array); $i++) {
            $output_array[] = $input_array[$i][$param];
        }

        return $output_array;
    }




    private function getTokenByUsername($username,$expired){

    
        $query="SELECT id,password_hash,selector_hash,expiry_date FROM " . COOKIE_TABLE . " WHERE (username = ? AND is_expired = ?);";
        $rows = $this -> runQuery($query,'si',array($username, $expired));

        if($rows === false) {
            $error = $this -> error();
            // Send the error to an administrator, log to a file, etc.
            return false;
        } 

        
    return $rows;
    }



    function checkSession(){

        if (! empty($_SESSION["user_id"])) {
            self::$isLoggedIn = true;
        }
    }






    function verifyCookies(){

    if ($this->isSetAuthCookie()) {
        
        $current_time = time();
        $current_date = date("Y-m-d H:i:s", $current_time);

        $isPasswordVerified = false;
        $isSelectorVerified = false;
        $isExpiryDateVerified = false;
        
        $username=$_COOKIE["member_login"];
        // Get token for username
        $userToken = $this->getTokenByUsername($username,0);
        
        // Validate random password cookie with database
        if (password_verify($_COOKIE["random_password"], $userToken[0]["password_hash"])) {
            $isPasswordVerified = true;
        }
        
        // Validate random selector cookie with database
        if (password_verify($_COOKIE["random_selector"], $userToken[0]["selector_hash"])) {
            $isSelectorVerified = true;
        }
        
        // check cookie expiration by date
        if($userToken[0]["expiry_date"] >= $current_date) {
            $isExpiryDateVerified = true;
        }
        
        // Redirect if all cookie based validation retuens true
        // Else, mark the token as expired and clear cookies
        if (!empty($userToken[0]["id"]) && $isPasswordVerified && $isSelectorVerified && $isExpiryDateVerified) {
            self::$isLoggedIn = true;
        } else {
            if(!empty($userToken)) {
                $controller->markExistingToken($user);
            }
            // clear cookies
            $util->clearAuthCookie();
        }
    }

    }

}