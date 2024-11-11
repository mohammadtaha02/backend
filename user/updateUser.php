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
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

// get input data and print it for debugging
$data = json_decode(file_get_contents("php://input"), true);
file_put_contents('debug.txt', print_r($data, true));

// check if all required fields are present
$required_fields = ['id', 'email', 'name', 'birth_date', 'male', 'is_admin'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo json_encode(array("status" => "error", "message" => "Missing input fields: " . implode(', ', $missing_fields)));
    exit();
}

// assign variables from input data
$id = $data['id'];
$email = $data['email'];
$name = $data['name'];
$birth_date = $data['birth_date'];
$male = (int)$data['male'];
$is_admin = (int)$data['is_admin'];

$sql = "UPDATE users SET email = ?, name = ?, birth_date = ?, male = ?, is_admin = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssiis", $email, $name, $birth_date, $male, $is_admin, $id);

if ($stmt->execute()) {
    echo json_encode(array("status" => "success", "message" => "User updated successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to update user: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
