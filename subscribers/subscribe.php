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


$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('debug.txt', print_r($data, true)); 

$subscribeData = $data['subscribeData'];

$userEmail = $subscribeData['userEmail'];
$age = $subscribeData['age'];
$height = $subscribeData['height'];
$weight = $subscribeData['weight'];
$gender = $subscribeData['gender'];
$fitnessGoal = $subscribeData['fitness_goal'];
$fitnessLevel = $subscribeData['fitness_level'];
$activityLevel = $subscribeData['activity_level'];

// Fetch user_id using the provided email
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    $conn->close();
    exit();
}

// Insert the subscription data into the database
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+1 year'));
$is_active = 1;

$sql = "INSERT INTO subscriptions (user_id, start_date, end_date, is_active, age, height, weight, gender, fitness_goal, fitness_level, activity_level) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issiiisssss", $user_id, $start_date, $end_date, $is_active, $age, $height, $weight, $gender, $fitnessGoal, $fitnessLevel, $activityLevel);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Subscription added successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add subscription."]);
}

$stmt->close();
$conn->close();

?>
