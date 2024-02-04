<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

$credentials = json_decode(file_get_contents("php://input"), true);

$token = ($credentials[0]);



$token_hash = hash("SHA256", $token);

var_dump($token_hash);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM users WHERE account_activation_hash = ? ";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param('s', $token_hash);

$stmt->execute();


$result = $stmt->get_result();

$user = $result->fetch_assoc();



if (!$user) {
  die("no unactivated account to activate");
};




$sql = "UPDATE users SET account_activation_hash = null WHERE id = ? ";


$stmt = $mysqli->prepare($sql);

$stmt->bind_param('s', $user["id"]);

if ($stmt->execute()) {
  echo json_encode(["activation_success" => "true", "user_data" => $user]);
};
