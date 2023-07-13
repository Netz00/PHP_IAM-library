<?php

interface PwdResetStorage
{
    function savePwdResetRequest($email, $selector, $hashedToken, $expires);
    function findValidPwdResetRequest($selector, $currentDate);
    function deleteAllUserPwdResetRequests($email);
}
