<?php 

class helper extends Db {



    

    public function isEmailExists($user_email)
    {
        $user_email = helper::clearText($user_email);
        $user_email = $this -> quote($user_email);

        $query="SELECT COUNT(IF(email = ?,1, NULL)) 'Value' FROM " . USERS_TABLE . ";";
        $rows = $this -> runQuery($query,'s',array($user_email));
    
        if($rows === false) {
            $error = $this -> error();
            // Handle error - inform administrator, log to file, show error page, etc.
            
            return $error;
        }
        if($rows[0]['Value']===0)
        return false;

        return true;

    }

    public function isLoginExists($username)
    { 
        $username = helper::clearText($username);
        $username = $this -> quote($username);

        $query="SELECT COUNT(IF(username = ?,1, NULL)) 'Value' FROM " . USERS_TABLE . ";";
        $rows = $this -> runQuery($query,'s',array($username));

        if($rows === false) {
            $error = $this -> error();
            // Handle error - inform administrator, log to file, show error page, etc.
            
            return $error;
        }

        if($rows[0]['Value']===0)
        return false;

        return true;
    }


    function isIP_Exists(){
        $query="SELECT COUNT(IF(ip_address = ?,1, NULL)) 'Value' FROM " . USERS_TABLE . ";";
        $rows = $this -> runQuery($query,'s',array($this->ip_addr()));
    
        if($rows === false) {
        $error = $this -> error();
        // SEND USER EMAIL THAT SOMEONE ACCESSED HIS ACCOUNT FORM ANOTHER IP ADRESS
        
        return $error;
        }
        if($rows[0]['Value']<MAX_ACC_PER_IP)
            return false;

        return true;
    }


    


    static function isCorrectFullname($fullname)
    {
        if (strlen($fullname) > MIN_USERNAME_LENGTH) {

            return true;
        }

        return false;
    }

    static function isCorrectLogin($username)
    {
        if (preg_match("/^([a-zA-Z]{4,24})?([a-zA-Z][a-zA-Z0-9_]{4,24})$/i", $username)) {

            return true;
        }

        return false;
    }

    static function isCorrectPassword($password)
    {

        if (preg_match('/^[a-z0-9_\d$@$!%*?&]{6,20}$/i', $password)) {

            return true;
        }

        return false;
    }

    static function isCorrectEmail($email)
    {
        if (preg_match('/[0-9a-z_-]+@[-0-9a-z_^\.]+\.[a-z]{2,3}/i', $email)) {

            return true;
        }

        return false;
    }


    static function clearText($text)
    {
        $text = trim($text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text);

        return $text;
    }

    static function clearInt($value)
    {
        $value = intval($value);

        if ($value < 0) {

            $value = 0;
        }

        return $value;
    }

    static function ip_addr()
    {
        (string) $ip_addr = 'undefined';

        if (isset($_SERVER['REMOTE_ADDR'])) $ip_addr = $_SERVER['REMOTE_ADDR'];

        return $ip_addr;
    }


}