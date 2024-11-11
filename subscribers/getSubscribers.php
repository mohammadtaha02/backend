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

// Joining subscriptions with users to get subscriber details
$sql = "SELECT s.*, u.email, u.name, u.male, u.birth_date 
        FROM subscriptions s 
        JOIN users u ON s.user_id = u.id
        WHERE s.is_active = 1";

$result = $conn->query($sql);

$subscribers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subscribers[] = $row;
    }
}

echo json_encode($subscribers);

$conn->close();
?>
