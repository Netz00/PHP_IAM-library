<?php 
//change username,email,passworde,delete account,get user data
abstract class user extends Db {

    protected static $isLoggedIn=false;
    protected static $userID;
    protected static $username;
    protected static $userEmail;
  

    public function getLoginStatus(){
        if (self::$userID !== null && self::$username !== null && self::$userEmail !== null) 
            if(self::$isLoggedIn)
                return true;

        return false;
    }
    public function getUserID(){return self::$userID;}
    public function getUsername(){return self::$username;}
    public function getUserEmail(){return self::$userEmail;}
    



     function updateUserData($username,  $email, $password){
        
        if(!$this->getLoginStatus())
            return false;
        

            $username = ($username!=="")?($username):(self::$username);
            $email = ($email!=="")?($email):(self::$userEmail);

        if($password!==""){
            $hash = $this->hash($password);
            $query="UPDATE " . USERS_TABLE . " SET username=?, password=?, email=?   WHERE id=?;";
            $result = $this -> update($query,'sssi',array($username, $hash, $email, self::$userID));
        }
        else{
            $query="UPDATE " . USERS_TABLE . " SET username=?, email=? WHERE id=?;";
            $result = $this -> update($query,'ssi',array($username, $email, self::$userID));
        }

        if($result === false) {
            $error = $this -> error();
            // Send the error to an administrator, log to a file, etc.
            return false;
        } 

        return true;
    }
    
       


   


function getUserData(){

    if(!$this->getLoginStatus())
    return false;

    //here you can request user messages, photos, anything thats specific to each user

    
}



}