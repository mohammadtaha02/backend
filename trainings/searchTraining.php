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
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"));

$searchTerm = $data->searchTerm;

$sql = "SELECT * FROM trainings WHERE description LIKE '%$searchTerm%'";

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
