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

if (!isset($data['product_id'])) {
    echo json_encode(array("status" => "error", "message" => "Product ID is required"));
    exit();
}

$productId = $data['product_id'];

// Check if the product is referenced in the purchase_items table
$sqlCheck = "SELECT COUNT(*) AS count FROM purchase_items WHERE product_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $productId);
$stmtCheck->execute();
$result = $stmtCheck->get_result();
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo json_encode(array("status" => "error", "message" => "Cannot delete product. It is referenced in purchase items."));
    $stmtCheck->close();
    $conn->close();
    exit();
}

// Proceed with deletion if no dependencies
$sql = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);

if ($stmt->execute()) {
    echo json_encode(array("status" => "success", "message" => "Product deleted successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to delete product: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
