<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_GET['user_id'];

$sql = "
    SELECT 
        cart.id as cart_id, 
        products.name as product_name, 
        products.price as product_price, 
        cart.quantity, 
        (products.price * cart.quantity) as total_price,
        products.image as product_image
    FROM 
        cart 
    JOIN 
        products 
    ON 
        cart.product_id = products.id 
    WHERE 
        cart.user_id = '$user_id'";

$result = $conn->query($sql);

$cartItems = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($cartItems, $row);
    }
}

echo json_encode($cartItems);

$conn->close();
?>
