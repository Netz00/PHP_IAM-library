<?php

class Session implements Authentication
{
    public function set($userID)
    {
        $_SESSION["user_id"] = $userID;
    }
    public function get()
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))
            return $_SESSION['user_id'];


        return false;
    }

    public function clear()
    {
        $_SESSION["user_id"] = "";
        session_destroy();
    }
}
