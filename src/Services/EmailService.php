<?php

namespace App\Services;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService {
  public function sendConfirmationEmail(UserModel $user) {
    
    
    $mail = new PHPMailer(true);


    // TODO: remove this when testing outside of localhost
    $mail->SMTPOptions = array(                                 
      'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      )
    );

    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = $_ENV['MAILHOST'];
    $mail->Username = $_ENV['USERNANE'];
    $mail->Password = $_ENV['PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom($_ENV['SEND_FROM'], $_ENV['SEND_FROM_NAME']);

    $mail->addAddress($user->email);

    $mail->addReplyTo($_ENV['REPLY_TO'], $_ENV['REPLY_TO_NAME']);

    $mail->isHTML(true);

    $mail->Subject = 'Email Confirmation';

    $message = 'Please confirm your email by clicking the following link: ';
    $message .= 'https://' . $_ENV['DOMAIN'] . '/gamer-finder-api/user/verification?' . http_build_query(['token' => urlencode($user->emailConfirmationToken)]);
    $mail->Body = $message;

    $mail->AltBody = $message;

    return $mail->send();
  }
}