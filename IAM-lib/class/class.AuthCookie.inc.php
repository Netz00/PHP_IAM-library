<?php

class AuthCookie implements RememberMe
{
    private $rememberMeStorage;

    function __construct(RememberMeStorage $rememberMeStorage)
    {
        $this->rememberMeStorage = $rememberMeStorage;
        return $this;
    }

    function create($username)
    {
        $expTime = $this->getExpieryDate();
        $random_password = $this->generateRandomPassword();
        $random_selector = $this->generateRandomSelector();
        $expiry_date = date("Y-m-d H:i:s", $expTime);

        $this->rememberMeStorage->saveRememberMe(
            $username,
            password_hash($random_password, PASSWORD_DEFAULT),
            password_hash($random_selector, PASSWORD_DEFAULT),
            $expiry_date
        );

        setcookie("member_login", $username, $expTime);
        setcookie("random_password", $random_password, $expTime);
        setcookie("random_selector", $random_selector, $expTime);

        return true;
    }

    function remove($username)
    {
        if (!$this->isSetAuthCookie())
            return;
        $this->rememberMeStorage->invalidateRememberMe($username);
        $this->clearAuthCookie();
    }


    function hasValid()
    {
        if (!$this->isSetAuthCookie())
            return false;

        $current_date = date("Y-m-d H:i:s", time());
        $username = $_COOKIE["member_login"];

        $userToken = $this->rememberMeStorage->findValidRememberMe($username, $current_date);

        if (empty($userToken)) {
            $this->clearAuthCookie();
            return false;
        }

        if (
            !password_verify($_COOKIE["random_password"], $userToken["password_hash"])
            || !password_verify($_COOKIE["random_selector"], $userToken["selector_hash"])
        ) {
            $this->rememberMeStorage->invalidateRememberMe($username);
            $this->clearAuthCookie();
            return false;
        }

        return $userToken["username"];
    }

    private function clearAuthCookie()
    {
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


    private function isSetAuthCookie()
    {
        if (
            isset($_COOKIE['member_login']) && isset($_COOKIE['random_password']) && isset($_COOKIE['random_selector'])
            && !empty($_COOKIE["member_login"]) && !empty($_COOKIE["random_password"]) && !empty($_COOKIE["random_selector"])
        )
            return true;

        return false;
    }

    private function generateRandomSelector()
    {
        return random_bytes(32);
    }

    private function generateRandomPassword()
    {
        return random_bytes(16);
    }

    private function getExpieryDate()
    {
        return  time() + REMEMBER_ME_EXPIRATION_TIME;
    }
}
