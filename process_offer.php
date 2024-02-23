<?php
$offer_data = json_decode(file_get_contents("php://input"), true);
if ($offer_data) {
    $mysqli = require __DIR__ . "/database.php";

    $sql = "INSERT INTO offers (title , description , price  ) VALUE ? , ? , ? ";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $offer_data["title"], $offer_data["description"], $offer_data["price"]);
    if ($stmt->execute()) {
        echo json_encode(["offer_added" => true]);
    }
}
