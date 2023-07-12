<?php

class PwdReset
{

    private $pwdResetStorage;
    private $credentialsStorage;
    private $kdf;

    public function __construct(CredentialsStorage $credentialsStorage, KDF $kdf, PwdResetStorage $pwdResetStorage)
    {
        $this->$credentialsStorage = $credentialsStorage;
        $this->$kdf = $kdf;
        $this->pwdResetStorage = $pwdResetStorage;

        return $this;
    }


    # TODO custom mail class and add exceptions
    public function initiatePwdReset($email)
    {
        if (
            !Helper::isEmailRegistered($email) ||
            !$this->credentialsStorage->isEmailTaken($email)
        )
            return false;

        $selector = $this->generateSelector();
        $token = $this->generateValidator();
        $url = PWD_RESET_URL . "?selector=" . $selector . "&validator=" . bin2hex($token);
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expires  = date("Y-m-d H:i:s", $this->getExpirationTime());

        $this->pwdResetStorage->deleteAll($email);

        $this->pwdResetStorage->save($email, $selector, $hashedToken, $expires);

        $to = $email;
        $subject = "Password reset";
        $message = $url;
        $header = "Password reset requested";
        $message = wordwrap($message, 200);
        mail($to, $subject, $message, $header);
    }

    function resetPwd($selector, $validator, $newPassword)
    {

        if (!$this->checkParams($selector, $validator))
            return false;

        $current_time = time();
        $currentDate = date("Y-m-d H:i:s", $current_time);


        $resetRequest = $this->pwdResetStorage->findNonExpired($selector, $currentDate);

        if ($resetRequest == NULL)
            return false;


        $token = $resetRequest["token"];
        $email = $resetRequest["email"];

        $tokenBin = hex2Bin($validator);

        if (!password_verify($tokenBin, $token)) {
            return false;
        }

        $this->pwdResetStorage->deleteAll($email);

        $newPwdHash = $this->kdf->hash($newPassword);

        $this->credentialsStorage->updatePassword($email, $newPwdHash);

        return true;
    }

    private function generateSelector()
    {
        return bin2hex(random_bytes(8));
    }

    private function generateValidator()
    {
        return random_bytes(32);
    }

    private function getExpirationTime()
    {
        return time() + PWD_RESET_REQ_EXPIRATION_TIME;
    }

    private function checkParams($selector, $validator)
    {
        if (
            !empty($selector)
            && !empty($validator)
            && ctype_xdigit($selector) == true
            && ctype_xdigit($validator) == true
        )
            return true;

        return false;
    }
}
