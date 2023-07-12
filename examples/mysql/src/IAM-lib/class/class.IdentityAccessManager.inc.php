<?php

class IdentityAccessManager
{

    private $credentialsStorage;
    private $rememberMe;

    private $auth;
    private $kdf;
    private $helper;

    function __construct(
        CredentialsStorage $credentialsStorage,
        Authentication $auth,
        KDF $kdf,
        Helper $helper,
        RememberMe $rememberMe
    ) {
        $this->credentialsStorage = $credentialsStorage;
        $this->auth = $auth;
        $this->kdf = $kdf;
        $this->helper = $helper;
        $this->rememberMe = $rememberMe;
        return $this;
    }


    function register($username, $email, $password)
    {
        if (!$this->helper->isCorrectLogin($username))
            throw new Exception("Username invalid.");

        if (!$this->helper->isCorrectEmail($email))
            throw new Exception("Email invalid.");

        if (!$this->helper->isCorrectPassword($password))
            throw new Exception("Password invalid.");


        // If login or email are taken, mysqli will throwi an error

        if (
            isset($_SERVER['REMOTE_ADDR'])
            && $this->credentialsStorage->identitiesPerIP($_SERVER['REMOTE_ADDR']) > MAX_ACC_PER_IP
        )
            throw new Exception("IP used too many times.");

        $this->credentialsStorage->addUser(
            $username,
            $email,
            $this->kdf->hash($password),
            $this->helper->ip_addr()
        );
        header("Location: /",  true,  301);
        exit;
    }

    function login($username, $password, $rememberMe)
    {

        if (!$this->helper->isCorrectLogin($username))
            throw new Exception("Username invalid.");

        if (!$this->helper->isCorrectPassword($password))
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
