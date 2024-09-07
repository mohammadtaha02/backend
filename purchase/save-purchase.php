<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}
// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Extract data from the request
$userEmail = $input['userEmail'];
$purchaseItems = $input['purchaseItems'];
$totalPrice = $input['totalPrice'];

// Fetch user_id using the email
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// If no user found, return error
if (!$userId) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    $conn->close();
    exit();
}

// Insert into the "purchases" table
$purchaseDate = date('Y-m-d H:i:s');
$sql = "INSERT INTO purchases (user_id, purchase_date, total_price) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isd", $userId, $purchaseDate, $totalPrice);
$stmt->execute();
$purchaseId = $stmt->insert_id;  // Get the purchase ID for this transaction

// Insert into the "purchase_items" table
foreach ($purchaseItems as $item) {
    $productId = $item['productId'];
    $quantity = $item['quantity'];
    $price = $item['price'];

    $sqlItem = "INSERT INTO purchase_items (purchase_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);
    $stmtItem->bind_param("iiid", $purchaseId, $productId, $quantity, $price);
    $stmtItem->execute();
}

// Return success response
echo json_encode(["status" => "success", "message" => "Purchase confirmed."]);

$conn->close();
?>
