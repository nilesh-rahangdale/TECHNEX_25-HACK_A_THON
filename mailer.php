<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;   // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'pranavshahakar88@gmail.com';  // Your Gmail address
    $mail->Password = 'ofpjxnleqkemuzllj';     // Use App Password if 2FA is on
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('pranavshahakar88@gmail.com', 'AlumniPortal');
    $mail->addAddress($email);  

    $mail->isHTML(true);
    $mail->Subject = "Password Reset";
    $mail->Body = 'Click <a href="http://localhost/alumni/reset-password.php?token=' . $token . '">here</a> to reset your password.';

    $mail->send();
    echo "Message sent, please check your inbox.";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
}

