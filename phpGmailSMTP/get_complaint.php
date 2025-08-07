<?php
// Session is already started in the calling file
require_once '../connection.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['email'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if user is admin
$email = mysqli_real_escape_string($con, $_SESSION['email']);
$sql = "SELECT role FROM usertable WHERE email = '$email'";
$result = mysqli_query($con, $sql);
if($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if($row['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit();
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if ID is provided
if(!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Complaint ID is required']);
    exit();
}

$id = mysqli_real_escape_string($con, $_GET['id']);

// Fetch complaint data
$sql = "SELECT * FROM garbageinfo WHERE Id = '$id'";
$result = mysqli_query($con, $sql);

if($result && mysqli_num_rows($result) > 0) {
    $complaint = mysqli_fetch_assoc($result);
    echo json_encode($complaint);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Complaint not found']);
}
?>
