<?php 
//handle password forgot
class pwdReset extends Db {

    //even if you fail to implement this will save you...
    static private $permissionToChangePWD=false;
    static private $userEmail;
    static private $areParamsValid=false;
    
    

    private function generateSelector() {
        return bin2hex(random_bytes(8));
    }


    private function generateValidator() {
        return random_bytes(32);
    }

    private function getExpirationTime() {
        return time() + RESET_RQUEST_EXPIRATION_TIME;
    }

    
    public function recoverRequest($email) {

                $selector = $this-> generateSelector();
                $token = $this-> generateValidator();
                $url= RESET_PASS_URL ."?selector=" . $selector . "&validator=" . bin2hex($token);

                $expires  = $this-> getExpirationTime();
                $expires  = date("Y-m-d H:i:s", $expires);

                $hashedToken=password_hash($token,PASSWORD_DEFAULT);



                $this -> deleteResetRequest($email);

                $query="INSERT INTO " . PWD_RESET_TABLE . " (pwdResetEmail,pwdResetSelector,pwdResetToken,pwdResetExpires) VALUES (?,?,?,?);";
                $result = $this -> insert($query,'ssss',array($email,$selector,$hashedToken,$expires));

                if($result === false) {
                    $error = $this -> error();
                    // Send the error to an administrator, log to a file, etc.
                    return false;
                } else {

                    $to=$email;
                    $subject = SUBJECT;
                    $message = MESSAGE1;
                    $message .= $url;
                    $message .= MESSAGE2;


                    $header = HEADER;
                    $message = wordwrap($message,200);
                    if(mail($to,$subject,$message,$header))
                        return true;
                }
                return false;
        
    }

    public function checkParams($selector,$validator){

            if(empty($selector) || empty($validator))  
            return 0;
            
            else
            if(ctype_xdigit($selector) !==false || ctype_xdigit($validator) !==false){
                self::$areParamsValid=true;
                return 1;
            }
            
        
            return 0;
    }
        

    function pwd_reset($selector,$validator) {
    
        if(!self::$areParamsValid)
        return false;
        
        $current_time = time();
        $currentDate = date("Y-m-d H:i:s", $current_time);
        
        $query="SELECT pwdResetToken,pwdResetEmail FROM " . PWD_RESET_TABLE . " WHERE pwdResetSelector=? AND pwdResetExpires >= ? LIMIT 1;";
        $rows = $this -> runQuery($query,'ss',array($selector,$currentDate));

        
        if($rows === false) {
            $error = $this -> error();
            // Handle error - inform administrator, log to file, show error page, etc.
            
            return false;
        }
        
                $pwdResetToken = $rows[0]["pwdResetToken"];
                $pwdResetEmail = $rows[0]["pwdResetEmail"];
            
            
                $tokenBin=hex2Bin($validator);

                if(password_verify($tokenBin,$pwdResetToken)){
                    self::$permissionToChangePWD=true;
                    self::$userEmail=$pwdResetEmail;
                    $this -> deleteResetRequest($pwdResetEmail);

                    return true;
                }
            return false;   
    }

    private function deleteResetRequest($email){
        $query="DELETE FROM " . PWD_RESET_TABLE . " WHERE pwdResetEmail=?;";
        $result = $this -> update($query,'s',array($email));
    }

    public function pwd_change($password,Sha256 $auth){
        
        if(self::$areParamsValid)
            if(self::$permissionToChangePWD)
                if(self::$userEmail!==''){
                    $newPwdHash=$auth->hash($password);
                    $query="UPDATE " . USERS_TABLE . "  SET password=? WHERE email=?;";
                    $result = $this -> update($query,'ss',array($newPwdHash,self::$userEmail));

                    self::$permissionToChangePWD=false;
                    self::$userEmail='';
                    return true;   
                }    
            return false;      
    }


}