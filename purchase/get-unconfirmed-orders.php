<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gymawi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}
// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);
$userEmail = isset($input['userEmail']) ? $input['userEmail'] : null;

// Fetch the user ID using the email
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Fetch unconfirmed orders for the user
$sql = "SELECT id FROM purchases WHERE user_id = ? AND order_confirmed = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$unconfirmedOrders = [];

while ($row = $result->fetch_assoc()) {
    $unconfirmedOrders[] = $row;  // Collect unconfirmed orders
}

$stmt->close();
$conn->close();

echo json_encode(["status" => "success", "unconfirmedOrders" => $unconfirmedOrders]);