<?php
require_once 'db_config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clean and validate input data
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$name = clean_input($_POST['name']);
$email = clean_input($_POST['email']);
$phone = clean_input($_POST['phone']);
$enquiry = clean_input($_POST['enquiry']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

if (!preg_match("/^\+?[0-9]{10,15}$/", $phone)) { // Updated phone validation
    die("Invalid phone number format");
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO contact_form_data (name, email, phone, enquiry) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $enquiry);

if ($stmt->execute()) {
    header("Location: thank_you.html"); // Redirect to a thank you page after successful submission
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>