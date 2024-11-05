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
file_put_contents('debug.txt', print_r($input, true)); 


$productId = isset($input['id']) ? $input['id'] : null;
$name = isset($input['name']) ? $input['name'] : '';
$description = isset($input['description']) ? $input['description'] : '';
$price = isset($input['price']) ? $input['price'] : 0;
$category = isset($input['category']) ? $input['category'] : '';
$quantity = isset($input['quantity']) ? $input['quantity'] : 0;
$image = isset($input['image']) ? $input['image'] : null;

if ($productId) {
    // Decide whether to include the image in the update or not
    if ($image !== null) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, quantity = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssi", $name, $description, $price, $category, $quantity, $image, $productId);
    } else {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $quantity, $productId);
    }    

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
}

$conn->close();

?>
