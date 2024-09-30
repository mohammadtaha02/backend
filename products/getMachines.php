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

$sql = "SELECT * FROM products WHERE category='Machine'";
$result = $conn->query($sql);

$machines = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($machines, $row);
    }
}

echo json_encode($machines);

$conn->close();
?>
