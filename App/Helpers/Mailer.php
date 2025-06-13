<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;
    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }
    public function send(string $email, string $subject, string $message, ?string $link = null): bool
    {
        $mail = $this->mail;
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hajjoobidah@gmail.com';
            $mail->Password = 'mohclnobxncduesx';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hajjoobidah@gmail.com', 'Obidah HAJJO');
            $mail->addAddress($email, 'Recipient');

            $mail->Subject = $subject;
            $mail->Body = $message;
            if ($link) {
                $mail->Body .= "<br><a href='http://{$link}'>{$link}</a>";
            }
            $mail->isHTML(true);
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
