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

// Check if 'id' is provided in the URL parameters
if (isset($_GET['id'])) {
    $cartItemId = $_GET['id'];

    // Prepare statement to delete the item from the cart securely
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cartItemId);

    if ($stmt->execute()) {
        echo json_encode(array("message" => "Item removed from cart successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(array("message" => "Invalid input"));
}

// Close the connection
$conn->close();
?>
