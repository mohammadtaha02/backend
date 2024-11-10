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

$userId = isset($_GET['userId']) ? $_GET['userId'] : null;

if ($userId) {
    $sql = "SELECT * FROM purchases WHERE user_id = ? AND order_confirmed = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    echo json_encode($orders);
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
}

$conn->close();
?>
