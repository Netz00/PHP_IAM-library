<?php

interface CredentialsStorage
{
    function addUser($username, $email, $password, $ipAddress);
    function findUserByUsername($username);
    function findUserbyId($id);
}
