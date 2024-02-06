<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');



use Firebase\JWT\JWT;

require "./vendor/autoload.php";

$login_data = json_decode(file_get_contents("php://input"), true);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM users WHERE email = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $login_data["email"]);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();


if ($user) {
   if (password_verify($login_data["password"], $user["password_hash"])) {
      $key = "mykey2014";

      $payload = ["user_email" => $user["email"]];

      $jwt_token = JWT::encode($payload, $key, "HS256");

      echo json_encode(["user_exist" => true, "token" =>  $jwt_token]);
   } else if (!password_verify($login_data["password"], $user["password_hash"])) {

      echo json_encode(["user_exist" => false, "reason" => "invalid password"]);
   }
}
