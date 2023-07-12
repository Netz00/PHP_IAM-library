<?php

class IdentityAccessManager
{
    private $credentialsStorage;
    private $rememberMe;

    private $auth;
    private $kdf;

    function __construct(
        CredentialsStorage $credentialsStorage,
        Authentication $auth,
        KDF $kdf,
        RememberMe $rememberMe
    ) {
        $this->credentialsStorage = $credentialsStorage;
        $this->auth = $auth;
        $this->kdf = $kdf;
        $this->rememberMe = $rememberMe;
        return $this;
    }


    function register($username, $email, $password)
    {
        if (!Helper::isCorrectLogin($username))
            throw new Exception("Username invalid.");

        if (!Helper::isCorrectEmail($email))
            throw new Exception("Email invalid.");

        if (!Helper::isCorrectPassword($password))
            throw new Exception("Password invalid.");


        // If login or email are taken, mysqli will throw an error

        $this->credentialsStorage->addUser(
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
