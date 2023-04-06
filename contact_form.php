<?php
require_once 'db_config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("We're experiencing technical difficulties. Please try again later.");
}

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
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

    if (!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
        die("Invalid phone number format");
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO contact_form_data (name, email, phone, enquiry) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        die("We're experiencing technical difficulties. Please try again later.");
    }
    $stmt->bind_param("ssss", $name, $email, $phone, $enquiry);

    if ($stmt->execute()) {
        // Redirect to thank_you.html after successful submission
        header("Location: thank_you.html");
        exit();
    } else {
        error_log("Error: " . $stmt->error);
        echo "<p class='error-message'>We're experiencing technical difficulties. Please try again later.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>