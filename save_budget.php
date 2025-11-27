<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || !isset($_POST["field"]) || !isset($_POST["value"])) {
    // If session or required POST data is missing, exit
    exit();
}

$user_id = $_SESSION["user_id"];
$field = $_POST["field"];
$value = $_POST["value"];

// Sanitize value and ensure it's a number
$value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

// Ensure value is a valid number
if (!is_numeric($value)) {
    exit(); // Exit if value is not numeric
}

// Ensure field exists in the table
$allowed_fields = ["income", "side_hustle", "rent", "utilities", "transport", "shopping", "entertainment", "maintenance", "emergency_fund", "savings"];
if (!in_array($field, $allowed_fields)) {
    exit(); // Exit if field is not allowed
}

// Update budget data
$sql = "UPDATE budget_data SET $field = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $value, $user_id);
$stmt->execute();

// Check if the query executed successfully
if ($stmt->affected_rows > 0) {
    // Success
    echo "Update successful.";
} else {
    // Failure (no rows affected)
    echo "No changes made.";
}

$stmt->close();
$conn->close();
?>
