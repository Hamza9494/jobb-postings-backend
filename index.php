<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With , Authorization");
header('Content-Type: application/json; charset=utf-8');


require "./vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$test = 'test git';
$headers = getallheaders();

$bearer_auth = explode("Bearer ", $headers["Authorization"]);

$jwt_token = $bearer_auth[1];

$key = "mykey2014";


$decoded = JWT::decode($jwt_token, new Key($key, "HS256"));

$decoded = (array) $decoded;


$email = $decoded["user_email"];
$mysqli = require __DIR__ . "/database.php";
$sql = "SELECT * FROM users WHERE email = ? ";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
echo json_encode($user);
