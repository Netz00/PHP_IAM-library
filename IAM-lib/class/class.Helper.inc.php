<?php

class Helper
{
    static function isCorrectLogin($username)
    {
        if (preg_match("/^([a-zA-Z]{" . MIN_USERNAME_LENGTH . ",24})?([a-zA-Z][a-zA-Z0-9_]{" . (MIN_USERNAME_LENGTH - 1) . ",24})$/i", $username))
            return true;

        return false;
    }

    static function isCorrectEmail($email)
    {
        if (preg_match('/[0-9a-z_-]+@[-0-9a-z_^\.]+\.[a-z]{2,63}$/i', $email))
            return true;

        return false;
    }


    static function isCorrectPassword($password)
    {

        if (preg_match('/^[a-z0-9_\d$@$!%*?&]{6,20}$/i', $password))
            return true;

        return false;
    }

    static function ip_addr()
    {
        (string) $ip_addr = 'undefined';

        if (isset($_SERVER['REMOTE_ADDR'])) $ip_addr = $_SERVER['REMOTE_ADDR'];

        return $ip_addr;
    }
}
