<?php
error_reporting(E_ERROR | E_PARSE);


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');


$signup_data = json_decode(file_get_contents("php://input"), true);

if (empty($signup_data["name"])) {
   echo json_encode(["name_error" => "name cannot be empty"]);
   die();
}

if (!filter_var($signup_data["email"], FILTER_VALIDATE_EMAIL)) {
   echo    json_encode(["email_error" => "invalid email"]);
   die();
}

if (strlen($signup_data["password"] < 12)) {
   echo json_encode(["password_error" => "password must be at least 12 characters"]);
   die();
}

if (!preg_match("/[a-z]/i", $signup_data["password"])) {
   echo json_encode(["password_error" => "password must contain at least one letter"]);
   die();
}

if (!preg_match("/[0-9]/", $signup_data["password"])) {
   echo json_encode(["password_error" => "password must contain at least one number"]);
   die();
}

if ($signup_data["password"] !== $signup_data["password_confirm"]) {
   echo  json_encode(["password_error" => "passwords does not match"]);
   die();
}

$password_hash = password_hash($signup_data["password"], PASSWORD_DEFAULT);

$activation_token = bin2hex(random_bytes(16));

$activation_token_hash = hash("SHA256", $activation_token);

$sql = "INSERT INTO users (name , email , password_hash , account_activation_hash) VALUE ( ? , ? , ? , ?) ";

$mysqli = require __DIR__  . "/database.php";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("ssss", $signup_data["name"], $signup_data["email"], $password_hash, $activation_token_hash);

if ($stmt->execute()) {
   $mail = require __DIR__ . "/mailer.php";

   //recipient details
   $mail->setFrom("job_postings@email.com");
   $mail->addAddress($signup_data['email']);

   //email content
   $mail->isHTML(true);
   $mail->Subject = " Job Postings Account Activation";
   $mail->Body = <<<END
    Click <a href= "http://localhost:3000/activate/$activation_token" >  here </a>  to activate your account.
 END;

   $mail->send();
   echo json_encode(["message" => "data recived", "user_data" => $signup_data]);
   exit;
} else if ($mysqli->errno === 1062) {
   echo json_encode(["email_error" => "email already exists my dude"]);
   die();
}
