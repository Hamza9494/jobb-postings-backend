<?php
$offer_data = json_decode(file_get_contents("php://input"), true);
if ($offer_data) {
    $mysqli = require __DIR__ . "/database.php";
    $sql = "INSERT INTO offers (user_id , freelancer_name , title , price , description) VALUE (? , ? , ? , ? , ?)";
}
