<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gymawi";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

// Get the input data
$data = json_decode(file_get_contents("php://input"));

// Verify that email is set
if (isset($data->email)) {
    $email = $data->email;

    // Prepare the SQL statement with the status filter
    $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);

    // Check if prepare was successful
    if ($stmt === false) {
        die(json_encode(array("status" => "error", "message" => "SQL prepare failed: " . $conn->error)));
    }

    // Bind parameter and execute
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(array("status" => "error", "message" => "User not found or account inactive."));
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Email not provided."));
}

// Close the connection
$conn->close();
?>
