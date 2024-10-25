<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Change to your database username
$password = "";      // Change to your database password
$dbname = "findwithus";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array('status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error)));
}

// Get the data from the AngularJS form
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['phone_no']) || empty($data['state']) || 
    empty($data['district']) || empty($data['pincode']) || empty($data['desired_job']) || empty($data['password'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Please fill in all required fields.'));
    exit;
}

// Prepare SQL query
$stmt = $conn->prepare("INSERT INTO fwu_details (name, email, phone_no, state, district, pincode, desired_job, vehicle_type, password, other_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Hash the password
$hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

// Bind parameters and execute the query
$stmt->bind_param("ssssssssss", $data['name'], $data['email'], $data['phone_no'], $data['state'], $data['district'], $data['pincode'], $data['desired_job'], $data['vehicle_type'], $hashed_password, $data['other_info']);

if ($stmt->execute()) {
    echo json_encode(array('status' => 'success', 'message' => 'Application submitted successfully.'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Failed to submit application: ' . $stmt->error));
}

// Close the connection
$stmt->close();
$conn->close();
?>
