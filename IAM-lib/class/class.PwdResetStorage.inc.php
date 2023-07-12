<?php

interface PwdResetStorage
{
    function savePwdResetRequest($email, $selector, $hashedToken, $expires);
    function findNonExpiredPwdResetRequest($selector, $currentDate);
    function deleteAllUserPwdResetRequests($email);
}
