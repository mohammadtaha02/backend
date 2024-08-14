<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"));

$user_id = $data->user_id;
$training_date = $data->training_date;
$description = $data->description;

$sql = "INSERT INTO trainings (user_id, training_date, description) VALUES ('$user_id', '$training_date', '$description')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("message" => "Training added successfully"));
} else {
    echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
}

$conn->close();
?>
