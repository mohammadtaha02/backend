// updateProductStatus.php
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

$sql = "UPDATE products SET is_deleted = 1 WHERE id = ?";
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
