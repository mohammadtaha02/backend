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

$email = $_GET['email'];

// Find the user ID by email
$user_id_query = "SELECT id FROM users WHERE email = '$email'";
$user_id_result = $conn->query($user_id_query);
if ($user_id_result->num_rows > 0) {
    $row = $user_id_result->fetch_assoc();
    $user_id = $row['id'];

    // Fetch progress based on the user ID
    $sql = "SELECT * FROM user_progress WHERE user_id = '$user_id' ORDER BY progress_date DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $progress = [];
        while($row = $result->fetch_assoc()) {
            $progress[] = $row;
        }
        echo json_encode($progress);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$conn->close();
?>