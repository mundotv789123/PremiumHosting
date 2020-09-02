<?php

namespace app\libs\utils;

class Mail
{

    const FROM_NAME = 'PremiumHosting';
    const FROM_EMAIL = 'no-reply@premiumhosting.com.br';
    const RETURN_EMAIL = 'no-reply@premiumhosting.com.br';

    public static function send($to, $subject, $message) {
        $headers  = "From: ".self::FROM_NAME." < ".self::FROM_EMAIL." >\n";
        $headers  .= "Reply-To: ".self::FROM_NAME." < ".self::FROM_EMAIL." >\n";
        //$headers .= "Cc: testsite < ".self::FROM_EMAIL." >\n";
        $headers .= "X-Sender: ".self::FROM_NAME." < ".self::FROM_EMAIL." >\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();
        $headers .= "X-Priority: 1\n"; // Urgent message!
        $headers .= "Return-Path: ".self::FROM_EMAIL."\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

        return mail($to, $subject, $message, $headers);
    }

}