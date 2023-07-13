<?php

class PwdReset
{

    private $pwdResetStorage;

    public function __construct(PwdResetStorage $pwdResetStorage)
    {
        $this->pwdResetStorage = $pwdResetStorage;
        return $this;
    }

    # TODO custom mail class
    function createRequest($email)
    {
        $selector = $this->generateSelector();
        $token = $this->generateValidator();
        $url = PWD_RESET_URL . "?selector=" . $selector . "&validator=" . bin2hex($token);
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expires  = date("Y-m-d H:i:s", $this->getExpirationTime());

        $this->pwdResetStorage->deleteAllUserPwdResetRequests($email);

        $this->pwdResetStorage->savePwdResetRequest($email, $selector, $hashedToken, $expires);

        $to = $email;
        $subject = "Password reset";
        $message = $url;
        $header = "Password reset requested";
        $message = wordwrap($message, 200);
        mail($to, $subject, $message, $header);
    }

    function verifyRequest($selector, $validator)
    {

        if (!$this->isHexadecimal($selector) || !$this->isHexadecimal($validator))
            throw new Exception("Invalid request");

        $resetRequest = $this->pwdResetStorage->findValidPwdResetRequest(
            $selector,
            date(
                "Y-m-d H:i:s",
                time()
            )
        );

        if (
            $resetRequest == NULL
            || !password_verify(hex2Bin($validator), $resetRequest["token"])
        )
            throw new Exception("Invalid request");

        $this->pwdResetStorage->deleteAllUserPwdResetRequests($resetRequest["email"]);

        return $resetRequest["email"];
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

    private function isHexadecimal($string)
    {
        if (!empty($string) && ctype_xdigit($string))
            return true;

        return false;
    }
}
