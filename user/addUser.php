<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

$conn = mysqli_connect("localhost", "root", "1234", "gymawi");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;
$name = $data->name;
$male = $data->male;
$birthDate = $data->birthDate;

$sql = "INSERT INTO users (email, password, name, male, birth_date) VALUES ('$email', '$password', '$name', '$male', '$birthDate')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["message" => "User added successfully"]);
} else {
    echo json_encode(["message" => "Error: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
