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

$email = $_POST['email'];
$workout_done = $_POST['workout_done'];
$calories_consumed = $_POST['calories_consumed'];
$progress_date = $_POST['progress_date'];

// Find the user ID by email
$user_id_query = "SELECT id FROM users WHERE email = '$email'";
$user_id_result = $conn->query($user_id_query);
if ($user_id_result->num_rows > 0) {
    $row = $user_id_result->fetch_assoc();
    $user_id = $row['id'];

    // Insert progress into the database
    $sql = "INSERT INTO user_progress (user_id, workout_done, calories_consumed, progress_date) 
            VALUES ('$user_id', '$workout_done', '$calories_consumed', '$progress_date')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$conn->close();
?>