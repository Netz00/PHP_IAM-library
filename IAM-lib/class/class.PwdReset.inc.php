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


        // Send an email

        ob_start();
?>
        <html>

        <body>
            Open the following link to reset your password.
            <a href="<?php echo $url; ?>">
                <?php echo $url; ?>
            </a>
        </body>

        </html>
<?php
        $html_text = ob_get_clean();

        $from = SMTP_EMAIL;
        $to = $email;
        $subject = "Password reset request";

        $mail = new PHPMailer(true);

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = SMTP_HOST;                               // Specify main and backup SMTP servers
        $mail->SMTPAuth = SMTP_AUTH;                               // Enable SMTP authentication
        $mail->Username = SMTP_USERNAME;                      // SMTP username
        $mail->Password = SMTP_PASSWORD;                      // SMTP password
        $mail->SMTPSecure = SMTP_SECURE;                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = SMTP_PORT;                                    // TCP port to connect to

        // $mail->addBCC("wgcraft.official@gmail.com", "CryptoExchange"); //enter admins mails
        $mail->Priority = 2;

        $mail->From = $from;
        $mail->FromName = APP_TITLE;
        $mail->addAddress($to);                               // Name is optional

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $html_text;

        if ($mail->send())
            header("Location: " . "/" . "?error=" . "Check your inbox (and spam also).",  true,  301);
        else
            header("Location: " . "/" . "?error=" . "Email couldn't be sent.",  true,  301);
        exit;
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
