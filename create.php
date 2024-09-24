<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "enrollment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$input = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($input['firstName']) || !isset($input['lastName']) || !isset($input['course']) || 
    !isset($input['yearLevel']) || !isset($input['isEnrolled'])) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid input"]);
    exit();
}

// Extract data
$firstName = $input['firstName'];
$lastName = $input['lastName'];
$course = $input['course'];
$yearLevel = $input['yearLevel'];
$isEnrolled = $input['isEnrolled'];

// Validate yearLevel
$validYearLevels = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year'];
if (!in_array($yearLevel, $validYearLevels)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid yearLevel"]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO students (firstName, lastName, course, yearLevel, isEnrolled) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $firstName, $lastName, $course, $yearLevel, $isEnrolled);

if ($stmt->execute()) {
    echo json_encode(["message" => "Student added successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to add student"]);
}

$stmt->close();
$conn->close();
?>
