<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

// Get the raw input data and decode it into a PHP array
$input = json_decode(file_get_contents("php://input"), true);

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

// Extract data
$id = isset($input['id']) ? intval($input['id']) : null;
$firstName = $input['firstName'];
$lastName = $input['lastName'];
$course = $input['course'];
$yearLevel = $input['yearLevel'];
$isEnrolled = $input['isEnrolled'];

// Validate that the 'id' is provided
if (!$id) {
    http_response_code(400);
    echo json_encode(["message" => "Student ID is required"]);
    exit;
}

// Validate yearLevel
$validYearLevels = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year'];
if (!in_array($yearLevel, $validYearLevels)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid yearLevel"]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("UPDATE students SET firstName = ?, lastName = ?, course = ?, yearLevel = ?, isEnrolled = ? WHERE id = ?");
$stmt->bind_param("ssssii", $firstName, $lastName, $course, $yearLevel, $isEnrolled, $id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Student updated successfully"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Failed to update student"]);
}

$stmt->close();
$conn->close();
?>
