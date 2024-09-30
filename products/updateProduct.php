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

$id = $data->id;
$name = $data->name;
$description = $data->description;
$price = $data->price;
$quantity = $data->quantity;
$image = $data->image;

$sql = "UPDATE products SET name='$name', description='$description', price='$price', quantity='$quantity', image='$image' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("message" => "Product updated successfully"));
} else {
    echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
}

$conn->close();
?>
