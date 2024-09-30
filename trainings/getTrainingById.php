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
    die("Connection failed: " . $conn->connect_error);
}

$training_id = $_GET['id'];

$sql = "SELECT id, user_id, training_date, description, image FROM trainings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $training = $result->fetch_assoc();
    echo json_encode($training);
} else {
    echo json_encode(array("message" => "Training not found"));
}

$conn->close();
?>
