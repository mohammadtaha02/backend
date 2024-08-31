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

$workout_id = $_GET['id'];

$sql = "SELECT * FROM workouts WHERE id=$workout_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $workout = $result->fetch_assoc();
    echo json_encode($workout);
} else {
    echo json_encode(array("message" => "Workout not found"));
}

$conn->close();
?>
