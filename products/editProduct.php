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
if (!isset($data['id'], $data['name'], $data['price'], $data['quantity'], $data['image'])) {
    echo json_encode(array("status" => "error", "message" => "Invalid input data."));
    exit();
}

$id = $data['id'];
$name = $data['name'];
$price = $data['price'];
$quantity = $data['quantity'];
$image = $data['image'];

$sql = "UPDATE products SET name = ?, price = ?, quantity = ?, image = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdisi", $name, $price, $quantity, $image, $id);

if ($stmt->execute()) {
    echo json_encode(array("status" => "success", "message" => "Product updated successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to update product: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
