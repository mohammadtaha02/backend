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
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$productId = $data['productId'];
$newQuantity = $data['quantity'];

$sql = "UPDATE products SET quantity = '$newQuantity' WHERE id = '$productId'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Quantity updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating quantity: " . $conn->error]);
}

$conn->close();
?>
