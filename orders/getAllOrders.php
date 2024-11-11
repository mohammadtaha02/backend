<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gymawi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

// get all purchases
$sql = "SELECT id, user_id, purchase_date, total_price, status FROM purchases";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die(json_encode(array("status" => "error", "message" => "No purchases found.")));
}

$purchases = [];
while ($purchase = $result->fetch_assoc()) {
    $purchase_id = $purchase['id'];
    $user_id = $purchase['user_id'];

    // get user email and name for each purchase
    $sqlUser = "SELECT email, name FROM users WHERE id = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();
    $userResult = $stmtUser->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $purchase['user_email'] = $user['email'];
        $purchase['user_name'] = $user['name'];
    } else {
        $purchase['user_email'] = "Unknown User";
        $purchase['user_name'] = "Unknown User";
    }

    // get all items for the current purchase
    $sqlItems = "SELECT product_id, quantity, price FROM purchase_items WHERE purchase_id = ?";
    $stmtItems = $conn->prepare($sqlItems);
    $stmtItems->bind_param("i", $purchase_id);
    $stmtItems->execute();
    $itemsResult = $stmtItems->get_result();

    $items = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $product_id = $item['product_id'];

        // get product name from products table
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
    // add items to the purchase
    $purchase['items'] = $items;
    $purchases[] = $purchase;
}
// return the response as JSON
echo json_encode(array("status" => "success", "data" => $purchases));

$conn->close();
?>
