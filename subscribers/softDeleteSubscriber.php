<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gymawi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) {
    echo json_encode(array("status" => "error", "message" => "Invalid input data."));
    exit();
}

$id = $data['id'];

// Mark the subscription as inactive
$sql = "UPDATE subscriptions SET is_active = 0 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(array("status" => "success", "message" => "Subscriber marked as inactive successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to update subscriber: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
