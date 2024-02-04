<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

$reset_password_data = json_decode(file_get_contents("php://input"), true);

$reset_token = $reset_password_data["id"];

$password = $reset_password_data["password"];

$password_confirm = $reset_password_data["password_confirm"];

if (strlen($password) < 12) {
    echo json_encode(["error" => "password cannot be less than 8 characters"]);
    die();
}

if ($password !== $password_confirm) {
    echo json_encode(["error" => "password does not match"]);
    die();
}

$password_hash = hash("sha256", $password);

$reset_token_hash = hash("sha256", $reset_token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM users WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $reset_token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();


if (!$user) {
    json_encode(["error" => "user not found"]);
    die();
} else {
    $sql = "UPDATE users SET password_hash = ? , reset_token_hash = null , expiry_time = null  WHERE email = ? ";

    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("ss", $password_hash, $user["email"]);

    if ($stmt->execute()) {
        echo json_encode(["done" => true]);
    }
}
