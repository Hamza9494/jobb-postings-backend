<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

$user_email = json_decode(file_get_contents("php://input"), true);




$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM users WHERE email = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $user_email["email"]);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user) {
    $mailer = require __DIR__ . "/mailer.php";

    $reset_token = bin2hex(random_bytes(16));

    $reset_token_hash = hash("SHA256", $reset_token);

    $expiry_time = date("y-m-d H:i:s ", time() + 60 * 60);

    $sql = "UPDATE users SET reset_token_hash = ? , expiry_time = ? WHERE email =  ? ";

    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("sss", $reset_token_hash, $expiry_time, $user["email"]);

    $stmt->execute();


    //recipient details
    $mail->setFrom("job_postings@email.com");
    $mail->addAddress($user_email['email']);

    //email content
    $mail->isHTML(true);
    $mail->Subject = " Job Postings Password Reset";
    $mail->Body = <<<END
    Click <a href= "http://localhost:3000/forgot_password_process/$reset_token" >  here </a>  to reset your password.
 END;

    $mail->send();
    echo json_encode(["message" => "email sent", "user_email" => $user["email"]]);
    exit;
}
