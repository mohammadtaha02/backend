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

$email = $_GET['email'];

// first, retrieve the user's ID from the users table using the email
$userSql = "SELECT id FROM users WHERE email = '$email'";
$userResult = $conn->query($userSql);

if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
    $userId = $user['id'];

    // fetch the subscription details from the subscriptions table using the user ID
    $subscriptionSql = "SELECT * FROM subscriptions WHERE user_id = '$userId'";
    $subscriptionResult = $conn->query($subscriptionSql);

    if ($subscriptionResult->num_rows > 0) {
        $subscriber = $subscriptionResult->fetch_assoc();
        echo json_encode($subscriber);
    } else {
        echo json_encode(['error' => 'No subscription found for this user.']);
    }
} else {
    echo json_encode(['error' => 'User not found.']);
}

$conn->close();
?>
