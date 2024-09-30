<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['email'])) {
    die("Error: Email not provided.");
}

$email = $_GET['email'];

$userQuery = "SELECT id FROM users WHERE email = '$email'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();

if (!$userData) {
    die("Error: User not found.");
}

$user_id = $userData['id'];

$sql = "SELECT id AS cart_id, user_id, product_id, quantity, total_price, product_name, product_price, product_image FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = array();

while ($row = $result->fetch_assoc()) {
    array_push($cartItems, $row);
}

echo json_encode($cartItems);


$conn->close();
?>
