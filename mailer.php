<?php

use PHPMailer\PHPMailer\PHPMailer;

require "./vendor/autoload.php";

$mail = new PHPMailer();

//phpmailer configuration
$mail->isSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->Username = "baitichhamza@gmail.com";
$mail->Password = 'ztep xxyw ogzw lkje';
$mail->SMTPSecure = "tls";
$mail->Port = 587;

return    $mail;


?>