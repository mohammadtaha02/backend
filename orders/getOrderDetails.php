<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gymawi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}

$purchaseId = isset($_GET['orderId']) ? $_GET['orderId'] : null;

if ($purchaseId) {
    $sql = "SELECT p.name AS product_name, pi.quantity, pi.price
            FROM purchase_items pi
            JOIN products p ON pi.product_id = p.id
            WHERE pi.purchase_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $purchaseId);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $orderDetails = [];

    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = $row;
    }

    echo json_encode($orderDetails);
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid order ID"]);
}

$conn->close();
?>
