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
    die(json_encode(array("message" => "Connection failed: " . $conn->connect_error)));
}

$ageGroup = $_GET['age_group'];
$fitnessGoal = $_GET['fitness_goal'];
$difficulty = $_GET['difficulty'];

$sql = "SELECT * FROM exercises 
        WHERE age_group = ? AND fitness_goal = ? AND difficulty = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $ageGroup, $fitnessGoal, $difficulty);
$stmt->execute();
$result = $stmt->get_result();

$exercises = [];
while ($row = $result->fetch_assoc()) {
    $exercises[] = $row;
}

echo json_encode($exercises);
?>
