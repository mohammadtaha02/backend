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
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = isset($input['orderId']) ? $input['orderId'] : null;

if ($orderId) {
    $sql = "UPDATE purchases SET order_confirmed = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order confirmed successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to confirm order"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid order ID"]);
}

$conn->close();
?>
