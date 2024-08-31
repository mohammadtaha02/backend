<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM products WHERE category='Supplement'";
$result = $conn->query($sql);

$supplements = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($supplements, $row);
    }
}

echo json_encode($supplements);

$conn->close();
?>