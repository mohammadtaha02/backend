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

$sql = "SELECT * FROM trainings";
$result = $conn->query($sql);

$trainings = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($trainings, $row);
    }
}

echo json_encode($trainings);

$conn->close();
?>
