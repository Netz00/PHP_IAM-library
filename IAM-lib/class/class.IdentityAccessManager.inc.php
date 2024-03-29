<?php

class IdentityAccessManager
{
    private $credentialsStorage;
    private $rememberMe;

    private $auth;
    private $kdf;

    function __construct(
        CredentialsStorage $credentialsStorage,
        RememberMe $rememberMe
    ) {
        $this->credentialsStorage = $credentialsStorage;
        $this->auth = new Session();
        $this->kdf = new Sha256();
        $this->rememberMe = $rememberMe;
        return $this;
    }

    function setThisKDF(KDF $kdf)
    {
        $this->kdf = $kdf;
        return $this;
    }
    function setThisAuthentication(Authentication $auth)
    {
        $this->auth = $auth;
        return $this;
    }
    function setThisRememberMe(RememberMe $rememberMe)
    {
        $this->rememberMe = $rememberMe;
        return $this;
    }


    // ---------------------- Password reset ----------------------

    private $pwdReset;
    function setPwdReset(PwdResetStorage $pwdResetStorage)
    {
        $this->pwdReset = new PwdReset($pwdResetStorage);
    }

    function createPwdResetRequest($email)
    {
        if (!Helper::isCorrectEmail($email))
            throw new Exception("Email invalid.");

        $userArray = $this->credentialsStorage->findUserbyEmail($email);

        if ($userArray == null)
            throw new Exception("Email invalid.");

        $this->pwdReset->createRequest($userArray["email"]);

        return true;
    }
    function resetPwd($selector, $validator, $newPassword)
    {
        if (!Helper::isCorrectPassword($newPassword))
            throw new Exception("Password invalid.");

        $this->credentialsStorage->updateIdentityDataByEmail(
            $this->pwdReset->verifyRequest($selector, $validator, $newPassword),
            "password",
            $this->kdf->hash($newPassword)
        );
    }

    // -----------------------------------------------------------


    function register($username, $email, $password)
    {
        if (!Helper::isCorrectLogin($username))
            throw new Exception("Username invalid.");

        if (!Helper::isCorrectEmail($email))
            throw new Exception("Email invalid.");

        if (!Helper::isCorrectPassword($password))
            throw new Exception("Password invalid.");


        // If login or email are taken, mysqli will throw an error

        $this->credentialsStorage->saveUser(
            $username,
            $email,
            $this->kdf->hash($password),
            Helper::ip_addr()
        );
        header("Location: /",  true,  301);
        exit;
    }

    function login($username, $password, $rememberMe)
    {

        if (!Helper::isCorrectLogin($username))
            throw new Exception("Username invalid.");

        if (!Helper::isCorrectPassword($password))
            throw new Exception("Password invalid.");


        $userArray = $this->credentialsStorage->findUserByUsername($username);

        if ($userArray == null || !$this->kdf->compare($password, $userArray["password"]))
            throw new Exception("Invalid credentials.");

        $this->auth->set($userArray["id"]);

        if ($rememberMe)
            $this->rememberMe->create($userArray["username"]);

        header("Location: /",  true,  301);
        exit;
    }

    function isUserLoggedIn()
    {
        $user_id = $this->auth->get();
        if ($user_id) {
            $userArray = $this->credentialsStorage->findUserbyId($user_id);
            return new User($userArray["id"], $userArray["username"]);
        }

        $username = $this->rememberMe->hasValid();
        if ($username) {
            $userArray = $this->credentialsStorage->findUserByUsername($username["username"]);
            return new User($userArray["id"], $userArray["username"]);
        }

        return null;
    }

    function logout(User $user)
    {
        $this->auth->clear();
        $this->rememberMe->remove($user->username);
        header("Location: /",  true,  301);
        exit;
    }
}
