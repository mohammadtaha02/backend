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
$order_id = isset($data['order_id']) ? $data['order_id'] : null;
$status = isset($data['status']) ? $data['status'] : null;

if (!$order_id || !$status) {
    die(json_encode(array("status" => "error", "message" => "Order ID and status are required.")));
}
// update the order status
$sql = "UPDATE purchases SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    // if the status is set to "Completed", update the product quantities
    if (strtolower($status) === 'completed') {
        // get all items for the current order
        $sqlItems = "SELECT product_id, quantity FROM purchase_items WHERE purchase_id = ?";
        $stmtItems = $conn->prepare($sqlItems);
        $stmtItems->bind_param("i", $order_id);
        $stmtItems->execute();
        $itemsResult = $stmtItems->get_result();

        while ($item = $itemsResult->fetch_assoc()) {
            $product_id = $item['product_id'];
            $quantity_purchased = $item['quantity'];

            // update product stock
            $sqlUpdateStock = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
            $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
            $stmtUpdateStock->bind_param("ii", $quantity_purchased, $product_id);
            $stmtUpdateStock->execute();
        }
    }

    echo json_encode(array("status" => "success", "message" => "Order status updated successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to update order status: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
