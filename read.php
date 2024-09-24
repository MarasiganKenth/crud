<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

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

// Check if an 'id' is provided in the request
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    // Fetch a specific student by 'id'
    $stmt = $conn->prepare("SELECT id, firstName, lastName, course, yearLevel, isEnrolled FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        // Convert id and isEnrolled to integers
        $student['id'] = intval($student['id']);
        $student['isEnrolled'] = intval($student['isEnrolled']);
        echo json_encode($student);
    } else {
        echo json_encode(["message" => "Student not found"]);
    }

    $stmt->close();
} else {
    // Fetch all students
    $sql = "SELECT id, firstName, lastName, course, yearLevel, isEnrolled FROM students";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            // Convert id and isEnrolled to integers
            $row['id'] = intval($row['id']);
            $row['isEnrolled'] = intval($row['isEnrolled']);
            $students[] = $row;
        }
        echo json_encode($students);
    } else {
        echo json_encode([]);
    }
}

$conn->close();
?>
