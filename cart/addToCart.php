<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"));

$user_id = $data->user_id;
$product_id = $data->product_id;
$quantity = $data->quantity;

$productQuery = "SELECT price FROM products WHERE id = '$product_id'";
$productResult = $conn->query($productQuery);
$productData = $productResult->fetch_assoc();

$product_price = $productData['price'];
$total_price = $quantity * $product_price;

$cartQuery = "SELECT id FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
$cartResult = $conn->query($cartQuery);

if ($cartResult->num_rows > 0) {
    $cartRow = $cartResult->fetch_assoc();
    $cart_id = $cartRow['id'];
    $updateQuery = "UPDATE cart SET quantity = quantity + '$quantity', total_price = total_price + '$total_price' WHERE id = '$cart_id'";
    if ($conn->query($updateQuery) === TRUE) {
        echo json_encode(array("message" => "Cart updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $conn->error));
    }
} else {
    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, total_price) VALUES ('$user_id', '$product_id', '$quantity', '$total_price')";
    if ($conn->query($insertQuery) === TRUE) {
        echo json_encode(array("message" => "Product added to cart successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $conn->error));
    }
}

$conn->close();
?>
