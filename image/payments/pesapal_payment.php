<?php
session_start();

$consumerKey = "OBlT4JdumNu8FoucIdXYBiNfKIpLkckM";
$consumerSecret = "jJ0Y2pnMBEVvlPxoAs1a9bknPIA=";

// API URLs
$token_url = "https://pay.pesapal.com/v3/api/Auth/RequestToken";
$order_url = "https://pay.pesapal.com/v3/api/Transactions/SubmitOrderRequest";

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

// Step 2: Create Order
$orderData = [
    "id" => uniqid(), // Unique Order ID
    "currency" => "KES",
    "amount" => "500", // Amount to pay
    "description" => "Budget Tracker Subscription",
    "callback_url" => "https://http://localhost/budget-tracker/pesapal_callback.php",
    "notification_id" => "YOUR_NOTIFICATION_ID",
    "billing_address" => [
        "email_address" => "tabithammbone210@gmail.com", // Replace with actual user's email
        "phone_number" => "0700000000",
        "first_name" => "John",
        "last_name" => "Doe"
    ]
];

$headers = [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
];

$ch = curl_init($order_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
$response = curl_exec($ch);
curl_close($ch);

$orderResponse = json_decode($response, true);

if (isset($orderResponse['redirect_url'])) {
    // Redirect user to Pesapal payment page
    header("Location: " . $orderResponse['redirect_url']);
    exit();
} else {
    echo "Failed to generate Pesapal payment link.";
}
?>
