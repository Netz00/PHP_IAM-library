<?php

interface RememberMeStorage
{
    function saveRememberMe($username, $random_password_hash, $random_selector_hash, $expiry_date);
    function invalidateRememberMe($username);
    function findValidRememberMe($username, $current_date);
}
