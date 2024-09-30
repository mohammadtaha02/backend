<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

$email = $_POST['email'];
$schedule_json = $_POST['schedule_json'];
$date_created = date('Y-m-d H:i:s');

$sql = "INSERT INTO user_schedules (user_email, schedule_json, date_created)
        VALUES ('$email', '$schedule_json', '$date_created')
        ON DUPLICATE KEY UPDATE schedule_json='$schedule_json', date_created='$date_created'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$conn->close();
?>
