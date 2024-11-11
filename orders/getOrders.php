<?php
// getOrders.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gymawi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

file_put_contents('debug.txt', print_r($_POST, true));

// Retrieve email from GET request
$email = isset($_GET['email']) ? $_GET['email'] : '';
if (empty($email)) {
    die(json_encode(array("status" => "error", "message" => "Email is required.")));
}

// Step 1: Get the user ID based on the email
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die(json_encode(array("status" => "error", "message" => "User not found.")));
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// Step 2: Get all purchases for the user
$sql = "SELECT id, purchase_date, total_price, status FROM purchases WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchasesResult = $stmt->get_result();

if ($purchasesResult->num_rows == 0) {
    die(json_encode(array("status" => "error", "message" => "No purchases found for this user.")));
}

$purchases = [];
while ($purchase = $purchasesResult->fetch_assoc()) {
    $purchase_id = $purchase['id'];
    
    // Step 3: Get all items for the current purchase
    $sqlItems = "SELECT product_id, quantity, price FROM purchase_items WHERE purchase_id = ?";
    $stmtItems = $conn->prepare($sqlItems);
    $stmtItems->bind_param("i", $purchase_id);
    $stmtItems->execute();
    $itemsResult = $stmtItems->get_result();

    $items = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $product_id = $item['product_id'];
        
        // Step 4: Get product name from products table
        $sqlProduct = "SELECT name FROM products WHERE id = ?";
        $stmtProduct = $conn->prepare($sqlProduct);
        $stmtProduct->bind_param("i", $product_id);
        $stmtProduct->execute();
        $productResult = $stmtProduct->get_result();
        
        if ($productResult->num_rows > 0) {
            $product = $productResult->fetch_assoc();
            $item['product_name'] = $product['name'];
        } else {
            $item['product_name'] = "Unknown Product";
        }
        
        $items[] = $item;
    }
    
    // Add items to the purchase
    $purchase['items'] = $items;
    $purchases[] = $purchase;
}

// Return the response as JSON
echo json_encode(array("status" => "success", "data" => $purchases));

// Close the connection
$conn->close();
?>
