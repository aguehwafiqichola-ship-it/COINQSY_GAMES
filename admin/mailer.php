<?php

// Inclure PHPMailer
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';
require __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// CONFIGURATION SMTP
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_USER = 'aguehwafiqichola@gmail.com'; // ton email
const SMTP_PASS = 'otxmvtimyqtusvwu'; // mot de passe d'application Gmail
const SMTP_PORT = 587;

const MAIL_FROM = 'aguehwafiqichola@gmail.com';
const MAIL_NAME = 'Coinqsy Support';

function sendMail($to, $subject, $html)
{

    $mail = new PHPMailer(true);

    try {

        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Encodage
        $mail->CharSet = "UTF-8";

        // Expéditeur
        $mail->setFrom(MAIL_FROM, MAIL_NAME);

        // Destinataire
        $mail->addAddress($to);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;

        // Envoyer
        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;

    }

}