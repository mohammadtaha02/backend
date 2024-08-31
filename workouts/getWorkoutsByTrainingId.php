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

$training_id = $_GET['training_id'];

$sql = "SELECT * FROM workouts WHERE training_id=$training_id";
$result = $conn->query($sql);

$workouts = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($workouts, $row);
    }
}

echo json_encode($workouts);

$conn->close();
?>
