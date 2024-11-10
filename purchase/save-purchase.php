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

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Extract data from the input
$userEmail = isset($input['userEmail']) ? $input['userEmail'] : null;
$purchaseItems = isset($input['purchaseItems']) ? $input['purchaseItems'] : [];
$totalPrice = isset($input['totalPrice']) ? $input['totalPrice'] : 0;

// Debug the incoming data
file_put_contents('debug_purchase.txt', print_r($input, true));

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
$stmt->close();

// Insert into the "purchase_items" table
foreach ($purchaseItems as $item) {
    // Check if all required fields are present
    if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['product_price'])) {
        echo json_encode(["status" => "error", "message" => "Missing product details for one of the items"]);
        $conn->close();
        exit();
    }

    $productId = $item['product_id'];
    $quantity = $item['quantity'];
    $price = $item['product_price'];

    $sqlItem = "INSERT INTO purchase_items (purchase_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);
    $stmtItem->bind_param("iiid", $purchaseId, $productId, $quantity, $price);
    $stmtItem->execute();

    // Update the product stock in the database
    $updateStockSql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
    $stmtUpdateStock = $conn->prepare($updateStockSql);
    $stmtUpdateStock->bind_param("ii", $quantity, $productId);
    $stmtUpdateStock->execute();
}
$stmtItem->close();
$stmtUpdateStock->close();

// Clear the cart after purchase
$sqlClearCart = "DELETE FROM cart WHERE user_id = ?";
$stmtClearCart = $conn->prepare($sqlClearCart);
$stmtClearCart->bind_param("i", $userId);
$stmtClearCart->execute();
$stmtClearCart->close();

// Send purchase confirmation email
$subject = "Purchase Confirmation - Order #{$purchaseId}";
$message = "<h2>Thank you for your purchase!</h2>";
$message .= "<p>Here are your order details:</p><ul>";
foreach ($purchaseItems as $item) {
    $message .= "<li>Product: {$item['product_name']}, Quantity: {$item['quantity']}, Price: {$item['product_price']}</li>";
}
$message .= "</ul>";
$message .= "<p>Total Price: {$totalPrice}</p>";
$message .= "<p>We appreciate your business!</p>";

$headers = "From: xtoulhaxor@gmail.com\r\n";
$headers .= "Reply-To: xtoulhaxor@gmail.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$emailSent = false;  // Flag for tracking email sending status
if (mail($userEmail, $subject, $message, $headers)) {
    $emailSent = true;
}

// Return a single JSON response with both the purchase status and email status
$response = [
    "status" => "success",
    "message" => "Purchase confirmed.",
    "purchaseId" => $purchaseId,
    "emailSent" => $emailSent
];

echo json_encode($response);

$conn->close();
?>
