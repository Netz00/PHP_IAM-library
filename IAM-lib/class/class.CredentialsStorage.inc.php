<?php

interface CredentialsStorage
{
    function saveUser($username, $email, $password, $ipAddress);
    function findUserByUsername($username);
    function findUserbyId($id);
    function findUserbyEmail($id);
    function updateIdentityDataByEmail($email, $property, $newValue);
}
