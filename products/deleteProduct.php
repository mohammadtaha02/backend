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

// Retrieve the productId from the query parameters
$productId = isset($_GET['productId']) ? $_GET['productId'] : null;

if ($productId) {
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete product"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
}

$conn->close();
?>
