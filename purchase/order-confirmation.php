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

// Extract the purchase ID and user ID from the input
$purchaseId = isset($input['purchaseId']) ? $input['purchaseId'] : null;
$userEmail = isset($input['userEmail']) ? $input['userEmail'] : null;

// Fetch user_id using the email
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Confirm the order for this user and purchase
$sqlConfirmOrder = "UPDATE purchases SET order_confirmed = 1 WHERE id = ? AND user_id = ?";
$stmtConfirmOrder = $conn->prepare($sqlConfirmOrder);
$stmtConfirmOrder->bind_param("ii", $purchaseId, $userId);

if ($stmtConfirmOrder->execute()) {
    echo json_encode(["status" => "success", "message" => "Order confirmed"]);
} else {
    echo json_encode(["status" => "error", "message" => "Order confirmation failed"]);
}

$stmtConfirmOrder->close();
$conn->close();
?>