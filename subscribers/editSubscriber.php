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

if (!isset($data['id'], $data['start_date'], $data['end_date'], $data['activity_level'], $data['fitness_goal'], $data['fitness_level'])) {
    echo json_encode(array("status" => "error", "message" => "Missing input fields."));
    exit();
}

$id = $data['id'];
$start_date = $data['start_date'];
$end_date = $data['end_date'];
$activity_level = $data['activity_level'];
$fitness_goal = $data['fitness_goal'];
$fitness_level = $data['fitness_level'];

$sql = "UPDATE subscriptions SET start_date = ?, end_date = ?, activity_level = ?, fitness_goal = ?, fitness_level = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $start_date, $end_date, $activity_level, $fitness_goal, $fitness_level, $id);

if ($stmt->execute()) {
    echo json_encode(array("status" => "success", "message" => "Subscriber updated successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to update subscriber: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
