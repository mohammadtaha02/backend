<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$data = json_decode(file_get_contents("php://input"));

// First, retrieve the numeric user_id from the users table based on the email.
if (isset($data->user_id) && isset($data->product_id) && isset($data->quantity)) {
    $user_email = $data->user_id;  // Actually email
    $product_id = $data->product_id;
    $quantity = $data->quantity;

    // Query to get the numeric user_id
    $userQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userData = $userResult->fetch_assoc();

    if ($userData) {
        $user_id = $userData['id'];  // Now you have the numeric user_id

        // Continue with the cart process
        $productQuery = "SELECT name, price, image FROM products WHERE id = ?";
        $stmt = $conn->prepare($productQuery);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $productResult = $stmt->get_result();
        $productData = $productResult->fetch_assoc();

        if ($productData) {
            $product_name = $productData['name'];
            $product_price = $productData['price'];
            $product_image = $productData['image'];
            $total_price = $quantity * $product_price;

            $cartQuery = "SELECT id FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($cartQuery);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $cartResult = $stmt->get_result();

            if ($cartResult->num_rows > 0) {
                $cartRow = $cartResult->fetch_assoc();
                $cart_id = $cartRow['id'];
                $updateQuery = "UPDATE cart SET quantity = quantity + ?, total_price = total_price + ? WHERE id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("idi", $quantity, $total_price, $cart_id);
                if ($stmt->execute()) {
                    echo json_encode(array("message" => "Cart updated successfully"));
                } else {
                    echo json_encode(array("message" => "Error: " . $stmt->error));
                }
            } else {
                $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, total_price, product_name, product_price, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("iiidsds", $user_id, $product_id, $quantity, $total_price, $product_name, $product_price, $product_image);

                if ($stmt->execute()) {
                    echo json_encode(array("message" => "Product added to cart successfully"));
                } else {
                    echo json_encode(array("message" => "Error: " . $stmt->error));
                }
            }
        } else {
            echo json_encode(array("message" => "Product not found"));
        }
    } else {
        echo json_encode(array("message" => "User not found"));
    }
} else {
    echo json_encode(array("message" => "Invalid input"));
}

$conn->close();

?>
