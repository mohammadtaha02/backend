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
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['subscriber'])) {
    die(json_encode(array("message" => "Subscriber data missing")));
}

$subscriberData = $input['subscriber'];
$email = $subscriberData['userEmail'];

// get user_id from the users table using the email
$userQuery = "SELECT id FROM users WHERE email = ?";
$userStmt = $conn->prepare($userQuery);
if (!$userStmt) {
    die(json_encode(array("status" => "error", "message" => "SQL prepare failed (userQuery): " . $conn->error)));
}

$userStmt->bind_param("s", $email);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    die(json_encode(array("status" => "error", "message" => "User not found")));
}

$userRow = $userResult->fetch_assoc();
$user_id = $userRow['id'];

// update the subscriptions table using the user_id
$sql = "UPDATE subscriptions SET age=?, height=?, weight=?, gender=?, fitness_goal=?, fitness_level=?, activity_level=? WHERE user_id=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(array("status" => "error", "message" => "SQL prepare failed (updateQuery): " . $conn->error)));
}

// bind parameters
$stmt->bind_param(
    "iiissssi",
    $subscriberData['age'],
    $subscriberData['height'],
    $subscriberData['weight'],
    $subscriberData['gender'],
    $subscriberData['fitness_goal'],
    $subscriberData['fitness_level'],
    $subscriberData['activity_level'],
    $user_id
);

// execute the statement and handle errors
if ($stmt->execute()) {
    echo json_encode(array("status" => "success"));
} else {
    echo json_encode(array("status" => "error", "message" => $stmt->error));
}

$stmt->close();
$userStmt->close();
$conn->close();
?>
