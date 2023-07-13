<?php

class MyQueries extends Db implements CredentialsStorage, PwdResetStorage, RememberMeStorage
{

    function addUser($username, $email, $password, $ipAddress)
    {
        $username = $this->quote($username);
        $email = $this->quote($email);
        $password = $this->quote($password);
        $ipAddress = $this->quote($ipAddress);

        $query = "INSERT INTO " . USERS_TABLE . " (username,email,password,ip_address) VALUES (?, ?, ?, ?);";
        try {
            $this->insert($query, 'ssss', array($username, $email, $password, $ipAddress));
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    function findUserByUsername($username)
    {
        $username = $this->quote($username);

        $query = "SELECT * FROM " . USERS_TABLE . " WHERE username = ? LIMIT 1;";

        try {
            $rows = $this->runQuery($query, 's', array($username));
            if ($rows)
                return $rows[0];
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }

    function findUserbyId($id)
    {
        $id = $this->quote($id);

        $query = "SELECT * FROM " . USERS_TABLE . " WHERE id = ? LIMIT 1;";

        try {
            $rows = $this->runQuery($query, 'i', array($id));
            if ($rows)
                return $rows[0];
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }

    function findUserbyEmail($email)
    {
        $id = $this->quote($email);

        $query = "SELECT * FROM " . USERS_TABLE . " WHERE email = ? LIMIT 1;";

        try {
            $rows = $this->runQuery($query, 's', array($email));
            if ($rows)
                return $rows[0];
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }


    function updateIdentityDataByEmail($email, $property, $newValue)
    {
        $id = $this->quote($email);

        $query = "UPDATE " . USERS_TABLE . " SET ?=? WHERE email=?;";

        try {
            $this->update($query, 'sss', array($email, $property, $newValue));
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }




    // ------------------ Remember me ... ------------------

    function saveRememberMe($username, $random_password_hash, $random_selector_hash, $expiry_date)
    {

        $username = $this->quote($username);
        $random_password_hash = $this->quote($random_password_hash);
        $random_selector_hash = $this->quote($random_selector_hash);
        $expiry_date = $this->quote($expiry_date);

        $query = "INSERT INTO " . COOKIE_TABLE . " (username, password_hash, selector_hash, expiry_date) VALUES (?, ?, ?, ?);";
        try {
            $this->insert($query, 'ssss', array($username, $random_password_hash, $random_selector_hash, $expiry_date));
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
    function invalidateRememberMe($username)
    {
        $username = $this->quote($username);
        $query = "UPDATE " . COOKIE_TABLE . " SET is_expired = 1 WHERE (is_expired = 0 AND username = ?);";
        try {
            $this->runQuery($query, 's', array($username));
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
    function findValidRememberMe($username, $current_date)
    {

        $username = $this->quote($username);
        $current_date = $this->quote($current_date);

        $query = "SELECT id,password_hash,selector_hash,expiry_date FROM " . COOKIE_TABLE . " WHERE (username = ? AND is_expired = 0 AND expiry_date >= ?);";

        try {
            $rows = $this->runQuery($query, 'ss', array($username, $current_date));
            if ($rows)
                return $rows[0];
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }

    // ------------------ Password reset ... ------------------

    function savePwdResetRequest($email, $selector, $hashedToken, $expires)
    {

        $email = $this->quote($email);
        $selector = $this->quote($selector);
        $hashedToken = $this->quote($hashedToken);
        $expires = $this->quote($expires);

        $query = "INSERT INTO " . PWD_RESET_TABLE . " (pwdResetEmail,pwdResetSelector,pwdResetToken,pwdResetExpires) VALUES (?,?,?,?);";
        try {
            $this->insert($query, 'ssss', array($email, $selector, $hashedToken, $expires));
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
    function findValidPwdResetRequest($selector, $currentDate)
    {
        $selector = $this->quote($selector);
        $currentDate = $this->quote($currentDate);

        $query = "SELECT pwdResetToken,pwdResetEmail FROM " . PWD_RESET_TABLE . " WHERE pwdResetSelector=? AND pwdResetExpires >= ? LIMIT 1;";
        try {
            $rows = $this->runQuery($query, 'ss', array($selector, $currentDate));
            if ($rows)
                return $rows[0];
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }
    function deleteAllUserPwdResetRequests($email)
    {
        $email = $this->quote($email);

        $query = "DELETE FROM " . PWD_RESET_TABLE . " WHERE pwdResetEmail=?;";
        try {
            $this->update($query, 's', array($email));
        } catch (mysqli_sql_exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    // ------------------ OTHER FUNCTIONS ... ------------------

    function findAllUsers()
    {
        $query = "SELECT * FROM " . USERS_TABLE . ";";
        $result = $this->runQuery($query, '', null);
        return $result;
    }

    function findAllRememberMe()
    {
        $query = "SELECT * FROM " . COOKIE_TABLE . ";";
        $result = $this->runQuery($query, '', null);
        return $result;
    }

    function findAllPwdResetRequests()
    {
        $query = "SELECT * FROM " . PWD_RESET_TABLE . ";";
        $result = $this->runQuery($query, '', null);
        return $result;
    }
}
