<?php
session_start();
include "db.php"; // Include database connection

$consumerKey = "YOUR_CONSUMER_KEY";
$consumerSecret = "YOUR_CONSUMER_SECRET";

// Get transaction reference from URL
if (!isset($_GET['OrderTrackingId'])) {
    die("Invalid request.");
}

$orderTrackingId = $_GET['OrderTrackingId'];
$token_url = "https://pay.pesapal.com/v3/api/Auth/RequestToken";
$status_url = "https://pay.pesapal.com/v3/api/Transactions/GetTransactionStatus?orderTrackingId=$orderTrackingId";

// Step 1: Get OAuth Token
$credentials = base64_encode("$consumerKey:$consumerSecret");

$headers = [
    "Authorization: Basic $credentials",
    "Content-Type: application/json"
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);
$token = $tokenData['token'] ?? '';

if (!$token) {
    die("Failed to get Pesapal token");
}

// Step 2: Check Payment Status
$headers = [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
];

$ch = curl_init($status_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$statusData = json_decode($response, true);

if ($statusData['payment_status_description'] === "Completed") {
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in
    $stmt = $conn->prepare("UPDATE users SET payment_status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to the budget tracker
    header("Location: dashboard.php");
    exit();
} else {
    echo "Payment not completed. Please try again.";
}
?>
