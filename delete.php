<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS request for preflight check
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the ID from the URL
$id = basename($_SERVER['REQUEST_URI']); // This will get the ID from the URL

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "enrollment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate that the 'id' is provided
if (!$id) {
    http_response_code(400);
    echo json_encode(["message" => "Student ID is required"]);
    exit;
}

// Prepare and bind
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Student deleted successfully"]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Student not found"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Failed to delete student"]);
}

$stmt->close();
$conn->close();
?>
